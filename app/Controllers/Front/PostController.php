<?php
/**
 * Blog — liste ve yazi.  DOCS.md 4.1, 11.2 (Article)
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Seo;
use Arcates\Models\Page;
use Arcates\Models\Post;

final class PostController extends Controller
{
    private const PER_PAGE = 12;

    /** `/blog` */
    public function index(Request $request, array $params): Response
    {
        $lang     = Lang::current();
        $page     = Page::published('blog', $lang);
        $category = $request->str('kategori');
        $number   = max(1, $request->int('sayfa', 1));

        $total = Post::countPublished($lang, $category);
        $posts = Post::published($lang, self::PER_PAGE, ($number - 1) * self::PER_PAGE, $category);

        $crumbs = [
            ['label' => __('home'), 'url' => url('/')],
            ['label' => $page['title'] ?? __('blog'), 'url' => url('/blog')],
        ];

        return $this->render('front/posts', [
            'page'       => $page,
            'posts'      => $posts,
            'categories' => Post::categories($lang),
            'category'   => $category,
            'number'     => $number,
            'pages'      => (int) ceil($total / self::PER_PAGE),
            'crumbs'     => $crumbs,
            'head'       => [
                'title'       => Seo::title($page['title'] ?? __('blog'), $page['meta_title'] ?? null),
                'description' => Seo::description(
                    $page['meta_description'] ?? null,
                    $page['excerpt'] ?? null,
                    (string) ($page['content'] ?? '')
                ),
                // Sayfalanmis listelerde canonical ilk sayfayi gosterir.
                'canonical'   => url('/blog'),
                'robots'      => $number > 1 ? 'noindex,follow' : (string) ($page['robots'] ?? 'index,follow'),
                'hreflang'    => $page !== null ? Seo::hreflang(Page::alternates((int) $page['id'])) : [],
                'schemas'     => array_filter([Seo::breadcrumbList($crumbs)]),
            ],
        ]);
    }

    /** `/blog/{slug}` */
    public function show(Request $request, array $params): Response
    {
        $lang = Lang::current();
        $slug = trim((string) ($params['slug'] ?? ''), '/');
        $post = Post::bySlugPublished($slug, $lang);

        if ($post === null) {
            return $this->notFound($request);
        }

        $path   = '/blog/' . $post['slug'];
        $crumbs = [
            ['label' => __('home'), 'url' => url('/')],
            ['label' => __('blog'), 'url' => url('/blog')],
            ['label' => (string) $post['title'], 'url' => url($path)],
        ];

        return $this->render('front/post', [
            'post'   => $post,
            'latest' => array_values(array_filter(
                Post::published($lang, 4),
                static fn (array $row): bool => (int) $row['id'] !== (int) $post['id']
            )),
            'crumbs' => $crumbs,
            'head'   => [
                'title'       => Seo::title((string) $post['title'], $post['meta_title'] ?? null),
                'description' => Seo::description(
                    $post['meta_description'] ?? null,
                    $post['excerpt'] ?? null,
                    (string) ($post['content'] ?? '')
                ),
                'canonical'   => url($path),
                'robots'      => (string) ($post['robots'] ?? 'index,follow'),
                'hreflang'    => $this->alternates((int) $post['id']),
                'schemas'     => array_filter([
                    Seo::article($post, url($path)),
                    Seo::breadcrumbList($crumbs),
                ]),
                'og_image_id' => $post['cover_id'] ?? null,
            ],
        ]);
    }

    private function alternates(int $postId): array
    {
        $rows = $this->db()->all(
            'SELECT t.lang, t.slug
               FROM post_translations t
               JOIN posts p ON p.id = t.post_id
              WHERE t.post_id = :id AND p.status = :status
                AND (p.published_at IS NULL OR p.published_at <= NOW())',
            [':id' => $postId, ':status' => 'published']
        );

        $slugs = [];
        foreach ($rows as $row) {
            $slugs[$row['lang']] = 'blog/' . $row['slug'];
        }

        return Seo::hreflang($slugs);
    }
}
