<?php
/**
 * Referanslar — liste ve detay.  DOCS.md 4.1, 11.2 (CreativeWork)
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Seo;
use Arcates\Models\Page;
use Arcates\Models\Project;

final class ProjectController extends Controller
{
    /** `/referanslar` */
    public function index(Request $request, array $params): Response
    {
        $lang = Lang::current();
        $page = Page::published('referanslar', $lang);

        $projects = Project::published($lang);

        $crumbs = [
            ['label' => __('home'), 'url' => url('/')],
            ['label' => $page['title'] ?? __('projects'), 'url' => url('/referanslar')],
        ];

        return $this->render('front/projects', [
            'page'     => $page,
            'projects' => $projects,
            'crumbs'   => $crumbs,
            'head'     => [
                'title'       => Seo::title($page['title'] ?? __('projects'), $page['meta_title'] ?? null),
                'description' => Seo::description(
                    $page['meta_description'] ?? null,
                    $page['excerpt'] ?? null,
                    (string) ($page['content'] ?? '')
                ),
                'canonical'   => url('/referanslar'),
                'robots'      => (string) ($page['robots'] ?? 'index,follow'),
                'hreflang'    => $page !== null ? Seo::hreflang(Page::alternates((int) $page['id'])) : [],
                'schemas'     => array_filter([Seo::breadcrumbList($crumbs)]),
            ],
        ]);
    }

    /** `/referanslar/{slug}` */
    public function show(Request $request, array $params): Response
    {
        $lang    = Lang::current();
        $slug    = trim((string) ($params['slug'] ?? ''), '/');
        $project = Project::bySlugPublished($slug, $lang);

        if ($project === null) {
            return $this->notFound($request);
        }

        $path   = '/referanslar/' . $project['slug'];
        $crumbs = [
            ['label' => __('home'), 'url' => url('/')],
            ['label' => __('projects'), 'url' => url('/referanslar')],
            ['label' => (string) $project['title'], 'url' => url($path)],
        ];

        $related = [];
        if (!empty($project['district'])) {
            $related = array_values(array_filter(
                Page::districtProjects((string) $project['district'], $lang, 4),
                static fn (array $row): bool => (int) $row['id'] !== (int) $project['id']
            ));
        }

        return $this->render('front/project', [
            'project' => $project,
            'related' => array_slice($related, 0, 3),
            'crumbs'  => $crumbs,
            'head'    => [
                'title'       => Seo::title((string) $project['title'], $project['meta_title'] ?? null),
                'description' => Seo::description(
                    $project['meta_description'] ?? null,
                    $project['excerpt'] ?? null,
                    (string) ($project['content'] ?? '')
                ),
                'canonical'   => url($path),
                'robots'      => (string) ($project['robots'] ?? 'index,follow'),
                'hreflang'    => $this->alternates((int) $project['id']),
                'schemas'     => array_filter([
                    Seo::creativeWork($project, url($path)),
                    Seo::breadcrumbList($crumbs),
                ]),
                'og_image_id' => $project['cover_id'] ?? null,
            ],
        ]);
    }

    private function alternates(int $projectId): array
    {
        $rows = $this->db()->all(
            'SELECT t.lang, t.slug
               FROM project_translations t
               JOIN projects p ON p.id = t.project_id
              WHERE t.project_id = :id AND p.status = :status',
            [':id' => $projectId, ':status' => 'published']
        );

        $slugs = [];
        foreach ($rows as $row) {
            $slugs[$row['lang']] = 'referanslar/' . $row['slug'];
        }

        return Seo::hreflang($slugs);
    }
}
