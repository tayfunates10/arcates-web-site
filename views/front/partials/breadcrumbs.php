<?php
/**
 * Kirinti yolu — gorsel karsiligi. Sema karsiligi head icinde uretilir.
 * DOCS.md 11.1, 11.2
 *
 * @var array $crumbs
 */

declare(strict_types=1);

use Arcates\Core\Security;

if (count($crumbs ?? []) < 2) {
    return;
}
?>
<nav class="crumbs" aria-label="<?= Security::e(__('breadcrumb')) ?>">
  <div class="wrap">
    <ol class="crumbs__list">
      <?php foreach ($crumbs as $index => $crumb): ?>
        <li class="crumbs__item">
          <?php if (($crumb['url'] ?? null) !== null && $index < count($crumbs) - 1): ?>
            <a href="<?= Security::e($crumb['url']) ?>"><?= Security::e($crumb['label']) ?></a>
          <?php else: ?>
            <span aria-current="page"><?= Security::e($crumb['label']) ?></span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</nav>
