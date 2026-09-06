<?php
/**
 * Kurulum tohum verisi.
 *
 * `/install` sirasinda varsayilan diller, ayarlar, anasayfa bolumleri, ilce
 * noktalari ve menu kayitlari yazilir.  DOCS.md 5, 8.3, 13
 *
 * Buradaki metinler yalnizca baslangic degerleridir; hepsi panelden
 * duzenlenebilir. Sablonlara sabit metin gomulmez.  DOCS.md 1 (ilke 4)
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Seeder
{
    public function __construct(private Database $db)
    {
    }

    /** Tum varsayilanlari yazar. Var olan kayitlar korunur. */
    public function run(): void
    {
        $this->languages();
        $this->settings();
        $this->homeSections();
        $this->districts();
        $this->menus();
    }

    // --- Diller -------------------------------------------------------------

    public function languages(): void
    {
        $rows = [
            ['code' => 'tr', 'name' => 'Turkce',  'direction' => 'ltr', 'is_default' => 1, 'is_active' => 1, 'sort' => 1],
            ['code' => 'en', 'name' => 'English', 'direction' => 'ltr', 'is_default' => 0, 'is_active' => 1, 'sort' => 2],
            ['code' => 'de', 'name' => 'Deutsch', 'direction' => 'ltr', 'is_default' => 0, 'is_active' => 1, 'sort' => 3],
            // Arapca sagdan sola yazilir. DOCS.md 11.3
            ['code' => 'ar', 'name' => 'العربية',  'direction' => 'rtl', 'is_default' => 0, 'is_active' => 1, 'sort' => 4],
        ];

        foreach ($rows as $row) {
            $exists = $this->db->value('SELECT code FROM languages WHERE code = :code', [':code' => $row['code']]);
            if ($exists === null) {
                $this->db->insert('languages', $row);
            }
        }
    }

    // --- Ayarlar ------------------------------------------------------------

    public function settings(): void
    {
        foreach (Settings::defaults() as $key => $value) {
            $exists = $this->db->value('SELECT `key` FROM settings WHERE `key` = :key', [':key' => $key]);
            if ($exists === null) {
                $this->db->insert('settings', ['key' => $key, 'value' => $value, 'autoload' => 1]);
            }
        }
    }

    // --- Anasayfa bolumleri -------------------------------------------------

    /**
     * Bolum sirasi sabittir (surum 1) ve DOCS.md 5'teki tabloyla birebir
     * aynidir.
     */
    public static function sectionOrder(): array
    {
        return ['header', 'hero', 'strip', 'services', 'coast', 'steps', 'works', 'faq', 'cta', 'footer'];
    }

    public function homeSections(): void
    {
        $sort = 0;
        foreach (self::sectionOrder() as $key) {
            $sort++;
            $exists = $this->db->value('SELECT `key` FROM home_sections WHERE `key` = :key', [':key' => $key]);

            if ($exists === null) {
                $this->db->insert('home_sections', [
                    'key'       => $key,
                    'is_active' => 1,
                    'sort'      => $sort,
                    'config'    => Security::json(self::sectionConfig($key)),
                ]);
            }

            foreach (['tr', 'en', 'de', 'ar'] as $lang) {
                $has = $this->db->value(
                    'SELECT section_key FROM home_section_translations WHERE section_key = :key AND lang = :lang',
                    [':key' => $key, ':lang' => $lang]
                );
                if ($has !== null) {
                    continue;
                }

                $content = self::sectionContent($key, $lang);
                if ($content === null) {
                    continue;
                }

                $this->db->insert('home_section_translations', [
                    'section_key' => $key,
                    'lang'        => $lang,
                    'content'     => Security::json($content),
                ]);
            }
        }
    }

    /** Dilden bagimsiz bolum ayarlari. DOCS.md 8.3 */
    public static function sectionConfig(string $key): array
    {
        return match ($key) {
            // Serit tam turu 34 saniye. DOCS.md 7.2
            'strip'    => ['speed' => 34, 'mobile_speed_percent' => 70],
            'services' => ['columns' => 3],
            // SVG viewBox="0 0 1000 190". DOCS.md 5.2
            'coast'    => ['view_width' => 1000, 'view_height' => 190],
            'works'    => ['limit' => 6],
            'faq'      => ['limit' => 6],
            default    => [],
        };
    }

    /**
     * Bolumun dile bagli varsayilan icerigi.
     *
     * Yalnizca Turkce tam doldurulur; diger diller panelden girilene kadar
     * bos birakilir ki eksik ceviri `hreflang` uretimine girmesin.
     * DOCS.md 11.3, test U-13
     */
    public static function sectionContent(string $key, string $lang): ?array
    {
        if ($lang !== 'tr') {
            return null;
        }

        return match ($key) {
            'header' => [
                'cta' => ['label' => 'Teklif Al', 'url' => '/iletisim'],
            ],

            'hero' => [
                'badge'       => 'Edremit Korfezi · Web Tasarim ve Yazilim',
                'line1'       => 'Korfezdeki isletmeler icin',
                'line2'       => 'hizli, bulunur ve',
                'line3'       => 'is getiren web siteleri',
                'description' => 'Edremit, Akcay, Altinoluk, Burhaniye ve Ayvalik\'ta '
                    . 'musterilerinizin sizi Google aramalarinda bulmasini saglayan siteler kuruyoruz. '
                    . 'Yonetimi kolay, hizli ve cok dilli.',
                'cta1'        => ['label' => 'Ucretsiz teklif alin', 'url' => '/iletisim'],
                'cta2'        => ['label' => 'Calismalarimiz', 'url' => '/referanslar'],
            ],

            'strip' => [
                'tags' => [
                    'Otel ve pansiyon', 'Zeytinyagi ureticisi', 'Restoran ve kafe',
                    'Emlak ofisi', 'Nakliyat', 'Tabela ve matbaa',
                    'Butik otel', 'Kamp alani', 'Zeytin kooperatifi', 'Diş kliniği',
                ],
            ],

            'services' => [
                'title'       => 'Ne yapiyoruz',
                'description' => 'Isletmenizin buyuklugu ne olursa olsun, isinizi buyuten '
                    . 'dijital altyapiyi kuruyoruz.',
                'cards'       => [
                    [
                        'icon'  => 'layout',
                        'color' => 'blue',
                        'title' => 'Kurumsal web tasarim',
                        'text'  => 'Mobilde hizli acilan, aramalarda gorunur, yonetimi kolay kurumsal siteler.',
                        'url'   => '/web-tasarim',
                    ],
                    [
                        'icon'  => 'cart',
                        'color' => 'coral',
                        'title' => 'E-ticaret sitesi',
                        'text'  => 'Zeytinyagi, zeytin ve yerel urunler icin satisa hazir magaza altyapisi.',
                        'url'   => '/e-ticaret-sitesi',
                    ],
                    [
                        'icon'  => 'calendar',
                        'color' => 'cyan',
                        'title' => 'Rezervasyon sistemi',
                        'text'  => 'Otel, pansiyon ve kamp alanlari icin komisyonsuz dogrudan rezervasyon.',
                        'url'   => '/rezervasyon-sistemi',
                    ],
                    [
                        'icon'  => 'search',
                        'color' => 'mint',
                        'title' => 'SEO hizmeti',
                        'text'  => 'Yerel aramalarda ust siralara cikmak icin teknik ve icerik calismasi.',
                        'url'   => '/seo-hizmeti',
                    ],
                    [
                        'icon'  => 'globe',
                        'color' => 'violet',
                        'title' => 'Coklu dil web sitesi',
                        'text'  => 'Turkce, Ingilizce, Almanca ve Arapca yayin; dogru hreflang kurulumu.',
                        'url'   => '/coklu-dil-web-sitesi',
                    ],
                    [
                        'icon'  => 'shield',
                        'color' => 'sun',
                        'title' => 'Web sitesi bakim',
                        'text'  => 'Guncelleme, yedekleme, guvenlik ve icerik destegi; aylik sabit ucret.',
                        'url'   => '/web-sitesi-bakim',
                    ],
                ],
            ],

            'coast' => [
                'title'       => 'Korfezin her ilcesinde calisiyoruz',
                'description' => 'Yerinde gorusme, yerel arama bilgisi ve bolgeyi taniyan bir ekip. '
                    . 'Ilcenizi secin, o bolgeye ozel calismalarimizi gorun.',
            ],

            'steps' => [
                'title' => 'Nasil calisiyoruz',
                'items' => [
                    [
                        'title' => 'Konusuyoruz',
                        'text'  => 'Isinizi, musterilerinizi ve rakiplerinizi dinliyoruz. '
                            . 'Hangi aramalarda gorunmeniz gerektigini birlikte belirliyoruz.',
                    ],
                    [
                        'title' => 'Kuruyoruz',
                        'text'  => 'Tasarim, icerik ve teknik kurulumu yapiyoruz. '
                            . 'Her sayfayi hiz ve arama gorunurlugu icin olcuyoruz.',
                    ],
                    [
                        'title' => 'Buyutuyoruz',
                        'text'  => 'Yayindan sonra hangi sayfanin is getirdigini olcuyor, '
                            . 'icerigi ve reklami buna gore duzenliyoruz.',
                    ],
                ],
            ],

            'works' => [
                'title'       => 'Son calismalar',
                'description' => 'Korfezde yayina aldigimiz projelerden bir bolumu.',
                'cta'         => ['label' => 'Tum referanslar', 'url' => '/referanslar'],
            ],

            'faq' => [
                'title'       => 'Sik sorulan sorular',
                'description' => 'Aklinizdaki sorunun cevabi burada yoksa bize yazin.',
            ],

            'cta' => [
                'title' => 'Projenizi konusalim',
                'text'  => 'Kisa bir gorusmeyle ihtiyacinizi netlestirelim, ayni hafta '
                    . 'fiyat ve takvim gonderelim.',
                'cta1'  => ['label' => 'Teklif isteyin', 'url' => '/iletisim'],
                'cta2'  => ['label' => 'Fiyatlari gorun', 'url' => '/fiyatlar'],
            ],

            'footer' => [
                'about' => 'Arcates Yazilim, Edremit Korfezi bolgesindeki isletmelere '
                    . 'web tasarim, e-ticaret ve yazilim hizmeti verir.',
                'columns' => [
                    [
                        'title' => 'Hizmetler',
                        'links' => [
                            ['label' => 'Web tasarim', 'url' => '/web-tasarim'],
                            ['label' => 'E-ticaret sitesi', 'url' => '/e-ticaret-sitesi'],
                            ['label' => 'Rezervasyon sistemi', 'url' => '/rezervasyon-sistemi'],
                            ['label' => 'SEO hizmeti', 'url' => '/seo-hizmeti'],
                        ],
                    ],
                    [
                        'title' => 'Bolgeler',
                        'links' => [
                            ['label' => 'Edremit', 'url' => '/edremit-web-tasarim'],
                            ['label' => 'Akcay', 'url' => '/akcay-web-tasarim'],
                            ['label' => 'Altinoluk', 'url' => '/altinoluk-web-tasarim'],
                            ['label' => 'Burhaniye', 'url' => '/burhaniye-web-tasarim'],
                        ],
                    ],
                    [
                        'title' => 'Kurumsal',
                        'links' => [
                            ['label' => 'Hakkimizda', 'url' => '/hakkimizda'],
                            ['label' => 'Referanslar', 'url' => '/referanslar'],
                            ['label' => 'Blog', 'url' => '/blog'],
                            ['label' => 'Iletisim', 'url' => '/iletisim'],
                        ],
                    ],
                ],
                'legal' => [
                    ['label' => 'KVKK Aydinlatma Metni', 'url' => '/kvkk'],
                    ['label' => 'Gizlilik Politikasi', 'url' => '/gizlilik-politikasi'],
                ],
            ],

            default => null,
        };
    }

    // --- Ilce noktalari -----------------------------------------------------

    /**
     * Bolge haritasi noktalari. `map_x` degeri 0-1000 arasindadir
     * (SVG viewBox genisligi).  DOCS.md 5.2
     */
    public static function districtSeed(): array
    {
        return [
            ['name' => 'Ayvalik',    'map_x' => 90,  'map_y' => 118, 'label_above' => 1, 'slug' => 'ayvalik-web-tasarim'],
            ['name' => 'Gomec',      'map_x' => 205, 'map_y' => 96,  'label_above' => 0, 'slug' => 'gomec-web-tasarim'],
            ['name' => 'Burhaniye',  'map_x' => 330, 'map_y' => 108, 'label_above' => 1, 'slug' => 'burhaniye-web-tasarim'],
            ['name' => 'Edremit',    'map_x' => 470, 'map_y' => 86,  'label_above' => 0, 'slug' => 'edremit-web-tasarim'],
            ['name' => 'Akcay',      'map_x' => 600, 'map_y' => 104, 'label_above' => 1, 'slug' => 'akcay-web-tasarim'],
            ['name' => 'Altinoluk',  'map_x' => 725, 'map_y' => 92,  'label_above' => 0, 'slug' => 'altinoluk-web-tasarim'],
            ['name' => 'Havran',     'map_x' => 845, 'map_y' => 112, 'label_above' => 1, 'slug' => 'havran-web-tasarim'],
            ['name' => 'Balikesir',  'map_x' => 940, 'map_y' => 84,  'label_above' => 0, 'slug' => 'balikesir-web-tasarim'],
        ];
    }

    public function districts(): void
    {
        $sort = 0;
        foreach (self::districtSeed() as $district) {
            $sort++;
            $exists = $this->db->value('SELECT id FROM districts WHERE name = :name', [':name' => $district['name']]);
            if ($exists !== null) {
                continue;
            }

            $this->db->insert('districts', [
                'name'        => $district['name'],
                'map_x'       => $district['map_x'],
                'map_y'       => $district['map_y'],
                'label_above' => $district['label_above'],
                'page_id'     => null,
                'sort'        => $sort,
                'is_active'   => 1,
            ]);
        }
    }

    // --- Menuler ------------------------------------------------------------

    public function menus(): void
    {
        if ((int) $this->db->count('menu_items') > 0) {
            return;
        }

        $main = [
            ['label' => 'Hizmetler',   'url' => '/web-tasarim'],
            ['label' => 'Bolgeler',    'url' => '/edremit-web-tasarim'],
            ['label' => 'Referanslar', 'url' => '/referanslar'],
            ['label' => 'Fiyatlar',    'url' => '/fiyatlar'],
            ['label' => 'Blog',        'url' => '/blog'],
            ['label' => 'Iletisim',    'url' => '/iletisim'],
        ];

        $sort = 0;
        foreach ($main as $item) {
            $sort++;
            $id = $this->db->insert('menu_items', [
                'menu_key' => 'main',
                'url'      => $item['url'],
                'target'   => '_self',
                'sort'     => $sort,
            ]);
            $this->db->insert('menu_item_translations', [
                'menu_item_id' => $id,
                'lang'         => 'tr',
                'label'        => $item['label'],
            ]);
        }
    }
}
