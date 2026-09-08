<?php
/**
 * Anasayfa bolumleri.
 *
 * Bolum sirasi sabittir (surum 1); her bolum panelden acilip kapatilabilir ve
 * icerigi duzenlenebilir. DOCS.md 5, 8.3, 9.2
 */

declare(strict_types=1);

namespace Arcates\Models;

use Arcates\Core\Database;
use Arcates\Core\Security;
use Arcates\Core\Seeder;

final class HomeSection extends Model
{
    protected static string $table = 'home_sections';
    protected static string $translations = 'home_section_translations';
    protected static string $foreignKey = 'section_key';

    /** Bolum anahtari => insan okunur ad. DOCS.md 5 */
    public const LABELS = [
        'header'   => 'Üst menü',
        'hero'     => 'Kahraman',
        'strip'    => 'Sektör grubu',
        'services' => 'Hizmet kartları',
        'steps'    => 'Süreç',
        'works'    => 'Örnek siteler',
        'coast'    => 'Hizmet bölgeleri',
        'faq'      => 'SSS',
        'cta'      => 'Çağrı bandı',
        'footer'   => 'Alt bilgi',
    ];

    /**
     * Tum bolumleri, verilen dildeki icerikleriyle birlikte dondurur.
     * Veritabanindaki eski sort degerleri R5 oncesinden kalmis olabilir;
     * sabit urun sirasi LABELS ile belirlenir.
     *
     * @return array<string, array{key:string, is_active:bool, sort:int, config:array, content:array}>
     */
    public static function all(string $lang): array
    {
        $rows = self::db()->all(
            'SELECT s.`key`, s.is_active, s.sort, s.config, t.content
               FROM home_sections s
               LEFT JOIN home_section_translations t
                      ON t.section_key = s.`key` AND t.lang = :lang',
            [':lang' => $lang]
        );

        $byKey = [];
        foreach ($rows as $row) {
            $byKey[$row['key']] = [
                'key'       => (string) $row['key'],
                'is_active' => (int) $row['is_active'] === 1,
                'sort'      => (int) $row['sort'],
                'config'    => self::decode($row['config']),
                'content'   => self::decode($row['content']),
            ];
        }

        $out = [];
        foreach (array_keys(self::LABELS) as $key) {
            if (isset($byKey[$key])) {
                $out[$key] = $byKey[$key];
                unset($byKey[$key]);
            }
        }
        foreach ($byKey as $key => $section) {
            $out[$key] = $section;
        }

        return $out;
    }

    /** Tek bolum. */
    public static function get(string $key, string $lang): ?array
    {
        $all = self::all($lang);
        return $all[$key] ?? null;
    }

    /**
     * Icerigi verilen dilde dondurur; o dilde ceviri yoksa varsayilan dile
     * duser. Boylece yeni bir dil eklendiginde anasayfa bos kalmaz.
     */
    public static function content(string $key, string $lang, string $fallback = 'tr'): array
    {
        $row = self::db()->first(
            'SELECT content FROM home_section_translations WHERE section_key = :key AND lang = :lang',
            [':key' => $key, ':lang' => $lang]
        );

        if ($row === null && $lang !== $fallback) {
            $row = self::db()->first(
                'SELECT content FROM home_section_translations WHERE section_key = :key AND lang = :lang',
                [':key' => $key, ':lang' => $fallback]
            );
        }

        return $row === null ? [] : self::decode($row['content']);
    }

    /** Bolumun ayarlarini kaydeder. */
    public static function saveConfig(string $key, array $config, bool $isActive): void
    {
        self::db()->update(
            'home_sections',
            ['config' => Security::json($config), 'is_active' => $isActive ? 1 : 0],
            ['key' => $key]
        );
    }

    /** Bolumun bir dildeki icerigini kaydeder. */
    public static function saveContent(string $key, string $lang, array $content): void
    {
        self::db()->upsert(
            'home_section_translations',
            ['section_key' => $key, 'lang' => $lang, 'content' => Security::json($content)],
            ['content']
        );
    }

    /** Bolumu acar veya kapatir. DOCS.md 9.2, test F-14 */
    public static function toggle(string $key): bool
    {
        $current = (int) self::db()->value('SELECT is_active FROM home_sections WHERE `key` = :key', [':key' => $key]);
        $next    = $current === 1 ? 0 : 1;

        self::db()->update('home_sections', ['is_active' => $next], ['key' => $key]);

        return $next === 1;
    }

    /** Eksik bolumleri varsayilanlarla tamamlar. */
    public static function ensureDefaults(): void
    {
        (new Seeder(Database::instance()))->homeSections();
    }

    private static function decode(mixed $raw): array
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
