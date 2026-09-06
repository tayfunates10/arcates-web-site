<?php
/**
 * Medya izgarasi.  DOCS.md 9.5
 *
 * @var array  $items
 * @var int    $total
 * @var int    $page
 * @var int    $pages
 * @var string $lang
 * @var array  $langs
 * @var int    $maxSize
 * @var array  $allowed
 */

declare(strict_types=1);

use Arcates\Core\Media;
use Arcates\Core\Security;
?>

<section class="panel">
  <h2 class="panel__title">Dosya yukle</h2>

  <form method="post" action="<?= Security::e(admin_url('medya/yukle')) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="field">
      <label for="files">Dosyalar</label>
      <input type="file" id="files" name="files[]" multiple
             accept="<?= Security::e('.' . implode(',.', $allowed)) ?>">
      <span class="field__hint">
        Kabul edilen turler: <?= Security::e(implode(', ', $allowed)) ?>.
        En fazla <?= Security::e(format_bytes($maxSize)) ?>.
        Yuklenen goruntuler icin thumb, medium, large ve WebP karsiliklari uretilir.
      </span>
    </div>

    <div class="form__actions">
      <button class="btn btn--primary" type="submit">Yukle</button>
    </div>
  </form>
</section>

<section class="panel">
  <div class="panel__head">
    <h2 class="panel__title"><?= Security::e((string) $total) ?> dosya</h2>
  </div>

  <?php if (!$items): ?>
    <p class="muted">Henuz dosya yuklenmemis.</p>
  <?php else: ?>
    <ul class="media-grid">
      <?php foreach ($items as $item): ?>
        <?php
        $isImage  = str_starts_with((string) $item['mime'], 'image/');
        $thumb    = $item['variants']['thumb']['path'] ?? $item['path'];
        $altText  = (string) ($item['alt'] ?? '');
        ?>
        <li class="media-card">
          <a class="media-card__frame" href="<?= Security::e(admin_url('medya/' . (int) $item['id'])) ?>">
            <?php if ($isImage): ?>
              <img src="<?= Security::e(Media::url($thumb)) ?>"
                   alt="<?= Security::e($altText !== '' ? $altText : (string) $item['filename']) ?>"
                   width="<?= (int) ($item['variants']['thumb']['width'] ?? $item['width'] ?? 320) ?>"
                   height="<?= (int) ($item['variants']['thumb']['height'] ?? $item['height'] ?? 240) ?>"
                   loading="lazy" decoding="async">
            <?php else: ?>
              <span class="media-card__file"><?= Security::e(strtoupper((string) pathinfo((string) $item['filename'], PATHINFO_EXTENSION))) ?></span>
            <?php endif; ?>
          </a>

          <div class="media-card__body">
            <?php if ($altText === '' && $isImage): ?>
              <span class="tag tag--warn">Alt metni eksik</span>
            <?php endif; ?>

            <p class="media-card__name"><?= Security::e($item['filename']) ?></p>
            <p class="media-card__meta">
              <?= Security::e(format_bytes((int) $item['size'])) ?>
              <?php if (!empty($item['width'])): ?>
                · <?= (int) $item['width'] ?>×<?= (int) $item['height'] ?>
              <?php endif; ?>
              <?php if ($item['variants']): ?>
                · <?= Security::e((string) count($item['variants'])) ?> varyant
              <?php endif; ?>
            </p>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if ($pages > 1): ?>
    <nav class="pager" aria-label="Sayfalar">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <?php if ($i === $page): ?>
          <span class="pager__item is-current" aria-current="page"><?= (int) $i ?></span>
        <?php else: ?>
          <a class="pager__item" href="<?= Security::e(admin_url('medya') . '?sayfa=' . $i) ?>"><?= (int) $i ?></a>
        <?php endif; ?>
      <?php endfor; ?>
    </nav>
  <?php endif; ?>
</section>
