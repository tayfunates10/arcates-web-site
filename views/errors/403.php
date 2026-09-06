<?php
/**
 * 403 — yetki yok.  DOCS.md 9.11, test S-10
 */

declare(strict_types=1);

use Arcates\Core\Security;

$message = $message ?? 'Bu sayfayi goruntuleme yetkiniz yok.';
?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Yetkisiz erisim</title>
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="<?= Security::e(asset('css/site.css')) ?>">
</head>
<body>
<main class="system">
  <div class="system__card">
    <p class="system__code">403</p>
    <h1>Yetkisiz erisim</h1>
    <p class="system__lead"><?= Security::e($message) ?></p>
    <div class="system__actions">
      <a class="btn btn--primary" href="<?= Security::e(admin_url()) ?>">Panoya don</a>
    </div>
  </div>
</main>
</body>
</html>
