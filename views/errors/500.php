<?php
/**
 * 500 — beklenmeyen hata. Canlida ayrinti gosterilmez.  DOCS.md 10.7
 */

declare(strict_types=1);

use Arcates\Core\Security;

?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Sunucu hatası</title>
<link rel="icon" href="/assets/img/favicon-32.png" sizes="32x32">
<link rel="stylesheet" href="<?= Security::e(asset('css/site.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/system-r7.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/r3-cta-status.css')) ?>">
</head>
<body class="system-r7 system-r7--error">
<main class="system">
  <div class="system__card">
    <span class="system__visual state-mark state-mark--error" aria-hidden="true"><span class="state-mark__glyph"></span></span>
    <p class="system__code">500</p>
    <h1>Beklenmeyen bir hata oluştu</h1>
    <p class="system__lead">
      Kayıt alındı ve inceleniyor. Kısa süre sonra tekrar deneyin.
    </p>
    <div class="system__actions">
      <a class="btn btn--primary" href="/">Anasayfaya dön</a>
    </div>
  </div>
</main>
</body>
</html>
