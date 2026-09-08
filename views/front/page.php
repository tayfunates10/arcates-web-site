<?php
/**
 * Genel sayfa sablonu.  DOCS.md 4.1
 *
 * Tek H1, H2 ile bolumlenmis yapi.  DOCS.md 11.1, testler O-01, E-05
 *
 * @var array $page
 * @var array $faqs
 * @var array $crumbs
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<header class="page-hero page-hero--simple section section--tight">
  <div class="wrap">
    <h1 class="page__title"><?= Security::e($page['title']) ?></h1>
    <?php if (!empty($page['excerpt'])): ?>
      <p class="page__lead u-measure-lead"><?= Security::e($page['excerpt']) ?></p>
    <?php endif; ?>
  </div>
</header>

<article class="section section--page section--content">
  <div class="wrap wrap--text">
    <div class="prose">
      <?= Security::sanitizeHtml((string) ($page['content'] ?? '')) ?>
    </div>
  </div>
</article>

<?= partial('front/partials/faq', ['faqs' => $faqs]) ?>
