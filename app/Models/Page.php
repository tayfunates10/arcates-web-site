<?php
/**
 * Sayfalar — `page`, `service`, `location`, `sector` turleri.
 *
 * DOCS.md 4.1-4.4, 8.2, 9.3
 */

declare(strict_types=1);

namespace Arcates\Models;

use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Security;

final class Page extends Model
{
    protected static string $table = 'pages';
    protected static string $translations = 'page_translations';
    protected static string $foreignKey = 'page_id';

    public const TYPES = [
        'page'     => 'Sayfa',
        'service'  => 'Hizmet',
        'location' => 'Ilce',
        'sector'   => 'Sektor',
    ];

    /** Tur basina varsayilan sablon. DOCS.md 4 */
    public const TEMPLATES = [
        'page'     => 'page',
        'service'  => 'service',
        'location' => 'location',
        'sector'   => 'sector',
    ];

    /**
     * Yayinlanmis sayfayi slug ile bulur.
     *
     * Taslak sayfa on yuzde 404 doner.  DOCS.md test F-02
     */
    public static function published(string $slug, string $lang): ?array
    {
        return self::db()->first(
            'SELECT p.*, t.title, t.slug, t.excerpt, t.content, t.meta_title, t.meta_description,
                    t.og_image_id, t.canonical, t.robots, t.schema_type, t.word_count, t.lang
               FROM pages p
               JOIN page_translations t ON t.page_id = p.id
              WHERE t.lang = :lang AND t.slug = :slug AND p.status = :status
              LIMIT 1',
            [':lang' => $lang, ':slug' => $slug, ':status' => 'published']
        );
    }

    /** Herhangi bir durumda sayfayi slug ile bulur (onizleme icin). */
    public static function anyBySlug(string $slug, string $lang): ?array
    {
        return self::db()->first(
            'SELECT p.*, t.title, t.slug, t.content, t.lang
               FROM pages p
               JOIN page_translations t ON t.page_id = p.id
              WHERE t.lang = :lang AND t.slug = :slug
              LIMIT 1',
            [':lang' => $lang, ':slug' => $slug]
        );
    }

    /**
     * Panel listesi.
     *
     * @param string $type   Bos ise tum turler.
     * @param string $lang   Basliklarin gosterilecegi dil.
     */
    public static function listing(string $type = '', string $lang = 'tr', string $search = ''): array
    {
        $sql = 'SELECT p.id, p.type, p.template, p.status, p.district, p.sort, p.updated_at,
                       t.title, t.slug, t.word_count, t.robots
                  FROM pages p
                  LEFT JOIN page_translations t ON t.page_id = p.id AND t.lang = :lang
                 WHERE 1 = 1';

        $args = [':lang' => $lang];

        if ($type !== '' && isset(self::TYPES[$type])) {
            $sql            .= ' AND p.type = :type';
            $args[':type']   = $type;
        }

        if ($search !== '') {
            $sql             .= ' AND (t.title LIKE :search OR t.slug LIKE :search)';
            $args[':search']  = '%' . $search . '%';
        }

        $sql .= ' ORDER BY p.type, p.sort, t.title';

        return self::db()->all($sql, $args);
    }

    /** Yayinlanmis sayfalarin adresleri; sitemap ve ic link denetimi icin. */
    public static function publishedTranslations(?string $lang = null): array
    {
        $sql = 'SELECT p.id, p.type, p.updated_at, t.lang, t.slug, t.title, t.robots
                  FROM pages p
                  JOIN page_translations t ON t.page_id = p.id
                 WHERE p.status = :status';
        $args = [':status' => 'published'];

        if ($lang !== null) {
            $sql          .= ' AND t.lang = :lang';
            $args[':lang'] = $lang;
        }

        return self::db()->all($sql . ' ORDER BY p.type, t.lang, t.slug', $args);
    }

    /** Sayfaya atanmis SSS kayitlari. DOCS.md 9.4, 11.2 */
    public static function faqs(int $pageId, string $lang): array
    {
        return self::db()->all(
            'SELECT f.id, ft.question, ft.answer
               FROM faq_page fp
               JOIN faqs f ON f.id = fp.faq_id AND f.status = 1
               JOIN faq_translations ft ON ft.faq_id = f.id AND ft.lang = :lang
              WHERE fp.page_id = :page
              ORDER BY f.sort, f.id',
            [':page' => $pageId, ':lang' => $lang]
        );
    }

    /** Ilceye ait referanslar. DOCS.md 4.7 — her ilce sayfasi en az bir ornek icermeli */
    public static function districtProjects(string $district, string $lang, int $limit = 3): array
    {
        return self::db()->all(
            'SELECT p.id, p.client_name, p.sector, p.district, p.live_url, p.cover_id,
                    t.title, t.slug, t.excerpt
               FROM projects p
               JOIN project_translations t ON t.project_id = p.id AND t.lang = :lang
              WHERE p.status = :status AND p.district = :district
              ORDER BY p.sort, p.id DESC
              LIMIT ' . max(1, min(24, $limit)),
            [':lang' => $lang, ':status' => 'published', ':district' => $district]
        );
    }

    /**
     * Sayfanin tum dillerdeki adresleri.
     *
     * `hreflang` seti bu listeden uretilir; karsiligi olmayan dil girmez.
     * DOCS.md 11.3
     */
    public static function alternates(int $pageId): array
    {
        $rows = self::db()->all(
            'SELECT t.lang, t.slug, t.robots
               FROM page_translations t
               JOIN pages p ON p.id = t.page_id
              WHERE t.page_id = :id AND p.status = :status',
            [':id' => $pageId, ':status' => 'published']
        );

        $out = [];
        foreach ($rows as $row) {
            $out[$row['lang']] = $row['slug'];
        }

        return $out;
    }

    /** Sayfayi ceviriyle birlikte kaydeder. */
    public static function save(array $page, array $translations, ?int $id = null): int
    {
        $db = self::db();

        return $db->transaction(static function (Database $db) use ($page, $translations, $id): int {
            if ($id !== null && $id > 0) {
                $db->update('pages', $page, ['id' => $id]);
                $pageId = $id;
            } else {
                $pageId = $db->insert('pages', $page);
            }

            foreach ($translations as $lang => $fields) {
                if (($fields['title'] ?? '') === '') {
                    // Basligi olmayan dil kaydedilmez; eksik ceviri hreflang'e
                    // girmemelidir. DOCS.md 11.3
                    $db->run(
                        'DELETE FROM page_translations WHERE page_id = :id AND lang = :lang',
                        [':id' => $pageId, ':lang' => $lang]
                    );
                    continue;
                }

                $fields['word_count'] = Security::wordCount((string) ($fields['content'] ?? ''));

                $existing = $db->first(
                    'SELECT id FROM page_translations WHERE page_id = :id AND lang = :lang',
                    [':id' => $pageId, ':lang' => $lang]
                );

                if ($existing !== null) {
                    $db->update('page_translations', $fields, ['id' => (int) $existing['id']]);
                } else {
                    $fields['page_id'] = $pageId;
                    $fields['lang']    = $lang;
                    $db->insert('page_translations', $fields);
                }
            }

            return $pageId;
        });
    }

    /** Sayfanin on yuzdeki adresi. */
    public static function url(string $slug, string $lang): string
    {
        return url('/' . ltrim($slug, '/'), $lang);
    }
}
