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

<article class="section section--page">
  <div class="wrap wrap--text">
    <header class="page__head">
      <h1 class="page__title"><?= Security::e($page['title']) ?></h1>
      <?php if (!empty($page['excerpt'])): ?>
        <p class="page__lead"><?= Security::e($page['excerpt']) ?></p>
      <?php endif; ?>
    </header>

    <div class="prose">
      <?= Security::sanitizeHtml((string) ($page['content'] ?? '')) ?>
    </div>
  </div>
</article>

<?= partial('front/partials/faq', ['faqs' => $faqs]) ?>
