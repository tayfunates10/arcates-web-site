<?php
/**
 * Referans yonetimi.
 *
 * Musteri adi, sektor, ilce, canli site linki, gorseller, yapilan isler.
 * DOCS.md 9.4
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Lang;
use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Security;
use Arcates\Core\Validator;
use Arcates\Models\Project;

final class ProjectController extends Controller
{
    protected string $section = 'projects';
    protected ?string $ability = 'projects.edit';

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        return $this->view('projects/index', [
            'title'    => 'Örnek siteler',
            'projects' => Project::listing(Lang::defaultCode(), $request->str('ara')),
            'search'   => $request->str('ara'),
        ]);
    }

    public function create(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        return $this->view('projects/form', $this->formData(null));
    }

    public function edit(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $project = Project::find((int) ($params['id'] ?? 0));
        if ($project === null) {
            return $this->back(admin_url('referanslar'), 'error', 'Örnek site kaydı bulunamadı.');
        }

        return $this->view('projects/form', $this->formData($project));
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
        $url = $id > 0 ? admin_url('referanslar/' . $id) : admin_url('referanslar/yeni');

        $default = Lang::defaultCode();
        $input   = $request->arr('t');

        $validator = new Validator([
            'client_name' => $request->str('client_name'),
            'title'       => trim((string) ($input[$default]['title'] ?? '')),
            'live_url'    => $request->str('live_url'),
        ], [
            'client_name' => 'Müşteri adı',
            'title'       => 'Varsayılan dildeki başlık',
        ]);

        $validator->required('client_name')->max('client_name', 150)
            ->required('title')->max('title', 200);

        if ($request->str('live_url') !== '') {
            $validator->url('live_url');
        }

        if ($validator->fails()) {
            return $this->withErrors($url, $validator->firstErrors(), $request->allPost());
        }

        $project = [
            'client_name' => $request->str('client_name'),
            'sector'      => $request->str('sector') ?: null,
            'district'    => $request->str('district') ?: null,
            'live_url'    => $request->str('live_url') ?: null,
            'cover_id'    => $request->int('cover_id') > 0 ? $request->int('cover_id') : null,
            'status'      => $request->str('status') === 'published' ? 'published' : 'draft',
            'sort'        => $request->int('sort'),
        ];

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
                'slug'             => Project::uniqueSlug($slug !== '' ? $slug : $title, $code, $id > 0 ? $id : null),
                'excerpt'          => mb_substr(trim((string) ($fields['excerpt'] ?? '')), 0, 400) ?: null,
                'content'          => Security::sanitizeHtml((string) ($fields['content'] ?? '')),
                'meta_title'       => mb_substr(trim((string) ($fields['meta_title'] ?? '')), 0, 180) ?: null,
                'meta_description' => mb_substr(trim((string) ($fields['meta_description'] ?? '')), 0, 320) ?: null,
                'robots'           => in_array($fields['robots'] ?? '', ['index,follow', 'noindex,follow'], true)
                    ? (string) $fields['robots']
                    : 'index,follow',
            ];
        }

        $gallery   = array_map('intval', $request->arr('gallery'));
        $projectId = Project::save($project, $translations, $gallery, $id > 0 ? $id : null);

        Logger::activity($id > 0 ? 'project.update' : 'project.create', 'project', $projectId, $project['client_name']);

        return $this->back(admin_url('referanslar/' . $projectId), 'success', 'Örnek site kaydedildi.');
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
        $this->db()->delete('projects', ['id' => $id]);
        Logger::activity('project.delete', 'project', $id);

        return $this->back(admin_url('referanslar'), 'success', 'Örnek site silindi.');
    }

    private function formData(?array $project): array
    {
        $translations = [];
        $gallery      = [];

        if ($project !== null) {
            foreach ($this->db()->all(
                'SELECT * FROM project_translations WHERE project_id = :id',
                [':id' => (int) $project['id']]
            ) as $row) {
                $translations[$row['lang']] = $row;
            }

            $gallery = $this->db()->column(
                'SELECT media_id FROM project_media WHERE project_id = :id ORDER BY sort',
                [':id' => (int) $project['id']]
            );
        }

        return [
            'title'        => $project === null ? 'Yeni örnek site' : 'Örnek siteyi düzenle',
            'project'      => $project,
            'translations' => $translations,
            'gallery'      => array_map('intval', $gallery),
            'langs'        => Lang::languages(),
            'media'        => $this->db()->all('SELECT id, filename, path FROM media ORDER BY created_at DESC LIMIT 200'),
            'districts'    => $this->db()->column('SELECT DISTINCT name FROM districts ORDER BY name'),
        ];
    }
}
