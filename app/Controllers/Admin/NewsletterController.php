<?php
/**
 * Bulten aboneleri.
 *
 * Liste, durum suzgeci, arama ve CSV disa aktarma. Kayitlar buradan
 * duzenlenmez: abonelik ziyaretcinin kendi eylemidir, panelden birini
 * "etkin" yapmak cift onayin anlamini ortadan kaldirirdi.
 * DOCS.md 9.9, 12
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Models\Newsletter;

final class NewsletterController extends Controller
{
    protected string $section = 'newsletter';
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

        return $this->view('newsletter/index', [
            'title'    => 'Bülten',
            'rows'     => Newsletter::listing($status, $search, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'statuses' => Newsletter::STATUSES,
            'status'   => $status,
            'search'   => $search,
            'counts'   => Newsletter::counts(),
            'page'     => $page,
        ]);
    }

    public function export(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $csv = Newsletter::toCsv();
        Logger::activity('newsletter.export', 'newsletter', null, (string) Newsletter::counts()['active'] . ' etkin abone');

        return Response::csv($csv, 'arcates-bulten-' . date('Y-m-d') . '.csv');
    }

    /**
     * Kaydi tamamen siler.
     *
     * Cikis yapan abone silinmez, `unsubscribed` kalir — silme yalnizca
     * kisinin "verilerimi silin" talebi icindir. Bu yuzden yalnizca yonetici
     * yapabilir ve islem gunlugune yazilir.
     */
    public function destroy(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id = (int) ($params['id'] ?? 0);
        $this->db()->delete('newsletter_subscribers', ['id' => $id]);
        Logger::activity('newsletter.delete', 'newsletter', $id);

        return $this->back(admin_url('bulten'), 'success', 'Kayıt silindi.');
    }
}
