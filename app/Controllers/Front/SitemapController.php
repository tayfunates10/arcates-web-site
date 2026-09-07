<?php
/**
 * `sitemap.xml` uretimi.
 *
 * Dinamik uretilir, yalnizca yayinlanmis icerik girer, `lastmod`
 * `updated_at`'ten gelir.  DOCS.md 11.4 — testler F-13, U-14
 *
 * `noindex` isaretli ceviriler haritaya girmez; her giris kendi dilindeki
 * karsiliklarini `xhtml:link` ile bildirir.  DOCS.md 11.3
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Security;
use Throwable;

final class SitemapController
{
    public function index(Request $request, array $params): Response
    {
        return Response::xml($this->build());
    }

    /** Site haritasini uretir. */
    public function build(): string
    {
        $entries = $this->entries();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
            . ' xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($entries as $entry) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . Security::e($entry['loc']) . "</loc>\n";

            if (($entry['lastmod'] ?? '') !== '') {
                $xml .= '    <lastmod>' . Security::e($entry['lastmod']) . "</lastmod>\n";
            }

            $xml .= '    <changefreq>' . Security::e($entry['changefreq']) . "</changefreq>\n";
            $xml .= '    <priority>' . Security::e($entry['priority']) . "</priority>\n";

            foreach ($entry['alternates'] ?? [] as $code => $href) {
                $xml .= '    <xhtml:link rel="alternate" hreflang="' . Security::e((string) $code)
                    . '" href="' . Security::e($href) . "\"/>\n";
            }

            $xml .= "  </url>\n";
        }

        return $xml . '</urlset>' . "\n";
    }

    /**
     * Haritaya girecek adresler.
     *
     * @return array<int, array{loc:string, lastmod:string, changefreq:string, priority:string, alternates:array}>
     */
    public function entries(): array
    {
        $entries = [];

        // Anasayfa — her etkin dilde.
        foreach (Lang::codes() as $code) {
            $entries[] = [
                'loc'        => url('/', $code),
                'lastmod'    => date('Y-m-d'),
                'changefreq' => 'weekly',
                'priority'   => $code === Lang::defaultCode() ? '1.0' : '0.8',
                'alternates' => $this->homeAlternates(),
            ];
        }

        $db = Database::instance();

        // Sayfalar — yalnizca yayinlanmis ve dizine girmesine izin verilenler.
        try {
            $rows = $db->all(
                'SELECT p.id, p.type, p.updated_at, t.lang, t.slug, t.robots
                   FROM pages p
                   JOIN page_translations t ON t.page_id = p.id
                  WHERE p.status = :status
                  ORDER BY p.type, t.lang, t.slug',
                [':status' => 'published']
            );

            $byPage = [];
            foreach ($rows as $row) {
                if ($this->isNoindex((string) $row['robots'])) {
                    continue;
                }
                $byPage[(int) $row['id']][] = $row;
            }

            foreach ($byPage as $translations) {
                $alternates = [];
                if (count($translations) > 1) {
                    foreach ($translations as $row) {
                        $alternates[$row['lang']] = url('/' . $row['slug'], (string) $row['lang']);
                    }
                }

                foreach ($translations as $row) {
                    $entries[] = [
                        'loc'        => url('/' . $row['slug'], (string) $row['lang']),
                        'lastmod'    => $this->date($row['updated_at']),
                        'changefreq' => 'monthly',
                        'priority'   => $row['type'] === 'page' ? '0.6' : '0.8',
                        'alternates' => $alternates,
                    ];
                }
            }
        } catch (Throwable) {
            // Tablolar henuz yoksa harita yalnizca anasayfayi icerir.
        }

        // Referanslar
        $entries = array_merge($entries, $this->collection(
            'SELECT p.updated_at, t.lang, t.slug, t.robots
               FROM projects p
               JOIN project_translations t ON t.project_id = p.id
              WHERE p.status = :status',
            [':status' => 'published'],
            '/referanslar/',
            '0.6'
        ));

        // Blog yazilari — ileri tarihli olanlar haritaya girmez.
        $entries = array_merge($entries, $this->collection(
            'SELECT p.updated_at, t.lang, t.slug, t.robots
               FROM posts p
               JOIN post_translations t ON t.post_id = p.id
              WHERE p.status = :status AND (p.published_at IS NULL OR p.published_at <= NOW())',
            [':status' => 'published'],
            '/blog/',
            '0.5'
        ));

        return $entries;
    }

    /** Ortak koleksiyon sorgusu. */
    private function collection(string $sql, array $args, string $prefix, string $priority): array
    {
        $out = [];

        try {
            foreach (Database::instance()->all($sql, $args) as $row) {
                if ($this->isNoindex((string) $row['robots'])) {
                    continue;
                }
                $out[] = [
                    'loc'        => url($prefix . $row['slug'], (string) $row['lang']),
                    'lastmod'    => $this->date($row['updated_at'] ?? null),
                    'changefreq' => 'monthly',
                    'priority'   => $priority,
                    'alternates' => [],
                ];
            }
        } catch (Throwable) {
        }

        return $out;
    }

    private function homeAlternates(): array
    {
        $out = [];
        foreach (Lang::codes() as $code) {
            $out[$code] = url('/', $code);
        }
        return count($out) > 1 ? $out : [];
    }

    private function isNoindex(string $robots): bool
    {
        return str_contains(strtolower($robots), 'noindex');
    }

    private function date(mixed $value): string
    {
        if (!is_string($value) || $value === '') {
            return '';
        }
        $time = strtotime($value);
        return $time === false ? '' : date('Y-m-d', $time);
    }
}
