<?php
/**
 * On yuz sayfa cozumleyicisi.
 *
 * Yol `/{slug}` bicimindedir; sayfa turune gore `page`, `service`,
 * `location` veya `sector` sablonu isler. DOCS.md 4, 5
 *
 * Taslak sayfa on yuzde 404 doner. Test F-02
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Seo;
use Arcates\Models\Page;

final class PageController extends Controller
{
    /** Sayfayi slug ile cozer. */
    public function show(Request $request, array $params): Response
    {
        $slug = trim((string) ($params['slug'] ?? ''), '/');
        if ($slug === '') {
            return $this->notFound($request);
        }

        $lang = Lang::current();
        $page = Page::published($slug, $lang);

        if ($page === null) {
            return $this->notFound($request);
        }

        $pageId       = (int) $page['id'];
        $faqs         = Page::faqs($pageId, $lang);
        $projects     = [];
        $relatedPages = [];

        // Her ilce sayfasi o ilceye ait en az bir ornek icermelidir.
        // DOCS.md 4.7
        if ($page['type'] === 'location' && !empty($page['district'])) {
            $projects = Page::districtProjects((string) $page['district'], $lang, 3);
        }

        // Ic sayfalardaki "diger hizmetler / sektorler / komsu bolgeler"
        // listesi mevcut yayinlanmis sayfa verisinden uretilir. Taslaklar ve
        // mevcut sayfa listeye girmez; sablona sabit baglanti gomulmez.
        if (in_array((string) $page['type'], ['location', 'service', 'sector'], true)) {
            foreach (Page::listing((string) $page['type'], $lang) as $related) {
                if ((int) ($related['id'] ?? 0) === $pageId || ($related['status'] ?? '') !== 'published') {
                    continue;
                }
                if (($related['title'] ?? '') === '' || ($related['slug'] ?? '') === '') {
                    continue;
                }
                $relatedPages[] = $related;
                if (count($relatedPages) >= 8) {
                    break;
                }
            }
        }

        $path      = '/' . $page['slug'];
        $crumbs    = $this->breadcrumbs($page, $path);
        $schemas   = $this->schemas($page, $faqs, $crumbs, $path);
        $template  = 'front/' . (in_array($page['template'], ['page', 'service', 'location', 'sector'], true)
            ? $page['template']
            : 'page');

        return $this->render($template, [
            'page'         => $page,
            'faqs'         => $faqs,
            'projects'     => $projects,
            'relatedPages' => $relatedPages,
            'crumbs'       => $crumbs,
            'body_class'   => 'page-type-' . (string) $page['type'],
            'head'         => [
                'title'       => Seo::title((string) $page['title'], $page['meta_title'] ?? null),
                'description' => Seo::description(
                    $page['meta_description'] ?? null,
                    $page['excerpt'] ?? null,
                    (string) ($page['content'] ?? '')
                ),
                'canonical'   => Seo::canonical($path, $page['canonical'] ?? null, $lang),
                'robots'      => (string) ($page['robots'] ?? 'index,follow'),
                'hreflang'    => $this->hreflang($page),
                'schemas'     => $schemas,
                'og_image_id' => $page['og_image_id'] ?? null,
                'styles'      => $page['type'] === 'sector' ? ['css/r3-sector-illustrations.css'] : [],
            ],
        ]);
    }

    /**
     * `hreflang` seti.
     *
     * Ilce sayfalari yalnizca Turkce yayinlanir; diger dillerde hreflang
     * verilmez. DOCS.md 4.6
     */
    private function hreflang(array $page): array
    {
        if ($page['type'] === 'location') {
            return [];
        }

        return Seo::hreflang(Page::alternates((int) $page['id']));
    }

    /** Kirinti yolu. DOCS.md 11.1 */
    private function breadcrumbs(array $page, string $path): array
    {
        $crumbs = [['label' => __('home'), 'url' => url('/')]];

        $section = match ($page['type']) {
            'service'  => ['label' => __('services'), 'url' => null],
            'location' => ['label' => __('locations'), 'url' => null],
            'sector'   => ['label' => __('sectors'), 'url' => null],
            default    => null,
        };

        if ($section !== null) {
            $crumbs[] = $section;
        }

        $crumbs[] = ['label' => (string) $page['title'], 'url' => url($path)];

        return $crumbs;
    }

    /** Sayfa turune gore yapisal veri. DOCS.md 11.2 */
    private function schemas(array $page, array $faqs, array $crumbs, string $path): array
    {
        $schemas = [];
        $type    = (string) ($page['schema_type'] ?? '') ?: match ($page['type']) {
            'service', 'sector' => 'Service',
            'location'          => 'Service',
            default             => '',
        };

        $description = Seo::description(
            $page['meta_description'] ?? null,
            $page['excerpt'] ?? null,
            (string) ($page['content'] ?? '')
        );

        if ($type === 'Service') {
            $schemas[] = Seo::service(
                (string) $page['title'],
                $description,
                $page['type'] === 'location' ? (string) ($page['district'] ?? '') : null
            );
        } elseif ($type === 'ProfessionalService') {
            $schemas[] = Seo::professionalService();
        }

        $faqSchema = Seo::faqPage($faqs);
        if ($faqSchema !== null) {
            $schemas[] = $faqSchema;
        }

        $breadcrumb = Seo::breadcrumbList(array_map(
            static fn (array $c): array => ['label' => $c['label'], 'url' => $c['url'] ?? ''],
            array_filter($crumbs, static fn (array $c): bool => ($c['url'] ?? null) !== null)
        ));
        if ($breadcrumb !== null) {
            $schemas[] = $breadcrumb;
        }

        return $schemas;
    }
}
