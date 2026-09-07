<?php
/**
 * `/robots.txt` ciktisi.
 *
 * Icerik panelden duzenlenebilir; bos birakilirsa guvenli varsayilan uretilir.
 * Sitemap satiri her zaman bulunur, panel yolu engellenir.
 * DOCS.md 9.7, test O-05
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\Config;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Settings;

final class RobotsController
{
    public function index(Request $request, array $params): Response
    {
        return Response::text($this->body());
    }

    /** Panelde saklanan metin yoksa varsayilani uretir. */
    public function body(): string
    {
        $custom = (string) Settings::get('robots_txt', '');
        $custom = trim($custom);

        if ($custom !== '') {
            // Sitemap satiri her zaman bulunmalidir. Test O-05
            if (!preg_match('/^\s*Sitemap:/mi', $custom)) {
                $custom .= "\n\nSitemap: " . path_url('/sitemap.xml');
            }
            return $custom . "\n";
        }

        return $this->defaultBody();
    }

    public function defaultBody(): string
    {
        $adminPath = '/' . trim((string) Config::get('app.admin_path', 'panel'), '/') . '/';

        $lines = [
            'User-agent: *',
            'Disallow: ' . $adminPath,
            'Disallow: /install',
            'Disallow: /tesekkurler',
            'Disallow: /uploads/',
            'Allow: /',
            '',
            'Sitemap: ' . path_url('/sitemap.xml'),
        ];

        return implode("\n", $lines) . "\n";
    }
}
