<?php
/**
 * Sayfa yonetimi.
 *
 * Tur filtresi (sayfa / hizmet / ilce / sektor), dil sekmeleri, tam SEO
 * paneli, icerik skoru ve Google sonuc onizlemesi.
 * Slug degisince otomatik 301 kaydi olusturulur; kapatilamaz.
 * DOCS.md 9.3, 9.6, test F-03
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Security;
use Arcates\Core\Validator;
use Arcates\Models\Page;
use Arcates\Models\Redirect;

final class PageController extends Controller
{
    protected string $section = 'pages';
    protected ?string $ability = 'pages.edit';

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $type   = $request->str('tur');
        $search = $request->str('ara');
        $lang   = $this->langParam($request);

        return $this->view('pages/index', [
            'title'  => 'Sayfalar',
            'pages'  => Page::listing($type, $lang, $search),
            'types'  => Page::TYPES,
            'type'   => $type,
            'search' => $search,
            'lang'   => $lang,
            'langs'  => Lang::languages(),
        ]);
    }

    public function create(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        return $this->view('pages/form', [
            'title'        => 'Yeni sayfa',
            'page'         => null,
            'translations' => [],
            'types'        => Page::TYPES,
            'langs'        => Lang::languages(),
            'score'        => null,
            'pages'        => $this->parentOptions(null),
        ]);
    }

    public function edit(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $id   = (int) ($params['id'] ?? 0);
        $page = Page::find($id);

        if ($page === null) {
            return $this->back(admin_url('sayfalar'), 'error', 'Sayfa bulunamadi.');
        }

        $translations = [];
        foreach ($this->db()->all('SELECT * FROM page_translations WHERE page_id = :id', [':id' => $id]) as $row) {
            $translations[$row['lang']] = $row;
        }

        return $this->view('pages/form', [
            'title'        => 'Sayfayi duzenle',
            'page'         => $page,
            'translations' => $translations,
            'types'        => Page::TYPES,
            'langs'        => Lang::languages(),
            'score'        => \Arcates\Core\Seo::score($page, $translations[Lang::defaultCode()] ?? []),
            'pages'        => $this->parentOptions($id),
        ]);
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
        $url = $id > 0 ? admin_url('sayfalar/' . $id) : admin_url('sayfalar/yeni');

        $defaultLang = Lang::defaultCode();
        $input       = $request->arr('t');

        $validator = new Validator([
            'type'   => $request->str('type'),
            'status' => $request->str('status'),
            'title'  => trim((string) ($input[$defaultLang]['title'] ?? '')),
        ], [
            'title' => 'Varsayilan dildeki baslik',
        ]);

        $validator->in('type', array_keys(Page::TYPES))
            ->in('status', ['draft', 'published'])
            ->required('title')->max('title', 200);

        if ($validator->fails()) {
            return $this->withErrors($url, $validator->firstErrors(), $request->allPost());
        }

        $type = $request->str('type');

        $page = [
            'type'      => $type,
            'template'  => Page::TEMPLATES[$type] ?? 'page',
            'status'    => $request->str('status') === 'published' ? 'published' : 'draft',
            'parent_id' => $request->int('parent_id') > 0 ? $request->int('parent_id') : null,
            'cover_id'  => $request->int('cover_id') > 0 ? $request->int('cover_id') : null,
            'district'  => $type === 'location' ? ($request->str('district') ?: null) : null,
            'sort'      => $request->int('sort'),
        ];

        // Mevcut slug'lar; degisenler icin 301 yazilacak. DOCS.md 9.3
        $previousSlugs = [];
        if ($id > 0) {
            foreach ($this->db()->all('SELECT lang, slug FROM page_translations WHERE page_id = :id', [':id' => $id]) as $row) {
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
            $slug = Page::uniqueSlug($slug !== '' ? $slug : $title, $code, $id > 0 ? $id : null);

            $translations[$code] = [
                'title'            => mb_substr($title, 0, 200),
                'slug'             => $slug,
                'excerpt'          => mb_substr(trim((string) ($fields['excerpt'] ?? '')), 0, 400) ?: null,
                'content'          => Security::sanitizeHtml((string) ($fields['content'] ?? '')),
                'meta_title'       => mb_substr(trim((string) ($fields['meta_title'] ?? '')), 0, 180) ?: null,
                'meta_description' => mb_substr(trim((string) ($fields['meta_description'] ?? '')), 0, 320) ?: null,
                'canonical'        => mb_substr(trim((string) ($fields['canonical'] ?? '')), 0, 255) ?: null,
                'robots'           => in_array($fields['robots'] ?? '', ['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow'], true)
                    ? (string) $fields['robots']
                    : 'index,follow',
                'schema_type'      => mb_substr(trim((string) ($fields['schema_type'] ?? '')), 0, 40) ?: null,
                'og_image_id'      => (int) ($fields['og_image_id'] ?? 0) > 0 ? (int) $fields['og_image_id'] : null,
            ];
        }

        $pageId = Page::save($page, $translations, $id > 0 ? $id : null);

        // Slug degisimlerinde otomatik 301. Bu davranis kapatilamaz.
        foreach ($translations as $code => $fields) {
            $old = $previousSlugs[$code] ?? '';
            $new = $fields['slug'] ?? '';
            if ($old !== '' && $new !== '' && $old !== $new) {
                Redirect::forSlugChange($old, $new, $code);
            }
        }

        // Sayfaya atanmis SSS kayitlari.
        $this->syncFaqs($pageId, array_map('intval', $request->arr('faqs')));

        Logger::activity($id > 0 ? 'page.update' : 'page.create', 'page', $pageId, $translations[$defaultLang]['title'] ?? '');

        return $this->back(admin_url('sayfalar/' . $pageId), 'success', 'Sayfa kaydedildi.');
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
        $this->db()->delete('pages', ['id' => $id]);
        Logger::activity('page.delete', 'page', $id);

        return $this->back(admin_url('sayfalar'), 'success', 'Sayfa silindi.');
    }

    /** Durum degistirme kisayolu (yayinla / taslaga al). */
    public function toggle(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id   = (int) ($params['id'] ?? 0);
        $page = Page::find($id);

        if ($page === null) {
            return $this->back(admin_url('sayfalar'), 'error', 'Sayfa bulunamadi.');
        }

        $next = $page['status'] === 'published' ? 'draft' : 'published';

        // Ilce sayfalari icin icerik skoru guclu uyari veriyorsa yayina
        // alinirken uyarilir; kayit engellenmez. DOCS.md 9.6, test F-20
        $warning = '';
        if ($next === 'published') {
            $translation = Page::translation($id, Lang::defaultCode()) ?? [];
            $score       = \Arcates\Core\Seo::score($page, $translation);
            foreach ($score['issues'] as $issue) {
                if ($issue['level'] === 'strong') {
                    $warning = $issue['message'];
                    break;
                }
            }
        }

        $this->db()->update('pages', ['status' => $next], ['id' => $id]);
        Logger::activity('page.status', 'page', $id, $next);

        if ($warning !== '') {
            return $this->back(admin_url('sayfalar'), 'warning', 'Sayfa yayinlandi ancak: ' . $warning);
        }

        return $this->back(admin_url('sayfalar'), 'success', $next === 'published' ? 'Sayfa yayinlandi.' : 'Sayfa taslaga alindi.');
    }

    // --- Yardimcilar --------------------------------------------------------

    private function langParam(Request $request): string
    {
        $lang = $request->str('dil');
        return Lang::exists($lang) ? $lang : Lang::defaultCode();
    }

    private function parentOptions(?int $excludeId): array
    {
        $rows = $this->db()->all(
            'SELECT p.id, t.title FROM pages p
               JOIN page_translations t ON t.page_id = p.id AND t.lang = :lang
              ORDER BY t.title',
            [':lang' => Lang::defaultCode()]
        );

        if ($excludeId === null) {
            return $rows;
        }

        return array_values(array_filter($rows, static fn (array $r): bool => (int) $r['id'] !== $excludeId));
    }

    private function syncFaqs(int $pageId, array $faqIds): void
    {
        $this->db()->transaction(static function (Database $db) use ($pageId, $faqIds): void {
            $db->run('DELETE FROM faq_page WHERE page_id = :id', [':id' => $pageId]);
            foreach (array_unique(array_filter($faqIds)) as $faqId) {
                $exists = $db->value('SELECT id FROM faqs WHERE id = :id', [':id' => $faqId]);
                if ($exists !== null) {
                    $db->insert('faq_page', ['faq_id' => $faqId, 'page_id' => $pageId]);
                }
            }
        });
    }
}
