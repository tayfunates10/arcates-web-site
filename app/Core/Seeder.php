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
            ['code' => 'tr', 'name' => 'Türkçe',  'direction' => 'ltr', 'is_default' => 1, 'is_active' => 1, 'sort' => 1],
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
                'badge'       => 'Edremit Körfezi · Web Tasarım ve Yazılım',
                'line1'       => 'Körfezdeki işletmeler için',
                'line2'       => 'hızlı, bulunur ve',
                'line3'       => 'iş getiren web siteleri',
                'description' => 'Edremit, Akçay, Altınoluk, Burhaniye ve Ayvalık\'ta '
                    . 'müşterilerinizin sizi Google aramalarında bulmasını sağlayan siteler kuruyoruz. '
                    . 'Yönetimi kolay, hızlı ve çok dilli.',
                'cta1'        => ['label' => 'Ücretsiz teklif alın', 'url' => '/iletisim'],
                'cta2'        => ['label' => 'Çalışmalarımız', 'url' => '/referanslar'],
            ],

            'strip' => [
                'tags' => [
                    'Otel ve pansiyon', 'Zeytinyağı üreticisi', 'Restoran ve kafe',
                    'Emlak ofisi', 'Nakliyat', 'Tabela ve matbaa',
                    'Butik otel', 'Kamp alanı', 'Zeytin kooperatifi', 'Diş kliniği',
                ],
            ],

            'services' => [
                'title'       => 'Ne yapıyoruz',
                'description' => 'İşletmenizin büyüklüğü ne olursa olsun, işinizi büyüten '
                    . 'dijital altyapıyı kuruyoruz.',
                'cards'       => [
                    [
                        'icon'  => 'layout',
                        'color' => 'blue',
                        'title' => 'Kurumsal web tasarım',
                        'text'  => 'Mobilde hızlı açılan, aramalarda görünür, yönetimi kolay kurumsal siteler.',
                        'url'   => '/web-tasarim',
                    ],
                    [
                        'icon'  => 'cart',
                        'color' => 'coral',
                        'title' => 'E-ticaret sitesi',
                        'text'  => 'Zeytinyağı, zeytin ve yerel ürünler için satışa hazır mağaza altyapısı.',
                        'url'   => '/e-ticaret-sitesi',
                    ],
                    [
                        'icon'  => 'calendar',
                        'color' => 'cyan',
                        'title' => 'Rezervasyon sistemi',
                        'text'  => 'Otel, pansiyon ve kamp alanları için komisyonsuz doğrudan rezervasyon.',
                        'url'   => '/rezervasyon-sistemi',
                    ],
                    [
                        'icon'  => 'search',
                        'color' => 'mint',
                        'title' => 'SEO hizmeti',
                        'text'  => 'Yerel aramalarda üst sıralara çıkmak için teknik ve içerik çalışması.',
                        'url'   => '/seo-hizmeti',
                    ],
                    [
                        'icon'  => 'globe',
                        'color' => 'violet',
                        'title' => 'Çoklu dil web sitesi',
                        'text'  => 'Türkçe, İngilizce, Almanca ve Arapça yayın; doğru hreflang kurulumu.',
                        'url'   => '/coklu-dil-web-sitesi',
                    ],
                    [
                        'icon'  => 'shield',
                        'color' => 'sun',
                        'title' => 'Web sitesi bakım',
                        'text'  => 'Güncelleme, yedekleme, güvenlik ve içerik desteği; aylık sabit ücret.',
                        'url'   => '/web-sitesi-bakim',
                    ],
                ],
            ],

            'coast' => [
                'title'       => 'Körfezin her ilçesinde çalışıyoruz',
                'description' => 'Yerinde görüşme, yerel arama bilgisi ve bölgeyi tanıyan bir ekip. '
                    . 'İlçenizi seçin, o bölgeye özel çalışmalarımızı görün.',
            ],

            'steps' => [
                'title' => 'Nasıl çalışıyoruz',
                'items' => [
                    [
                        'title' => 'Konuşuyoruz',
                        'text'  => 'İşinizi, müşterilerinizi ve rakiplerinizi dinliyoruz. '
                            . 'Hangi aramalarda görünmeniz gerektiğini birlikte belirliyoruz.',
                    ],
                    [
                        'title' => 'Kuruyoruz',
                        'text'  => 'Tasarım, içerik ve teknik kurulumu yapıyoruz. '
                            . 'Her sayfayı hız ve arama görünürlüğü için ölçüyoruz.',
                    ],
                    [
                        'title' => 'Büyütüyoruz',
                        'text'  => 'Yayından sonra hangi sayfanın iş getirdiğini ölçüyor, '
                            . 'içeriği ve reklamı buna göre düzenliyoruz.',
                    ],
                ],
            ],

            'works' => [
                'title'       => 'Son çalışmalar',
                'description' => 'Körfezde yayına aldığımız projelerden bir bölümü.',
                'cta'         => ['label' => 'Tüm referanslar', 'url' => '/referanslar'],
            ],

            'faq' => [
                'title'       => 'Sık sorulan sorular',
                'description' => 'Aklınızdaki sorunun cevabı burada yoksa bize yazın.',
            ],

            'cta' => [
                'title' => 'Projenizi konuşalım',
                'text'  => 'Kısa bir görüşmeyle ihtiyacınızı netleştirelim, aynı hafta '
                    . 'fiyat ve takvim gönderelim.',
                'cta1'  => ['label' => 'Teklif isteyin', 'url' => '/iletisim'],
                'cta2'  => ['label' => 'Fiyatları görün', 'url' => '/fiyatlar'],
            ],

            'footer' => [
                'about' => 'Arcates Yazılım, Edremit Körfezi bölgesindeki işletmelere '
                    . 'web tasarım, e-ticaret ve yazılım hizmeti verir.',
                'columns' => [
                    [
                        'title' => 'Hizmetler',
                        'links' => [
                            ['label' => 'Web tasarım', 'url' => '/web-tasarim'],
                            ['label' => 'E-ticaret sitesi', 'url' => '/e-ticaret-sitesi'],
                            ['label' => 'Rezervasyon sistemi', 'url' => '/rezervasyon-sistemi'],
                            ['label' => 'SEO hizmeti', 'url' => '/seo-hizmeti'],
                        ],
                    ],
                    [
                        'title' => 'Bölgeler',
                        'links' => [
                            ['label' => 'Edremit', 'url' => '/edremit-web-tasarim'],
                            ['label' => 'Akçay', 'url' => '/akcay-web-tasarim'],
                            ['label' => 'Altınoluk', 'url' => '/altinoluk-web-tasarim'],
                            ['label' => 'Burhaniye', 'url' => '/burhaniye-web-tasarim'],
                        ],
                    ],
                    [
                        'title' => 'Kurumsal',
                        'links' => [
                            ['label' => 'Hakkımızda', 'url' => '/hakkimizda'],
                            ['label' => 'Referanslar', 'url' => '/referanslar'],
                            ['label' => 'Blog', 'url' => '/blog'],
                            ['label' => 'İletişim', 'url' => '/iletisim'],
                        ],
                    ],
                ],
                'legal' => [
                    ['label' => 'KVKK Aydınlatma Metni', 'url' => '/kvkk'],
                    ['label' => 'Gizlilik Politikası', 'url' => '/gizlilik-politikasi'],
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
            ['name' => 'Ayvalık',    'map_x' => 90,  'map_y' => 118, 'label_above' => 1, 'slug' => 'ayvalik-web-tasarim'],
            ['name' => 'Gömeç',      'map_x' => 205, 'map_y' => 96,  'label_above' => 0, 'slug' => 'gomec-web-tasarim'],
            ['name' => 'Burhaniye',  'map_x' => 330, 'map_y' => 108, 'label_above' => 1, 'slug' => 'burhaniye-web-tasarim'],
            ['name' => 'Edremit',    'map_x' => 470, 'map_y' => 86,  'label_above' => 0, 'slug' => 'edremit-web-tasarim'],
            ['name' => 'Akçay',      'map_x' => 600, 'map_y' => 104, 'label_above' => 1, 'slug' => 'akcay-web-tasarim'],
            ['name' => 'Altınoluk',  'map_x' => 725, 'map_y' => 92,  'label_above' => 0, 'slug' => 'altinoluk-web-tasarim'],
            ['name' => 'Havran',     'map_x' => 845, 'map_y' => 112, 'label_above' => 1, 'slug' => 'havran-web-tasarim'],
            ['name' => 'Balıkesir',  'map_x' => 940, 'map_y' => 84,  'label_above' => 0, 'slug' => 'balikesir-web-tasarim'],
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
            ['label' => 'Bölgeler',    'url' => '/edremit-web-tasarim'],
            ['label' => 'Referanslar', 'url' => '/referanslar'],
            ['label' => 'Fiyatlar',    'url' => '/fiyatlar'],
            ['label' => 'Blog',        'url' => '/blog'],
            ['label' => 'İletişim',    'url' => '/iletisim'],
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
