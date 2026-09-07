<?php
/**
 * Menuler — `main` ve `footer`.
 *
 * DOCS.md 8.5, 9.3, test F-10
 */

declare(strict_types=1);

namespace Arcates\Models;

use Arcates\Core\Database;

final class MenuItem extends Model
{
    protected static string $table = 'menu_items';
    protected static string $translations = 'menu_item_translations';
    protected static string $foreignKey = 'menu_item_id';

    public const KEYS = ['main' => 'Üst menü', 'footer' => 'Alt menü'];

    /**
     * Menuyu agac olarak dondurur.
     *
     * Sayfaya bagli ogeler icin adres, sayfanin o dildeki slug'indan uretilir;
     * sayfa taslaktaysa oge menude gorunmez.
     */
    public static function tree(string $menuKey, string $lang): array
    {
        $rows = self::db()->all(
            'SELECT m.id, m.parent_id, m.page_id, m.url, m.target, m.sort,
                    t.label,
                    pt.slug AS page_slug,
                    p.status AS page_status
               FROM menu_items m
               LEFT JOIN menu_item_translations t ON t.menu_item_id = m.id AND t.lang = :lang
               LEFT JOIN pages p ON p.id = m.page_id
               LEFT JOIN page_translations pt ON pt.page_id = m.page_id AND pt.lang = :lang2
              WHERE m.menu_key = :key
              ORDER BY m.sort, m.id',
            [':lang' => $lang, ':lang2' => $lang, ':key' => $menuKey]
        );

        $items = [];
        foreach ($rows as $row) {
            $label = (string) ($row['label'] ?? '');
            if ($label === '') {
                // Bu dilde etiketi olmayan oge gosterilmez.
                continue;
            }

            if ($row['page_id'] !== null) {
                if ($row['page_status'] !== 'published' || $row['page_slug'] === null) {
                    continue;
                }
                $href = url('/' . $row['page_slug'], $lang);
            } else {
                $raw  = (string) ($row['url'] ?? '');
                if ($raw === '') {
                    continue;
                }
                $href = preg_match('#^https?://#i', $raw) === 1 ? $raw : url($raw, $lang);
            }

            $items[(int) $row['id']] = [
                'id'        => (int) $row['id'],
                'parent_id' => $row['parent_id'] !== null ? (int) $row['parent_id'] : null,
                'label'     => $label,
                'href'      => $href,
                'target'    => $row['target'] === '_blank' ? '_blank' : '_self',
                'children'  => [],
            ];
        }

        $tree = [];
        foreach ($items as $id => $item) {
            $parent = $item['parent_id'];
            if ($parent !== null && isset($items[$parent])) {
                continue;
            }
            $tree[$id] = &$items[$id];
        }

        foreach ($items as $id => $item) {
            $parent = $item['parent_id'];
            if ($parent !== null && isset($items[$parent])) {
                $items[$parent]['children'][] = &$items[$id];
            }
        }

        return array_values($tree);
    }

    /** Panel listesi; taslak sayfalar da gorunur. */
    public static function listing(string $menuKey, string $lang): array
    {
        return self::db()->all(
            'SELECT m.id, m.parent_id, m.page_id, m.url, m.target, m.sort,
                    t.label, pt.title AS page_title, p.status AS page_status
               FROM menu_items m
               LEFT JOIN menu_item_translations t ON t.menu_item_id = m.id AND t.lang = :lang
               LEFT JOIN pages p ON p.id = m.page_id
               LEFT JOIN page_translations pt ON pt.page_id = m.page_id AND pt.lang = :lang2
              WHERE m.menu_key = :key
              ORDER BY m.sort, m.id',
            [':lang' => $lang, ':lang2' => $lang, ':key' => $menuKey]
        );
    }

    /** Oge ve cevirilerini kaydeder. */
    public static function save(array $item, array $labels, ?int $id = null): int
    {
        return self::db()->transaction(static function (Database $db) use ($item, $labels, $id): int {
            if ($id !== null && $id > 0) {
                $db->update('menu_items', $item, ['id' => $id]);
                $itemId = $id;
            } else {
                $itemId = $db->insert('menu_items', $item);
            }

            foreach ($labels as $lang => $label) {
                $label = trim((string) $label);

                if ($label === '') {
                    $db->run(
                        'DELETE FROM menu_item_translations WHERE menu_item_id = :id AND lang = :lang',
                        [':id' => $itemId, ':lang' => $lang]
                    );
                    continue;
                }

                $db->upsert(
                    'menu_item_translations',
                    ['menu_item_id' => $itemId, 'lang' => $lang, 'label' => mb_substr($label, 0, 120)],
                    ['label']
                );
            }

            return $itemId;
        });
    }
}
