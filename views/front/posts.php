<?php
/**
 * Blog listesi. DOCS.md 4.1, 9.4
 *
 * @var array|null $page
 * @var array      $posts
 * @var array      $categories
 * @var string     $category
 * @var int        $number
 * @var int        $pages
 * @var array      $crumbs
 */

declare(strict_types=1);

use Arcates\Core\Media;
use Arcates\Core\Security;
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<header class="page-hero section section--tight">
  <div class="wrap">
    <h1 class="page__title"><?= Security::e($page['title'] ?? __('blog')) ?></h1>
    <?php if (!empty($page['excerpt'])): ?>
      <p class="page__lead u-measure"><?= Security::e($page['excerpt']) ?></p>
    <?php endif; ?>

    <?php if ($categories): ?>
      <nav class="chips" aria-label="<?= Security::e(__('category')) ?>">
        <a class="chips__item<?= $category === '' ? ' is-current' : '' ?>" href="<?= Security::e(url('/blog')) ?>">
          <?= Security::e(__('all')) ?>
        </a>
        <?php foreach ($categories as $row): ?>
          <a class="chips__item<?= $category === $row['category'] ? ' is-current' : '' ?>"
             href="<?= Security::e(url('/blog') . '?kategori=' . rawurlencode((string) $row['category'])) ?>">
            <?= Security::e($row['category']) ?> (<?= (int) $row['total'] ?>)
          </a>
        <?php endforeach; ?>
      </nav>
    <?php endif; ?>
  </div>
</header>

<section class="section section--content" data-reveal-group>
  <div class="wrap">
    <?php if (!$posts): ?>
      <p class="muted"><?= Security::e(__('no_results')) ?></p>
    <?php else: ?>
      <ul class="post-grid">
        <?php foreach ($posts as $post): ?>
          <?php $minutes = max(1, (int) ceil(((int) ($post['word_count'] ?? 0)) / 200)); ?>
          <li class="post-card" data-reveal>
            <?php if (!empty($post['cover'])): ?>
              <a class="post-card__media" href="<?= Security::e(url('/blog/' . $post['slug'])) ?>" tabindex="-1" aria-hidden="true">
                <img src="<?= Security::e(Media::url((string) $post['cover']['path'])) ?>"
                     alt="" width="<?= (int) $post['cover']['width'] ?>" height="<?= (int) $post['cover']['height'] ?>"
                     loading="lazy" decoding="async">
              </a>
            <?php else: ?>
              <a class="post-card__media post-card__media--fallback" href="<?= Security::e(url('/blog/' . $post['slug'])) ?>" tabindex="-1" aria-hidden="true">
                <?= partial('front/partials/editorial-cover', ['category' => (string) ($post['category'] ?? '')]) ?>
              </a>
            <?php endif; ?>

            <div class="post-card__body">
              <div class="post-card__meta">
                <?php if (!empty($post['category'])): ?>
                  <span class="badge"><?= Security::e($post['category']) ?></span>
                <?php endif; ?>
                <time datetime="<?= Security::e(date('Y-m-d', strtotime((string) ($post['published_at'] ?: 'now')))) ?>">
                  <?= Security::e(format_date($post['published_at'])) ?>
                </time>
                <span><?= Security::e(__('reading_time', ['minutes' => $minutes])) ?></span>
              </div>

              <h2 class="post-card__title">
                <a href="<?= Security::e(url('/blog/' . $post['slug'])) ?>"><?= Security::e($post['title']) ?></a>
              </h2>

              <?php if (!empty($post['excerpt'])): ?>
                <p class="post-card__text"><?= Security::e($post['excerpt']) ?></p>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <?php if ($pages > 1): ?>
      <nav class="pager" aria-label="<?= Security::e(__('pagination')) ?>">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <?php $query = ($category !== '' ? 'kategori=' . rawurlencode($category) . '&' : '') . 'sayfa=' . $i; ?>
          <?php if ($i === $number): ?>
            <span class="pager__item is-current" aria-current="page"><?= (int) $i ?></span>
          <?php else: ?>
            <a class="pager__item" href="<?= Security::e(url('/blog') . '?' . $query) ?>"><?= (int) $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>
  </div>
</section>
