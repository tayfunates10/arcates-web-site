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

<section class="section section--tight">
  <div class="wrap wrap--text">
    <header class="page__head">
      <h1 class="page__title"><?= Security::e($page['title'] ?? __('faq')) ?></h1>
      <?php if (!empty($page['excerpt'])): ?>
        <p class="page__lead"><?= Security::e($page['excerpt']) ?></p>
      <?php endif; ?>
    </header>

    <?php if (!empty($page['content'])): ?>
      <div class="prose"><?= Security::sanitizeHtml((string) $page['content']) ?></div>
    <?php endif; ?>
  </div>
</section>

<?php if (!$faqs): ?>
  <section class="section section--tight">
    <div class="wrap wrap--text"><p class="muted"><?= Security::e(__('no_results')) ?></p></div>
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
