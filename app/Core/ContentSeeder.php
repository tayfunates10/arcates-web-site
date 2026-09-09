<?php
/**
 * Eksik baslangic icerigini idempotent olarak tamamlar.
 *
 * Var olan kayitlara dokunmaz; yalnizca eksik sayfa, bolge, sektor,
 * ornek proje, blog ve SSS kayitlarini ekler. Hem yeni kurulum hem de
 * paneldeki guvenli bakim islemi bu sinifi kullanabilir.
 */

declare(strict_types=1);

namespace Arcates\Core;

use Arcates\Models\Faq;
use Arcates\Models\Page;
use Arcates\Models\Post;
use Arcates\Models\Project;

final class ContentSeeder
{
    public function __construct(private Database $db)
    {
    }

    /**
     * @return array{pages:int,projects:int,posts:int,faqs:int,linked_districts:int,skipped:int}
     */
    public function runMissing(): array
    {
        (new Seeder($this->db))->run();

        $summary = [
            'pages' => 0,
            'projects' => 0,
            'posts' => 0,
            'faqs' => 0,
            'linked_districts' => 0,
            'skipped' => 0,
        ];
        $slugToPage = [];

        $writePage = function (array $item, string $type) use (&$summary, &$slugToPage): void {
            $existing = $this->db->first(
                'SELECT page_id FROM page_translations WHERE lang = :lang AND slug = :slug',
                [':lang' => 'tr', ':slug' => $item['slug']]
            );

            if ($existing !== null) {
                $slugToPage[$item['slug']] = (int) $existing['page_id'];
                $summary['skipped']++;
                return;
            }

            $pageId = Page::save(
                [
                    'type' => $type,
                    'template' => Page::TEMPLATES[$type] ?? 'page',
                    'status' => 'published',
                    'district' => $item['district'] ?? null,
                    'sort' => (int) ($item['sort'] ?? 0),
                ],
                ['tr' => [
                    'title' => $item['title'],
                    'slug' => $item['slug'],
                    'excerpt' => $item['excerpt'] ?? null,
                    'content' => $item['content'],
                    'meta_title' => $item['meta_title'] ?? null,
                    'meta_description' => $item['meta_description'] ?? null,
                    'robots' => 'index,follow',
                    'schema_type' => $type === 'page' ? null : 'Service',
                ]]
            );

            $slugToPage[$item['slug']] = $pageId;
            $summary['pages']++;
        };

        foreach (require ARC_ROOT . '/db/seed/pages.php' as $item) {
            $writePage($item, $item['type']);
        }
        foreach (require ARC_ROOT . '/db/seed/locations.php' as $item) {
            $writePage($item + ['sort' => 30], 'location');
        }
        foreach (require ARC_ROOT . '/db/seed/sectors.php' as $item) {
            $writePage($item, 'sector');
        }

        foreach (require ARC_ROOT . '/db/seed/projects.php' as $item) {
            $exists = $this->db->first(
                'SELECT project_id FROM project_translations WHERE lang = :lang AND slug = :slug',
                [':lang' => 'tr', ':slug' => $item['slug']]
            );
            if ($exists !== null) {
                $summary['skipped']++;
                continue;
            }

            Project::save(
                [
                    'client_name' => $item['client'],
                    'sector' => $item['sector'],
                    'district' => $item['district'],
                    'status' => 'published',
                    'sort' => 0,
                ],
                ['tr' => [
                    'title' => $item['title'],
                    'slug' => $item['slug'],
                    'excerpt' => $item['excerpt'],
                    'content' => $item['content'],
                    'robots' => 'index,follow',
                ]],
                []
            );
            $summary['projects']++;
        }

        foreach (require ARC_ROOT . '/db/seed/posts.php' as $item) {
            $exists = $this->db->first(
                'SELECT post_id FROM post_translations WHERE lang = :lang AND slug = :slug',
                [':lang' => 'tr', ':slug' => $item['slug']]
            );
            if ($exists !== null) {
                $summary['skipped']++;
                continue;
            }

            Post::save(
                [
                    'category' => $item['category'],
                    'status' => 'published',
                    'published_at' => $item['published_at'],
                ],
                ['tr' => [
                    'title' => $item['title'],
                    'slug' => $item['slug'],
                    'excerpt' => $item['excerpt'],
                    'content' => $item['content'],
                    'meta_title' => $item['meta_title'],
                    'meta_description' => $item['meta_description'],
                    'robots' => 'index,follow',
                ]]
            );
            $summary['posts']++;
        }

        foreach (require ARC_ROOT . '/db/seed/faqs.php' as $item) {
            $exists = $this->db->first(
                'SELECT faq_id FROM faq_translations WHERE lang = :lang AND question = :q',
                [':lang' => 'tr', ':q' => $item['q']]
            );
            if ($exists !== null) {
                $summary['skipped']++;
                continue;
            }

            $pageIds = [];
            foreach ($item['pages'] as $slug) {
                if (isset($slugToPage[$slug])) {
                    $pageIds[] = $slugToPage[$slug];
                    continue;
                }
                $row = $this->db->first(
                    'SELECT page_id FROM page_translations WHERE lang = :lang AND slug = :slug',
                    [':lang' => 'tr', ':slug' => $slug]
                );
                if ($row !== null) {
                    $pageIds[] = (int) $row['page_id'];
                }
            }

            Faq::save(
                ['sort' => (int) $item['sort'], 'status' => 1],
                ['tr' => ['question' => $item['q'], 'answer' => $item['a']]],
                $pageIds,
                (bool) $item['home']
            );
            $summary['faqs']++;
        }

        foreach (Seeder::districtSeed() as $district) {
            $row = $this->db->first(
                'SELECT page_id FROM page_translations WHERE lang = :lang AND slug = :slug',
                [':lang' => 'tr', ':slug' => $district['slug']]
            );
            if ($row !== null) {
                $this->db->update(
                    'districts',
                    ['page_id' => (int) $row['page_id']],
                    ['name' => $district['name']]
                );
                $summary['linked_districts']++;
            }
        }

        Logger::info('Eksik başlangıç içeriği tamamlandı', $summary);
        return $summary;
    }

    public function missingCoreCount(): int
    {
        $missing = 0;
        foreach (['iletisim', 'fiyatlar', 'web-tasarim', 'edremit-web-tasarim'] as $slug) {
            $exists = $this->db->value(
                'SELECT pt.page_id
                   FROM page_translations pt
                   JOIN pages p ON p.id = pt.page_id
                  WHERE pt.lang = :lang AND pt.slug = :slug AND p.status = :status
                  LIMIT 1',
                [':lang' => 'tr', ':slug' => $slug, ':status' => 'published']
            );
            if ($exists === null) {
                $missing++;
            }
        }
        return $missing;
    }
}
