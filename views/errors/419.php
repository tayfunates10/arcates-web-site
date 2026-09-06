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
<title>Oturum dogrulanamadi</title>
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="<?= Security::e(asset('css/site.css')) ?>">
</head>
<body>
<main class="system">
  <div class="system__card">
    <p class="system__code">419</p>
    <h1>Oturum dogrulanamadi</h1>
    <p class="system__lead">
      Form dogrulama belirteci gecersiz veya suresi dolmus. Sayfayi yenileyip
      formu yeniden gonderin.
    </p>
    <div class="system__actions">
      <a class="btn btn--primary" href="/">Anasayfaya don</a>
    </div>
  </div>
</main>
</body>
</html>
