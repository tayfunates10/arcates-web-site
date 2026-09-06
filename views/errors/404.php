<?php
/**
 * 404 sayfasi.  DOCS.md 14.4 (F-11), yayin oncesi teslim listesi
 *
 * @var string|null $title
 * @var string|null $message
 */

declare(strict_types=1);

use Arcates\Core\Security;

$title   = $title   ?? 'Sayfa bulunamadi';
$message = $message ?? 'Aradiginiz sayfa tasinmis veya kaldirilmis olabilir.';
?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,follow">
<title><?= Security::e($title) ?></title>
<link rel="stylesheet" href="<?= Security::e(asset('css/site.css')) ?>">
</head>
<body>
<main class="system">
  <div class="system__card">
    <p class="system__code">404</p>
    <h1><?= Security::e($title) ?></h1>
    <p class="system__lead"><?= Security::e($message) ?></p>
    <div class="system__actions">
      <a class="btn btn--primary" href="/">Anasayfaya don</a>
      <a class="btn btn--ghost" href="/iletisim">Bize yazin</a>
    </div>
  </div>
</main>
</body>
</html>
