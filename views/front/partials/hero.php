<?php
/**
 * Kahraman bolumu.  DOCS.md 5 (bolum 2), 5.1, 7.3
 *
 * H1 uc satirdan olusur, ucuncu satir degrade renklidir. Degrade metin
 * `background-clip:text` ile uretilir; gorsel kullanilmaz, arama motorunda
 * okunabilir kalir.
 *
 * Sagdaki sekil kumesi tamamen dekoratiftir, `aria-hidden="true"` tasir.
 *
 * @var array $content Panelden gelen bolum icerigi.
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
<section class="hero">
  <div class="wrap hero__grid">

    <div class="hero__body">
      <?php /* Tek H1; satirlar maskeli, gorunurluk JavaScript'e bagli degil. */ ?>
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
          <a class="btn btn--primary" href="<?= Security::e(url((string) ($cta1['url'] ?? '/iletisim'))) ?>">
            <?= Security::e($cta1['label']) ?>
          </a>
        <?php endif; ?>

        <?php if ($cta2 !== null && ($cta2['label'] ?? '') !== ''): ?>
          <a class="btn btn--ghost" href="<?= Security::e(url((string) ($cta2['url'] ?? '/referanslar'))) ?>">
            <?= Security::e($cta2['label']) ?>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <?php /* Dekoratif sekil kumesi — icerige bilgi eklemez. DOCS.md 5.1 */ ?>
    <div class="shapes" aria-hidden="true">
      <div class="shape shape--card" data-depth="1.0">
        <span class="shape__row"></span>
        <span class="shape__row"></span>
        <span class="shape__row"></span>
        <span class="shape__chart">
          <span></span><span></span><span></span><span></span><span></span>
        </span>
      </div>

      <div class="shape shape--circle" data-depth="1.6"></div>
      <div class="shape shape--ring" data-depth="0.8"></div>
      <div class="shape shape--square" data-depth="1.3"></div>
      <div class="shape shape--pill" data-depth="0.6"></div>
      <div class="shape shape--triangle" data-depth="1.1"></div>

      <svg class="shape shape--line" viewBox="0 0 240 120" data-depth="0.4" focusable="false">
        <path d="M6 96 L48 70 L88 82 L128 44 L172 56 L232 14"></path>
      </svg>
    </div>

  </div>
</section>
