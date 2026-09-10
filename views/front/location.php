<?php
/**
 * Ilce sayfasi sablonu. DOCS.md 4.3, 4.7
 *
 * @var array $page
 * @var array $faqs
 * @var array $projects
 * @var array $relatedPages
 * @var array $crumbs
 * @var array $_site
 */

declare(strict_types=1);

use Arcates\Core\ContentOutline;
use Arcates\Core\Security;
use Arcates\Core\Settings;

$outline = ContentOutline::prepare((string) ($page['content'] ?? ''));
$hours   = (array) ($_site['hours'] ?? []);
$firstHours = $hours[0] ?? null;
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<header class="page-hero section section--tight">
  <div class="wrap">
    <h1 class="page__title"><?= Security::e($page['title']) ?></h1>
    <?php if (!empty($page['excerpt'])): ?>
      <p class="page__lead u-measure-lead"><?= Security::e($page['excerpt']) ?></p>
    <?php endif; ?>

    <dl class="quick-facts" aria-label="<?= Security::e(__('quick_facts')) ?>">
      <?php if (!empty($page['district'])): ?>
        <div class="quick-facts__item">
          <dt><?= Security::e(__('district')) ?></dt>
          <dd><?= Security::e($page['district']) ?></dd>
        </div>
      <?php endif; ?>
      <?php if (($_site['phone'] ?? '') !== ''): ?>
        <div class="quick-facts__item">
          <dt><?= Security::e(__('phone')) ?></dt>
          <dd><?= Security::e($_site['phone']) ?></dd>
        </div>
      <?php endif; ?>
      <?php if (is_array($firstHours) && ($firstHours['opens'] ?? '') !== ''): ?>
        <div class="quick-facts__item">
          <dt><?= Security::e(__('opening_hours')) ?></dt>
          <dd><?= Security::e(($firstHours['days'] ?? '') . ' ' . ($firstHours['opens'] ?? '') . '–' . ($firstHours['closes'] ?? '')) ?></dd>
        </div>
      <?php endif; ?>
    </dl>
  </div>
</header>

<article class="section section--page section--content">
  <div class="wrap">
    <div class="content-shell">
      <div class="content-main" data-outline>
        <div class="prose u-measure">
          <?= $outline['html'] ?>
        </div>
      </div>

      <div class="content-aside">
        <?= partial('front/partials/toc', ['items' => $outline['items']]) ?>
        <?= partial('front/partials/aside-cta') ?>
      </div>
    </div>
  </div>
</article>

<?php if ($projects ?? []): ?>
  <section class="section section--tight section--related" data-reveal-group>
    <div class="wrap">
      <header class="section__head">
        <h2 class="section__title" data-reveal>
          <?= Security::e($page['district'] ?? '') ?> <?= Security::e(__('related_projects')) ?>
        </h2>
      </header>

      <?= partial('front/partials/notice', ['text' => Settings::get('projects_notice', Settings::defaults()['projects_notice'])]) ?>

      <ul class="works">
        <?php foreach ($projects as $project): ?>
          <li class="work" data-reveal>
            <p class="work__meta">
              <?= Security::e($project['sector'] ?? '') ?>
              <?php if (!empty($project['district'])): ?> · <?= Security::e($project['district']) ?><?php endif; ?>
            </p>
            <h3 class="work__title">
              <a href="<?= Security::e(url('/referanslar/' . $project['slug'])) ?>"><?= Security::e($project['title']) ?></a>
            </h3>
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

<?php if ($relatedPages ?? []): ?>
  <section class="section section--tight section--neighbors">
    <div class="wrap">
      <?= partial('front/partials/related-links', [
          'items' => $relatedPages,
          'label' => __('nearby_locations'),
      ]) ?>
    </div>
  </section>
<?php endif; ?>

<?= partial('front/partials/cta') ?>
