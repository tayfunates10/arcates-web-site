<?php
/** Surec — R5 tek bagli yuzey, numarali uc adim. */
declare(strict_types=1);
use Arcates\Core\Security;
$content = $content ?? [];
$items = (array) ($content['items'] ?? []);
if (!$items) return;
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
          <li class="step" data-reveal>
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
