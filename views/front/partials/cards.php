<?php
/** Hizmet kartlari — R5 tek mavi gorsel aile. */
declare(strict_types=1);
use Arcates\Core\Security;
$content = $content ?? [];
$cards = (array) ($content['cards'] ?? []);
if (!$cards) return;
$icons = [
    'layout' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M9 9v11"/>',
    'cart' => '<circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/><path d="M2 3h3l2.6 12.4a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 7H6"/>',
    'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
    'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/>',
    'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.7 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.7-3.8-9S9.5 5.6 12 3Z"/>',
    'shield' => '<path d="M12 3l7 3v5.5c0 4.3-2.9 8.3-7 9.5-4.1-1.2-7-5.2-7-9.5V6l7-3Z"/><path d="m9 12 2.2 2.2L15.5 10"/>',
];
?>
<section class="section section--loose home-services" data-reveal-group>
  <div class="wrap">
    <header class="section__head home-services__head">
      <?php if (($content['title'] ?? '') !== ''): ?><h2 class="section__title" data-reveal><?= Security::e($content['title']) ?></h2><?php endif; ?>
      <?php if (($content['description'] ?? '') !== ''): ?><p class="section__lead" data-reveal><?= Security::e($content['description']) ?></p><?php endif; ?>
    </header>
    <ul class="cards cards--services">
      <?php foreach ($cards as $index => $card): ?>
        <?php $icon = $icons[(string) ($card['icon'] ?? '')] ?? $icons['layout']; ?>
        <li class="card service-card" data-reveal>
          <span class="service-card__index" aria-hidden="true"><?= (int) ($index + 1) ?></span>
          <span class="card__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" focusable="false"><?= $icon ?></svg></span>
          <h3 class="card__title">
            <?php if (($card['url'] ?? '') !== ''): ?><a href="<?= Security::e(url((string) $card['url'])) ?>"><?= Security::e($card['title'] ?? '') ?></a><?php else: ?><?= Security::e($card['title'] ?? '') ?><?php endif; ?>
          </h3>
          <?php if (($card['text'] ?? '') !== ''): ?><p class="card__text"><?= Security::e($card['text']) ?></p><?php endif; ?>
          <?php if (($card['url'] ?? '') !== ''): ?>
            <span class="service-card__more" aria-hidden="true">→</span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
