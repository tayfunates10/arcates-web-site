<?php
/**
 * Arcates Web Site — otomatik yukleyici
 *
 * Composer yoktur. PSR-4 benzeri basit bir esleme kullanilir:
 *   Arcates\Core\Database   -> app/Core/Database.php
 *   Arcates\Models\Page     -> app/Models/Page.php
 *   Arcates\Controllers\... -> app/Controllers/...
 *
 * DOCS.md 1 (bagimlilik yok), 3 (klasor yapisi)
 */

declare(strict_types=1);

if (!defined('ARC_ROOT')) {
    define('ARC_ROOT', dirname(__DIR__));
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'Arcates\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $path     = ARC_ROOT . '/app/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($path)) {
        require $path;
    }
});

require ARC_ROOT . '/app/helpers.php';
