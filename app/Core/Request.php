<?php
/**
 * Gelen HTTP istegi.
 *
 * Ham diziler ($_GET, $_POST, $_SERVER) dogrudan kullanilmaz; her erisim bu
 * sinif uzerinden gecer.  DOCS.md 10.1, 10.2
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Request
{
    private function __construct(
        private string $method,
        private string $path,
        private array $query,
        private array $post,
        private array $files,
        private array $server,
        private array $cookies
    ) {
    }

    /** Kuresel degiskenlerden istek uretir. */
    public static function capture(): self
    {
        $uri  = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);
        $path = is_string($path) ? $path : '/';
        $path = '/' . trim(rawurldecode($path), '/');

        return new self(
            strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')),
            $path === '/' ? '/' : rtrim($path, '/'),
            $_GET,
            $_POST,
            $_FILES,
            $_SERVER,
            $_COOKIE
        );
    }

    /** Testler icin elle istek uretir. */
    public static function make(string $method, string $path, array $post = [], array $query = [], array $server = []): self
    {
        $path = '/' . trim($path, '/');
        return new self(
            strtoupper($method),
            $path === '/' ? '/' : rtrim($path, '/'),
            $query,
            $post,
            [],
            $server,
            []
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /** Sorgu dizesi olmadan yol. Daima `/` ile baslar. */
    public function path(): string
    {
        return $this->path;
    }

    /** Yol parcalari. */
    public function segments(): array
    {
        return array_values(array_filter(explode('/', $this->path), static fn ($s) => $s !== ''));
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /** Kirpilmis dize olarak POST degeri. */
    public function str(string $key, string $default = ''): string
    {
        $value = $this->post[$key] ?? $this->query[$key] ?? $default;
        return is_scalar($value) ? trim((string) $value) : $default;
    }

    public function int(string $key, int $default = 0): int
    {
        $value = $this->post[$key] ?? $this->query[$key] ?? null;
        return is_numeric($value) ? (int) $value : $default;
    }

    public function bool(string $key): bool
    {
        $value = $this->post[$key] ?? $this->query[$key] ?? null;
        return in_array($value, ['1', 1, true, 'true', 'on', 'evet'], true);
    }

    public function arr(string $key): array
    {
        $value = $this->post[$key] ?? $this->query[$key] ?? [];
        return is_array($value) ? $value : [];
    }

    public function allPost(): array
    {
        return $this->post;
    }

    public function allQuery(): array
    {
        return $this->query;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;
        return is_array($file) ? $file : null;
    }

    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }

    /** Ziyaretci IP adresi. Vekil basliklarina guvenilmez. */
    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public function userAgent(): string
    {
        return substr((string) ($this->server['HTTP_USER_AGENT'] ?? ''), 0, 255);
    }

    public function referrer(): ?string
    {
        $ref = $this->server['HTTP_REFERER'] ?? null;
        return is_string($ref) && $ref !== '' ? substr($ref, 0, 255) : null;
    }

    public function isSecure(): bool
    {
        $https = $this->server['HTTPS'] ?? '';
        return $https !== '' && strtolower((string) $https) !== 'off';
    }

    public function host(): string
    {
        return (string) ($this->server['HTTP_HOST'] ?? parse_url((string) Config::get('app.base_url', ''), PHP_URL_HOST) ?? 'localhost');
    }

    /** Tam istek adresi. */
    public function fullUrl(): string
    {
        $base  = rtrim((string) Config::get('app.base_url', ''), '/');
        $query = $this->query ? '?' . http_build_query($this->query) : '';
        return $base . $this->path . $query;
    }

    public function isAjax(): bool
    {
        return strtolower((string) ($this->server['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
    }

    /**
     * Cihaz turu. Bot imzasi tasiyan istekler istatistige girmez.
     * DOCS.md 9.10, test U-15
     */
    public function device(): string
    {
        $ua = strtolower($this->userAgent());
        if ($ua === '') {
            return 'bot';
        }
        if (preg_match('/(bot|crawler|spider|crawl|slurp|mediapartners|facebookexternalhit|preview|lighthouse|pingdom|monitor|headless|curl|wget|python-requests|semrush|ahrefs|dotbot|petalbot)/i', $ua) === 1) {
            return 'bot';
        }
        if (preg_match('/(ipad|tablet|kindle|silk|playbook)/i', $ua) === 1) {
            return 'tablet';
        }
        if (preg_match('/(mobile|iphone|ipod|android.*mobile|windows phone|blackberry|opera mini)/i', $ua) === 1) {
            return 'mobile';
        }
        return 'desktop';
    }

    /** UTM parametreleri. DOCS.md 12 */
    public function utm(): array
    {
        $out = [];
        foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'] as $key) {
            $value = $this->query[$key] ?? null;
            if (is_string($value) && $value !== '') {
                $out[$key] = substr($value, 0, 120);
            }
        }
        return $out;
    }
}
