<?php
/**
 * On yuz denetleyicileri icin temel sinif.
 *
 * Duzen, ortak veriler (menu, ayarlar, dil listesi), bakim modu ve 404
 * uretimi burada toplanir.  DOCS.md 5, 9.11, 11
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\Auth;
use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Settings;
use Arcates\Core\View;
use Arcates\Models\MenuItem;
use Arcates\Models\HomeSection;

abstract class Controller
{
    protected function db(): Database
    {
        return Database::instance();
    }

    /** On yuz duzeni icinde sablon isler. */
    protected function render(string $template, array $data = [], int $status = 200): Response
    {
        // Sayfaya ozel paylasim gorseli varsa mutlak adrese cevrilir.
        if (isset($data['head']['og_image_id']) && (int) $data['head']['og_image_id'] > 0) {
            $media = \Arcates\Core\Media::find((int) $data['head']['og_image_id']);
            if ($media !== null) {
                $variant = $media['variants']['large'] ?? null;
                $data['head']['og_image'] = path_url(
                    \Arcates\Core\Media::url((string) ($variant['path'] ?? $media['path']))
                );
            }
        }

        $data['head'] = array_merge([
            'title'       => (string) Settings::get('site_name', ''),
            'description' => (string) Settings::get('meta_description', ''),
            'canonical'   => url('/'),
            'robots'      => 'index,follow',
            'hreflang'    => [],
            'schemas'     => [],
            'og_image_id' => null,
        ], $data['head'] ?? []);

        $data += [
            'crumbs' => [],
            'body_class' => '',
        ];

        $lang = Lang::current();

        // Header ve footer tum ziyaretci sayfalarinda ayni panel icerigini kullanir.
        // Sayfanin bilerek verdigi null/bos degerleri gecersiz kilmayiz.
        if (!array_key_exists('headerCta', $data)) {
            $header = HomeSection::content('header', $lang, Lang::defaultCode());
            $data['headerCta'] = $header['cta'] ?? null;
        }
        if (!array_key_exists('footerContent', $data)) {
            $data['footerContent'] = HomeSection::content('footer', $lang, Lang::defaultCode());
        }

        $data['_site'] = [
            'name'      => (string) Settings::get('site_name', ''),
            'tagline'   => (string) Settings::get('site_tagline', ''),
            'phone'     => (string) Settings::get('nap_phone', ''),
            'email'     => (string) Settings::get('nap_email', ''),
            'street'    => (string) Settings::get('nap_street', ''),
            'district'  => (string) Settings::get('nap_district', ''),
            'city'      => (string) Settings::get('nap_city', ''),
            'postcode'  => (string) Settings::get('nap_postcode', ''),
            'hours'     => Settings::getArray('opening_hours'),
            'social'    => Settings::getArray('social_links'),
            'analytics' => (string) Settings::get('analytics_code', ''),
        ];

        // Dil degistirici, sayfanin o dildeki karsiligina gider; karsiligi
        // olmayan dil icin o dilin anasayfasina duser. DOCS.md 11.3
        $alternates = [];
        foreach ($data['head']['hreflang'] as $item) {
            if ($item['hreflang'] !== 'x-default') {
                $alternates[$item['hreflang']] = $item['href'];
            }
        }
        $data['_alternates'] = $alternates;

        $data['_menu']   = MenuItem::tree('main', $lang);
        $data['_footer'] = MenuItem::tree('footer', $lang);
        $data['_lang']   = $lang;
        $data['_dir']    = Lang::direction($lang);
        $data['_langs']  = Lang::languages();

        View::shareMany(['_lang' => $lang, '_dir' => $data['_dir']]);

        return Response::html(View::renderIn('front/layout', $template, $data), $status);
    }

    /**
     * 404 yaniti. Kayit tutma ve yonlendirme denetimi faz 8'deki
     * `NotFoundController` icinde yapilir.  DOCS.md 9.8, test F-11
     */
    protected function notFound(Request $request): Response
    {
        return (new NotFoundController())->handle($request);
    }

    /**
     * Bakim modu. Ziyaretci bakim sayfasini gorur, oturum acmis yonetici
     * siteyi normal gorur.  DOCS.md 9.11, test F-17
     */
    public static function maintenanceResponse(): ?Response
    {
        if (!Settings::getBool('maintenance_mode')) {
            return null;
        }

        if (Auth::check()) {
            return null;
        }

        return Response::html(
            View::render('errors/503', ['message' => (string) Settings::get('maintenance_text', '')]),
            503
        )->header('Retry-After', '3600');
    }
}
