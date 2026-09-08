<?php
/**
 * Blog yazisi. DOCS.md 4.1, 11.2
 *
 * @var array $post
 * @var array $latest
 * @var array $crumbs
 */

declare(strict_types=1);

use Arcates\Core\ContentOutline;
use Arcates\Core\Media;
use Arcates\Core\Security;

$outline = ContentOutline::prepare((string) ($post['content'] ?? ''));
$minutes = max(1, (int) ceil(((int) ($post['word_count'] ?? 0)) / 200));
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<header class="page-hero section section--tight">
  <div class="wrap">
    <h1 class="page__title u-measure"><?= Security::e($post['title']) ?></h1>
    <p class="post__meta">
      <time datetime="<?= Security::e(date('Y-m-d', strtotime((string) ($post['published_at'] ?: 'now')))) ?>">
        <?= Security::e(format_date($post['published_at'])) ?>
      </time>
      <span>· <?= Security::e(__('reading_time', ['minutes' => $minutes])) ?></span>
      <?php if (!empty($post['author_name'])): ?><span>· <?= Security::e($post['author_name']) ?></span><?php endif; ?>
    </p>
    <?php if (!empty($post['excerpt'])): ?>
      <p class="page__lead u-measure-lead"><?= Security::e($post['excerpt']) ?></p>
    <?php endif; ?>
  </div>
</header>

<article class="section section--page section--content">
  <div class="wrap">
    <div class="article-shell">
      <div class="article-main" data-outline>
        <?php if (!empty($post['cover'])): ?>
          <img class="project-cover"
               src="<?= Security::e(Media::url((string) $post['cover']['path'])) ?>"
               alt="<?= Security::e($post['cover']['alt'] ?: $post['title']) ?>"
               width="<?= (int) $post['cover']['width'] ?>"
               height="<?= (int) $post['cover']['height'] ?>"
               decoding="async">
        <?php endif; ?>

        <div class="prose u-measure"><?= $outline['html'] ?></div>
      </div>

      <aside class="article-aside">
        <?= partial('front/partials/toc', ['items' => $outline['items']]) ?>

        <?php if ($latest): ?>
          <nav class="latest-list" aria-label="<?= Security::e(__('latest_posts')) ?>">
            <p class="latest-list__label"><?= Security::e(__('latest_posts')) ?></p>
            <ul>
              <?php foreach (array_slice($latest, 0, 3) as $item): ?>
                <li>
                  <a href="<?= Security::e(url('/blog/' . $item['slug'])) ?>"><?= Security::e($item['title']) ?></a>
                  <span><?= Security::e(format_date($item['published_at'])) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </nav>
        <?php endif; ?>

        <?= partial('front/partials/aside-cta') ?>
      </aside>
    </div>
  </div>
</article>

<?= partial('front/partials/cta') ?>
