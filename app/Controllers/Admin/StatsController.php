<?php
/**
 * Istatistik ekrani.
 *
 * Gunluk ziyaretci ve goruntuleme, en cok girilen sayfalar, referans
 * kaynaklari, cihaz ve dil dagilimi, aylik CSV rapor.
 * `user_agent` bot imzasi tasiyorsa `device='bot'` isaretlenir ve grafiklere
 * girmez.  DOCS.md 9.10
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Visits;

final class StatsController extends Controller
{
    protected string $section = 'stats';
    protected ?string $ability = 'stats.view';

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $days = max(7, min(365, $request->int('gun', 30)));

        return $this->view('stats/index', [
            'title'      => 'İstatistik',
            'days'       => $days,
            'series'     => Visits::series($days),
            'topPaths'   => Visits::topPaths($days),
            'referrers'  => Visits::referrers($days),
            'devices'    => Visits::breakdown('device', $days),
            'languages'  => Visits::breakdown('lang', $days),
            'bots'       => Visits::botCount($days),
            'month'      => date('Y-m'),
        ]);
    }

    /** Aylik CSV rapor. DOCS.md 9.10 */
    public function export(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $month = $request->str('ay');
        if (preg_match('/^\d{4}-\d{2}$/', $month) !== 1) {
            $month = date('Y-m');
        }

        return Response::csv(
            Visits::monthlyCsv($month),
            'arcates-istatistik-' . $month . '.csv'
        );
    }
}
