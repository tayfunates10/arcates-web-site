<?php
/**
 * Form kayitlari listesi ve donusum raporu.  DOCS.md 9.9, 12
 *
 * @var array  $rows
 * @var array  $statuses
 * @var string $status
 * @var string $search
 * @var int    $total
 * @var int    $page
 * @var int    $pages
 * @var array  $conversion
 * @var array  $sources
 * @var int    $retention
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<form class="filters" method="get" action="<?= Security::e(admin_url('formlar')) ?>">
  <div class="field">
    <label for="durum">Durum</label>
    <select id="durum" name="durum">
      <option value="">Tumu</option>
      <?php foreach ($statuses as $key => $label): ?>
        <option value="<?= Security::e($key) ?>" <?= $status === $key ? 'selected' : '' ?>>
          <?= Security::e($label) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="field">
    <label for="ara">Ara</label>
    <input type="search" id="ara" name="ara" value="<?= Security::e($search) ?>" placeholder="Ad, e-posta, mesaj">
  </div>

  <button class="btn btn--ghost btn--sm" type="submit">Filtrele</button>
  <a class="btn btn--ghost btn--sm"
     href="<?= Security::e(admin_url('formlar/csv') . '?durum=' . rawurlencode($status) . '&ara=' . rawurlencode($search)) ?>">
    CSV indir
  </a>
</form>

<section class="panel">
  <h2 class="panel__title"><?= Security::e((string) $total) ?> kayit</h2>

  <div class="table-scroll">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Tarih</th>
          <th scope="col">Ad</th>
          <th scope="col">Iletisim</th>
          <th scope="col">Hizmet</th>
          <th scope="col">Kaynak sayfa</th>
          <th scope="col">Durum</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="6" class="muted">Kayit yok.</td></tr>
        <?php endif; ?>

        <?php foreach ($rows as $row): ?>
          <tr>
            <td><?= Security::e(format_date($row['created_at'], true)) ?></td>
            <td>
              <a href="<?= Security::e(admin_url('formlar/' . (int) $row['id'])) ?>">
                <?= Security::e($row['name'] ?? '—') ?>
              </a>
            </td>
            <td class="wrap-anywhere">
              <?= Security::e($row['email'] ?? '') ?><br>
              <span class="muted"><?= Security::e($row['phone'] ?? '') ?></span>
            </td>
            <td><?= Security::e($row['service'] ?? '—') ?></td>
            <td class="wrap-anywhere"><code><?= Security::e(str_limit((string) ($row['source_url'] ?? ''), 40)) ?></code></td>
            <td>
              <span class="tag tag--<?= $row['status'] === 'won' ? 'ok' : ($row['status'] === 'lost' ? 'off' : 'warn') ?>">
                <?= Security::e($statuses[$row['status']] ?? $row['status']) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pages > 1): ?>
    <nav class="pager" aria-label="Sayfalar">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <?php if ($i === $page): ?>
          <span class="pager__item is-current" aria-current="page"><?= (int) $i ?></span>
        <?php else: ?>
          <a class="pager__item"
             href="<?= Security::e(admin_url('formlar') . '?sayfa=' . $i . '&durum=' . rawurlencode($status)) ?>"><?= (int) $i ?></a>
        <?php endif; ?>
      <?php endfor; ?>
    </nav>
  <?php endif; ?>
</section>

<div class="grid grid--2">

  <section class="panel">
    <h2 class="panel__title">Hangi sayfa is getiriyor</h2>
    <p class="muted">Son 90 gun. Kaynak sayfa, formun gonderildigi adrestir.</p>

    <?php if (!$conversion): ?>
      <p class="muted">Henuz veri yok.</p>
    <?php else: ?>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Sayfa</th>
            <th scope="col" class="num">Kayit</th>
            <th scope="col" class="num">Surecte</th>
            <th scope="col" class="num">Kazanildi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($conversion as $row): ?>
            <tr>
              <td class="wrap-anywhere"><code><?= Security::e($row['source_url']) ?></code></td>
              <td class="num"><?= (int) $row['total'] ?></td>
              <td class="num"><?= (int) $row['in_progress'] ?></td>
              <td class="num">
                <span class="tag tag--<?= (int) $row['won'] > 0 ? 'ok' : 'draft' ?>"><?= (int) $row['won'] ?></span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  <section class="panel">
    <h2 class="panel__title">Kaynak dagilimi</h2>

    <?php if (!$sources): ?>
      <p class="muted">Henuz veri yok.</p>
    <?php else: ?>
      <?php $max = max(1, max(array_column($sources, 'count'))); ?>
      <ul class="funnel">
        <?php foreach (array_slice($sources, 0, 8) as $row): ?>
          <?php $step = max(5, (int) (round(($row['count'] / $max) * 20) * 5)); ?>
          <li>
            <span class="funnel__label"><?= Security::e($row['source']) ?></span>
            <span class="funnel__bar is-w<?= Security::e((string) $step) ?>"></span>
            <span class="funnel__count"><?= (int) $row['count'] ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

</div>

<section class="panel panel--form">
  <h2 class="panel__title">Saklama suresi</h2>
  <p class="muted">
    KVKK geregi kayitlar sinirsiz saklanmaz. Suresi dolan kayitlar bu
    ekrandan veya gunluk gorevle silinir.
  </p>

  <form method="post" action="<?= Security::e(admin_url('formlar/temizle')) ?>"
        data-confirm="Suresi dolan kayitlar kalici olarak silinecek. Devam edilsin mi?">
    <?= csrf_field() ?>

    <div class="field">
      <label for="days">Saklama suresi (gun)</label>
      <input type="number" id="days" name="days" min="30" max="3650" value="<?= (int) $retention ?>">
      <span class="field__hint">Bu suredan eski kayitlar silinir.</span>
    </div>

    <div class="form__actions">
      <button class="btn btn--danger" type="submit">Suresi dolanlari sil</button>
    </div>
  </form>
</section>
