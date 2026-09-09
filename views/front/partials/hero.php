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
      <picture class="hero-scene__art">
        <source type="image/webp"
                srcset="<?= Security::e(asset('img/redesign/hero-640.webp')) ?> 640w, <?= Security::e(asset('img/redesign/hero-1280.webp')) ?> 1280w"
                sizes="(max-width: 720px) 100vw, (max-width: 940px) 560px, 46vw">
        <img class="hero-scene__image"
             src="<?= Security::e(asset('img/redesign/hero-1280.webp')) ?>"
             alt="" aria-hidden="true" width="1280" height="960"
             loading="eager" decoding="async">
      </picture>
    </div>
  </div>
</section>
