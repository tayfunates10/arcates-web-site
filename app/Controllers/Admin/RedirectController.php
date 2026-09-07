<?php
/**
 * Yonlendirme ve 404 yonetimi.
 *
 * Yonlendirme listesi ve elle ekleme, dongu kontrolu, 404 listesi ve tek
 * tikla yonlendirmeye donusturme. DOCS.md 9.8 — testler F-11, F-12, S-18.
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Validator;
use Arcates\Models\Redirect;

final class RedirectController extends Controller
{
    protected string $section = 'redirects';
    protected ?string $ability = 'redirects.manage';

    private const PER_PAGE = 50;

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $search = $request->str('ara');
        return $this->view('redirects/index', [
            'title'     => 'Yönlendirmeler ve 404',
            'redirects' => Redirect::listing($search),
            'notFound'  => $this->notFoundList(),
            'search'    => $search,
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

        $validator = new Validator($request->allPost(), [
            'from_path' => 'Kaynak adres',
            'to_path'   => 'Hedef adres',
        ]);
        $validator->required('from_path')->max('from_path', 255)
            ->required('to_path')->max('to_path', 255)
            ->in('code', ['301', '302', '307', '308']);

        if ($validator->fails()) {
            return $this->withErrors(admin_url('yonlendirmeler'), $validator->firstErrors(), $request->allPost());
        }

        $id = (int) ($params['id'] ?? 0);
        $result = Redirect::put(
            $request->str('from_path'),
            $request->str('to_path'),
            (int) ($request->str('code') ?: 301),
            $id > 0 ? $id : null
        );

        if (!$result['ok']) {
            return $this->back(admin_url('yonlendirmeler'), 'error', $result['message']);
        }

        Logger::activity('redirect.save', 'redirect', $id > 0 ? $id : null, $request->str('from_path') . ' → ' . $request->str('to_path'));
        return $this->back(admin_url('yonlendirmeler'), 'success', $result['message']);
    }

    public function destroy(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id  = (int) ($params['id'] ?? 0);
        $row = Redirect::find($id);
        if ($row === null) {
            return $this->back(admin_url('yonlendirmeler'), 'error', 'Yönlendirme bulunamadı.');
        }
        $this->db()->delete('redirects', ['id' => $id]);
        Logger::activity('redirect.delete', 'redirect', $id, (string) $row['from_path']);
        return $this->back(admin_url('yonlendirmeler'), 'success', 'Yönlendirme silindi.');
    }

    public function convert(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id  = (int) ($params['id'] ?? 0);
        $row = $this->db()->first('SELECT * FROM not_found WHERE id = :id', [':id' => $id]);
        if ($row === null) {
            return $this->back(admin_url('yonlendirmeler'), 'error', 'Kayıt bulunamadı.');
        }

        $target = $request->str('to_path');
        if ($target === '') {
            return $this->back(admin_url('yonlendirmeler'), 'error', 'Hedef adres boş olamaz.');
        }

        $result = Redirect::put((string) $row['path'], $target, 301);
        if (!$result['ok']) {
            return $this->back(admin_url('yonlendirmeler'), 'error', $result['message']);
        }

        $this->db()->delete('not_found', ['id' => $id]);
        Logger::activity('redirect.convert', 'redirect', null, $row['path'] . ' → ' . $target);
        return $this->back(admin_url('yonlendirmeler'), 'success', $row['path'] . ' adresi ' . $target . ' hedefine yönlendirildi.');
    }

    public function forget(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }
        $this->db()->delete('not_found', ['id' => (int) ($params['id'] ?? 0)]);
        return $this->back(admin_url('yonlendirmeler'), 'success', 'Kayıt listeden kaldırıldı.');
    }

    private function notFoundList(): array
    {
        return $this->db()->all('SELECT * FROM not_found ORDER BY hits DESC, last_seen DESC LIMIT ' . self::PER_PAGE);
    }
}
