<?php
/**
 * Ornek site detayi. DOCS.md 9.4, 11.2
 *
 * @var array $project
 * @var array $related
 * @var array $crumbs
 */

declare(strict_types=1);

use Arcates\Core\Media;
use Arcates\Core\Security;
use Arcates\Core\Settings;
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<header class="page-hero section section--tight">
  <div class="wrap">
    <h1 class="page__title u-measure"><?= Security::e($project['title']) ?></h1>
    <?php if (!empty($project['excerpt'])): ?>
      <p class="page__lead u-measure-lead"><?= Security::e($project['excerpt']) ?></p>
    <?php endif; ?>
    <?= partial('front/partials/notice', ['text' => Settings::get('projects_notice', Settings::defaults()['projects_notice'])]) ?>
  </div>
</header>

<article class="section section--page section--content">
  <div class="wrap">
    <dl class="project-meta">
      <div>
        <dt><?= Security::e(__('client')) ?></dt>
        <dd><?= Security::e($project['client_name']) ?></dd>
      </div>
      <?php if (!empty($project['sector'])): ?>
        <div><dt><?= Security::e(__('sector')) ?></dt><dd><?= Security::e($project['sector']) ?></dd></div>
      <?php endif; ?>
      <?php if (!empty($project['district'])): ?>
        <div><dt><?= Security::e(__('district')) ?></dt><dd><?= Security::e($project['district']) ?></dd></div>
      <?php endif; ?>
      <?php if (!empty($project['live_url'])): ?>
        <div>
          <dt><?= Security::e(__('visit_site')) ?></dt>
          <dd><a href="<?= Security::e(Security::url((string) $project['live_url'])) ?>" target="_blank" rel="noopener nofollow"><?= Security::e(parse_url((string) $project['live_url'], PHP_URL_HOST) ?: $project['live_url']) ?></a></dd>
        </div>
      <?php endif; ?>
    </dl>

    <div class="project-detail">
      <?php if (!empty($project['cover'])): ?>
        <figure class="project-shot project-shot--detail">
          <img class="project-cover"
               src="<?= Security::e(Media::url((string) $project['cover']['path'])) ?>"
               alt="<?= Security::e($project['cover']['alt'] ?: $project['title']) ?>"
               width="<?= (int) $project['cover']['width'] ?>" height="<?= (int) $project['cover']['height'] ?>"
               decoding="async">
        </figure>
      <?php endif; ?>

      <div class="project-story">
        <div class="prose u-measure"><?= Security::sanitizeHtml((string) ($project['content'] ?? '')) ?></div>
      </div>

      <?php if (!empty($project['gallery'])): ?>
        <ul class="gallery">
          <?php foreach ($project['gallery'] as $image): ?>
            <?php $variant = $image['variants']['medium'] ?? null; ?>
            <li class="project-gallery__frame">
              <img src="<?= Security::e(Media::url((string) ($variant['path'] ?? $image['path']))) ?>"
                   alt="<?= Security::e($image['alt'] ?: $project['title']) ?>"
                   width="<?= (int) ($variant['width'] ?? $image['width'] ?? 768) ?>"
                   height="<?= (int) ($variant['height'] ?? $image['height'] ?? 480) ?>"
                   loading="lazy" decoding="async">
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</article>

<?php if ($related): ?>
  <section class="section section--tight section--related" data-reveal-group>
    <div class="wrap">
      <header class="section__head"><h2 class="section__title" data-reveal><?= Security::e(__('related_projects')) ?></h2></header>
      <ul class="works">
        <?php foreach ($related as $item): ?>
          <li class="work" data-reveal>
            <p class="work__meta"><?= Security::e($item['sector'] ?? '') ?></p>
            <h3 class="work__title"><a href="<?= Security::e(url('/referanslar/' . $item['slug'])) ?>"><?= Security::e($item['title']) ?></a></h3>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
<?php endif; ?>

<?= partial('front/partials/cta') ?>
