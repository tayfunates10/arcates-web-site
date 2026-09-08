<?php
/**
 * Hizmet sayfasi sablonu. DOCS.md 4.2, 11.2
 *
 * @var array $page
 * @var array $faqs
 * @var array $relatedPages
 * @var array $crumbs
 */

declare(strict_types=1);

use Arcates\Core\ContentOutline;
use Arcates\Core\Security;

$outline = ContentOutline::prepare((string) ($page['content'] ?? ''));
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<header class="page-hero section section--tight">
  <div class="wrap">
    <span class="page__eyebrow"><?= Security::e(__('services')) ?></span>
    <h1 class="page__title"><?= Security::e($page['title']) ?></h1>
    <?php if (!empty($page['excerpt'])): ?>
      <p class="page__lead u-measure-lead"><?= Security::e($page['excerpt']) ?></p>
    <?php endif; ?>
  </div>
</header>

<article class="section section--page section--content">
  <div class="wrap">
    <div class="content-shell">
      <div class="content-main" data-outline>
        <div class="prose u-measure"><?= $outline['html'] ?></div>
      </div>

      <div class="content-aside">
        <?= partial('front/partials/toc', ['items' => $outline['items']]) ?>
        <?= partial('front/partials/related-links', [
            'items' => $relatedPages ?? [],
            'label' => __('other_services'),
        ]) ?>
        <?= partial('front/partials/aside-cta') ?>
      </div>
    </div>
  </div>
</article>

<?= partial('front/partials/faq', ['faqs' => $faqs]) ?>
<?= partial('front/partials/cta') ?>
