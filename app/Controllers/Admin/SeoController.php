<?php
/**
 * SEO paneli.
 *
 * `robots.txt` duzenleyici, sitemap durumu ve yeniden uretme, varsayilan
 * meta sablonu, Search Console ve Analytics kod alani, tum sayfalarin meta
 * durumu tablosu.  DOCS.md 9.7 — testler O-01…O-08
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Controllers\Front\RobotsController;
use Arcates\Controllers\Front\SitemapController;
use Arcates\Core\Lang;
use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Seo;
use Arcates\Core\Settings;
use Arcates\Core\Validator;

final class SeoController extends Controller
{
    protected string $section = 'seo';
    protected ?string $ability = 'seo.view';

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $sitemap = new SitemapController();
        $entries = $sitemap->entries();

        return $this->view('seo/index', [
            'title'        => 'SEO',
            'robots'       => (string) Settings::get('robots_txt', ''),
            'robotsPreview' => (new RobotsController())->body(),
            'entryCount'   => count($entries),
            'metaTable'    => $this->metaTable(),
            'issueCount'   => $this->issueCount(),
            'settings'     => Settings::all(),
            'langs'        => Lang::languages(),
        ]);
    }

    public function update(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $validator = new Validator($request->allPost(), [
            'meta_title_pattern' => 'Başlık şablonu',
            'meta_description'   => 'Varsayılan açıklama',
        ]);

        $validator->max('meta_title_pattern', 120)
            ->max('meta_description', 320)
            ->max('gsc_verification', 200)
            ->max('robots_txt', 4000);

        if ($validator->fails()) {
            return $this->withErrors(admin_url('seo'), $validator->firstErrors(), $request->allPost());
        }

        Settings::setMany([
            'meta_title_pattern' => $request->str('meta_title_pattern'),
            'meta_description'   => $request->str('meta_description'),
            'gsc_verification'   => $request->str('gsc_verification'),
            // Analytics kodu ekrana basilmaz; yalnizca saklanir ve yonetici
            // tarafindan girilir. Icerik guvenlik politikasi satir ici script
            // yasakladigi icin on yuze basilmaz. DOCS.md 10.7
            'analytics_code'     => mb_substr(trim((string) $request->post('analytics_code', '')), 0, 4000),
            'robots_txt'         => mb_substr(trim((string) $request->post('robots_txt', '')), 0, 4000),
        ]);

        Logger::activity('seo.update', 'settings', null, 'SEO ayarları');

        return $this->back(admin_url('seo'), 'success', 'SEO ayarları kaydedildi.');
    }

    /** Sitemap onizlemesi; uretim dinamiktir, onbellek tutulmaz. */
    public function sitemap(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        return Response::xml((new SitemapController())->build());
    }

    /**
     * Tum sayfalarin meta durumu.  DOCS.md 9.7
     *
     * @return array<int, array<string, mixed>>
     */
    private function metaTable(): array
    {
        $rows = $this->db()->all(
            'SELECT p.id, p.type, p.status, p.district,
                    t.lang, t.title, t.slug, t.meta_title, t.meta_description,
                    t.robots, t.word_count, t.content
               FROM pages p
               JOIN page_translations t ON t.page_id = p.id
              ORDER BY p.type, t.lang, t.slug'
        );

        $out = [];
        foreach ($rows as $row) {
            $score = Seo::score(
                ['id' => (int) $row['id'], 'type' => $row['type'], 'district' => $row['district']],
                $row
            );

            $out[] = [
                'id'          => (int) $row['id'],
                'type'        => (string) $row['type'],
                'status'      => (string) $row['status'],
                'lang'        => (string) $row['lang'],
                'title'       => (string) $row['title'],
                'slug'        => (string) $row['slug'],
                'meta_title'  => (string) ($row['meta_title'] ?? ''),
                'meta_length' => mb_strlen((string) ($row['meta_title'] ?? '')),
                'desc_length' => mb_strlen((string) ($row['meta_description'] ?? '')),
                'robots'      => (string) $row['robots'],
                'words'       => (int) $row['word_count'],
                'score'       => $score['score'],
                'issues'      => $score['issues'],
            ];
        }

        return $out;
    }

    /** Guclu uyari tasiyan sayfa sayisi. */
    private function issueCount(): int
    {
        $count = 0;
        foreach ($this->metaTable() as $row) {
            foreach ($row['issues'] as $issue) {
                if ($issue['level'] === 'strong') {
                    $count++;
                    break;
                }
            }
        }
        return $count;
    }
}
