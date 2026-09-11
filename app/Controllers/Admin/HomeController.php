<?php
/**
 * Anasayfa yoneticisi.
 *
 * Bolum listesi; her bolum icin ac/kapat anahtari ve duzenleme ekrani.
 * Kahraman bolumunde canli onizleme. Bolge haritasinda ilce noktalari
 * surukle-birak ile konumlandirilir, map_x/map_y otomatik hesaplanir.
 * DOCS.md 9.2 — testler F-14, F-15, F-16
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Lang;
use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Security;
use Arcates\Core\Validator;
use Arcates\Models\District;
use Arcates\Models\HomeSection;

final class HomeController extends Controller
{
    protected string $section = 'home';
    protected ?string $ability = 'home.edit';

    /** Bolum listesi. */
    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        HomeSection::ensureDefaults();

        return $this->view('home/index', [
            'title'    => 'Anasayfa',
            'sections' => HomeSection::all(Lang::defaultCode()),
            'labels'   => HomeSection::LABELS,
        ]);
    }

    /** Bolumu acar veya kapatir. DOCS.md 9.2, test F-14 */
    public function toggle(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $key = (string) ($params['key'] ?? '');
        if (!isset(HomeSection::LABELS[$key])) {
            return $this->back(admin_url('anasayfa'), 'error', 'Bölüm bulunamadı.');
        }

        $active = HomeSection::toggle($key);
        Logger::activity('home.toggle', 'home_section', null, $key . ' → ' . ($active ? 'acik' : 'kapali'));

        return $this->back(
            admin_url('anasayfa'),
            'success',
            HomeSection::LABELS[$key] . ' bölümü ' . ($active ? 'acildi' : 'kapatildi') . '.'
        );
    }

    /** Bolum duzenleme ekrani. */
    public function edit(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $key = (string) ($params['key'] ?? '');
        if (!isset(HomeSection::LABELS[$key])) {
            return $this->back(admin_url('anasayfa'), 'error', 'Bölüm bulunamadı.');
        }

        $section = HomeSection::get($key, Lang::defaultCode());
        if ($section === null) {
            HomeSection::ensureDefaults();
            $section = HomeSection::get($key, Lang::defaultCode());
        }

        $contents = [];
        foreach (Lang::codes() as $code) {
            $row = $this->db()->first(
                'SELECT content FROM home_section_translations WHERE section_key = :key AND lang = :lang',
                [':key' => $key, ':lang' => $code]
            );
            $contents[$code] = $row === null ? [] : (json_decode((string) $row['content'], true) ?: []);
        }

        return $this->view('home/edit', [
            'title'     => HomeSection::LABELS[$key] . ' bölümü',
            'key'       => $key,
            'label'     => HomeSection::LABELS[$key],
            'section'   => $section,
            'contents'  => $contents,
            'langs'     => Lang::languages(),
            'districts' => $key === 'coast' ? District::listing(Lang::defaultCode()) : [],
            'pages'     => $key === 'coast' ? $this->locationPages() : [],
        ]);
    }

    /** Bolum icerigini kaydeder. DOCS.md 9.2, test F-15 */
    public function update(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $key = (string) ($params['key'] ?? '');
        if (!isset(HomeSection::LABELS[$key])) {
            return $this->back(admin_url('anasayfa'), 'error', 'Bölüm bulunamadı.');
        }

        $url = admin_url('anasayfa/' . $key);

        // Dilden bagimsiz ayarlar.
        $config = $this->readConfig($key, $request);
        HomeSection::saveConfig($key, $config, $request->bool('is_active'));

        // Dile bagli icerik.
        foreach (Lang::codes() as $code) {
            $raw = $request->arr('c')[$code] ?? [];
            if (!is_array($raw)) {
                continue;
            }
            $current = HomeSection::content($key, $code, $code);
            HomeSection::saveContent($key, $code, $this->readContent($key, $raw, $current));
        }

        Logger::activity('home.update', 'home_section', null, $key);

        return $this->back($url, 'success', HomeSection::LABELS[$key] . ' bölümü kaydedildi.');
    }

    // --- Ilce haritasi ------------------------------------------------------

    /**
     * Ilce noktalarini kaydeder.
     *
     * `map_x` ve `map_y` panelde surukle-birak ile hesaplanir ve gizli
     * alanlarda gonderilir; JavaScript kapaliyken sayi alanlarindan elle
     * girilebilir.  DOCS.md 9.2, test F-16
     */
    public function saveDistricts(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $rows = $request->arr('districts');
        $kept = [];

        foreach ($rows as $index => $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $validator = new Validator($row, ['name' => 'İlçe adı']);
            $validator->max('name', 60)
                ->between('map_x', 0, 1000)
                ->between('map_y', 0, 190);

            if ($validator->fails()) {
                return $this->withErrors(admin_url('anasayfa/coast'), $validator->firstErrors());
            }

            $data = [
                'name'        => mb_substr($name, 0, 60),
                'map_x'       => max(0, min(1000, (int) ($row['map_x'] ?? 0))),
                'map_y'       => max(0, min(190, (int) ($row['map_y'] ?? 90))),
                'label_above' => !empty($row['label_above']) ? 1 : 0,
                'page_id'     => (int) ($row['page_id'] ?? 0) > 0 ? (int) $row['page_id'] : null,
                'sort'        => (int) ($row['sort'] ?? $index),
                'is_active'   => !empty($row['is_active']) ? 1 : 0,
            ];

            $id = (int) ($row['id'] ?? 0);

            if ($id > 0) {
                $this->db()->update('districts', $data, ['id' => $id]);
                $kept[] = $id;
            } else {
                $kept[] = $this->db()->insert('districts', $data);
            }
        }

        // Formda kalmayan noktalar silinir.
        foreach ($this->db()->column('SELECT id FROM districts') as $id) {
            if (!in_array((int) $id, $kept, true)) {
                $this->db()->delete('districts', ['id' => (int) $id]);
            }
        }

        Logger::activity('home.districts', 'district', null, count($kept) . ' nokta');

        return $this->back(admin_url('anasayfa/coast'), 'success', 'İlçe noktaları kaydedildi.');
    }

    // --- Yardimcilar --------------------------------------------------------

    /** Bolum turune gore dilden bagimsiz ayarlari okur. */
    private function readConfig(string $key, Request $request): array
    {
        $config = $request->arr('config');

        return match ($key) {
            'strip' => [
                // Serit tam turu saniye. DOCS.md 7.2
                'speed' => max(10, min(120, (int) ($config['speed'] ?? 34))),
                'mobile_speed_percent' => max(30, min(100, (int) ($config['mobile_speed_percent'] ?? 70))),
            ],
            'services' => ['columns' => max(2, min(4, (int) ($config['columns'] ?? 3)))],
            'coast'    => [
                'view_width'  => 1000,
                'view_height' => 190,
            ],
            'works' => ['limit' => max(1, min(24, (int) ($config['limit'] ?? 6)))],
            'faq'   => ['limit' => max(1, min(24, (int) ($config['limit'] ?? 6)))],
            default => [],
        };
    }

    /**
     * Bolum turune gore dile bagli icerigi okur ve temizler.
     *
     * Her metin `Security::e()` ile basilacagi icin burada yalnizca kirpma ve
     * uzunluk sinirlamasi yapilir; zengin metin alanlari ayrica temizlenir.
     */
    /**
     * Kahraman sahnesi — laptop ustu yuzen katmanlar.
     *
     * Sahnenin kendi form alanlari henuz yok. Form sahne gondermediginde
     * saklanan deger oldugu gibi korunur; aksi halde isletme baslik satirini
     * her duzenlediginde sahne sessizce silinirdi. Gonderilen deger ise
     * diger alanlar gibi temizlenip sinirlanir.
     */
    private function readScene(mixed $raw, array $current): array
    {
        if (!is_array($raw)) {
            return $current;
        }

        $text = static fn (mixed $value, int $max): string => mb_substr(trim((string) $value), 0, $max);

        $pairs = static function (mixed $list, string $first, string $second, int $max) use ($text): array {
            $rows = [];
            foreach (is_array($list) ? $list : [] as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $a = $text($row[$first] ?? '', $max);
                if ($a === '') {
                    continue;
                }
                $rows[] = [$first => $a, $second => $text($row[$second] ?? '', $max)];
            }
            return $rows;
        };

        return [
            'card' => [
                'title' => $text($raw['card']['title'] ?? '', 60),
                'text'  => $text($raw['card']['text'] ?? '', 120),
            ],
            'chips' => array_slice($pairs($raw['chips'] ?? [], 'value', 'label', 40), 0, 2),
            'rail'  => array_slice($pairs($raw['rail'] ?? [], 'label', 'icon', 60), 0, 5),
        ];
    }

    private function readContent(string $key, array $raw, array $current = []): array
    {
        $text = static fn (mixed $value, int $max = 400): string => mb_substr(trim((string) $value), 0, $max);

        $link = static function (mixed $value) use ($text): array {
            $value = is_array($value) ? $value : [];
            return [
                'label' => $text($value['label'] ?? '', 60),
                'url'   => $text($value['url'] ?? '', 255),
            ];
        };

        return match ($key) {
            'header' => ['cta' => $link($raw['cta'] ?? [])],

            'hero' => [
                'badge'       => $text($raw['badge'] ?? '', 120),
                'line1'       => $text($raw['line1'] ?? '', 120),
                'line2'       => $text($raw['line2'] ?? '', 120),
                'line3'       => $text($raw['line3'] ?? '', 120),
                'description' => $text($raw['description'] ?? '', 600),
                'cta1'        => $link($raw['cta1'] ?? []),
                'cta2'        => $link($raw['cta2'] ?? []),
                'scene'       => $this->readScene($raw['scene'] ?? null, (array) ($current['scene'] ?? [])),
            ],

            'strip' => [
                'tags' => array_values(array_filter(array_map(
                    static fn ($tag): string => mb_substr(trim((string) $tag), 0, 60),
                    is_array($raw['tags'] ?? null) ? $raw['tags'] : []
                ), static fn (string $tag): bool => $tag !== '')),
            ],

            'services' => [
                'title'       => $text($raw['title'] ?? '', 160),
                'description' => $text($raw['description'] ?? '', 400),
                'cards'       => array_values(array_filter(array_map(
                    static fn ($card): array => [
                        'icon'  => preg_replace('/[^a-z]/', '', strtolower((string) ($card['icon'] ?? ''))) ?: 'layout',
                        'color' => preg_replace('/[^a-z]/', '', strtolower((string) ($card['color'] ?? ''))) ?: 'blue',
                        'title' => mb_substr(trim((string) ($card['title'] ?? '')), 0, 120),
                        'text'  => mb_substr(trim((string) ($card['text'] ?? '')), 0, 320),
                        'url'   => mb_substr(trim((string) ($card['url'] ?? '')), 0, 255),
                    ],
                    is_array($raw['cards'] ?? null) ? $raw['cards'] : []
                ), static fn (array $card): bool => $card['title'] !== '')),
            ],

            'coast' => [
                'title'       => $text($raw['title'] ?? '', 160),
                'description' => $text($raw['description'] ?? '', 400),
            ],

            'steps' => [
                'title' => $text($raw['title'] ?? '', 160),
                'items' => array_values(array_filter(array_map(
                    static fn ($item): array => [
                        'title' => mb_substr(trim((string) ($item['title'] ?? '')), 0, 120),
                        'text'  => mb_substr(trim((string) ($item['text'] ?? '')), 0, 400),
                    ],
                    is_array($raw['items'] ?? null) ? $raw['items'] : []
                ), static fn (array $item): bool => $item['title'] !== '')),
            ],

            'works' => [
                'title'       => $text($raw['title'] ?? '', 160),
                'description' => $text($raw['description'] ?? '', 400),
                'cta'         => $link($raw['cta'] ?? []),
            ],

            'faq' => [
                'title'       => $text($raw['title'] ?? '', 160),
                'description' => $text($raw['description'] ?? '', 400),
            ],

            'cta' => [
                'title' => $text($raw['title'] ?? '', 160),
                'text'  => $text($raw['text'] ?? '', 400),
                'cta1'  => $link($raw['cta1'] ?? []),
                'cta2'  => $link($raw['cta2'] ?? []),
            ],

            'footer' => [
                'about'   => $text($raw['about'] ?? '', 400),
                'columns' => array_values(array_filter(array_map(
                    static fn ($column): array => [
                        'title' => mb_substr(trim((string) ($column['title'] ?? '')), 0, 60),
                        'links' => array_values(array_filter(array_map(
                            static fn ($l): array => [
                                'label' => mb_substr(trim((string) ($l['label'] ?? '')), 0, 60),
                                'url'   => mb_substr(trim((string) ($l['url'] ?? '')), 0, 255),
                            ],
                            is_array($column['links'] ?? null) ? $column['links'] : []
                        ), static fn (array $l): bool => $l['label'] !== '')),
                    ],
                    is_array($raw['columns'] ?? null) ? $raw['columns'] : []
                ), static fn (array $c): bool => $c['title'] !== '')),
                'legal'   => array_values(array_filter(array_map(
                    static fn ($l): array => [
                        'label' => mb_substr(trim((string) ($l['label'] ?? '')), 0, 60),
                        'url'   => mb_substr(trim((string) ($l['url'] ?? '')), 0, 255),
                    ],
                    is_array($raw['legal'] ?? null) ? $raw['legal'] : []
                ), static fn (array $l): bool => $l['label'] !== '')),
            ],

            default => [],
        };
    }

    /** Ilce noktasina baglanabilecek sayfalar. */
    private function locationPages(): array
    {
        return $this->db()->all(
            'SELECT p.id, t.title, p.status
               FROM pages p
               JOIN page_translations t ON t.page_id = p.id AND t.lang = :lang
              WHERE p.type IN (:location, :service, :page)
              ORDER BY p.type, t.title',
            [':lang' => Lang::defaultCode(), ':location' => 'location', ':service' => 'service', ':page' => 'page']
        );
    }
}
