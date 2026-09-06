<?php
/**
 * 503 — bakim modu. Ziyaretci bakim sayfasi gorur, yonetici siteyi gorur.
 * DOCS.md 9.11, test F-17
 */

declare(strict_types=1);

use Arcates\Core\Security;

$message = $message ?? 'Sitemiz kisa sureligine bakimda.';
?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Bakim calismasi</title>
<link rel="stylesheet" href="<?= Security::e(asset('css/site.css')) ?>">
</head>
<body>
<main class="system">
  <div class="system__card">
    <p class="system__code">Bakim</p>
    <h1>Kisa bir ara veriyoruz</h1>
    <p class="system__lead"><?= Security::e($message) ?></p>
  </div>
</main>
</body>
</html>
