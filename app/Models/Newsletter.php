<?php
/**
 * Bulten abonelikleri — cift onay (double opt-in).
 *
 * Akis:
 *   1. Ziyaretci adresini girer ve acik riza kutusunu isaretler -> `pending`
 *   2. Adrese giden baglantiya tiklar                            -> `active`
 *   3. Her iletideki cikis baglantisina tiklar                   -> `unsubscribed`
 *
 * Kayit hicbir adimda silinmez. `unsubscribed` satiri, onayin geri
 * alindiginin kaydidir; silinirse ayni adres icin "onay vardi" ile "onay
 * geri alindi" ayirt edilemez.  DOCS.md 8.4, 12
 *
 * Adresin listede olup olmadigi disariya sizdirilmaz: `subscribe()` her
 * durumda ayni sonucu doner, cagiran taraf ziyaretciye tek bir mesaj gosterir.
 */

declare(strict_types=1);

namespace Arcates\Models;

use Arcates\Core\Security;

final class Newsletter extends Model
{
    protected static string $table = 'newsletter_subscribers';

    public const STATUSES = [
        'pending'      => 'Onay bekliyor',
        'active'       => 'Etkin',
        'unsubscribed' => 'Çıktı',
    ];

    /** Onay ve cikis baglantilarindaki gizli anahtarin uzunlugu. */
    public const TOKEN_LENGTH = 64;

    /**
     * Aboneligi baslatir ya da tazeler.
     *
     * Her cagride yeni bir anahtar uretilir: eski baglantiyi tasiyan bir
     * e-posta baskasinin eline gectiyse gecersiz kalsin.
     *
     * @return array{token:string, zaten_etkin:bool}
     */
    public static function subscribe(string $email, string $lang, ?string $ip, string $source): array
    {
        $email = self::normalize($email);
        $token = bin2hex(random_bytes(32));
        $simdi = date('Y-m-d H:i:s');

        $mevcut = self::db()->first(
            'SELECT id, status FROM newsletter_subscribers WHERE email = :email',
            [':email' => $email]
        );

        if ($mevcut === null) {
            self::db()->insert('newsletter_subscribers', [
                'email'          => $email,
                'lang'           => $lang !== '' ? substr($lang, 0, 2) : null,
                'status'         => 'pending',
                'token'          => $token,
                'consent_at'     => $simdi,
                'consent_ip'     => Security::packIp($ip),
                'consent_source' => mb_substr($source, 0, 255),
            ]);

            return ['token' => $token, 'zaten_etkin' => false];
        }

        // Zaten etkin bir abone yeniden kaydolmaya calisirsa yeni onay
        // istemeyiz; ikinci bir onay e-postasi gondermek gereksiz ve
        // rahatsiz edici olur. Anahtari yine de tazeleriz ki cagiran taraf
        // isterse "zaten kayitlisiniz" iletisinde cikis baglantisi verebilsin.
        $etkin = $mevcut['status'] === 'active';

        self::db()->update(
            'newsletter_subscribers',
            $etkin
                ? ['token' => $token]
                : [
                    'status'         => 'pending',
                    'token'          => $token,
                    'lang'           => $lang !== '' ? substr($lang, 0, 2) : null,
                    'consent_at'     => $simdi,
                    'consent_ip'     => Security::packIp($ip),
                    'consent_source' => mb_substr($source, 0, 255),
                    // Yeniden kaydolan bir cikmis abone temiz sayfa acar.
                    'unsubscribed_at' => null,
                    'confirmed_at'    => null,
                ],
            ['id' => (int) $mevcut['id']]
        );

        return ['token' => $token, 'zaten_etkin' => $etkin];
    }

    /** Onay baglantisi. Zaten etkinse de true doner (baglantiya iki kez tiklamak hata degil). */
    public static function confirm(string $token): bool
    {
        $satir = self::byToken($token);
        if ($satir === null) {
            return false;
        }

        if ($satir['status'] === 'active') {
            return true;
        }

        self::db()->update(
            'newsletter_subscribers',
            ['status' => 'active', 'confirmed_at' => date('Y-m-d H:i:s'), 'unsubscribed_at' => null],
            ['id' => (int) $satir['id']]
        );

        return true;
    }

