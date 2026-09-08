<?php
/**
 * Icerik basliklarindan uretilen erisilebilir icindekiler listesi.
 * Baslik hiyerarsisini degistirmemek icin ek bir h2/h3 uretmez.
 *
 * @var array $items
 */

declare(strict_types=1);

use Arcates\Core\Security;

$items = $items ?? [];
if (!$items) {
    return;
}
?>
<nav class="toc" data-toc aria-label="<?= Security::e(__('table_of_contents')) ?>">
  <p class="toc__label"><?= Security::e(__('table_of_contents')) ?></p>
  <ul class="toc__list">
    <?php foreach ($items as $item): ?>
      <li class="toc__item toc__item--l<?= (int) ($item['level'] ?? 2) ?>">
        <a class="toc__link" data-toc-link href="#<?= Security::e($item['id'] ?? '') ?>">
          <?= Security::e($item['label'] ?? '') ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
