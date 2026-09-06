<?php

declare(strict_types=1);

namespace Arcates\Core;

final class Request
{
    public function method(): string { return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'); }
    public function path(): string { $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH); $path = '/' . ltrim((string) $path, '/'); return $path === '//' ? '/' : $path; }
    public function input(string $key, mixed $default = null): mixed { return $_POST[$key] ?? $_GET[$key] ?? $default; }
    public function post(): array { return $_POST; }
    public function ip(): string { return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'; }
    public function userAgent(): string { return mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255); }
    public function referrer(): string { return mb_substr((string) ($_SERVER['HTTP_REFERER'] ?? ''), 0, 255); }
}
