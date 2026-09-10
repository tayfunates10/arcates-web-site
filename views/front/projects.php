<?php
/**
 * Ornek site listesi. DOCS.md 4.1
 *
 * @var array|null $page
 * @var array      $projects
 * @var array      $crumbs
 */

declare(strict_types=1);

use Arcates\Core\Media;
use Arcates\Core\Security;
use Arcates\Core\Settings;
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<header class="page-hero section section--tight">
  <div class="wrap">
    <h1 class="page__title"><?= Security::e($page['title'] ?? __('projects')) ?></h1>
    <?php if (!empty($page['excerpt'])): ?>
      <p class="page__lead u-measure-lead"><?= Security::e($page['excerpt']) ?></p>
    <?php endif; ?>

    <?= partial('front/partials/notice', ['text' => Settings::get('projects_notice', Settings::defaults()['projects_notice'])]) ?>

    <?php if (!empty($page['content'])): ?>
      <div class="prose u-measure project-intro"><?= Security::sanitizeHtml((string) $page['content']) ?></div>
    <?php endif; ?>
  </div>
</header>

<section class="section section--content" data-reveal-group>
  <div class="wrap">
    <?php if (!$projects): ?>
      <div class="empty-state" role="status">
        <span class="state-mark state-mark--empty" aria-hidden="true"><span class="state-mark__glyph"></span></span>
        <div class="empty-state__copy"><p><?= Security::e(__('no_results')) ?></p></div>
      </div>
    <?php else: ?>
      <ul class="project-grid">
        <?php foreach ($projects as $project): ?>
          <li class="project-card" data-reveal>
            <?php if (!empty($project['cover'])): ?>
              <a class="project-card__media project-shot" href="<?= Security::e(url('/referanslar/' . $project['slug'])) ?>" tabindex="-1" aria-hidden="true">
                <img src="<?= Security::e(Media::url((string) $project['cover']['path'])) ?>"
                     alt="" width="<?= (int) $project['cover']['width'] ?>" height="<?= (int) $project['cover']['height'] ?>"
                     loading="lazy" decoding="async">
              </a>
            <?php endif; ?>

            <div class="project-card__body">
              <p class="project-card__meta">
                <?php if (!empty($project['sector'])): ?><span class="badge"><?= Security::e($project['sector']) ?></span><?php endif; ?>
                <?php if (!empty($project['district'])): ?><span><?= Security::e($project['district']) ?></span><?php endif; ?>
              </p>
              <h2 class="project-card__title">
                <a href="<?= Security::e(url('/referanslar/' . $project['slug'])) ?>"><?= Security::e($project['title']) ?></a>
              </h2>
              <?php if (!empty($project['excerpt'])): ?>
                <p class="project-card__text"><?= Security::e($project['excerpt']) ?></p>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</section>

<?= partial('front/partials/cta') ?>
