<?php
/**
 * SEO yardimcilari: meta uretimi, hreflang seti, yapisal veri ve icerik skoru.
 *
 * DOCS.md 9.6, 11.1, 11.2, 11.3 — testler U-09, U-10, U-11, U-13, O-01…O-08
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Seo
{
    /** Baslik ve aciklama sinirlari. DOCS.md 9.6, 11.1 */
    public const TITLE_MAX       = 60;
    public const DESCRIPTION_MAX = 160;

    /** Kelime esikleri. DOCS.md 9.6 */
    public const WORDS_MIN          = 300;
    public const WORDS_MIN_LOCATION = 500;

    /** Ilce sayfalari arasi kabul edilen en yuksek ortusme. DOCS.md 9.6, 14.8 O-06 */
    public const SIMILARITY_LIMIT = 0.70;

    // --- Meta ---------------------------------------------------------------

    /**
     * Sayfa basligi. Panelde tanimli sablon uygulanir.
     * Varsayilan: `%title% | %site%`  DOCS.md 9.7
     */
    public static function title(string $pageTitle, ?string $metaTitle = null): string
    {
        $explicit = trim((string) $metaTitle);
        if ($explicit !== '') {
            return $explicit;
        }

        $pattern = (string) Settings::get('meta_title_pattern', '%title% | %site%');
        $site    = (string) Settings::get('site_name', (string) Config::get('app.name', 'Arcates'));

        return trim(str_replace(['%title%', '%site%'], [$pageTitle, $site], $pattern));
    }

    /** Aciklama; verilmemisse ozet veya icerikten uretilir. */
    public static function description(?string $meta, ?string $excerpt = null, ?string $content = null): string
    {
        foreach ([$meta, $excerpt] as $candidate) {
            $value = trim((string) $candidate);
            if ($value !== '') {
                return mb_substr($value, 0, self::DESCRIPTION_MAX + 60);
            }
        }

        if ($content !== null && $content !== '') {
            return str_limit($content, 155);
        }

        return (string) Settings::get('meta_description', '');
    }

    /** Mutlak canonical adres. DOCS.md 11.1, test O-03 */
    public static function canonical(string $path, ?string $override = null, ?string $lang = null): string
    {
        $explicit = trim((string) $override);
        if ($explicit !== '') {
            return preg_match('#^https?://#i', $explicit) === 1 ? $explicit : path_url($explicit);
        }

        return url($path, $lang);
    }

    /**
     * `hreflang` seti.
     *
     * Karsiligi olmayan dil icin `hreflang` verilmez; `x-default` varsayilan
     * dile isaret eder.  DOCS.md 11.3, test U-13
     *
     * @param array<string,string> $slugs Dil kodu => slug
     * @return array<int, array{hreflang:string, href:string}>
     */
    public static function hreflang(array $slugs): array
    {
        $out     = [];
        $default = Lang::defaultCode();

        foreach ($slugs as $code => $slug) {
            $slug = trim((string) $slug);
            if ($slug === '' || !Lang::exists((string) $code)) {
                continue;
            }
            $out[] = [
                'hreflang' => (string) $code,
                'href'     => url('/' . ltrim($slug, '/'), (string) $code),
            ];
        }

        // Tek dil varsa hreflang seti anlamsizdir.
        if (count($out) < 2) {
            return [];
        }

        if (isset($slugs[$default]) && trim((string) $slugs[$default]) !== '') {
            $out[] = [
                'hreflang' => 'x-default',
                'href'     => url('/' . ltrim((string) $slugs[$default], '/'), $default),
            ];
        }

        return $out;
    }

    // --- Yapisal veri -------------------------------------------------------

    /**
     * `ProfessionalService`. Isim, adres ve telefon Google Isletme Profili ile
     * birebir ayni olmalidir. Uydurma yorum veya AggregateRating yazilmaz.
     * DOCS.md 11.2
     */
    public static function professionalService(): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'ProfessionalService',
            'name'     => (string) Settings::get('nap_name', (string) Settings::get('site_name', '')),
            'url'      => path_url('/'),
        ];

        $description = (string) Settings::get('meta_description', '');
        if ($description !== '') {
            $schema['description'] = $description;
        }

        $phone = (string) Settings::get('nap_phone', '');
        if ($phone !== '') {
            $schema['telephone'] = $phone;
        }

        $email = (string) Settings::get('nap_email', '');
        if ($email !== '') {
            $schema['email'] = $email;
        }

        $address = array_filter([
            '@type'           => 'PostalAddress',
            'streetAddress'   => (string) Settings::get('nap_street', ''),
            'addressLocality' => (string) Settings::get('nap_district', ''),
            'addressRegion'   => (string) Settings::get('nap_city', ''),
            'postalCode'      => (string) Settings::get('nap_postcode', ''),
            'addressCountry'  => (string) Settings::get('nap_country', 'TR'),
        ], static fn ($v): bool => $v !== '');

        if (count($address) > 1) {
            $schema['address'] = $address;
        }

        $lat = (string) Settings::get('nap_lat', '');
        $lng = (string) Settings::get('nap_lng', '');
        if ($lat !== '' && $lng !== '') {
            $schema['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => $lat, 'longitude' => $lng];
        }

        $hours = Settings::getArray('opening_hours');
        if ($hours) {
            $schema['openingHoursSpecification'] = array_map(
                static fn (array $h): array => [
                    '@type'  => 'OpeningHoursSpecification',
                    'dayOfWeek' => $h['days'] ?? '',
                    'opens'  => $h['opens'] ?? '',
                    'closes' => $h['closes'] ?? '',
                ],
                $hours
            );
        }

        $social = Settings::getArray('social_links');
        if ($social) {
            $schema['sameAs'] = array_values(array_filter(array_column($social, 'url')));
        }

        return $schema;
    }

    /** Hizmet sayfalari. Ilce sayfalarinda `areaServed` eklenir. DOCS.md 11.2 */
    public static function service(string $name, string $description, ?string $areaServed = null): array
    {
        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Service',
            'name'        => $name,
            'description' => $description,
            'provider'    => [
                '@type' => 'ProfessionalService',
                'name'  => (string) Settings::get('nap_name', ''),
                'url'   => path_url('/'),
            ],
        ];

        if ($areaServed !== null && $areaServed !== '') {
            $schema['areaServed'] = ['@type' => 'Place', 'name' => $areaServed];
        }

        return $schema;
    }

    /** SSS bolumu olan her sayfada. DOCS.md 11.2 */
    public static function faqPage(array $faqs): ?array
    {
        if (!$faqs) {
            return null;
        }

        return [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array_map(
                static fn (array $faq): array => [
                    '@type'          => 'Question',
                    'name'           => (string) ($faq['question'] ?? ''),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => Security::toPlainText((string) ($faq['answer'] ?? '')),
                    ],
                ],
                $faqs
            ),
        ];
    }

    /** Kirinti yolu semasi; tum ic sayfalarda. DOCS.md 11.2 */
    public static function breadcrumbList(array $crumbs): ?array
    {
        if (count($crumbs) < 2) {
            return null;
        }

        $items = [];
        foreach (array_values($crumbs) as $i => $crumb) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => (string) ($crumb['label'] ?? ''),
                'item'     => (string) ($crumb['url'] ?? ''),
            ];
        }

        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    /** Referans detay sayfasi. DOCS.md 11.2 */
    public static function creativeWork(array $project, string $url): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'CreativeWork',
            'name'     => (string) ($project['title'] ?? $project['client_name'] ?? ''),
            'url'      => $url,
            'creator'  => ['@type' => 'Organization', 'name' => (string) Settings::get('nap_name', '')],
        ];

        $excerpt = (string) ($project['excerpt'] ?? '');
        if ($excerpt !== '') {
            $schema['description'] = $excerpt;
        }

        return $schema;
    }

    /** Blog yazisi. DOCS.md 11.2 */
    public static function article(array $post, string $url): array
    {
        $schema = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => mb_substr((string) ($post['title'] ?? ''), 0, 110),
            'url'           => $url,
            'mainEntityOfPage' => $url,
            'publisher'     => ['@type' => 'Organization', 'name' => (string) Settings::get('nap_name', '')],
        ];

        if (!empty($post['published_at'])) {
            $schema['datePublished'] = date('c', strtotime((string) $post['published_at']));
        }
        if (!empty($post['updated_at'])) {
            $schema['dateModified'] = date('c', strtotime((string) $post['updated_at']));
        }
        if (!empty($post['author_name'])) {
            $schema['author'] = ['@type' => 'Person', 'name' => (string) $post['author_name']];
        }

        return $schema;
    }

    // --- Icerik skoru -------------------------------------------------------

    /**
     * Icerik skoru. Kaydi engellemez, eksikleri listeler.  DOCS.md 9.6
     *
     * @param array $page        `pages` satiri (type, id, district).
     * @param array $translation `page_translations` satiri.
     * @return array{score:int, issues:array<int, array{level:string, message:string, check:string}>}
     */
    public static function score(array $page, array $translation): array
    {
        $issues = [];
        $type   = (string) ($page['type'] ?? 'page');
        $lang   = (string) ($translation['lang'] ?? Lang::defaultCode());

        $content = (string) ($translation['content'] ?? '');
        $words   = (int) ($translation['word_count'] ?? Security::wordCount($content));

        $add = static function (string $level, string $check, string $message) use (&$issues): void {
            $issues[] = ['level' => $level, 'check' => $check, 'message' => $message];
        };

        // Kelime sayisi
        if ($type === 'location' && $words < self::WORDS_MIN_LOCATION) {
            $add(
                'strong',
                'words',
                "İlçe sayfası {$words} kelime. En az " . self::WORDS_MIN_LOCATION
                . ' kelime özgün metin gerekir; aksi halde sayfa doorway page sayılabilir.'
            );
        } elseif ($words < self::WORDS_MIN) {
            $add('warn', 'words', "İçerik {$words} kelime. En az " . self::WORDS_MIN . ' kelime önerilir.');
        }

        // H1 sayisi
        $h1Count = preg_match_all('#<h1\b#i', $content);
        // Sablon basligi H1 uretir; icerikte ek H1 olmamalidir.
        if ($h1Count > 0) {
            $add('warn', 'h1', 'İçerikte ' . $h1Count . ' adet H1 var. Sayfa başlığı zaten H1 üretir; içerikte H2 kullanın.');
        }

        // Meta baslik
        $metaTitle = trim((string) ($translation['meta_title'] ?? ''));
        if ($metaTitle === '') {
            $add('warn', 'meta_title', 'Meta başlık boş. Boş bırakılırsa sayfa başlığı kullanılır.');
        } elseif (mb_strlen($metaTitle) > self::TITLE_MAX) {
            $add('warn', 'meta_title', 'Meta başlık ' . mb_strlen($metaTitle) . ' karakter; ' . self::TITLE_MAX . ' karakteri aşıyor.');
        }

        // Meta aciklama
        $metaDescription = trim((string) ($translation['meta_description'] ?? ''));
        if ($metaDescription === '') {
            $add('warn', 'meta_description', 'Meta açıklama boş.');
        } elseif (mb_strlen($metaDescription) > self::DESCRIPTION_MAX) {
            $add('warn', 'meta_description', 'Meta açıklama ' . mb_strlen($metaDescription) . ' karakter; ' . self::DESCRIPTION_MAX . ' karakteri aşıyor.');
        }

        // Slug
        $slug = (string) ($translation['slug'] ?? '');
        if ($slug !== '' && !Security::isCleanSlug($slug)) {
            $add('warn', 'slug', 'Adres Türkçe karakter veya boşluk içeriyor.');
        }

        // Alt metni eksik gorsel
        $missingAlt = self::countImagesWithoutAlt($content);
        if ($missingAlt > 0) {
            $add('warn', 'alt', $missingAlt . ' görselde alt metni eksik.');
        }

        // Ic link
        $internalLinks = self::countInternalLinks($content);
        if ($internalLinks < 2) {
            $add('warn', 'links', 'İçerikte ' . $internalLinks . ' iç link var; en az 2 önerilir.');
        }

        $pageId = (int) ($page['id'] ?? 0);

        if ($pageId > 0) {
            // Sayfaya atanmis SSS
            if (in_array($type, ['location', 'service'], true)) {
                $faqCount = self::countPageFaqs($pageId);
                if ($faqCount === 0) {
                    $add('warn', 'faq', 'Sayfaya atanmış SSS kaydı yok.');
                }
            }

            // Ilceye ozel referans
            if ($type === 'location') {
                $district = (string) ($page['district'] ?? '');
                if ($district === '' || self::countDistrictProjects($district) === 0) {
                    $add('warn', 'reference', 'Bu ilçeye ait yayınlanmış referans yok.');
                }

                // Benzerlik — ilce sayfalarinin birbirine benzemesi kritiktir.
                $similar = self::mostSimilarLocation($pageId, $lang, $content);
                if ($similar !== null && $similar['ratio'] >= self::SIMILARITY_LIMIT) {
                    $add(
                        'strong',
                        'similarity',
                        sprintf(
                            '"%s" ilçe sayfasıyla %%%d örtüşüyor. %%%d üstü örtüşme doorway page riski taşır.',
                            $similar['title'],
                            (int) round($similar['ratio'] * 100),
                            (int) round(self::SIMILARITY_LIMIT * 100)
                        )
                    );
                }
            }
        }

        // Skor: her uyari 8, her guclu uyari 20 puan dusurur.
        $score = 100;
        foreach ($issues as $issue) {
            $score -= $issue['level'] === 'strong' ? 20 : 8;
        }

        return ['score' => max(0, $score), 'issues' => $issues];
    }

    /** Alt metni bos veya eksik gorsel sayisi. DOCS.md 9.6, test E-03 */
    public static function countImagesWithoutAlt(string $html): int
    {
        if (preg_match_all('#<img\b[^>]*>#i', $html, $matches) === 0) {
            return 0;
        }

        $missing = 0;
        foreach ($matches[0] as $tag) {
            if (preg_match('#\salt\s*=\s*("([^"]*)"|\'([^\']*)\')#i', $tag, $alt) !== 1) {
                $missing++;
                continue;
            }
            $value = trim($alt[2] !== '' ? $alt[2] : ($alt[3] ?? ''));
            // Dekoratif gorsel alt="" tasiyabilir; bu durumda aria-hidden beklenir.
            if ($value === '' && stripos($tag, 'aria-hidden') === false) {
                $missing++;
            }
        }

        return $missing;
    }

    /** Icerikteki ic link sayisi. DOCS.md 9.6, 11.1 */
    public static function countInternalLinks(string $html): int
    {
        if (preg_match_all('#<a\b[^>]*href\s*=\s*("([^"]*)"|\'([^\']*)\')#i', $html, $matches, PREG_SET_ORDER) === 0) {
            return 0;
        }

        $base  = rtrim((string) Config::get('app.base_url', ''), '/');
        $count = 0;

        foreach ($matches as $match) {
            $href = trim($match[2] !== '' ? $match[2] : ($match[3] ?? ''));
            if ($href === '' || str_starts_with($href, '#')) {
                continue;
            }
            if (str_starts_with($href, '/') || ($base !== '' && str_starts_with($href, $base))) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Iki metnin ortusme orani (0-1).
     *
     * Kelime kumesi karsilastirmasi kullanilir; kelime sirasi degistirilerek
     * cogaltilmis metinler de yakalanir.  DOCS.md 9.6, test U-11
     */
    public static function similarity(string $a, string $b): float
    {
        $wordsA = self::wordSet($a);
        $wordsB = self::wordSet($b);

        if (!$wordsA || !$wordsB) {
            return 0.0;
        }

        $shared = count(array_intersect_key($wordsA, $wordsB));

        // Kucuk metne gore oran; kisa bir metnin uzun metne gomulmesi de yakalanir.
        $denominator = min(count($wordsA), count($wordsB));

        return $denominator > 0 ? round($shared / $denominator, 4) : 0.0;
    }

    /** Metni benzersiz kelime kumesine cevirir. */
    private static function wordSet(string $text): array
    {
        $plain = mb_strtolower(Security::toPlainText($text), 'UTF-8');
        $words = preg_split('/[^\p{L}\p{N}]+/u', $plain, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $set = [];
        foreach ($words as $word) {
            // Cok kisa kelimeler (baglaclar) ayirt edici degildir.
            if (mb_strlen($word) < 3) {
                continue;
            }
            $set[$word] = true;
        }

        return $set;
    }

    /**
     * Bu icerige en cok benzeyen diger ilce sayfasi.
     *
     * @return array{title:string, ratio:float, page_id:int}|null
     */
    public static function mostSimilarLocation(int $pageId, string $lang, string $content): ?array
    {
        try {
            $rows = Database::instance()->all(
                'SELECT t.page_id, t.title, t.content
                   FROM page_translations t
                   JOIN pages p ON p.id = t.page_id
                  WHERE p.type = :type AND t.lang = :lang AND t.page_id <> :id',
                [':type' => 'location', ':lang' => $lang, ':id' => $pageId]
            );
        } catch (\Throwable) {
            return null;
        }

        $best = null;
        foreach ($rows as $row) {
            $ratio = self::similarity($content, (string) $row['content']);
            if ($best === null || $ratio > $best['ratio']) {
                $best = ['title' => (string) $row['title'], 'ratio' => $ratio, 'page_id' => (int) $row['page_id']];
            }
        }

        return $best;
    }

    private static function countPageFaqs(int $pageId): int
    {
        try {
            return (int) Database::instance()->value(
                'SELECT COUNT(*) FROM faq_page WHERE page_id = :id',
                [':id' => $pageId]
            );
        } catch (\Throwable) {
            return 0;
        }
    }

    private static function countDistrictProjects(string $district): int
    {
        try {
            return (int) Database::instance()->value(
                'SELECT COUNT(*) FROM projects WHERE district = :district AND status = :status',
                [':district' => $district, ':status' => 'published']
            );
        } catch (\Throwable) {
            return 0;
        }
    }
}
