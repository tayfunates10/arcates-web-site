<?php
/**
 * Pano.
 *
 * Son 30 gun ziyaretci grafigi, yeni form sayisi, donusum hunisi,
 * en cok ziyaret edilen 10 sayfa, son 404 kayitlari, taslak icerik sayisi,
 * sistem durumu.  DOCS.md 9.1
 */
declare(strict_types=1);
namespace Arcates\Controllers\Admin;
use Arcates\Core\Auth;
use Arcates\Core\ContentSeeder;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Throwable;
final class DashboardController extends Controller
{
    protected string $section = 'dashboard';
    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        return $this->view('dashboard', [
            'title'    => 'Pano',
            'visits'   => $this->visitSeries(),
            'funnel'   => $this->funnel(),
            'topPages' => $this->topPages(),
            'notFound' => $this->recentNotFound(),
            'drafts'   => $this->draftCounts(),
            'system'   => $this->systemState(),
            'newForms' => $this->newSubmissionCount(),
            'contentRepair' => $this->contentRepairState(),
        ]);
    }

    /** Eksik kurulum icerigini, mevcut kayitlara dokunmadan tamamlar. */
    public function seedContent(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        try {
            $summary = (new ContentSeeder($this->db()))->runMissing();
            $added = $summary['pages'] + $summary['projects'] + $summary['posts'] + $summary['faqs'];
            $message = $added > 0
                ? "Başlangıç içeriği tamamlandı: {$summary['pages']} sayfa, {$summary['projects']} örnek site, {$summary['posts']} blog yazısı, {$summary['faqs']} SSS eklendi."
                : 'Başlangıç içeriği zaten tam; mevcut kayıtlara dokunulmadı.';
            return $this->back(admin_url(), 'success', $message);
        } catch (Throwable $e) {
            return $this->back(admin_url(), 'error', 'İçerik tamamlanamadı: ' . $e->getMessage());
        }
    }

    private function contentRepairState(): array
    {
        try {
            return [
                'missing' => (new ContentSeeder($this->db()))->missingCoreCount(),
                'can_run' => Auth::isAdmin(),
            ];
        } catch (Throwable) {
            return ['missing' => 0, 'can_run' => false];
        }
    }

    /** Son 30 gunun gunluk ziyaret ve oturum sayisi. Botlar disaridadir. */
    private function visitSeries(): array
    {
        $series = [];
        for ($i = 29; $i >= 0; $i--) {
            $day          = date('Y-m-d', strtotime("-{$i} days"));
            $series[$day] = ['day' => $day, 'views' => 0, 'sessions' => 0];
        }

        try {
            $rows = $this->db()->all(
                'SELECT DATE(created_at) AS day, COUNT(*) AS views, COUNT(DISTINCT session_hash) AS sessions
                   FROM visits
                  WHERE created_at >= :since AND device <> :bot
                  GROUP BY DATE(created_at)',
                [':since' => date('Y-m-d 00:00:00', strtotime('-29 days')), ':bot' => 'bot']
            );

            foreach ($rows as $row) {
                $day = (string) $row['day'];
                if (isset($series[$day])) {
                    $series[$day]['views']    = (int) $row['views'];
                    $series[$day]['sessions'] = (int) $row['sessions'];
                }
            }

            $daily = $this->db()->all(
                'SELECT day, SUM(views) AS views, SUM(sessions) AS sessions
                   FROM visits_daily WHERE day >= :since GROUP BY day',
                [':since' => date('Y-m-d', strtotime('-29 days'))]
            );
            foreach ($daily as $row) {
                $day = (string) $row['day'];
                if (isset($series[$day]) && $series[$day]['views'] === 0) {
                    $series[$day]['views']    = (int) $row['views'];
                    $series[$day]['sessions'] = (int) $row['sessions'];
                }
            }
        } catch (Throwable) {
        }

        return array_values($series);
    }

    private function funnel(): array
    {
        $labels = [
            'new'       => 'Yeni',
            'contacted' => 'Arandı',
            'quoted'    => 'Teklif',
            'won'       => 'Kazanıldı',
            'lost'      => 'Kaybedildi',
        ];
        $counts = array_fill_keys(array_keys($labels), 0);
        try {
            foreach ($this->db()->all('SELECT status, COUNT(*) AS total FROM submissions GROUP BY status') as $row) {
                $counts[$row['status']] = (int) $row['total'];
            }
        } catch (Throwable) {
        }
        $out = [];
        foreach ($labels as $key => $label) {
            $out[] = ['key' => $key, 'label' => $label, 'count' => $counts[$key]];
        }
        return $out;
    }

    private function newSubmissionCount(): int
    {
        try {
            return (int) $this->db()->value(
                'SELECT COUNT(*) FROM submissions WHERE status = :status AND created_at >= :since',
                [':status' => 'new', ':since' => date('Y-m-d H:i:s', strtotime('-30 days'))]
            );
        } catch (Throwable) {
            return 0;
        }
    }

    private function topPages(): array
    {
        try {
            return $this->db()->all(
                'SELECT path, COUNT(*) AS views
                   FROM visits
                  WHERE created_at >= :since AND device <> :bot
                  GROUP BY path
                  ORDER BY views DESC
                  LIMIT 10',
                [':since' => date('Y-m-d H:i:s', strtotime('-30 days')), ':bot' => 'bot']
            );
        } catch (Throwable) {
            return [];
        }
    }

    private function recentNotFound(): array
    {
        try {
            return $this->db()->all('SELECT path, hits, last_seen FROM not_found ORDER BY last_seen DESC LIMIT 10');
        } catch (Throwable) {
            return [];
        }
    }

    private function draftCounts(): array
    {
        $out = ['pages' => 0, 'projects' => 0, 'posts' => 0];
        try {
            $out['pages']    = (int) $this->db()->count('pages', ['status' => 'draft']);
            $out['projects'] = (int) $this->db()->count('projects', ['status' => 'draft']);
            $out['posts']    = (int) $this->db()->count('posts', ['status' => 'draft']);
        } catch (Throwable) {
        }
        return $out;
    }

    private function systemState(): array
    {
        $backupDir  = ARC_ROOT . '/storage/backups';
        $lastBackup = null;
        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*.sql.gz') ?: [];
            if ($files) {
                usort($files, static fn ($a, $b) => filemtime($b) <=> filemtime($a));
                $lastBackup = date('Y-m-d H:i', (int) filemtime($files[0]));
            }
        }
        $free  = @disk_free_space(ARC_ROOT);
        $total = @disk_total_space(ARC_ROOT);
        return [
            'php'          => PHP_VERSION,
            'disk_free'    => is_float($free) ? (int) $free : null,
            'disk_total'   => is_float($total) ? (int) $total : null,
            'last_backup'  => $lastBackup,
            'uploads_ok'   => is_writable(ARC_ROOT . '/public/uploads'),
            'storage_ok'   => is_writable(ARC_ROOT . '/storage'),
            'debug_on'     => (bool) config('app.debug', false),
            'install_open' => !is_file(ARC_ROOT . '/storage/installed.lock'),
        ];
    }
}
