<?php
/**
 * Site ici arama.
 *
 * Referansta ust menude bir arama dugmesi var; arkasinda gercek bir arama
 * olmadan onu koymak ziyaretciye calisan bir sey vaat edip vermemek olurdu.
 * Bu model o dugmenin arkasini doldurur.
 *
 * Kapsam: yalnizca YAYINLANMIS icerik — sayfalar, blog yazilari ve ornek
 * siteler. Taslak, ileri tarihli ya da pasif kayit sonuclarda cikmaz.
 *
 * Her sorgu hazirlanmis ifadedir (CLAUDE.md 6). Aranan metindeki LIKE
 * jokerleri (`%`, `_`, `\`) kacislanir; aksi halde tek basina `%` yazan
 * ziyaretci butun tabloyu doner.
 *
 * DOCS.md 4.1, 8.2
 */

declare(strict_types=1);

namespace Arcates\Models;

use Arcates\Core\Database;

final class Search
{
    /** Daha kisa aramalar tum tabloyu tarar, sonuc da anlamsiz olur. */
    public const MIN_LENGTH = 2;

    /** Tur basina en cok kac sonuc dondurulur. */
    private const PER_TYPE = 8;

    private static function db(): Database
    {
        return Database::instance();
    }

    /**
     * Aranan metni LIKE deseni haline getirir.
     *
     * `addcslashes` yerine acik degistirme: kacis karakterinin kendisi de
     * kacislanmali ve sirasi onemli.
     */
    private static function pattern(string $query): string
    {
        $safe = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $query);

        return '%' . $safe . '%';
    }

    /** Aranan metin gecerli mi. */
    public static function isValid(string $query): bool
    {
        return mb_strlen(trim($query)) >= self::MIN_LENGTH;
    }

    /**
     * Tum turlerde arar ve tur adiyla gruplanmis sonuc dondurur.
     *
     * @return array<string, list<array{title: string, url: string, excerpt: string}>>
     */
    public static function all(string $query, string $lang): array
    {
        $query = trim($query);
        if (!self::isValid($query)) {
            return [];
        }

        $groups = [
            'pages'    => self::pages($query, $lang),
            'posts'    => self::posts($query, $lang),
            'projects' => self::projects($query, $lang),
        ];

        return array_filter($groups, static fn (array $rows): bool => $rows !== []);
    }

    /** Toplam sonuc sayisi. */
    public static function count(array $groups): int
    {
        $total = 0;
        foreach ($groups as $rows) {
            $total += count($rows);
        }

        return $total;
    }

    /** @return list<array{title: string, url: string, excerpt: string}> */
    public static function pages(string $query, string $lang): array
    {
        $desen = self::pattern($query);

        $rows = self::db()->all(
            'SELECT t.title, t.slug, t.excerpt
               FROM pages p
               JOIN page_translations t ON t.page_id = p.id AND t.lang = :lang
              WHERE p.status = :status
                AND (t.title LIKE :q1 ESCAPE \'\\\\\' OR t.excerpt LIKE :q2 ESCAPE \'\\\\\')
              ORDER BY CHAR_LENGTH(t.title), t.title
              LIMIT ' . self::PER_TYPE,
            [':lang' => $lang, ':status' => 'published', ':q1' => $desen, ':q2' => $desen]
        );

        return array_map(static fn (array $row): array => [
            'title'   => (string) $row['title'],
            'url'     => url('/' . $row['slug']),
            'excerpt' => (string) ($row['excerpt'] ?? ''),
        ], $rows);
    }

    /** @return list<array{title: string, url: string, excerpt: string}> */
    public static function posts(string $query, string $lang): array
    {
        $desen = self::pattern($query);

        $rows = self::db()->all(
            'SELECT t.title, t.slug, t.excerpt
               FROM posts p
               JOIN post_translations t ON t.post_id = p.id AND t.lang = :lang
              WHERE p.status = :status
                AND (p.published_at IS NULL OR p.published_at <= NOW())
                AND (t.title LIKE :q1 ESCAPE \'\\\\\' OR t.excerpt LIKE :q2 ESCAPE \'\\\\\')
              ORDER BY p.published_at DESC, p.id DESC
              LIMIT ' . self::PER_TYPE,
            [':lang' => $lang, ':status' => 'published', ':q1' => $desen, ':q2' => $desen]
        );

        return array_map(static fn (array $row): array => [
            'title'   => (string) $row['title'],
            'url'     => url('/blog/' . $row['slug']),
            'excerpt' => (string) ($row['excerpt'] ?? ''),
        ], $rows);
    }

    /** @return list<array{title: string, url: string, excerpt: string}> */
    public static function projects(string $query, string $lang): array
    {
        $desen = self::pattern($query);

        $rows = self::db()->all(
            'SELECT t.title, t.slug, t.excerpt
               FROM projects p
               JOIN project_translations t ON t.project_id = p.id AND t.lang = :lang
              WHERE p.status = :status
                AND (t.title LIKE :q1 ESCAPE \'\\\\\' OR t.excerpt LIKE :q2 ESCAPE \'\\\\\')
              ORDER BY p.sort, p.id DESC
              LIMIT ' . self::PER_TYPE,
            [':lang' => $lang, ':status' => 'published', ':q1' => $desen, ':q2' => $desen]
        );

        return array_map(static fn (array $row): array => [
            'title'   => (string) $row['title'],
            'url'     => url('/referanslar/' . $row['slug']),
            'excerpt' => (string) ($row['excerpt'] ?? ''),
        ], $rows);
    }
}
