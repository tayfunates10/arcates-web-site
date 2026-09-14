<?php
/**
 * Bulten abone listesi.  DOCS.md 9.9, 12
 *
 * Kayitlar buradan duzenlenmez: abonelik ziyaretcinin kendi eylemidir,
 * panelden birini "etkin" yapmak cift onayin anlamini ortadan kaldirirdi.
 * Silme yalnizca kisinin "verilerimi silin" talebi icindir.
 *
 * @var array  $rows
 * @var array  $statuses
 * @var string $status
 * @var string $search
 * @var array  $counts
 * @var int    $page
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<div class="cards cards--stats">
  <?php foreach ($statuses as $key => $label): ?>
    <div class="card card--stat">
      <strong><?= (int) ($counts[$key] ?? 0) ?></strong>
      <span><?= Security::e($label) ?></span>
    </div>
  <?php endforeach; ?>
</div>

<p class="muted">
  Yalnızca <strong>Etkin</strong> aboneler onay bağlantısına tıklamış kişilerdir;
  ileti yalnızca onlara gönderilebilir. Çıkan aboneler listeden silinmez —
  kayıt, onayın geri alındığının kanıtıdır.
</p>

<form class="filters" method="get" action="<?= Security::e(admin_url('bulten')) ?>">
  <div class="field">
    <label for="durum">Durum</label>
    <select id="durum" name="durum">
      <option value="">Tümü</option>
      <?php foreach ($statuses as $key => $label): ?>
        <option value="<?= Security::e($key) ?>" <?= $status === $key ? 'selected' : '' ?>>
          <?= Security::e($label) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="field">
    <label for="ara">E-posta ara</label>
    <input type="search" id="ara" name="ara" value="<?= Security::e($search) ?>">
  </div>

  <div class="form__actions">
    <button class="btn btn--primary" type="submit">Süz</button>
    <a class="btn btn--ghost" href="<?= Security::e(admin_url('bulten/csv')) ?>">CSV indir</a>
  </div>
</form>

<div class="table-scroll">
  <table class="table">
    <caption class="visually-hidden">Bülten aboneleri</caption>
    <thead>
      <tr>
        <th scope="col">E-posta</th>
        <th scope="col">Durum</th>
        <th scope="col">Dil</th>
        <th scope="col">Onay zamanı</th>
        <th scope="col">Kaynak</th>
        <th scope="col">İşlem</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows === []): ?>
        <tr><td colspan="6" class="muted">Kayıt yok.</td></tr>
      <?php endif; ?>

      <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= Security::e((string) $row['email']) ?></td>
          <td><?= Security::e($statuses[(string) $row['status']] ?? (string) $row['status']) ?></td>
          <td><?= Security::e((string) ($row['lang'] ?? '')) ?></td>
          <td><?= Security::e((string) ($row['consent_at'] ?? '')) ?></td>
          <td class="muted"><?= Security::e((string) ($row['consent_source'] ?? '')) ?></td>
          <td>
            <form method="post" action="<?= Security::e(admin_url('bulten/' . (int) $row['id'] . '/sil')) ?>"
                  data-confirm="Kayit kalici olarak silinecek. Devam edilsin mi?">
              <?= csrf_field() ?>
              <button class="btn btn--danger btn--sm" type="submit">Sil</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