    /** Cikis baglantisi. Zaten cikmissa da true doner. */
    public static function unsubscribe(string $token): bool
    {
        $satir = self::byToken($token);
        if ($satir === null) {
            return false;
        }

        if ($satir['status'] === 'unsubscribed') {
            return true;
        }

        self::db()->update(
            'newsletter_subscribers',
            ['status' => 'unsubscribed', 'unsubscribed_at' => date('Y-m-d H:i:s')],
            ['id' => (int) $satir['id']]
        );

        return true;
    }

    /** Anahtarla kayit. Bicimi tutmayan anahtar veritabanina hic gitmez. */
    public static function byToken(string $token): ?array
    {
        if (preg_match('~^[a-f0-9]{' . self::TOKEN_LENGTH . '}$~', $token) !== 1) {
            return null;
        }

        return self::db()->first(
            'SELECT * FROM newsletter_subscribers WHERE token = :token',
            [':token' => $token]
        );
    }

    /** Ayni IP'den son bir saatteki kayit sayisi. */
    public static function recentByIp(?string $ip): int
    {
        $paket = Security::packIp($ip);
        if ($paket === null) {
            return 0;
        }

        return (int) self::db()->value(
            'SELECT COUNT(*) FROM newsletter_subscribers
              WHERE consent_ip = :ip AND consent_at > :since',
            [':ip' => $paket, ':since' => date('Y-m-d H:i:s', time() - 3600)]
        );
    }

    /** Panel listesi. */
    public static function listing(string $status = '', string $search = '', int $limit = 100, int $offset = 0): array
    {
        $sql  = 'SELECT * FROM newsletter_subscribers WHERE 1 = 1';
        $args = [];

        if ($status !== '' && isset(self::STATUSES[$status])) {
            $sql .= ' AND status = :status';
            $args[':status'] = $status;
        }
        if ($search !== '') {
            $sql .= ' AND email LIKE :q ESCAPE \'\\\\\'';
            $args[':q'] = '%' . self::escapeLike($search) . '%';
        }

        $sql .= ' ORDER BY created_at DESC LIMIT ' . max(1, min(500, $limit)) . ' OFFSET ' . max(0, $offset);

        $rows = self::db()->all($sql, $args);
        foreach ($rows as &$row) {
            $row['ip'] = Security::unpackIp($row['consent_ip'] ?? null);
        }
        unset($row);

        return $rows;
    }

    /** Durum basina sayac. */
    public static function counts(): array
    {
        $out = array_fill_keys(array_keys(self::STATUSES), 0);

        foreach (self::db()->all('SELECT status, COUNT(*) AS n FROM newsletter_subscribers GROUP BY status') as $row) {
            $out[(string) $row['status']] = (int) $row['n'];
        }

        return $out;
    }

    /**
     * CSV govdesi.
     *
     * Anahtar disari verilmez: CSV dosyasi elden ele dolasabilir, anahtari
     * bilen herkes o kisiyi listeden cikarabilir. Onay zamani, IP ve kaynak
     * sayfa ise bilerek yer alir — onayin kaniti bunlardir.
     */
    public static function toCsv(): string
    {
        $handle = fopen('php://temp', 'r+');
        if ($handle === false) {
            return '';
        }

        fputcsv($handle, ['E-posta', 'Durum', 'Dil', 'Onay zamanı', 'Onay IP', 'Kaynak', 'Onaylandı', 'Çıkış']);

        foreach (self::db()->all('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC') as $row) {
            fputcsv($handle, [
                (string) $row['email'],
                self::STATUSES[(string) $row['status']] ?? (string) $row['status'],
                (string) ($row['lang'] ?? ''),
                (string) ($row['consent_at'] ?? ''),
                (string) (Security::unpackIp($row['consent_ip'] ?? null) ?? ''),
                (string) ($row['consent_source'] ?? ''),
                (string) ($row['confirmed_at'] ?? ''),
                (string) ($row['unsubscribed_at'] ?? ''),
            ]);
        }

        rewind($handle);
        $csv = (string) stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    private static function normalize(string $email): string
    {
        return mb_substr(mb_strtolower(trim($email)), 0, 190);
    }

    /** LIKE jokerleri harf olarak aransin. */
    private static function escapeLike(string $q): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
    }
}
