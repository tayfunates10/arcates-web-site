<?php
/**
 * Form kaydi ayrintisi.  DOCS.md 9.9, 12
 *
 * @var array $row
 * @var array $statuses
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<p><a class="btn btn--ghost btn--sm" href="<?= Security::e(admin_url('formlar')) ?>">← Kayit listesi</a></p>

<div class="grid grid--2">

  <section class="panel">
    <h2 class="panel__title">Mesaj</h2>

    <ul class="system__list">
      <li><span>Ad soyad</span><span class="system__state"><?= Security::e($row['name'] ?? '—') ?></span></li>
      <li>
        <span>E-posta</span>
        <span class="system__state">
          <a href="mailto:<?= Security::e($row['email'] ?? '') ?>"><?= Security::e($row['email'] ?? '—') ?></a>
        </span>
      </li>
      <li>
        <span>Telefon</span>
        <span class="system__state">
          <a href="tel:<?= Security::e(preg_replace('/[^0-9+]/', '', (string) ($row['phone'] ?? ''))) ?>">
            <?= Security::e($row['phone'] ?? '—') ?>
          </a>
        </span>
      </li>
      <li><span>Hizmet</span><span class="system__state"><?= Security::e($row['service'] ?? '—') ?></span></li>
      <li><span>Tarih</span><span class="system__state"><?= Security::e(format_date($row['created_at'], true)) ?></span></li>
      <li><span>Dil</span><span class="system__state"><?= Security::e(strtoupper((string) ($row['lang'] ?? ''))) ?></span></li>
      <li>
        <span>KVKK onayi</span>
        <span class="system__state system__state--<?= (int) $row['kvkk_consent'] === 1 ? 'ok' : 'bad' ?>">
          <?= (int) $row['kvkk_consent'] === 1 ? 'verildi' : 'yok' ?>
        </span>
      </li>
    </ul>

    <h3 class="panel__title">Metin</h3>
    <p class="message-body"><?= nl2br(Security::e((string) ($row['message'] ?? ''))) ?></p>
  </section>

  <div>
    <section class="panel">
      <h2 class="panel__title">Nereden geldi</h2>
      <p class="muted">Bu alanlar hangi sayfanin is getirdigini gosterir.</p>

      <ul class="system__list">
        <li>
          <span>Kaynak sayfa</span>
          <span class="system__state"><code><?= Security::e($row['source_url'] ?? '—') ?></code></span>
        </li>
        <li>
          <span>Geldigi yer</span>
          <span class="system__state wrap-anywhere"><?= Security::e(str_limit((string) ($row['referrer'] ?? ''), 50) ?: '—') ?></span>
        </li>
        <?php foreach (['utm_source' => 'UTM kaynak', 'utm_medium' => 'UTM ortam', 'utm_campaign' => 'UTM kampanya'] as $key => $label): ?>
          <?php if (!empty($row['utm'][$key])): ?>
            <li><span><?= Security::e($label) ?></span><span class="system__state"><?= Security::e($row['utm'][$key]) ?></span></li>
          <?php endif; ?>
        <?php endforeach; ?>
        <li><span>IP</span><span class="system__state"><?= Security::e($row['ip'] ?? '—') ?></span></li>
      </ul>
    </section>

    <section class="panel panel--form">
      <h2 class="panel__title">Durum ve not</h2>

      <form method="post" action="<?= Security::e(admin_url('formlar/' . (int) $row['id'])) ?>">
        <?= csrf_field() ?>

        <div class="field">
          <label for="status">Durum</label>
          <select id="status" name="status">
            <?php foreach ($statuses as $key => $label): ?>
              <option value="<?= Security::e($key) ?>" <?= $row['status'] === $key ? 'selected' : '' ?>>
                <?= Security::e($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="note">Not</label>
          <textarea id="note" name="note" rows="5" maxlength="4000"><?= Security::e((string) ($row['note'] ?? '')) ?></textarea>
        </div>

        <div class="form__actions">
          <button class="btn btn--primary" type="submit">Kaydet</button>
        </div>
      </form>
    </section>

    <section class="panel">
      <form method="post" action="<?= Security::e(admin_url('formlar/' . (int) $row['id'] . '/sil')) ?>"
            data-confirm="Bu kaydi kalici olarak silmek istiyor musunuz?">
        <?= csrf_field() ?>
        <button class="btn btn--danger btn--sm" type="submit">Kaydi sil</button>
      </form>
    </section>
  </div>

</div>
