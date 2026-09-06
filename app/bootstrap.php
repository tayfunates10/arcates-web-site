<?php

declare(strict_types=1);

use Arcates\Core\Session;

spl_autoload_register(static function (string $class): void {
    $prefix = 'Arcates\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require_once __DIR__ . '/helpers.php';

$configFile = dirname(__DIR__) . '/config/config.php';
if (!is_file($configFile)) {
    $configFile = dirname(__DIR__) . '/config/config.example.php';
}
$config = require $configFile;

date_default_timezone_set((string) ($config['app']['timezone'] ?? 'Europe/Istanbul'));

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self'; base-uri 'self'; frame-ancestors 'self'; form-action 'self'");

Session::start();

return $config;
