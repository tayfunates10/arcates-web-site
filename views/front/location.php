<?php
/**
 * Ilce sayfasi sablonu.  DOCS.md 4.3, 4.7
 *
 * Her ilce sayfasi en az 500 kelime ozgun metin, o ilceye ait en az bir
 * referans ve ilceye ozel SSS icermelidir.
 *
 * @var array $page
 * @var array $faqs
 * @var array $projects
 * @var array $crumbs
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<article class="section section--page">
  <div class="wrap wrap--text">
    <header class="page__head">
      <span class="page__eyebrow"><?= Security::e($page['district'] ?? __('locations')) ?></span>
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

<?php if ($projects ?? []): ?>
  <section class="section section--works" data-reveal-group>
    <div class="wrap">
      <header class="section__head">
        <h2 class="section__title" data-reveal>
          <?= Security::e($page['district'] ?? '') ?> <?= Security::e(__('related_projects')) ?>
        </h2>
      </header>

      <ul class="works">
        <?php foreach ($projects as $project): ?>
          <li class="work" data-reveal>
            <h3 class="work__title">
              <a href="<?= Security::e(url('/referanslar/' . $project['slug'])) ?>">
                <?= Security::e($project['title']) ?>
              </a>
            </h3>
            <p class="work__meta">
              <?= Security::e($project['sector'] ?? '') ?>
              <?php if (!empty($project['district'])): ?>
                · <?= Security::e($project['district']) ?>
              <?php endif; ?>
            </p>
            <?php if (!empty($project['excerpt'])): ?>
              <p class="work__text"><?= Security::e($project['excerpt']) ?></p>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
<?php endif; ?>

<?= partial('front/partials/faq', ['faqs' => $faqs]) ?>
<?= partial('front/partials/cta') ?>
