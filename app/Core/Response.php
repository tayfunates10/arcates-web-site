<?php

declare(strict_types=1);

namespace Arcates\Core;

final class Response
{
    public static function status(int $code): void { http_response_code($code); }
    public static function redirect(string $url, int $code = 302): never { header('Location: ' . $url, true, $code); exit; }
    public static function text(string $body, int $code = 200): void { http_response_code($code); header('Content-Type: text/plain; charset=UTF-8'); echo $body; }
    public static function html(string $body, int $code = 200): void { http_response_code($code); header('Content-Type: text/html; charset=UTF-8'); echo $body; }
}
