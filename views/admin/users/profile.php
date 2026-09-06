<?php
/**
 * Hesabim — ad ve sifre degistirme.  DOCS.md 9.11
 *
 * @var array $user
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<section class="panel panel--form">
  <form method="post" action="<?= Security::e(admin_url('hesabim')) ?>">
    <?= csrf_field() ?>

    <div class="field">
      <label for="name">Ad soyad</label>
      <input type="text" id="name" name="name" required maxlength="120"
             value="<?= Security::e(old('name', $user['name'] ?? '')) ?>">
      <?php if ($m = error_for('name')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
    </div>

    <div class="field">
      <label>E-posta</label>
      <input type="email" value="<?= Security::e($user['email'] ?? '') ?>" disabled>
      <span class="field__hint">E-posta adresini yalnizca yonetici degistirebilir.</span>
    </div>

    <hr class="rule">

    <div class="field">
      <label for="current_password">Mevcut sifre</label>
      <input type="password" id="current_password" name="current_password" autocomplete="current-password">
      <?php if ($m = error_for('current_password')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
    </div>

    <div class="field">
      <label for="password">Yeni sifre</label>
      <input type="password" id="password" name="password" autocomplete="new-password">
      <span class="field__hint">En az 10 karakter. Degistirmek istemiyorsaniz bos birakin.</span>
      <?php if ($m = error_for('password')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
    </div>

    <div class="field">
      <label for="password_confirm">Yeni sifre tekrar</label>
      <input type="password" id="password_confirm" name="password_confirm" autocomplete="new-password">
      <?php if ($m = error_for('password_confirm')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
    </div>

    <div class="form__actions">
      <button class="btn btn--primary" type="submit">Kaydet</button>
    </div>
  </form>
</section>
