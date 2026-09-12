<?php
/**
 * Anasayfa.
 *
 * CMS içeriklerini referans görsel tabanlı koyu arayüze besler.
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Seo;
use Arcates\Core\Settings;
use Arcates\Models\HomeSection;
use Arcates\Models\Post;
use Arcates\Models\Project;

final class HomeController extends Controller
{
    public function index(Request $request, array $params): Response
    {
        $lang     = Lang::current();
        $sections = HomeSection::all($lang);

        foreach ($sections as $key => $section) {
            if ($section['content'] === []) {
                $sections[$key]['content'] = HomeSection::content($key, $lang, Lang::defaultCode());
            }
        }

        $worksLimit = max(3, (int) ($sections['works']['config']['limit'] ?? Settings::getInt('works_limit', 6)));
        $projects   = ($sections['works']['is_active'] ?? false) ? Project::latest($lang, $worksLimit) : [];
        $posts      = Post::published($lang, 3);

        $heroContent = $sections['hero']['content'] ?? [];
        $title       = trim(implode(' ', array_filter([
            (string) ($heroContent['line1'] ?? ''),
            (string) ($heroContent['line2'] ?? ''),
            (string) ($heroContent['line3'] ?? ''),
        ])));

        $siteName = (string) Settings::get('site_name', '');

        return $this->render('front/home', [
            'sections'      => $sections,
            'projects'      => $projects,
            'posts'         => $posts,
            'headerCta'     => $sections['header']['content']['cta'] ?? null,
            'footerContent' => $sections['footer']['content'] ?? [],
            'body_class'    => 'is-home is-reference-home',
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
                'schemas'     => [Seo::professionalService()],
                'styles'      => [
                    'css/reference-home.css',
                    'css/reference-parity.css',
                    'css/reference-parity-hotfix.css',
                    'css/reference-parity-final.css',
                    'css/reference-hero-scene.css',

                    // Kahramanin son katmani: parity dosyalarindan sonra
                    // gelmeli, aksi halde ayni agirliktaki kurallar eziyor.
                    'css/reference-hero.css',
                ],
            ],
        ]);
    }

    private function homeHreflang(): array
    {
        $slugs = [];
        foreach (Lang::codes() as $code) {
            $slugs[$code] = '/';
        }

        return Seo::hreflang($slugs);
    }
}
