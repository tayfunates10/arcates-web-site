<?php
/**
 * Kurulum sihirbazi ekrani.  DOCS.md 13
 *
 * @var int   $step
 * @var array $requirements
 * @var bool  $ready
 * @var array $dbState
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
<title>Kurulum — Arcates Web Site</title>
<link rel="icon" href="/assets/img/favicon-32.png" sizes="32x32">
<link rel="stylesheet" href="<?= Security::e(asset('css/site.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/system-r7.css')) ?>">
</head>
<body class="system-r7 system-r7--install">
<main class="system">
  <div class="system__card">

    <p class="system__code">Kurulum</p>
    <p class="system__lead">Arcates Web Site kurulum sihirbazı. Adımları sırayla tamamlayın.</p>

    <ol class="system__steps">
      <li<?= $step === 1 ? ' aria-current="step"' : '' ?>>1. Gereksinimler</li>
      <li<?= $step === 2 ? ' aria-current="step"' : '' ?>>2. Veritabanı</li>
      <li<?= $step === 3 ? ' aria-current="step"' : '' ?>>3. Yönetici</li>
    </ol>

    <?php foreach ($flash as $item): ?>
      <div class="notice notice--<?= Security::e($item['type']) ?>"><?= Security::e($item['message']) ?></div>
    <?php endforeach; ?>

    <?php if ($step === 1): ?>
      <h2>Sunucu gereksinimleri</h2>
      <ul class="system__list">
        <?php foreach ($requirements as $check): ?>
          <li>
            <span><?= Security::e($check['label']) ?></span>
            <span class="system__state system__state--<?= $check['ok'] ? 'ok' : 'bad' ?>">
              <?= Security::e($check['detail']) ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php if (!$ready): ?>
        <div class="notice notice--warning">
          Eksikleri giderdikten sonra sayfayı yenileyin.
          <code>config/config.example.php</code> dosyasını <code>config/config.php</code>
          olarak kopyalamayı ve veritabanı bilgilerini girmeyi unutmayın.
        </div>
      <?php endif; ?>
      <div class="system__actions">
        <a class="btn btn--primary" href="/install">Yeniden denetle</a>
      </div>

    <?php elseif ($step === 2): ?>
      <h2>Veritabanı</h2>
      <ul class="system__list">
        <li>
          <span>Bağlantı</span>
          <span class="system__state system__state--<?= $dbState['connected'] ? 'ok' : 'bad' ?>">
            <?= $dbState['connected'] ? 'kuruldu' : 'kurulamadi' ?>
          </span>
        </li>
        <li>
          <span>Şema</span>
          <span class="system__state system__state--<?= $dbState['schema'] ? 'ok' : 'bad' ?>">
            <?= $dbState['schema'] ? 'hazir' : 'uygulanmadi' ?>
          </span>
        </li>
      </ul>

      <?php if (!$dbState['connected']): ?>
        <div class="notice notice--error">
          Veritabanına bağlanılamadı. <code>config/config.php</code> içindeki
          <code>db</code> değerlerini kontrol edin.
          <?php if ($dbState['message'] !== ''): ?>
            <br><small><?= Security::e($dbState['message']) ?></small>
          <?php endif; ?>
        </div>
        <div class="system__actions">
          <a class="btn btn--primary" href="/install">Yeniden dene</a>
        </div>
      <?php else: ?>
        <p>Şemayı uygulamak <code>db/schema.sql</code> dosyasındaki tabloları oluşturur
           ve varsayılan dilleri, ayarları, anasayfa bölümlerini yazar.</p>
        <form method="post" action="/install">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="schema">
          <div class="system__actions">
            <button class="btn btn--primary" type="submit">Şemayı uygula</button>
          </div>
        </form>
      <?php endif; ?>

    <?php else: ?>
      <h2>İlk yönetici hesabı</h2>
      <?php if ($dbState['users'] > 0): ?>
        <div class="notice notice--warning">
          Bu veritabanında zaten kullanıcı var. Kurulumu tamamlamak için
          <code>storage/installed.lock</code> dosyasını elle oluşturun.
        </div>
      <?php else: ?>
        <form method="post" action="/install">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="admin">

          <div class="field">
            <label for="name">Ad soyad</label>
            <input type="text" id="name" name="name" required maxlength="120"
                   value="<?= Security::e($old['name'] ?? '') ?>">
            <?php if (isset($errors['name'])): ?>
              <span class="field__error"><?= Security::e($errors['name']) ?></span>
            <?php endif; ?>
          </div>

          <div class="field">
            <label for="email">E-posta</label>
            <input type="email" id="email" name="email" required maxlength="190"
                   value="<?= Security::e($old['email'] ?? '') ?>">
            <?php if (isset($errors['email'])): ?>
              <span class="field__error"><?= Security::e($errors['email']) ?></span>
            <?php endif; ?>
          </div>

          <div class="field">
            <label for="password">Şifre</label>
            <input type="password" id="password" name="password" required autocomplete="new-password">
            <span class="field__hint">En az 10 karakter.</span>
            <?php if (isset($errors['password'])): ?>
              <span class="field__error"><?= Security::e($errors['password']) ?></span>
            <?php endif; ?>
          </div>

          <div class="field">
            <label for="password_confirm">Şifre tekrar</label>
            <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password">
            <?php if (isset($errors['password_confirm'])): ?>
              <span class="field__error"><?= Security::e($errors['password_confirm']) ?></span>
            <?php endif; ?>
          </div>

          <div class="system__actions">
            <button class="btn btn--primary" type="submit">Kurulumu tamamla</button>
          </div>
        </form>
      <?php endif; ?>
    <?php endif; ?>

  </div>
</main>
</body>
</html>
