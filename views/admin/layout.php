<?php
/**
 * Panel duzeni.
 *
 * Panelde satir ici script bulunmaz; tum davranis `admin.js` icindedir.
 * DOCS.md 10.7
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
<link rel="stylesheet" href="<?= Security::e(asset('css/admin.css')) ?>">
</head>
<body class="admin">

<a class="skip-link" href="#panel-icerik">Icerige gec</a>

<div class="admin__shell">

  <aside class="admin__side">
    <a class="admin__brand" href="<?= Security::e(admin_url()) ?>">
      <span class="admin__mark" aria-hidden="true"></span>
      <span>Arcates</span>
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
      <a class="admin__side-link" href="/" target="_blank" rel="noopener">Siteyi gor</a>
    </div>
  </aside>

  <div class="admin__main">

    <header class="admin__top">
      <h1 class="admin__title"><?= Security::e($title ?: 'Panel') ?></h1>

      <div class="admin__account">
        <?php if ($_user !== null): ?>
          <a class="admin__account-link" href="<?= Security::e(admin_url('hesabim')) ?>">
            <?= Security::e($_user['name']) ?>
            <span class="admin__role"><?= Security::e($_user['role'] === 'admin' ? 'Yonetici' : 'Editor') ?></span>
          </a>
          <form method="post" action="<?= Security::e(admin_url('cikis')) ?>" class="admin__logout">
            <?= csrf_field() ?>
            <button class="btn btn--ghost btn--sm" type="submit">Cikis</button>
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
</body>
</html>
