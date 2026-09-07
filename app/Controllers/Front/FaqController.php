<?php
/**
 * SSS sayfasi.  DOCS.md 4.1, 11.2 (FAQPage)
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Seo;
use Arcates\Models\Faq;
use Arcates\Models\Page;

final class FaqController extends Controller
{
    public function index(Request $request, array $params): Response
    {
        $lang = Lang::current();
        $page = Page::published('sss', $lang);
        $faqs = Faq::published($lang);

        $crumbs = [
            ['label' => __('home'), 'url' => url('/')],
            ['label' => $page['title'] ?? __('faq'), 'url' => url('/sss')],
        ];

        return $this->render('front/faqs', [
            'page'   => $page,
            'faqs'   => $faqs,
            'crumbs' => $crumbs,
            'head'   => [
                'title'       => Seo::title($page['title'] ?? __('faq'), $page['meta_title'] ?? null),
                'description' => Seo::description(
                    $page['meta_description'] ?? null,
                    $page['excerpt'] ?? null,
                    (string) ($page['content'] ?? '')
                ),
                'canonical'   => url('/sss'),
                'robots'      => (string) ($page['robots'] ?? 'index,follow'),
                'hreflang'    => $page !== null ? Seo::hreflang(Page::alternates((int) $page['id'])) : [],
                'schemas'     => array_filter([Seo::faqPage($faqs), Seo::breadcrumbList($crumbs)]),
            ],
        ]);
    }
}
