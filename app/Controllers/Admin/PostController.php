<?php
/**
 * Blog yonetimi.
 *
 * Kategori, kapak, yayin tarihi, ileri tarihli yayin.  DOCS.md 9.4
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Auth;
use Arcates\Core\Lang;
use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Security;
use Arcates\Core\Validator;
use Arcates\Models\Post;
use Arcates\Models\Redirect;

final class PostController extends Controller
{
    protected string $section = 'posts';
    protected ?string $ability = 'posts.edit';

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        return $this->view('posts/index', [
            'title'  => 'Blog',
            'posts'  => Post::listing(Lang::defaultCode(), $request->str('ara')),
            'search' => $request->str('ara'),
        ]);
    }

    public function create(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        return $this->view('posts/form', $this->formData(null));
    }

    public function edit(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $post = Post::find((int) ($params['id'] ?? 0));
        if ($post === null) {
            return $this->back(admin_url('blog'), 'error', 'Yazi bulunamadi.');
        }

        return $this->view('posts/form', $this->formData($post));
    }

    public function store(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id  = (int) ($params['id'] ?? 0);
        $url = $id > 0 ? admin_url('blog/' . $id) : admin_url('blog/yeni');

        $default = Lang::defaultCode();
        $input   = $request->arr('t');

        $validator = new Validator(
            ['title' => trim((string) ($input[$default]['title'] ?? ''))],
            ['title' => 'Varsayilan dildeki baslik']
        );
        $validator->required('title')->max('title', 200);

        if ($validator->fails()) {
            return $this->withErrors($url, $validator->firstErrors(), $request->allPost());
        }

        $publishedAt = trim((string) $request->post('published_at', ''));
        $timestamp   = $publishedAt !== '' ? strtotime($publishedAt) : false;

        $post = [
            'category'     => $request->str('category') ?: null,
            'cover_id'     => $request->int('cover_id') > 0 ? $request->int('cover_id') : null,
            'author_id'    => Auth::id(),
            'status'       => $request->str('status') === 'published' ? 'published' : 'draft',
            // Ileri tarihli yayin: tarih gelene kadar on yuzde gorunmez.
            // DOCS.md 9.4
            'published_at' => $timestamp !== false ? date('Y-m-d H:i:s', $timestamp) : date('Y-m-d H:i:s'),
        ];

        $previousSlugs = [];
        if ($id > 0) {
            foreach ($this->db()->all('SELECT lang, slug FROM post_translations WHERE post_id = :id', [':id' => $id]) as $row) {
                $previousSlugs[$row['lang']] = $row['slug'];
            }
        }

        $translations = [];
        foreach (Lang::codes() as $code) {
            $fields = $input[$code] ?? [];
            $title  = trim((string) ($fields['title'] ?? ''));

            if ($title === '') {
                $translations[$code] = ['title' => ''];
                continue;
            }

            $slug = trim((string) ($fields['slug'] ?? ''));

            $translations[$code] = [
                'title'            => mb_substr($title, 0, 200),
                'slug'             => Post::uniqueSlug($slug !== '' ? $slug : $title, $code, $id > 0 ? $id : null),
                'excerpt'          => mb_substr(trim((string) ($fields['excerpt'] ?? '')), 0, 400) ?: null,
                'content'          => Security::sanitizeHtml((string) ($fields['content'] ?? '')),
                'meta_title'       => mb_substr(trim((string) ($fields['meta_title'] ?? '')), 0, 180) ?: null,
                'meta_description' => mb_substr(trim((string) ($fields['meta_description'] ?? '')), 0, 320) ?: null,
                'robots'           => in_array($fields['robots'] ?? '', ['index,follow', 'noindex,follow'], true)
                    ? (string) $fields['robots']
                    : 'index,follow',
            ];
        }

        $postId = Post::save($post, $translations, $id > 0 ? $id : null);

        // Slug degisiminde otomatik 301. DOCS.md 9.3
        foreach ($translations as $code => $fields) {
            $old = $previousSlugs[$code] ?? '';
            $new = $fields['slug'] ?? '';
            if ($old !== '' && $new !== '' && $old !== $new) {
                Redirect::forSlugChange('blog/' . $old, 'blog/' . $new, $code);
            }
        }

        Logger::activity($id > 0 ? 'post.update' : 'post.create', 'post', $postId, $translations[$default]['title'] ?? '');

        return $this->back(admin_url('blog/' . $postId), 'success', 'Yazi kaydedildi.');
    }

    public function destroy(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id = (int) ($params['id'] ?? 0);
        $this->db()->delete('posts', ['id' => $id]);
        Logger::activity('post.delete', 'post', $id);

        return $this->back(admin_url('blog'), 'success', 'Yazi silindi.');
    }

    private function formData(?array $post): array
    {
        $translations = [];

        if ($post !== null) {
            foreach ($this->db()->all(
                'SELECT * FROM post_translations WHERE post_id = :id',
                [':id' => (int) $post['id']]
            ) as $row) {
                $translations[$row['lang']] = $row;
            }
        }

        return [
            'title'        => $post === null ? 'Yeni yazi' : 'Yaziyi duzenle',
            'post'         => $post,
            'translations' => $translations,
            'langs'        => Lang::languages(),
            'media'        => $this->db()->all('SELECT id, filename, path FROM media ORDER BY created_at DESC LIMIT 200'),
            'categories'   => $this->db()->column(
                'SELECT DISTINCT category FROM posts WHERE category IS NOT NULL AND category <> :empty ORDER BY category',
                [':empty' => '']
            ),
        ];
    }
}
