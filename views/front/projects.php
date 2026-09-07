<?php
/**
 * Referans listesi.  DOCS.md 4.1
 *
 * @var array|null $page
 * @var array      $projects
 * @var array      $crumbs
 */

declare(strict_types=1);

use Arcates\Core\Security;
use Arcates\Core\Settings;
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<section class="section section--tight">
  <div class="wrap">
    <header class="page__head">
      <h1 class="page__title"><?= Security::e($page['title'] ?? __('projects')) ?></h1>
      <?php if (!empty($page['excerpt'])): ?>
        <p class="page__lead"><?= Security::e($page['excerpt']) ?></p>
      <?php endif; ?>
    </header>

    <?= partial('front/partials/notice', ['text' => Settings::get('projects_notice', '')]) ?>

    <?php if (!empty($page['content'])): ?>
      <div class="prose wrap--text"><?= Security::sanitizeHtml((string) $page['content']) ?></div>
    <?php endif; ?>
  </div>
</section>

<section class="section section--tight" data-reveal-group>
  <div class="wrap">
    <?php if (!$projects): ?>
      <p class="muted"><?= Security::e(__('no_results')) ?></p>
    <?php else: ?>
      <ul class="works">
        <?php foreach ($projects as $project): ?>
          <li class="work" data-reveal>
            <?php if (!empty($project['cover'])): ?>
              <div class="work__media">
                <img src="<?= Security::e(Arcates\Core\Media::url((string) $project['cover']['path'])) ?>"
                     alt="<?= Security::e($project['cover']['alt'] ?: $project['title']) ?>"
                     width="<?= (int) $project['cover']['width'] ?>"
                     height="<?= (int) $project['cover']['height'] ?>"
                     loading="lazy" decoding="async">
              </div>
            <?php endif; ?>

            <h2 class="work__title">
              <a href="<?= Security::e(url('/referanslar/' . $project['slug'])) ?>">
                <?= Security::e($project['title']) ?>
              </a>
            </h2>

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
    <?php endif; ?>
  </div>
</section>

<?= partial('front/partials/cta') ?>
