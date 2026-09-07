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
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="<?= Security::e(asset('css/site.css')) ?>">
</head>
<body>
<main class="system">
  <div class="system__card">
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
