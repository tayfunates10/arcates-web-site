<?php
/**
 * `/robots.txt` icin geri donus noktasi.
 *
 * Normalde yonlendirici `/robots.txt` yolunu karsilar; bu dosya sunucunun
 * dogrudan `robots.php` cagirmasi durumunda ayni ciktiyi uretir.
 * DOCS.md 3, 9.7
 */

declare(strict_types=1);

define('ARC_ROOT', dirname(__DIR__));

require ARC_ROOT . '/app/autoload.php';

Arcates\Core\Config::load();

$controller = new Arcates\Controllers\Front\RobotsController();
$controller->index(Arcates\Core\Request::capture(), [])->send();
