<?php
/**
 * Panel giris ekrani.  DOCS.md 10.4, 10.5
 *
 * @var array $errors
 * @var array $old
 * @var array $flash
 */

declare(strict_types=1);

use Arcates\Core\Security;

?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Panel girişi — Arcates</title>
<link rel="icon" href="/assets/img/favicon-32.png" sizes="32x32">
<link rel="stylesheet" href="<?= Security::e(asset('css/admin.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/admin-redesign.css')) ?>">
</head>
<body class="admin admin--auth">
<main class="system">
  <div class="system__card system__card--narrow">

    <p class="system__code">Panel</p>
    <h1>Giriş yapın</h1>

    <?php foreach ($flash as $item): ?>
      <div class="notice notice--<?= Security::e($item['type']) ?>" role="status">
        <?= Security::e($item['message']) ?>
      </div>
    <?php endforeach; ?>

    <?php if (isset($errors['email'])): ?>
      <div class="notice notice--error" role="alert"><?= Security::e($errors['email']) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= Security::e(admin_url('giris')) ?>" autocomplete="on">
      <?= csrf_field() ?>

      <div class="field">
        <label for="email">E-posta</label>
        <input type="email" id="email" name="email" required autocomplete="username"
               value="<?= Security::e($old['email'] ?? '') ?>">
      </div>

      <div class="field">
        <label for="password">Şifre</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
        <?php if (isset($errors['password'])): ?>
          <span class="field__error"><?= Security::e($errors['password']) ?></span>
        <?php endif; ?>
      </div>

      <div class="system__actions">
        <button class="btn btn--primary" type="submit">Giriş yap</button>
        <a class="btn btn--ghost" href="/">Siteye dön</a>
      </div>
    </form>

  </div>
</main>
</body>
</html>
