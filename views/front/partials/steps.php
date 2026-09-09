<?php
/** Surec — R3 G-04 bagli yuzey + ozgun dekoratif vektor sahneler. */
declare(strict_types=1);
use Arcates\Core\Security;
$content = $content ?? [];
$items = (array) ($content['items'] ?? []);
if (!$items) return;

$visuals = [
    '<circle cx="17" cy="17" r="7"/><path d="M11 28c2.8-3.6 7.2-5.4 12-5.4 5 0 9.2 1.9 12 5.7"/><path d="M25 12h12a5 5 0 0 1 5 5v6a5 5 0 0 1-5 5h-3l-5 5v-5h-4"/>',
    '<rect x="6" y="9" width="36" height="28" rx="5"/><path d="M6 16h36M13 12.5h.1M18 12.5h.1M12 23h11v8H12zM28 22h8M28 27h8M28 32h5"/>',
    '<path d="M8 36 18 26l7 5 15-18"/><path d="M31 13h9v9"/><circle cx="8" cy="36" r="3"/><circle cx="18" cy="26" r="3"/><circle cx="25" cy="31" r="3"/><circle cx="40" cy="13" r="3"/>',
];
?>
<section class="section section--loose home-steps" data-reveal-group data-reveal-steps>
  <div class="wrap">
    <header class="section__head">
      <?php if (($content['title'] ?? '') !== ''): ?><h2 class="section__title" data-reveal><?= Security::e($content['title']) ?></h2><?php endif; ?>
      <?php if (($content['description'] ?? '') !== ''): ?><p class="section__lead" data-reveal><?= Security::e($content['description']) ?></p><?php endif; ?>
    </header>

    <div class="steps-surface">
      <ol class="steps steps--connected">
        <?php foreach ($items as $index => $step): ?>
          <?php $visual = $visuals[$index % count($visuals)]; ?>
          <li class="step" data-reveal>
            <div class="step__visual" aria-hidden="true">
              <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false"><?= $visual ?></svg>
            </div>
            <div class="step__marker"><span class="step__number" aria-hidden="true"><?= (int) ($index + 1) ?></span></div>
            <div class="step__content">
              <h3 class="step__title"><?= Security::e($step['title'] ?? '') ?></h3>
              <?php if (($step['text'] ?? '') !== ''): ?><p class="step__text"><?= Security::e($step['text']) ?></p><?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </div>
</section>
