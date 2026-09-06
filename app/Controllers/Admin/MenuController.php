<?php
/**
 * Menu yonetimi.
 *
 * Ust menu ve alt menu ogeleri; sira degisikligi on yuze yansir.
 * DOCS.md 8.5, 9.3 — test F-10
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Lang;
use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Models\MenuItem;

final class MenuController extends Controller
{
    protected string $section = 'menus';
    protected ?string $ability = 'pages.edit';

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $menuKey = $request->str('menu');
        if (!isset(MenuItem::KEYS[$menuKey])) {
            $menuKey = 'main';
        }

        $items = [];
        foreach (MenuItem::listing($menuKey, Lang::defaultCode()) as $row) {
            $row['labels'] = [];
            foreach ($this->db()->all(
                'SELECT lang, label FROM menu_item_translations WHERE menu_item_id = :id',
                [':id' => (int) $row['id']]
            ) as $t) {
                $row['labels'][$t['lang']] = $t['label'];
            }
            $items[] = $row;
        }

        return $this->view('menus/index', [
            'title'   => 'Menuler',
            'menuKey' => $menuKey,
            'keys'    => MenuItem::KEYS,
            'items'   => $items,
            'langs'   => Lang::languages(),
            'pages'   => $this->db()->all(
                'SELECT p.id, t.title FROM pages p
                   JOIN page_translations t ON t.page_id = p.id AND t.lang = :lang
                  ORDER BY t.title',
                [':lang' => Lang::defaultCode()]
            ),
        ]);
    }

    /** Tum menuyu tek gonderimde kaydeder. */
    public function save(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $menuKey = $request->str('menu');
        if (!isset(MenuItem::KEYS[$menuKey])) {
            $menuKey = 'main';
        }

        $rows = $request->arr('items');
        $kept = [];

        foreach ($rows as $index => $row) {
            $labels = is_array($row['labels'] ?? null) ? $row['labels'] : [];
            $anyLabel = false;
            foreach ($labels as $label) {
                if (trim((string) $label) !== '') {
                    $anyLabel = true;
                    break;
                }
            }

            if (!$anyLabel) {
                // Etiketi olmayan satir kaydedilmez; var olan kayit silinir.
                continue;
            }

            $pageId = (int) ($row['page_id'] ?? 0);
            $url    = trim((string) ($row['url'] ?? ''));

            $item = [
                'menu_key'  => $menuKey,
                'parent_id' => (int) ($row['parent_id'] ?? 0) > 0 ? (int) $row['parent_id'] : null,
                'page_id'   => $pageId > 0 ? $pageId : null,
                'url'       => $pageId > 0 ? null : ($url !== '' ? mb_substr($url, 0, 255) : null),
                'target'    => ($row['target'] ?? '_self') === '_blank' ? '_blank' : '_self',
                'sort'      => (int) ($row['sort'] ?? $index),
            ];

            if ($item['page_id'] === null && $item['url'] === null) {
                continue;
            }

            $id     = (int) ($row['id'] ?? 0);
            $kept[] = MenuItem::save($item, $labels, $id > 0 ? $id : null);
        }

        // Formda kalmayan ogeler silinir.
        $existing = $this->db()->column(
            'SELECT id FROM menu_items WHERE menu_key = :key',
            [':key' => $menuKey]
        );

        foreach ($existing as $id) {
            if (!in_array((int) $id, $kept, true)) {
                $this->db()->delete('menu_items', ['id' => (int) $id]);
            }
        }

        Logger::activity('menu.save', 'menu', null, $menuKey . ' — ' . count($kept) . ' oge');

        return $this->back(admin_url('menuler') . '?menu=' . $menuKey, 'success', 'Menu kaydedildi.');
    }
}
