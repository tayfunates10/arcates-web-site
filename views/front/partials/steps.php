<?php
/**
 * Surec adimlari — 3 adim: baslik, metin.  DOCS.md 5 (bolum 6), 7.2
 *
 * @var array $content
 */

declare(strict_types=1);

use Arcates\Core\Security;

$content = $content ?? [];
$items   = (array) ($content['items'] ?? []);

if (!$items) {
    return;
}
?>
<section class="section" data-reveal-group data-reveal-steps>
  <div class="wrap">
    <header class="section__head">
      <?php if (($content['title'] ?? '') !== ''): ?>
        <h2 class="section__title" data-reveal><?= Security::e($content['title']) ?></h2>
      <?php endif; ?>
    </header>

    <ol class="steps">
      <?php foreach ($items as $index => $step): ?>
        <li class="step" data-reveal>
          <span class="step__number" aria-hidden="true"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
          <h3 class="step__title"><?= Security::e($step['title'] ?? '') ?></h3>
          <?php if (($step['text'] ?? '') !== ''): ?>
            <p class="step__text"><?= Security::e($step['text']) ?></p>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
