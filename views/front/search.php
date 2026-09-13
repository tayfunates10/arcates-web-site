<?php
/**
 * Arama sonuclari.  DOCS.md 4.1
 *
 * Baslik ve etiketler dil dosyasindan gelir; sablona sabit metin
 * gomulmez. Form GET: veri degistirmez, CSRF gerekmez.
 *
 * @var string $query
 * @var array  $groups
 * @var int    $total
 * @var bool   $tooShort
 */

declare(strict_types=1);

use Arcates\Core\Security;
use Arcates\Models\Search;

$baslik = [
    'pages'    => __('pages'),
    'posts'    => __('blog'),
    'projects' => __('projects'),
];
?>

<section class="section search">
  <div class="wrap">
    <h1 class="search__title"><?= Security::e(__('search')) ?></h1>
    <p class="search__intro"><?= Security::e(__('search_intro')) ?></p>

    <form class="search__form" action="<?= Security::e(url('/ara')) ?>" method="get" role="search">
      <label class="visually-hidden" for="q"><?= Security::e(__('search_label')) ?></label>
      <input type="search" id="q" name="q" maxlength="80"
             value="<?= Security::e($query) ?>"
             placeholder="<?= Security::e(__('search_placeholder')) ?>"
             autocomplete="off">
      <button class="btn btn--primary" type="submit"><?= Security::e(__('search')) ?></button>
    </form>

    <?php if ($tooShort): ?>
      <p class="search__note">
        <?= Security::e(sprintf(__('search_too_short'), Search::MIN_LENGTH)) ?>
      </p>

    <?php elseif ($query !== '' && $total === 0): ?>
      <p class="search__note">
        <?= Security::e(sprintf(__('search_no_results'), $query)) ?>
      </p>

    <?php elseif ($query !== ''): ?>
      <p class="search__note">
        <?= Security::e(sprintf(__('search_result_count'), $total, $query)) ?>
      </p>

      <?php foreach ($groups as $tur => $sonuclar): ?>
        <section class="search__group">
          <h2 class="search__group-title"><?= Security::e($baslik[$tur] ?? $tur) ?></h2>
          <ul class="search__list">
            <?php foreach ($sonuclar as $sonuc): ?>
              <li class="search__item">
                <a class="search__link" href="<?= Security::e($sonuc['url']) ?>">
                  <?= Security::e($sonuc['title']) ?>
                </a>
                <?php if (($sonuc['excerpt'] ?? '') !== ''): ?>
                  <p class="search__excerpt"><?= Security::e($sonuc['excerpt']) ?></p>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
