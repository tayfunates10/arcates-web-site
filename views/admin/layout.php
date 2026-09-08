<?php
/**
 * Panel duzeni.
 *
 * Panelde satir ici script bulunmaz; tum davranis `admin.js` ve yeniden
 * tasarim yardimcisi `admin-redesign.js` icindedir. DOCS.md 10.7
 *
 * @var string $content
 * @var string $title
 * @var string $section
 * @var array  $_flash
 * @var array  $_user
 * @var array  $_menu
 */

declare(strict_types=1);

use Arcates\Core\Security;

$section = $section ?? '';
$_flash  = $_flash ?? [];
$_menu   = $_menu ?? [];
$_user   = $_user ?? null;
?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= Security::e($title !== '' ? $title . ' — Arcates Panel' : 'Arcates Panel') ?></title>
<link rel="icon" href="/assets/img/favicon-32.png" sizes="32x32">
<link rel="stylesheet" href="<?= Security::e(asset('css/admin.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/admin-redesign.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/admin-r7.css')) ?>">
</head>
<body class="admin admin--r7">

<a class="skip-link" href="#panel-icerik">İçeriğe geç</a>

<div class="admin__shell">

  <aside class="admin__side" id="admin-side" data-admin-side>
    <a class="admin__brand" href="<?= Security::e(admin_url()) ?>">
      <img class="admin__logo" src="/assets/img/logo-wordmark.png" alt="Arcates Yazılım"
           width="158" height="53" decoding="async">
    </a>

    <nav class="admin__nav" aria-label="Panel menusu">
      <ul>
        <?php foreach ($_menu as $item): ?>
          <li>
            <a href="<?= Security::e($item['url']) ?>"
               class="admin__nav-link<?= $section === $item['key'] ? ' is-current' : '' ?>"
               <?= $section === $item['key'] ? 'aria-current="page"' : '' ?>>
              <?= Security::e($item['label']) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <div class="admin__side-foot">
      <a class="admin__side-link" href="/" target="_blank" rel="noopener">Siteyi gör</a>
    </div>
  </aside>

  <div class="admin__nav-backdrop" data-admin-nav-backdrop aria-hidden="true"></div>

  <div class="admin__main">

    <header class="admin__top">
      <button class="admin__nav-toggle" type="button" data-admin-nav-toggle
              aria-expanded="false" aria-controls="admin-side">
        <span class="visually-hidden">Panel menüsünü aç veya kapat</span>
        <span class="admin__nav-toggle-bars" aria-hidden="true"></span>
      </button>

      <h1 class="admin__title"><?= Security::e($title ?: 'Panel') ?></h1>

      <div class="admin__account">
        <?php if ($_user !== null): ?>
          <a class="admin__account-link" href="<?= Security::e(admin_url('hesabim')) ?>">
            <?= Security::e($_user['name']) ?>
            <span class="admin__role"><?= Security::e($_user['role'] === 'admin' ? 'Yönetici' : 'Editör') ?></span>
          </a>
          <form method="post" action="<?= Security::e(admin_url('cikis')) ?>" class="admin__logout">
            <?= csrf_field() ?>
            <button class="btn btn--ghost btn--sm" type="submit">Çıkış</button>
          </form>
        <?php endif; ?>
      </div>
    </header>

    <main class="admin__content" id="panel-icerik">
      <?php foreach ($_flash as $item): ?>
        <div class="notice notice--<?= Security::e($item['type']) ?>" role="status">
          <?= Security::e($item['message']) ?>
        </div>
      <?php endforeach; ?>

      <?= $content ?>
    </main>

  </div>
</div>

<script src="<?= Security::e(asset('js/admin.js')) ?>" defer></script>
<script src="<?= Security::e(asset('js/admin-redesign.js')) ?>" defer></script>
</body>
</html>
