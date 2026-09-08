<?php
/**
 * Blog yazilari.
 *
 * Kategori, kapak, yayin tarihi, ileri tarihli yayin. DOCS.md 8.2, 9.4
 */

declare(strict_types=1);

namespace Arcates\Models;

use Arcates\Core\Database;
use Arcates\Core\Media;
use Arcates\Core\Security;

final class Post extends Model
{
    protected static string $table = 'posts';
    protected static string $translations = 'post_translations';
    protected static string $foreignKey = 'post_id';

    public static function published(string $lang, int $limit = 20, int $offset = 0, string $category = ''): array
    {
        $sql = 'SELECT p.id, p.category, p.cover_id, p.published_at, p.updated_at,
                       t.title, t.slug, t.excerpt, t.word_count
                  FROM posts p
                  JOIN post_translations t ON t.post_id = p.id AND t.lang = :lang
                 WHERE p.status = :status
                   AND (p.published_at IS NULL OR p.published_at <= NOW())';

        $args = [':lang' => $lang, ':status' => 'published'];

        if ($category !== '') {
            $sql .= ' AND p.category = :category';
            $args[':category'] = $category;
        }

        $sql .= ' ORDER BY p.published_at DESC, p.id DESC'
            . ' LIMIT ' . max(1, min(100, $limit)) . ' OFFSET ' . max(0, $offset);

        return self::attachCovers(self::db()->all($sql, $args), $lang);
    }

    public static function countPublished(string $lang, string $category = ''): int
    {
        $sql = 'SELECT COUNT(*) FROM posts p
                  JOIN post_translations t ON t.post_id = p.id AND t.lang = :lang
                 WHERE p.status = :status
                   AND (p.published_at IS NULL OR p.published_at <= NOW())';

        $args = [':lang' => $lang, ':status' => 'published'];
        if ($category !== '') {
            $sql .= ' AND p.category = :category';
            $args[':category'] = $category;
        }

        return (int) self::db()->value($sql, $args);
    }

    public static function bySlugPublished(string $slug, string $lang): ?array
    {
        $row = self::db()->first(
            'SELECT p.*, t.title, t.slug, t.excerpt, t.content, t.meta_title, t.meta_description,
                    t.robots, t.lang, t.word_count, u.name AS author_name
               FROM posts p
               JOIN post_translations t ON t.post_id = p.id AND t.lang = :lang
               LEFT JOIN users u ON u.id = p.author_id
              WHERE t.slug = :slug AND p.status = :status
                AND (p.published_at IS NULL OR p.published_at <= NOW())
              LIMIT 1',
            [':lang' => $lang, ':slug' => $slug, ':status' => 'published']
        );

        if ($row === null) {
            return null;
        }

        return self::attachCovers([$row], $lang)[0];
    }

    public static function categories(string $lang): array
    {
        return self::db()->all(
            'SELECT p.category, COUNT(*) AS total
               FROM posts p
               JOIN post_translations t ON t.post_id = p.id AND t.lang = :lang
              WHERE p.status = :status AND p.category IS NOT NULL AND p.category <> :empty
                AND (p.published_at IS NULL OR p.published_at <= NOW())
              GROUP BY p.category
              ORDER BY p.category',
            [':lang' => $lang, ':status' => 'published', ':empty' => '']
        );
    }

    public static function listing(string $lang, string $search = ''): array
    {
        $sql = 'SELECT p.id, p.category, p.status, p.published_at, p.updated_at, t.title, t.slug, t.word_count
                  FROM posts p
                  LEFT JOIN post_translations t ON t.post_id = p.id AND t.lang = :lang
                 WHERE 1 = 1';
        $args = [':lang' => $lang];

        if ($search !== '') {
            $sql .= ' AND (t.title LIKE :q OR p.category LIKE :q)';
            $args[':q'] = '%' . $search . '%';
        }

        return self::db()->all($sql . ' ORDER BY p.published_at DESC, p.id DESC', $args);
    }

    public static function save(array $post, array $translations, ?int $id = null): int
    {
        return self::db()->transaction(static function (Database $db) use ($post, $translations, $id): int {
            if ($id !== null && $id > 0) {
                $db->update('posts', $post, ['id' => $id]);
                $postId = $id;
            } else {
                $postId = $db->insert('posts', $post);
            }

            foreach ($translations as $lang => $fields) {
                if (($fields['title'] ?? '') === '') {
                    $db->run(
                        'DELETE FROM post_translations WHERE post_id = :id AND lang = :lang',
                        [':id' => $postId, ':lang' => $lang]
                    );
                    continue;
                }

                $fields['word_count'] = Security::wordCount((string) ($fields['content'] ?? ''));
                $existing = $db->first(
                    'SELECT id FROM post_translations WHERE post_id = :id AND lang = :lang',
                    [':id' => $postId, ':lang' => $lang]
                );

                if ($existing !== null) {
                    $db->update('post_translations', $fields, ['id' => (int) $existing['id']]);
                } else {
                    $fields['post_id'] = $postId;
                    $fields['lang'] = $lang;
                    $db->insert('post_translations', $fields);
                }
            }

            return $postId;
        });
    }

    private static function attachCovers(array $rows, string $lang): array
    {
        $ids = array_values(array_filter(array_map(
            static fn (array $row): int => (int) ($row['cover_id'] ?? 0),
            $rows
        )));

        $covers = [];
        if ($ids) {
            foreach (self::db()->all(
                'SELECT m.id, m.path, m.width, m.height, m.variants, t.alt
                   FROM media m
                   LEFT JOIN media_translations t ON t.media_id = m.id AND t.lang = :lang
                  WHERE m.id IN (' . implode(',', array_map('intval', $ids)) . ')',
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
            $row['cover'] = $covers[(int) ($row['cover_id'] ?? 0)] ?? null;
        }

        return $rows;
    }
}
