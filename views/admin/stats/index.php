<?php
/**
 * Istatistik ekrani.  DOCS.md 9.10
 *
 * @var int   $days
 * @var array $series
 * @var array $topPaths
 * @var array $referrers
 * @var array $devices
 * @var array $languages
 * @var int   $bots
 * @var string $month
 */

declare(strict_types=1);

use Arcates\Core\Security;

$maxViews      = max(1, max(array_column($series, 'views')));
$totalViews    = array_sum(array_column($series, 'views'));
$totalSessions = array_sum(array_column($series, 'sessions'));
?>

<form class="filters" method="get" action="<?= Security::e(admin_url('istatistik')) ?>">
  <div class="field">
    <label for="gun">Dönem</label>
    <select id="gun" name="gun">
      <?php foreach ([7 => 'Son 7 gün', 30 => 'Son 30 gün', 90 => 'Son 90 gün', 365 => 'Son 1 yıl'] as $value => $label): ?>
        <option value="<?= (int) $value ?>" <?= $days === $value ? 'selected' : '' ?>><?= Security::e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <button class="btn btn--ghost btn--sm" type="submit">Göster</button>
  <a class="btn btn--ghost btn--sm"
     href="<?= Security::e(admin_url('istatistik/csv') . '?ay=' . rawurlencode($month)) ?>">
    Aylık CSV
  </a>
</form>

<div class="cards">
  <div class="card card--stat">
    <span class="card__label">Görüntüleme</span>
    <strong class="card__value"><?= Security::e(number_format($totalViews, 0, ',', '.')) ?></strong>
  </div>
  <div class="card card--stat">
    <span class="card__label">Oturum</span>
    <strong class="card__value"><?= Security::e(number_format($totalSessions, 0, ',', '.')) ?></strong>
  </div>
  <div class="card card--stat">
    <span class="card__label">Günlük ortalama</span>
    <strong class="card__value"><?= Security::e(number_format($totalViews / max(1, $days), 1, ',', '.')) ?></strong>
  </div>
  <div class="card card--stat">
    <span class="card__label">Bot isteği (grafikte yok)</span>
    <strong class="card__value"><?= Security::e(number_format($bots, 0, ',', '.')) ?></strong>
  </div>
</div>

<section class="panel">
  <h2 class="panel__title">Günlük görüntüleme</h2>

  <?php if ($totalViews === 0): ?>
    <p class="muted">Bu dönemde ziyaret kaydı yok.</p>
  <?php else: ?>
    <div class="chart" role="img"
         aria-label="Son <?= (int) $days ?> günün günlük görüntülemesi. Toplam <?= (int) $totalViews ?>.">
      <?php foreach ($series as $day): ?>
        <?php $step = max(5, (int) (round(((int) $day['views'] / $maxViews) * 20) * 5)); ?>
        <span class="chart__bar is-h<?= Security::e((string) $step) ?>"
              title="<?= Security::e(format_date($day['day']) . ': ' . $day['views'] . ' görüntüleme, ' . $day['sessions'] . ' oturum') ?>"></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<div class="grid grid--2">

  <section class="panel">
    <h2 class="panel__title">En çok girilen sayfalar</h2>
    <?php if (!$topPaths): ?>
      <p class="muted">Kayıt yok.</p>
    <?php else: ?>
      <table class="table">
        <thead><tr><th scope="col">Adres</th><th scope="col" class="num">Görüntüleme</th><th scope="col" class="num">Oturum</th></tr></thead>
        <tbody>
          <?php foreach ($topPaths as $row): ?>
            <tr>
              <td class="wrap-anywhere"><a href="<?= Security::e($row['path']) ?>" target="_blank" rel="noopener"><?= Security::e($row['path']) ?></a></td>
              <td class="num"><?= (int) $row['views'] ?></td>
              <td class="num"><?= (int) $row['sessions'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  <section class="panel">
    <h2 class="panel__title">Referans kaynakları</h2>
    <?php if (!$referrers): ?>
      <p class="muted">Kayıt yok.</p>
    <?php else: ?>
      <?php $maxRef = max(1, max(array_column($referrers, 'count'))); ?>
      <ul class="funnel">
        <?php foreach ($referrers as $row): ?>
          <?php $step = max(5, (int) (round(($row['count'] / $maxRef) * 20) * 5)); ?>
          <li>
            <span class="funnel__label"><?= Security::e($row['host']) ?></span>
            <span class="funnel__bar is-w<?= Security::e((string) $step) ?>"></span>
            <span class="funnel__count"><?= (int) $row['count'] ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

</div>

<div class="grid grid--2">

  <section class="panel">
    <h2 class="panel__title">Cihaz dağılımı</h2>
    <?php if (!$devices): ?>
      <p class="muted">Kayıt yok.</p>
    <?php else: ?>
      <?php $maxDevice = max(1, max(array_column($devices, 'total'))); ?>
      <ul class="funnel">
        <?php foreach ($devices as $row): ?>
          <?php $step = max(5, (int) (round(((int) $row['total'] / $maxDevice) * 20) * 5)); ?>
          <li>
            <span class="funnel__label"><?= Security::e($row['label']) ?></span>
            <span class="funnel__bar is-w<?= Security::e((string) $step) ?>"></span>
            <span class="funnel__count"><?= (int) $row['total'] ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section class="panel">
    <h2 class="panel__title">Dil dağılımı</h2>
    <?php if (!$languages): ?>
      <p class="muted">Kayıt yok.</p>
    <?php else: ?>
      <?php $maxLang = max(1, max(array_column($languages, 'total'))); ?>
      <ul class="funnel">
        <?php foreach ($languages as $row): ?>
          <?php $step = max(5, (int) (round(((int) $row['total'] / $maxLang) * 20) * 5)); ?>
          <li>
            <span class="funnel__label"><?= Security::e(strtoupper((string) ($row['label'] ?? '—'))) ?></span>
            <span class="funnel__bar is-w<?= Security::e((string) $step) ?>"></span>
            <span class="funnel__count"><?= (int) $row['total'] ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

</div>
