<?php
/**
 * Form kayitlari ve donusum takibi.
 *
 * Hangi ilce veya hizmet sayfasinin gercekten is getirdigini gostermek icin
 * `source_url`, `referrer`, `utm` ve dil saklanir.  DOCS.md 12, 9.9
 */

declare(strict_types=1);

namespace Arcates\Models;

use Arcates\Core\Config;
use Arcates\Core\Security;

final class Submission extends Model
{
    protected static string $table = 'submissions';

    /** Durum etiketleri; donusum hunisinin sirasi budur. DOCS.md 9.1 */
    public const STATUSES = [
        'new'       => 'Yeni',
        'contacted' => 'Arandı',
        'quoted'    => 'Teklif',
        'won'       => 'Kazanıldı',
        'lost'      => 'Kaybedildi',
    ];

    public static function listing(string $status = '', string $search = '', int $limit = 100, int $offset = 0): array
    {
        $sql  = 'SELECT * FROM submissions WHERE 1 = 1';
        $args = [];

        if ($status !== '' && isset(self::STATUSES[$status])) {
            $sql            .= ' AND status = :status';
            $args[':status'] = $status;
        }

        if ($search !== '') {
            $sql        .= ' AND (name LIKE :q OR email LIKE :q OR phone LIKE :q OR message LIKE :q OR source_url LIKE :q)';
            $args[':q']  = '%' . $search . '%';
        }

        $sql .= ' ORDER BY created_at DESC LIMIT ' . max(1, min(500, $limit)) . ' OFFSET ' . max(0, $offset);

        $rows = self::db()->all($sql, $args);

        foreach ($rows as &$row) {
            $row['ip']  = Security::unpackIp($row['ip']);
            $row['utm'] = self::decodeUtm($row['utm'] ?? null);
        }

        return $rows;
    }

    public static function countBy(string $status = '', string $search = ''): int
    {
        $sql  = 'SELECT COUNT(*) FROM submissions WHERE 1 = 1';
        $args = [];

        if ($status !== '' && isset(self::STATUSES[$status])) {
            $sql            .= ' AND status = :status';
            $args[':status'] = $status;
        }
        if ($search !== '') {
            $sql        .= ' AND (name LIKE :q OR email LIKE :q OR phone LIKE :q OR message LIKE :q OR source_url LIKE :q)';
            $args[':q']  = '%' . $search . '%';
        }

        return (int) self::db()->value($sql, $args);
    }

    public static function get(int $id): ?array
    {
        $row = self::db()->first('SELECT * FROM submissions WHERE id = :id', [':id' => $id]);
        if ($row === null) {
            return null;
        }

        $row['ip']  = Security::unpackIp($row['ip']);
        $row['utm'] = self::decodeUtm($row['utm'] ?? null);

        return $row;
    }

    /**
     * Donusum raporu: hangi sayfa kac kayit ve kac kazanim getirdi.
     * DOCS.md 12 — test F-09
     */
    public static function conversionReport(int $days = 90): array
    {
        return self::db()->all(
            'SELECT source_url,
                    COUNT(*) AS total,
                    SUM(status = :won) AS won,
                    SUM(status = :lost) AS lost,
                    SUM(status IN (:quoted, :contacted)) AS in_progress
               FROM submissions
              WHERE created_at >= :since AND source_url IS NOT NULL AND source_url <> :empty
              GROUP BY source_url
              ORDER BY won DESC, total DESC
              LIMIT 50',
            [
                ':won'       => 'won',
                ':lost'      => 'lost',
                ':quoted'    => 'quoted',
                ':contacted' => 'contacted',
                ':since'     => date('Y-m-d H:i:s', strtotime("-{$days} days")),
                ':empty'     => '',
            ]
        );
    }

    /** Kaynak dagilimi (utm_source). */
    public static function sourceReport(int $days = 90): array
    {
        $rows = self::db()->all(
            'SELECT utm, referrer FROM submissions WHERE created_at >= :since',
            [':since' => date('Y-m-d H:i:s', strtotime("-{$days} days"))]
        );

        $counts = [];
        foreach ($rows as $row) {
            $utm    = self::decodeUtm($row['utm'] ?? null);
            $source = $utm['utm_source'] ?? null;

            if ($source === null) {
                $referrer = (string) ($row['referrer'] ?? '');
                if ($referrer === '') {
                    $source = 'dogrudan';
                } else {
                    $host   = parse_url($referrer, PHP_URL_HOST);
                    $source = is_string($host) && $host !== '' ? $host : 'diger';
                }
            }

            $counts[$source] = ($counts[$source] ?? 0) + 1;
        }

        arsort($counts);

        $out = [];
        foreach ($counts as $source => $count) {
            $out[] = ['source' => (string) $source, 'count' => $count];
        }

        return $out;
    }

    /**
     * Saklama suresi dolan kayitlari siler.  DOCS.md 10.9
     *
     * @return int Silinen kayit sayisi.
     */
    public static function purgeExpired(?int $days = null): int
    {
        $days = $days ?? (int) Config::get('privacy.submission_retention_days', 730);
        if ($days <= 0) {
            return 0;
        }

        return self::db()->run(
            'DELETE FROM submissions WHERE created_at < :cutoff',
            [':cutoff' => date('Y-m-d H:i:s', strtotime("-{$days} days"))]
        )->rowCount();
    }

    /** CSV disa aktarma. DOCS.md 9.9 */
    public static function toCsv(array $rows): string
    {
        $handle = fopen('php://temp', 'r+');
        if ($handle === false) {
            return '';
        }

        fputcsv($handle, [
            'Kayıt', 'Tarih', 'Durum', 'Ad', 'E-posta', 'Telefon', 'Hizmet',
            'Mesaj', 'Kaynak sayfa', 'Geldiği yer', 'UTM kaynak', 'UTM ortam',
            'UTM kampanya', 'Dil', 'KVKK onayı', 'Not',
        ]);

        foreach ($rows as $row) {
            $utm = is_array($row['utm'] ?? null) ? $row['utm'] : [];

            fputcsv($handle, [
                $row['id'] ?? '',
                $row['created_at'] ?? '',
                self::STATUSES[$row['status'] ?? 'new'] ?? '',
                $row['name'] ?? '',
                $row['email'] ?? '',
                $row['phone'] ?? '',
                $row['service'] ?? '',
                preg_replace('/\s+/u', ' ', (string) ($row['message'] ?? '')),
                $row['source_url'] ?? '',
                $row['referrer'] ?? '',
                $utm['utm_source'] ?? '',
                $utm['utm_medium'] ?? '',
                $utm['utm_campaign'] ?? '',
                $row['lang'] ?? '',
                (int) ($row['kvkk_consent'] ?? 0) === 1 ? 'evet' : 'hayir',
                preg_replace('/\s+/u', ' ', (string) ($row['note'] ?? '')),
            ]);
        }

        rewind($handle);
        $csv = (string) stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    private static function decodeUtm(mixed $raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }
        if (!is_string($raw) || $raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}
