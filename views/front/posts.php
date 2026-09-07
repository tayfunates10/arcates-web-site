<?php
/**
 * Blog listesi.  DOCS.md 4.1, 9.4
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

<section class="section section--tight">
  <div class="wrap">
    <header class="page__head">
      <h1 class="page__title"><?= Security::e($page['title'] ?? __('blog')) ?></h1>
      <?php if (!empty($page['excerpt'])): ?>
        <p class="page__lead"><?= Security::e($page['excerpt']) ?></p>
      <?php endif; ?>
    </header>

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
</section>

<section class="section section--tight" data-reveal-group>
  <div class="wrap">
    <?php if (!$posts): ?>
      <p class="muted"><?= Security::e(__('no_results')) ?></p>
    <?php else: ?>
      <ul class="works">
        <?php foreach ($posts as $post): ?>
          <li class="work" data-reveal>
            <?php if (!empty($post['cover'])): ?>
              <div class="work__media">
                <img src="<?= Security::e(Media::url((string) $post['cover']['path'])) ?>"
                     alt="<?= Security::e($post['cover']['alt'] ?: $post['title']) ?>"
                     width="<?= (int) $post['cover']['width'] ?>"
                     height="<?= (int) $post['cover']['height'] ?>"
                     loading="lazy" decoding="async">
              </div>
            <?php endif; ?>

            <h2 class="work__title">
              <a href="<?= Security::e(url('/blog/' . $post['slug'])) ?>"><?= Security::e($post['title']) ?></a>
            </h2>

            <p class="work__meta">
              <?php if (!empty($post['category'])): ?><?= Security::e($post['category']) ?> · <?php endif; ?>
              <time datetime="<?= Security::e(date('Y-m-d', strtotime((string) ($post['published_at'] ?: 'now')))) ?>">
                <?= Security::e(format_date($post['published_at'])) ?>
              </time>
            </p>

            <?php if (!empty($post['excerpt'])): ?>
              <p class="work__text"><?= Security::e($post['excerpt']) ?></p>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <?php if ($pages > 1): ?>
      <nav class="pager" aria-label="Sayfalar">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <?php
          $query = ($category !== '' ? 'kategori=' . rawurlencode($category) . '&' : '') . 'sayfa=' . $i;
          ?>
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
