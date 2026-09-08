<?php
/**
 * 503 — bakim modu. Ziyaretci bakim sayfasi gorur, yonetici siteyi gorur.
 * DOCS.md 9.11, test F-17
 */

declare(strict_types=1);

use Arcates\Core\Security;

$message = $message ?? 'Sitemiz kısa süreliğine bakımda.';
?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Bakım çalışması</title>
<link rel="icon" href="/assets/img/favicon-32.png" sizes="32x32">
<link rel="stylesheet" href="<?= Security::e(asset('css/site.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/system-r7.css')) ?>">
</head>
<body class="system-r7 system-r7--error">
<main class="system">
  <div class="system__card">
    <p class="system__code">Bakım</p>
    <h1>Kısa bir ara veriyoruz</h1>
    <p class="system__lead"><?= Security::e($message) ?></p>
  </div>
</main>
</body>
</html>
