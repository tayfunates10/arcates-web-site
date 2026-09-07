<?php
/**
 * Referanslar (portfoy).  DOCS.md 8.2, 9.4, 11.2
 */

declare(strict_types=1);

namespace Arcates\Models;

use Arcates\Core\Database;
use Arcates\Core\Media;
use Arcates\Core\Security;

final class Project extends Model
{
    protected static string $table = 'projects';
    protected static string $translations = 'project_translations';
    protected static string $foreignKey = 'project_id';

    /** Yayinlanmis son N referans; kapak gorseli ve alt metniyle. */
    public static function latest(string $lang, int $limit = 6): array
    {
        $rows = self::db()->all(
            'SELECT p.id, p.client_name, p.sector, p.district, p.live_url, p.cover_id,
                    t.title, t.slug, t.excerpt
               FROM projects p
               JOIN project_translations t ON t.project_id = p.id AND t.lang = :lang
              WHERE p.status = :status
              ORDER BY p.sort, p.id DESC
              LIMIT ' . max(1, min(24, $limit)),
            [':lang' => $lang, ':status' => 'published']
        );

        return self::attachCovers($rows, $lang);
    }

    /** Tum yayinlanmis referanslar. */
    public static function published(string $lang): array
    {
        $rows = self::db()->all(
            'SELECT p.id, p.client_name, p.sector, p.district, p.live_url, p.cover_id,
                    t.title, t.slug, t.excerpt
               FROM projects p
               JOIN project_translations t ON t.project_id = p.id AND t.lang = :lang
              WHERE p.status = :status
              ORDER BY p.sort, p.id DESC',
            [':lang' => $lang, ':status' => 'published']
        );

        return self::attachCovers($rows, $lang);
    }

    /** Tek referans, slug ile. */
    public static function bySlugPublished(string $slug, string $lang): ?array
    {
        $row = self::db()->first(
            'SELECT p.*, t.title, t.slug, t.excerpt, t.content, t.meta_title, t.meta_description, t.robots, t.lang
               FROM projects p
               JOIN project_translations t ON t.project_id = p.id AND t.lang = :lang
              WHERE t.slug = :slug AND p.status = :status
              LIMIT 1',
            [':lang' => $lang, ':slug' => $slug, ':status' => 'published']
        );

        if ($row === null) {
            return null;
        }

        $attached  = self::attachCovers([$row], $lang);
        $row       = $attached[0];
        $row['gallery'] = self::gallery((int) $row['id'], $lang);

        return $row;
    }

    /** Galeri gorselleri. DOCS.md 9.4 */
    public static function gallery(int $projectId, string $lang): array
    {
        $rows = self::db()->all(
            'SELECT m.id, m.path, m.width, m.height, m.variants, t.alt
               FROM project_media pm
               JOIN media m ON m.id = pm.media_id
               LEFT JOIN media_translations t ON t.media_id = m.id AND t.lang = :lang
              WHERE pm.project_id = :id
              ORDER BY pm.sort, m.id',
            [':id' => $projectId, ':lang' => $lang]
        );

        foreach ($rows as &$row) {
            $row['variants'] = Media::decodeVariants($row['variants'] ?? null);
        }

        return $rows;
    }

    public static function listing(string $lang, string $search = ''): array
    {
        $sql = 'SELECT p.id, p.client_name, p.sector, p.district, p.status, p.sort, p.created_at,
                       t.title, t.slug
                  FROM projects p
                  LEFT JOIN project_translations t ON t.project_id = p.id AND t.lang = :lang
                 WHERE 1 = 1';
        $args = [':lang' => $lang];

        if ($search !== '') {
            $sql            .= ' AND (p.client_name LIKE :q OR t.title LIKE :q OR p.district LIKE :q)';
            $args[':q']      = '%' . $search . '%';
        }

        return self::db()->all($sql . ' ORDER BY p.sort, p.id DESC', $args);
    }

    /** Referansi cevirileriyle kaydeder. */
    public static function save(array $project, array $translations, array $gallery = [], ?int $id = null): int
    {
        return self::db()->transaction(static function (Database $db) use ($project, $translations, $gallery, $id): int {
            if ($id !== null && $id > 0) {
                $db->update('projects', $project, ['id' => $id]);
                $projectId = $id;
            } else {
                $projectId = $db->insert('projects', $project);
            }

            foreach ($translations as $lang => $fields) {
                if (($fields['title'] ?? '') === '') {
                    $db->run(
                        'DELETE FROM project_translations WHERE project_id = :id AND lang = :lang',
                        [':id' => $projectId, ':lang' => $lang]
                    );
                    continue;
                }

                $existing = $db->first(
                    'SELECT id FROM project_translations WHERE project_id = :id AND lang = :lang',
                    [':id' => $projectId, ':lang' => $lang]
                );

                if ($existing !== null) {
                    $db->update('project_translations', $fields, ['id' => (int) $existing['id']]);
                } else {
                    $fields['project_id'] = $projectId;
                    $fields['lang']       = $lang;
                    $db->insert('project_translations', $fields);
                }
            }

            $db->run('DELETE FROM project_media WHERE project_id = :id', [':id' => $projectId]);
            $sort = 0;
            foreach (array_unique(array_filter(array_map('intval', $gallery))) as $mediaId) {
                $exists = $db->value('SELECT id FROM media WHERE id = :id', [':id' => $mediaId]);
                if ($exists !== null) {
                    $db->insert('project_media', ['project_id' => $projectId, 'media_id' => $mediaId, 'sort' => $sort++]);
                }
            }

            return $projectId;
        });
    }

    /** Kapak gorsellerini satirlara ekler. */
    private static function attachCovers(array $rows, string $lang): array
    {
        $ids = array_values(array_filter(array_map(
            static fn (array $row): int => (int) ($row['cover_id'] ?? 0),
            $rows
        )));

        $covers = [];
        if ($ids) {
            $placeholders = implode(',', array_map('intval', $ids));
            foreach (self::db()->all(
                'SELECT m.id, m.path, m.width, m.height, m.variants, t.alt
                   FROM media m
                   LEFT JOIN media_translations t ON t.media_id = m.id AND t.lang = :lang
                  WHERE m.id IN (' . $placeholders . ')',
                [':lang' => $lang]
            ) as $media) {
                $variants = Media::decodeVariants($media['variants'] ?? null);
                $covers[(int) $media['id']] = [
                    'path'   => $variants['medium']['path'] ?? $media['path'],
                    'width'  => (int) ($variants['medium']['width'] ?? $media['width'] ?? 768),
                    'height' => (int) ($variants['medium']['height'] ?? $media['height'] ?? 480),
                    'alt'    => (string) ($media['alt'] ?? ''),
                ];
            }
        }

        foreach ($rows as &$row) {
            $coverId      = (int) ($row['cover_id'] ?? 0);
            $row['cover'] = $covers[$coverId] ?? null;
        }

        return $rows;
    }
}
