<?php
/**
 * Form kayitlari.
 *
 * Kayit listesi, durum etiketleri, not alani, kaynak sayfa ve referrer,
 * CSV disa aktarma, saklama suresi dolan kayitlari toplu silme.
 * DOCS.md 9.9, 10.9 — testler F-08, F-09
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Settings;
use Arcates\Models\Submission;

final class SubmissionController extends Controller
{
    protected string $section = 'submissions';
    protected ?string $ability = 'submissions.manage';

    private const PER_PAGE = 50;

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $status = $request->str('durum');
        $search = $request->str('ara');
        $page   = max(1, $request->int('sayfa', 1));
        $total  = Submission::countBy($status, $search);

        return $this->view('submissions/index', [
            'title'      => 'Formlar',
            'rows'       => Submission::listing($status, $search, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'statuses'   => Submission::STATUSES,
            'status'     => $status,
            'search'     => $search,
            'total'      => $total,
            'page'       => $page,
            'pages'      => (int) ceil($total / self::PER_PAGE),
            'conversion' => Submission::conversionReport(),
            'sources'    => Submission::sourceReport(),
            'retention'  => (int) Settings::getInt('submission_days', (int) config('privacy.submission_retention_days', 730)),
        ]);
    }

    public function show(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        $row = Submission::get((int) ($params['id'] ?? 0));
        if ($row === null) {
            return $this->back(admin_url('formlar'), 'error', 'Kayıt bulunamadı.');
        }
        return $this->view('submissions/show', [
            'title'    => 'Form kaydı #' . $row['id'],
            'row'      => $row,
            'statuses' => Submission::STATUSES,
        ]);
    }

    public function update(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id  = (int) ($params['id'] ?? 0);
        $row = Submission::get($id);
        if ($row === null) {
            return $this->back(admin_url('formlar'), 'error', 'Kayıt bulunamadı.');
        }

        $status = $request->str('status');
        if (!isset(Submission::STATUSES[$status])) {
            $status = (string) $row['status'];
        }

        $this->db()->update('submissions', [
            'status' => $status,
            'note'   => mb_substr(trim((string) $request->post('note', '')), 0, 4000) ?: null,
        ], ['id' => $id]);

        Logger::activity('submission.update', 'submission', $id, $status);
        return $this->back(admin_url('formlar/' . $id), 'success', 'Kayıt güncellendi.');
    }

    public function destroy(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }
        $id = (int) ($params['id'] ?? 0);
        $this->db()->delete('submissions', ['id' => $id]);
        Logger::activity('submission.delete', 'submission', $id);
        return $this->back(admin_url('formlar'), 'success', 'Kayıt silindi.');
    }

    public function export(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        $rows = Submission::listing($request->str('durum'), $request->str('ara'), 500);
        Logger::activity('submission.export', 'submission', null, count($rows) . ' kayıt');
        return Response::csv(Submission::toCsv($rows), 'arcates-formlar-' . date('Y-m-d') . '.csv');
    }

    public function purge(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }
        $days = max(30, min(3650, $request->int('days', 730)));
        Settings::set('submission_days', (string) $days);
        $deleted = Submission::purgeExpired($days);
        Logger::activity('submission.purge', 'submission', null, $deleted . ' kayıt, ' . $days . ' gün');
        return $this->back(admin_url('formlar'), 'success', $deleted . ' kayıt silindi. Saklama süresi ' . $days . ' gün olarak kaydedildi.');
    }
}
