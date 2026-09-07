<?php
/**
 * Pano.  DOCS.md 9.1
 *
 * @var array $visits
 * @var array $funnel
 * @var array $topPages
 * @var array $notFound
 * @var array $drafts
 * @var array $system
 * @var int   $newForms
 */

declare(strict_types=1);

use Arcates\Core\Security;

$maxViews = 0;
foreach ($visits as $day) {
    $maxViews = max($maxViews, (int) $day['views']);
}
$totalViews    = array_sum(array_column($visits, 'views'));
$totalSessions = array_sum(array_column($visits, 'sessions'));
?>

<div class="cards">
  <div class="card card--stat">
    <span class="card__label">Son 30 gün görüntüleme</span>
    <strong class="card__value"><?= Security::e(number_format($totalViews, 0, ',', '.')) ?></strong>
  </div>
  <div class="card card--stat">
    <span class="card__label">Son 30 gün oturum</span>
    <strong class="card__value"><?= Security::e(number_format($totalSessions, 0, ',', '.')) ?></strong>
  </div>
  <div class="card card--stat">
    <span class="card__label">Yeni form</span>
    <strong class="card__value"><?= Security::e((string) $newForms) ?></strong>
  </div>
  <div class="card card--stat">
    <span class="card__label">Taslak içerik</span>
    <strong class="card__value"><?= Security::e((string) array_sum($drafts)) ?></strong>
  </div>
</div>

<section class="panel">
  <h2 class="panel__title">Son 30 gün</h2>
  <?php if ($maxViews === 0): ?>
    <p class="muted">Henüz ziyaret kaydı yok.</p>
  <?php else: ?>
    <div class="chart" role="img"
         aria-label="Son 30 günün günlük görüntüleme sayısı. Toplam <?= Security::e((string) $totalViews) ?> görüntüleme.">
      <?php foreach ($visits as $day): ?>
        <?php
        // Yukseklik satir ici stil yerine sinifla verilir; icerik guvenlik
        // politikasi satir ici stile izin vermez ve boylece cubuklar
        // JavaScript olmadan da dogru cizilir. DOCS.md 7.1, 10.7
        $ratio = $maxViews > 0 ? ((int) $day['views'] / $maxViews) : 0;
        $step  = max(5, (int) (round($ratio * 20) * 5));
        ?>
        <span class="chart__bar is-h<?= Security::e((string) $step) ?>"
              title="<?= Security::e(format_date($day['day']) . ': ' . $day['views'] . ' görüntüleme') ?>"></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<div class="grid grid--2">

  <section class="panel">
    <h2 class="panel__title">Dönüşüm hunisi</h2>
    <?php $funnelMax = max(1, max(array_column($funnel, 'count'))); ?>
    <ul class="funnel">
      <?php foreach ($funnel as $stage): ?>
        <li>
          <span class="funnel__label"><?= Security::e($stage['label']) ?></span>
          <?php $step = max(5, (int) (round(($stage['count'] / $funnelMax) * 20) * 5)); ?>
        <span class="funnel__bar is-w<?= Security::e((string) $step) ?>"></span>
          <span class="funnel__count"><?= Security::e((string) $stage['count']) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
    <a class="btn btn--ghost btn--sm" href="<?= Security::e(admin_url('formlar')) ?>">Form kayıtları</a>
  </section>

  <section class="panel">
    <h2 class="panel__title">Sistem durumu</h2>
    <ul class="system__list">
      <li><span>PHP sürümü</span><span class="system__state"><?= Security::e($system['php']) ?></span></li>
      <li>
        <span>Disk boş alan</span>
        <span class="system__state">
          <?= $system['disk_free'] !== null ? Security::e(format_bytes($system['disk_free'])) : '—' ?>
        </span>
      </li>
      <li>
        <span>Son yedek</span>
        <span class="system__state system__state--<?= $system['last_backup'] ? 'ok' : 'bad' ?>">
          <?= Security::e($system['last_backup'] ?? 'yok') ?>
        </span>
      </li>
      <li>
        <span>storage/ yazılabilir</span>
        <span class="system__state system__state--<?= $system['storage_ok'] ? 'ok' : 'bad' ?>">
          <?= $system['storage_ok'] ? 'evet' : 'hayir' ?>
        </span>
      </li>
      <li>
        <span>uploads/ yazılabilir</span>
        <span class="system__state system__state--<?= $system['uploads_ok'] ? 'ok' : 'bad' ?>">
          <?= $system['uploads_ok'] ? 'evet' : 'hayir' ?>
        </span>
      </li>
      <li>
        <span>Hata gösterimi</span>
        <span class="system__state system__state--<?= $system['debug_on'] ? 'bad' : 'ok' ?>">
          <?= $system['debug_on'] ? 'açık — canlıda kapatın' : 'kapali' ?>
        </span>
      </li>
      <li>
        <span>Kurulum sihirbazı</span>
        <span class="system__state system__state--<?= $system['install_open'] ? 'bad' : 'ok' ?>">
          <?= $system['install_open'] ? 'açık — kilitleyin' : 'kapali' ?>
        </span>
      </li>
    </ul>
  </section>

</div>

<div class="grid grid--2">

  <section class="panel">
    <h2 class="panel__title">En çok ziyaret edilen sayfalar</h2>
    <?php if (!$topPages): ?>
      <p class="muted">Henüz kayıt yok.</p>
    <?php else: ?>
      <table class="table">
        <thead><tr><th scope="col">Adres</th><th scope="col" class="num">Görüntüleme</th></tr></thead>
        <tbody>
        <?php foreach ($topPages as $row): ?>
          <tr>
            <td><a href="<?= Security::e($row['path']) ?>" target="_blank" rel="noopener"><?= Security::e($row['path']) ?></a></td>
            <td class="num"><?= Security::e((string) $row['views']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  <section class="panel">
    <h2 class="panel__title">Son 404 kayıtları</h2>
    <?php if (!$notFound): ?>
      <p class="muted">Kırık adres yok.</p>
    <?php else: ?>
      <table class="table">
        <thead><tr><th scope="col">Adres</th><th scope="col" class="num">İstek</th><th scope="col">Son</th></tr></thead>
        <tbody>
        <?php foreach ($notFound as $row): ?>
          <tr>
            <td><?= Security::e($row['path']) ?></td>
            <td class="num"><?= Security::e((string) $row['hits']) ?></td>
            <td><?= Security::e(format_date($row['last_seen'], true)) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <a class="btn btn--ghost btn--sm" href="<?= Security::e(admin_url('yonlendirmeler')) ?>">Yönlendirmelere git</a>
    <?php endif; ?>
  </section>

</div>
