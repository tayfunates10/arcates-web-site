<?php
/**
 * Kullanici olusturma ve duzenleme formu.  DOCS.md 9.11, 10.4
 *
 * @var array|null $user
 */

declare(strict_types=1);

use Arcates\Core\Security;

$isNew  = $user === null;
$action = $isNew
    ? admin_url('kullanicilar/yeni')
    : admin_url('kullanicilar/' . (int) $user['id']);
?>

<section class="panel panel--form">
  <form method="post" action="<?= Security::e($action) ?>">
    <?= csrf_field() ?>

    <div class="field">
      <label for="name">Ad soyad</label>
      <input type="text" id="name" name="name" required maxlength="120"
             value="<?= Security::e(old('name', $user['name'] ?? '')) ?>">
      <?php if ($m = error_for('name')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
    </div>

    <div class="field">
      <label for="email">E-posta</label>
      <input type="email" id="email" name="email" required maxlength="190"
             value="<?= Security::e(old('email', $user['email'] ?? '')) ?>">
      <?php if ($m = error_for('email')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
    </div>

    <div class="field">
      <label for="role">Rol</label>
      <select id="role" name="role">
        <?php $role = old('role', $user['role'] ?? 'editor'); ?>
        <option value="editor" <?= $role === 'editor' ? 'selected' : '' ?>>Editor — yalnizca icerik</option>
        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Yonetici — tam yetki</option>
      </select>
      <span class="field__hint">Editor rolü kullanicilar, ayarlar ve yedekleme bolumlerini goremez.</span>
    </div>

    <div class="field field--check">
      <label>
        <input type="checkbox" name="status" value="1"
               <?= (int) ($user['status'] ?? 1) === 1 ? 'checked' : '' ?>>
        Hesap etkin
      </label>
    </div>

    <div class="field">
      <label for="password">Sifre<?= $isNew ? '' : ' (degistirmek icin doldurun)' ?></label>
      <input type="password" id="password" name="password" autocomplete="new-password"
             <?= $isNew ? 'required' : '' ?>>
      <span class="field__hint">En az 10 karakter.</span>
      <?php if ($m = error_for('password')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
    </div>

    <div class="field">
      <label for="password_confirm">Sifre tekrar</label>
      <input type="password" id="password_confirm" name="password_confirm" autocomplete="new-password"
             <?= $isNew ? 'required' : '' ?>>
      <?php if ($m = error_for('password_confirm')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
    </div>

    <div class="form__actions">
      <button class="btn btn--primary" type="submit"><?= $isNew ? 'Olustur' : 'Kaydet' ?></button>
      <a class="btn btn--ghost" href="<?= Security::e(admin_url('kullanicilar')) ?>">Vazgec</a>
    </div>
  </form>
</section>
