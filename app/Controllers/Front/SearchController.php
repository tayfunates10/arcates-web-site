<?php
/**
 * Site ici arama sonuclari.  DOCS.md 4.1
 *
 * Arama GET ile calisir: sonuc sayfasi paylasilabilir ve geri tusu
 * beklendigi gibi davranir. Veri degistirmedigi icin CSRF gerekmez.
 *
 * Sonuc sayfalari `noindex` isaretlenir: arama sonucu sayfalarinin arama
 * motoruna girmesi ince icerik sayilir ve kendi sayfalarimizla rekabet
 * eder.  DOCS.md 11
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Seo;
use Arcates\Models\Search;

final class SearchController extends Controller
{
    public function index(Request $request, array $params): Response
    {
        $lang  = Lang::current();
        $query = trim((string) $request->get('q', ''));

        // Cok uzun girdi sorguyu buyutmekten baska ise yaramaz.
        $query = mb_substr($query, 0, 80);

        $groups = $query === '' ? [] : Search::all($query, $lang);
        $total  = Search::count($groups);

        $crumbs = [
            ['label' => __('home'), 'url' => url('/')],
            ['label' => __('search'), 'url' => url('/ara')],
        ];

        return $this->render('front/search', [
            'query'  => $query,
            'groups' => $groups,
            'total'  => $total,
            'tooShort' => $query !== '' && !Search::isValid($query),
            'crumbs' => $crumbs,
            'head'   => [
                'title'       => Seo::title(
                    $query === '' ? __('search') : __('search') . ': ' . $query
                ),
                'description' => __('search_intro'),
                'canonical'   => url('/ara'),
                'robots'      => 'noindex,follow',
                'hreflang'    => [],
                'schemas'     => array_filter([Seo::breadcrumbList($crumbs)]),
            ],
        ]);
    }
}
