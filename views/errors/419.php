<?php
/**
 * 419 — oturum belirteci gecersiz.  DOCS.md 10.5, testler S-03, S-04
 */

declare(strict_types=1);

use Arcates\Core\Security;

?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Oturum doğrulanamadı</title>
<link rel="icon" href="/assets/img/favicon-32.png" sizes="32x32">
<link rel="stylesheet" href="<?= Security::e(asset('css/site.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/system-r7.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/r3-cta-status.css')) ?>">
</head>
<body class="system-r7 system-r7--error">
<main class="system">
  <div class="system__card">
    <span class="system__visual state-mark state-mark--error" aria-hidden="true"><span class="state-mark__glyph"></span></span>
    <p class="system__code">419</p>
    <h1>Oturum doğrulanamadı</h1>
    <p class="system__lead">
      Form doğrulama belirteci geçersiz veya süresi dolmuş. Sayfayı yenileyip
      formu yeniden gönderin.
    </p>
    <div class="system__actions">
      <a class="btn btn--primary" href="/">Anasayfaya dön</a>
    </div>
  </div>
</main>
</body>
</html>
