<?php
/**
 * Bolge haritasi noktalari.
 *
 * Her ilce noktasi ilgili ilce sayfasina baglantidir.  DOCS.md 5.2, 8.3, 9.2
 */

declare(strict_types=1);

namespace Arcates\Models;

final class District extends Model
{
    protected static string $table = 'districts';

    /**
     * Etkin ilceler; sayfaya bagli olanlarin adresi cozulur.
     *
     * @return array<int, array{id:int, name:string, map_x:int, map_y:int, label_above:int, url:string}>
     */
    public static function forMap(string $lang): array
    {
        $rows = self::db()->all(
            'SELECT d.id, d.name, d.map_x, d.map_y, d.label_above, d.sort,
                    t.slug, p.status
               FROM districts d
               LEFT JOIN pages p ON p.id = d.page_id
               LEFT JOIN page_translations t ON t.page_id = d.page_id AND t.lang = :lang
              WHERE d.is_active = 1
              ORDER BY d.sort, d.map_x',
            [':lang' => $lang]
        );

        $out = [];
        foreach ($rows as $row) {
            $url = '';
            if ($row['slug'] !== null && $row['status'] === 'published') {
                $url = url('/' . $row['slug'], $lang);
            }

            $out[] = [
                'id'          => (int) $row['id'],
                'name'        => (string) $row['name'],
                'map_x'       => (int) $row['map_x'],
                'map_y'       => (int) $row['map_y'],
                'label_above' => (int) $row['label_above'],
                'url'         => $url,
            ];
        }

        return $out;
    }

    /** Panel listesi; sayfa baglantisi cozulmeden. */
    public static function listing(string $lang): array
    {
        return self::db()->all(
            'SELECT d.*, t.title AS page_title
               FROM districts d
               LEFT JOIN page_translations t ON t.page_id = d.page_id AND t.lang = :lang
              ORDER BY d.sort, d.map_x',
            [':lang' => $lang]
        );
    }
}
