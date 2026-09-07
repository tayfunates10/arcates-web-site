<?php
/**
 * Ziyaret kaydi ve istatistik.
 *
 * `user_agent` bot imzasi tasiyorsa `device='bot'` isaretlenir ve grafiklere
 * girmez. Ham kayitlar 90 gun sonra `visits_daily` tablosuna toplanir.
 * DOCS.md 8.4, 9.10 — testler U-15, F-P11
 */

declare(strict_types=1);

namespace Arcates\Core;

use Throwable;

final class Visits
{
    /** Kaydedilmeyecek yollar. */
    private const SKIP_PREFIXES = ['/panel', '/install', '/assets', '/uploads', '/sitemap.xml', '/robots.txt'];

    /**
     * Ziyareti kaydeder.
     *
     * Kayit hicbir zaman istegi engellemez; hata halinde sessizce gunluge
     * duser.
     */
    public static function record(Request $request, string $lang): void
    {
        $path = $request->path();

        foreach (self::SKIP_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return;
            }
        }

        // Panel oturumu acik olan kendi ziyaretini saymaz.
        if (Auth::check()) {
            return;
        }

        try {
            Database::instance()->insert('visits', [
                'path'         => mb_substr($path, 0, 255),
                // Ziyaretciyi kimliklendirmeden ayirt eden karma.
                'session_hash' => Security::sessionHash(
                    $request->ip(),
                    $request->userAgent(),
                    (string) Config::get('app.base_url', '')
                ),
                'referrer'     => $request->referrer(),
                // Bot imzasi tasiyan istekler isaretlenir ve grafiklere girmez.
                'device'       => $request->device(),
                'lang'         => $lang,
            ]);
        } catch (Throwable $e) {
            Logger::exception($e);
        }
    }

    /**
     * Gunluk seri. Ham kayitlar ve toplanmis kayitlar birlestirilir.
     *
     * @return array<int, array{day:string, views:int, sessions:int}>
     */
    public static function series(int $days = 30): array
    {
        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day          = date('Y-m-d', strtotime("-{$i} days"));
            $series[$day] = ['day' => $day, 'views' => 0, 'sessions' => 0];
        }

        $since = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));

        try {
            foreach (Database::instance()->all(
                'SELECT DATE(created_at) AS day, COUNT(*) AS views, COUNT(DISTINCT session_hash) AS sessions
                   FROM visits
                  WHERE created_at >= :since AND device <> :bot
                  GROUP BY DATE(created_at)',
                [':since' => $since . ' 00:00:00', ':bot' => 'bot']
            ) as $row) {
                $day = (string) $row['day'];
                if (isset($series[$day])) {
                    $series[$day]['views']    = (int) $row['views'];
                    $series[$day]['sessions'] = (int) $row['sessions'];
                }
            }

            foreach (Database::instance()->all(
                'SELECT day, SUM(views) AS views, SUM(sessions) AS sessions
                   FROM visits_daily WHERE day >= :since GROUP BY day',
                [':since' => $since]
            ) as $row) {
                $day = (string) $row['day'];
                if (isset($series[$day]) && $series[$day]['views'] === 0) {
                    $series[$day]['views']    = (int) $row['views'];
                    $series[$day]['sessions'] = (int) $row['sessions'];
                }
            }
        } catch (Throwable $e) {
            Logger::exception($e);
        }

        return array_values($series);
    }

    /** En cok girilen sayfalar. */
    public static function topPaths(int $days = 30, int $limit = 20): array
    {
        try {
            return Database::instance()->all(
                'SELECT path, COUNT(*) AS views, COUNT(DISTINCT session_hash) AS sessions
                   FROM visits
                  WHERE created_at >= :since AND device <> :bot
                  GROUP BY path ORDER BY views DESC LIMIT ' . max(1, min(100, $limit)),
                [':since' => date('Y-m-d H:i:s', strtotime("-{$days} days")), ':bot' => 'bot']
            );
        } catch (Throwable) {
            return [];
        }
    }

    /** Referans kaynaklari. */
    public static function referrers(int $days = 30, int $limit = 15): array
    {
        try {
            $rows = Database::instance()->all(
                'SELECT referrer FROM visits
                  WHERE created_at >= :since AND device <> :bot AND referrer IS NOT NULL AND referrer <> :empty',
                [':since' => date('Y-m-d H:i:s', strtotime("-{$days} days")), ':bot' => 'bot', ':empty' => '']
            );
        } catch (Throwable) {
            return [];
        }

        $hosts = [];
        foreach ($rows as $row) {
            $host = parse_url((string) $row['referrer'], PHP_URL_HOST);
            $host = is_string($host) && $host !== '' ? $host : 'bilinmiyor';
            $hosts[$host] = ($hosts[$host] ?? 0) + 1;
        }

        arsort($hosts);

        $out = [];
        foreach (array_slice($hosts, 0, $limit, true) as $host => $count) {
            $out[] = ['host' => (string) $host, 'count' => $count];
        }

        return $out;
    }

    /** Cihaz ve dil dagilimi. DOCS.md 9.10 */
    public static function breakdown(string $column, int $days = 30): array
    {
        $column = Database::identifier($column);

        try {
            return Database::instance()->all(
                'SELECT `' . $column . '` AS label, COUNT(*) AS total
                   FROM visits
                  WHERE created_at >= :since AND device <> :bot
                  GROUP BY `' . $column . '` ORDER BY total DESC',
                [':since' => date('Y-m-d H:i:s', strtotime("-{$days} days")), ':bot' => 'bot']
            );
        } catch (Throwable) {
            return [];
        }
    }

    /** Bot istekleri; grafiklere girmez ama sayilir. */
    public static function botCount(int $days = 30): int
    {
        try {
            return (int) Database::instance()->value(
                'SELECT COUNT(*) FROM visits WHERE created_at >= :since AND device = :bot',
                [':since' => date('Y-m-d H:i:s', strtotime("-{$days} days")), ':bot' => 'bot']
            );
        } catch (Throwable) {
            return 0;
        }
    }

    /**
     * Eski ham kayitlari `visits_daily`'ye toplar ve siler.
     * DOCS.md 8.4 — gunluk cron
     *
     * @return array{rolled:int, deleted:int}
     */
    public static function rollup(?int $days = null): array
    {
        $days   = $days ?? (int) Config::get('privacy.visit_retention_days', 90);
        $cutoff = date('Y-m-d', strtotime("-{$days} days"));
        $db     = Database::instance();

        $rows = $db->all(
            'SELECT DATE(created_at) AS day, path, COUNT(*) AS views, COUNT(DISTINCT session_hash) AS sessions
               FROM visits
              WHERE created_at < :cutoff AND device <> :bot
              GROUP BY DATE(created_at), path',
            [':cutoff' => $cutoff . ' 00:00:00', ':bot' => 'bot']
        );

        foreach ($rows as $row) {
            $db->upsert(
                'visits_daily',
                [
                    'day'      => (string) $row['day'],
                    'path'     => (string) $row['path'],
                    'views'    => (int) $row['views'],
                    'sessions' => (int) $row['sessions'],
                ],
                ['views', 'sessions']
            );
        }

        $deleted = $db->run(
            'DELETE FROM visits WHERE created_at < :cutoff',
            [':cutoff' => $cutoff . ' 00:00:00']
        )->rowCount();

        return ['rolled' => count($rows), 'deleted' => $deleted];
    }

    /** Aylik CSV rapor. DOCS.md 9.10 */
    public static function monthlyCsv(string $month): string
    {
        $start = $month . '-01 00:00:00';
        $end   = date('Y-m-t 23:59:59', strtotime($start));

        $rows = Database::instance()->all(
            'SELECT DATE(created_at) AS day, path, device, lang,
                    COUNT(*) AS views, COUNT(DISTINCT session_hash) AS sessions
               FROM visits
              WHERE created_at BETWEEN :start AND :end AND device <> :bot
              GROUP BY DATE(created_at), path, device, lang
              ORDER BY day, views DESC',
            [':start' => $start, ':end' => $end, ':bot' => 'bot']
        );

        $handle = fopen('php://temp', 'r+');
        if ($handle === false) {
            return '';
        }

        fputcsv($handle, ['Gun', 'Adres', 'Cihaz', 'Dil', 'Goruntuleme', 'Oturum']);
        foreach ($rows as $row) {
            fputcsv($handle, [
                $row['day'], $row['path'], $row['device'], $row['lang'],
                $row['views'], $row['sessions'],
            ]);
        }

        rewind($handle);
        $csv = (string) stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
