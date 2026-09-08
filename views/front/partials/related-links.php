<?php
/**
 * Ilgili hizmet, sektor veya ilce baglantilari.
 *
 * @var array  $items
 * @var string $label
 */

declare(strict_types=1);

use Arcates\Core\Security;

$items = $items ?? [];
$label = $label ?? '';
if (!$items || $label === '') {
    return;
}
?>
<nav class="related-links" aria-label="<?= Security::e($label) ?>">
  <p class="related-links__label"><?= Security::e($label) ?></p>
  <ul class="related-links__list">
    <?php foreach ($items as $item): ?>
      <li>
        <a href="<?= Security::e(url('/' . ltrim((string) ($item['slug'] ?? ''), '/'))) ?>">
          <?= Security::e($item['title'] ?? '') ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
