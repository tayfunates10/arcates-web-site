<?php
/**
 * Arcates Web Site — tek giris noktasi.
 *
 * `public/.htaccess` dosya veya klasor olmayan her istegi buraya yonlendirir.
 * DOCS.md 3, 10.10
 */

declare(strict_types=1);

define('ARC_ROOT', dirname(__DIR__));
define('ARC_START', microtime(true));

require ARC_ROOT . '/app/autoload.php';

$app = new Arcates\Core\App();
$app->loadRoutes();
$app->run();
