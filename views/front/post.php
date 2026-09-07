<?php
/**
 * Blog yazisi.  DOCS.md 4.1, 11.2 (Article)
 *
 * @var array $post
 * @var array $latest
 * @var array $crumbs
 */

declare(strict_types=1);

use Arcates\Core\Media;
use Arcates\Core\Security;
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<article class="section section--page">
  <div class="wrap wrap--text">
    <header class="page__head">
      <?php if (!empty($post['category'])): ?>
        <span class="page__eyebrow"><?= Security::e($post['category']) ?></span>
      <?php endif; ?>

      <h1 class="page__title"><?= Security::e($post['title']) ?></h1>

      <p class="post__meta">
        <time datetime="<?= Security::e(date('Y-m-d', strtotime((string) ($post['published_at'] ?: 'now')))) ?>">
          <?= Security::e(format_date($post['published_at'])) ?>
        </time>
        <?php if (!empty($post['author_name'])): ?>
          · <?= Security::e($post['author_name']) ?>
        <?php endif; ?>
      </p>

      <?php if (!empty($post['excerpt'])): ?>
        <p class="page__lead"><?= Security::e($post['excerpt']) ?></p>
      <?php endif; ?>
    </header>

    <?php if (!empty($post['cover'])): ?>
      <img class="project-cover"
           src="<?= Security::e(Media::url((string) $post['cover']['path'])) ?>"
           alt="<?= Security::e($post['cover']['alt'] ?: $post['title']) ?>"
           width="<?= (int) $post['cover']['width'] ?>"
           height="<?= (int) $post['cover']['height'] ?>"
           decoding="async">
    <?php endif; ?>

    <div class="prose">
      <?= Security::sanitizeHtml((string) ($post['content'] ?? '')) ?>
    </div>
  </div>
</article>

<?php if ($latest): ?>
  <section class="section section--tight" data-reveal-group>
    <div class="wrap">
      <header class="section__head">
        <h2 class="section__title" data-reveal><?= Security::e(__('latest_posts')) ?></h2>
      </header>

      <ul class="works">
        <?php foreach (array_slice($latest, 0, 3) as $item): ?>
          <li class="work" data-reveal>
            <h3 class="work__title">
              <a href="<?= Security::e(url('/blog/' . $item['slug'])) ?>"><?= Security::e($item['title']) ?></a>
            </h3>
            <p class="work__meta"><?= Security::e(format_date($item['published_at'])) ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
<?php endif; ?>

<?= partial('front/partials/cta') ?>
