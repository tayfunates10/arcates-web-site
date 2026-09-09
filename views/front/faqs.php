<?php
/**
 * SSS sayfasi.  DOCS.md 4.1, 11.2
 *
 * @var array|null $page
 * @var array      $faqs
 * @var array      $crumbs
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<header class="page-hero page-hero--simple section section--tight">
  <div class="wrap">
    <h1 class="page__title"><?= Security::e($page['title'] ?? __('faq')) ?></h1>
    <?php if (!empty($page['excerpt'])): ?>
      <p class="page__lead u-measure-lead"><?= Security::e($page['excerpt']) ?></p>
    <?php endif; ?>
  </div>
</header>

<?php if (!empty($page['content'])): ?>
  <section class="section section--page section--content">
    <div class="wrap wrap--text">
      <div class="prose"><?= Security::sanitizeHtml((string) $page['content']) ?></div>
    </div>
  </section>
<?php endif; ?>

<?php if (!$faqs): ?>
  <section class="section section--tight">
    <div class="wrap wrap--text">
      <div class="empty-state" role="status">
        <span class="state-mark state-mark--empty" aria-hidden="true"><span class="state-mark__glyph"></span></span>
        <div class="empty-state__copy"><p><?= Security::e(__('no_results')) ?></p></div>
      </div>
    </div>
  </section>
<?php else: ?>
  <section class="section section--faq" data-reveal-group>
    <div class="wrap">
      <div class="faq">
        <?php foreach ($faqs as $faq): ?>
          <details class="faq__item" data-reveal>
            <summary class="faq__question"><?= Security::e($faq['question']) ?></summary>
            <div class="faq__answer"><?= Security::sanitizeHtml((string) $faq['answer']) ?></div>
          </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?= partial('front/partials/cta') ?>
