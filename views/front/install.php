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
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="<?= Security::e(asset('css/site.css')) ?>">
</head>
<body>
<main class="system">
  <div class="system__card">

    <p class="system__code">Kurulum</p>
    <p class="system__lead">Arcates Web Site kurulum sihirbazi. Adimlari sirayla tamamlayin.</p>

    <ol class="system__steps">
      <li<?= $step === 1 ? ' aria-current="step"' : '' ?>>1. Gereksinimler</li>
      <li<?= $step === 2 ? ' aria-current="step"' : '' ?>>2. Veritabani</li>
      <li<?= $step === 3 ? ' aria-current="step"' : '' ?>>3. Yonetici</li>
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
          Eksikleri giderdikten sonra sayfayi yenileyin.
          <code>config/config.example.php</code> dosyasini <code>config/config.php</code>
          olarak kopyalamayi ve veritabani bilgilerini girmeyi unutmayin.
        </div>
      <?php endif; ?>
      <div class="system__actions">
        <a class="btn btn--primary" href="/install">Yeniden denetle</a>
      </div>

    <?php elseif ($step === 2): ?>
      <h2>Veritabani</h2>
      <ul class="system__list">
        <li>
          <span>Baglanti</span>
          <span class="system__state system__state--<?= $dbState['connected'] ? 'ok' : 'bad' ?>">
            <?= $dbState['connected'] ? 'kuruldu' : 'kurulamadi' ?>
          </span>
        </li>
        <li>
          <span>Sema</span>
          <span class="system__state system__state--<?= $dbState['schema'] ? 'ok' : 'bad' ?>">
            <?= $dbState['schema'] ? 'hazir' : 'uygulanmadi' ?>
          </span>
        </li>
      </ul>

      <?php if (!$dbState['connected']): ?>
        <div class="notice notice--error">
          Veritabanina baglanilamadi. <code>config/config.php</code> icindeki
          <code>db</code> degerlerini kontrol edin.
          <?php if ($dbState['message'] !== ''): ?>
            <br><small><?= Security::e($dbState['message']) ?></small>
          <?php endif; ?>
        </div>
        <div class="system__actions">
          <a class="btn btn--primary" href="/install">Yeniden dene</a>
        </div>
      <?php else: ?>
        <p>Semayi uygulamak <code>db/schema.sql</code> dosyasindaki tablolari olusturur
           ve varsayilan dilleri, ayarlari, anasayfa bolumlerini yazar.</p>
        <form method="post" action="/install">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="schema">
          <div class="system__actions">
            <button class="btn btn--primary" type="submit">Semayi uygula</button>
          </div>
        </form>
      <?php endif; ?>

    <?php else: ?>
      <h2>Ilk yonetici hesabi</h2>
      <?php if ($dbState['users'] > 0): ?>
        <div class="notice notice--warning">
          Bu veritabaninda zaten kullanici var. Kurulumu tamamlamak icin
          <code>storage/installed.lock</code> dosyasini elle olusturun.
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
            <label for="password">Sifre</label>
            <input type="password" id="password" name="password" required autocomplete="new-password">
            <span class="field__hint">En az 10 karakter.</span>
            <?php if (isset($errors['password'])): ?>
              <span class="field__error"><?= Security::e($errors['password']) ?></span>
            <?php endif; ?>
          </div>

          <div class="field">
            <label for="password_confirm">Sifre tekrar</label>
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
