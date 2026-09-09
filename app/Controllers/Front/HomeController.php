<?php
/**
 * Anasayfa.
 *
 * Sabit sirali bolumlerden olusur; her bolum panelden acilip kapatilabilir
 * ve icerigi duzenlenebilir. Sira, tam arayuz yeniden tasarim sozlesmesinde
 * hero → strip → services → steps → works → coast → faq → cta'dir.
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Seo;
use Arcates\Core\Settings;
use Arcates\Models\District;
use Arcates\Models\Faq;
use Arcates\Models\HomeSection;
use Arcates\Models\Page;
use Arcates\Models\Project;

final class HomeController extends Controller
{
    public function index(Request $request, array $params): Response
    {
        $lang     = Lang::current();
        $sections = HomeSection::all($lang);

        // Bir dilde ceviri yoksa varsayilan dile duselim ki anasayfa bos kalmasin.
        foreach ($sections as $key => $section) {
            if ($section['content'] === []) {
                $sections[$key]['content'] = HomeSection::content($key, $lang, Lang::defaultCode());
            }
        }

        $worksLimit = (int) ($sections['works']['config']['limit'] ?? Settings::getInt('works_limit', 6));
        $faqLimit   = (int) ($sections['faq']['config']['limit'] ?? 6);

        $projects  = ($sections['works']['is_active'] ?? false) ? Project::latest($lang, $worksLimit) : [];
        $faqs      = ($sections['faq']['is_active'] ?? false) ? Faq::forHome($lang, $faqLimit) : [];
        $districts = ($sections['coast']['is_active'] ?? false) ? District::forMap($lang) : [];

        // Sektor seridi artik dekoratif metin dongusu degil, gercek ve
        // yayinlanmis sektor sayfalarina giden sakin bir baglanti grubudur.
        $sectors = [];
        if ($sections['strip']['is_active'] ?? false) {
            foreach (Page::listing('sector', $lang) as $sector) {
                if (($sector['status'] ?? '') !== 'published' || ($sector['slug'] ?? '') === '' || ($sector['title'] ?? '') === '') {
                    continue;
                }
                $sectors[] = [
                    'title' => (string) $sector['title'],
                    'slug'  => (string) $sector['slug'],
                ];
            }
        }

        $heroContent = $sections['hero']['content'] ?? [];
        $title       = trim(implode(' ', array_filter([
            (string) ($heroContent['line1'] ?? ''),
            (string) ($heroContent['line2'] ?? ''),
            (string) ($heroContent['line3'] ?? ''),
        ])));

        $siteName = (string) Settings::get('site_name', '');

        $schemas = [Seo::professionalService()];
        $faqSchema = Seo::faqPage($faqs);
        if ($faqSchema !== null) {
            $schemas[] = $faqSchema;
        }

        return $this->render('front/home', [
            'sections'      => $sections,
            'projects'      => $projects,
            'faqs'          => $faqs,
            'districts'     => $districts,
            'sectors'       => $sectors,
            'headerCta'     => $sections['header']['content']['cta'] ?? null,
            'footerContent' => $sections['footer']['content'] ?? [],
            'body_class'    => 'is-home is-redesign-home',
            'head'          => [
                'title'       => Seo::title($title !== '' ? $title : $siteName, (string) Settings::get('home_meta_title', '')),
                'description' => Seo::description(
                    (string) Settings::get('home_meta_description', ''),
                    (string) ($heroContent['description'] ?? ''),
                    null
                ),
                'canonical'   => url('/'),
                'robots'      => 'index,follow',
                'hreflang'    => $this->homeHreflang(),
                'schemas'     => $schemas,
                'styles'      => [
                    'css/home-redesign.css',
                    'css/r3-service-illustrations.css',
                    'css/r3-process-region-visuals.css',
                ],
            ],
        ]);
    }

    /** Anasayfa her etkin dilde yayindadir. DOCS.md 11.3 */
    private function homeHreflang(): array
    {
        $slugs = [];
        foreach (Lang::codes() as $code) {
            $slugs[$code] = '/';
        }

        return Seo::hreflang($slugs);
    }
}
