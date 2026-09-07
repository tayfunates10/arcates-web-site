<?php
/**
 * Sektor sayfasi sablonu.  DOCS.md 4.4
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
      <span class="page__eyebrow"><?= Security::e(__('sectors')) ?></span>
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
<?= partial('front/partials/cta') ?>
