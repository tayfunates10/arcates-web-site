<?php
/**
 * Iletisim sayfasi — teklif formunu icerir.  DOCS.md 12
 *
 * @var array $page
 * @var array $faqs
 * @var array $crumbs
 * @var array $services
 * @var array $errors
 * @var array $old
 * @var array $flash
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<article class="section section--page section--tight">
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

<?= partial('front/partials/form', [
    'services'   => $services,
    'errors'     => $errors,
    'old'        => $old,
    'flash'      => $flash,
    'returnPath' => (string) $page['slug'],
]) ?>

<?= partial('front/partials/faq', ['faqs' => $faqs]) ?>
