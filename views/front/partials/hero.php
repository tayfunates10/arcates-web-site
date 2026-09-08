<?php
/**
 * Ana sayfa hero — R5 tam yeniden tasarim.
 *
 * H1 paneldeki uc satirlik icerigi kullanir. Sag sahne temsili web + mobil
 * arayuz ve Arcates baglanti geometrisidir; metrik/musteri verisi uretmez.
 * Dekoratif oldugu icin tek grup halinde aria-hidden tasir.
 */
declare(strict_types=1);

use Arcates\Core\Security;

$content = $content ?? [];
$lines = array_values(array_filter([
    (string) ($content['line1'] ?? ''),
    (string) ($content['line2'] ?? ''),
    (string) ($content['line3'] ?? ''),
], static fn (string $line): bool => $line !== ''));
$cta1 = $content['cta1'] ?? null;
$cta2 = $content['cta2'] ?? null;
?>
<section class="hero hero--redesign">
  <div class="wrap hero__grid">
    <div class="hero__body">
      <h1 class="hero__title">
        <?php foreach ($lines as $index => $line): ?>
          <span class="hero__line hero__line--<?= (int) ($index + 1) ?><?= $index === 2 ? ' hero__line--accent' : '' ?>">
            <span><?= Security::e($line) ?></span>
          </span>
        <?php endforeach; ?>
      </h1>

      <?php if (($content['description'] ?? '') !== ''): ?>
        <p class="hero__text"><?= Security::e($content['description']) ?></p>
      <?php endif; ?>

      <div class="hero__actions">
        <?php if ($cta1 !== null && ($cta1['label'] ?? '') !== ''): ?>
          <a class="btn btn--primary" href="<?= Security::e(url((string) ($cta1['url'] ?? '/iletisim#teklif-formu'))) ?>">
            <?= Security::e($cta1['label']) ?>
          </a>
        <?php endif; ?>
        <?php if ($cta2 !== null && ($cta2['label'] ?? '') !== ''): ?>
          <a class="btn btn--ghost btn--hero-secondary" href="<?= Security::e(url((string) ($cta2['url'] ?? '/referanslar'))) ?>">
            <?= Security::e($cta2['label']) ?>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <div class="hero-scene" aria-hidden="true">
      <svg class="hero-scene__orbit" viewBox="0 0 620 520" focusable="false">
        <path d="M82 430 C118 228 258 70 532 78 C578 80 604 118 588 160 C548 264 430 394 220 450"></path>
        <circle cx="91" cy="411" r="7"></circle>
        <circle cx="531" cy="79" r="7"></circle>
      </svg>

      <div class="hero-scene__web">
        <div class="ui-window__bar"><span></span><span></span><span></span></div>
        <div class="ui-window__nav"><b></b><i></i><i></i><i></i></div>
        <div class="ui-window__hero">
          <div><strong></strong><strong></strong><em></em><small></small></div>
          <span class="ui-window__visual"></span>
        </div>
        <div class="ui-window__cards"><span></span><span></span><span></span></div>
      </div>

      <div class="hero-scene__mobile">
        <div class="ui-mobile__speaker"></div>
        <div class="ui-mobile__screen">
          <b></b><strong></strong><strong></strong><em></em>
          <div><span></span><span></span></div>
        </div>
      </div>

      <div class="hero-scene__mark">
        <img src="/assets/img/logo-mark-light.png" alt="" width="64" height="64" decoding="async">
      </div>
    </div>
  </div>
</section>
