<?php
/**
 * Sektor sayfasi sablonu. DOCS.md 4.4
 *
 * @var array $page
 * @var array $faqs
 * @var array $relatedPages
 * @var array $crumbs
 */

declare(strict_types=1);

use Arcates\Core\ContentOutline;
use Arcates\Core\Security;

$outline = ContentOutline::prepare((string) ($page['content'] ?? ''));
$sectorVisuals = [
    'otel-pansiyon-web-sitesi' => 'otel-pansiyon-web-sitesi',
    'zeytinyagi-e-ticaret-sitesi' => 'zeytinyagi-e-ticaret-sitesi',
    'restoran-kafe-qr-menu' => 'restoran-kafe-qr-menu',
    'emlak-web-sitesi' => 'emlak-web-sitesi',
    'nakliyat-web-sitesi' => 'nakliyat-web-sitesi',
    'tabela-matbaa-web-sitesi' => 'tabela-matbaa-web-sitesi',
];
$sectorVisual = $sectorVisuals[(string) ($page['slug'] ?? '')] ?? null;
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<header class="page-hero section section--tight">
  <div class="wrap page-hero__sector-grid">
    <div class="page-hero__sector-copy">
      <h1 class="page__title"><?= Security::e($page['title']) ?></h1>
      <?php if (!empty($page['excerpt'])): ?>
        <p class="page__lead u-measure-lead"><?= Security::e($page['excerpt']) ?></p>
      <?php endif; ?>
    </div>

    <?php if ($sectorVisual !== null): ?>
      <figure class="sector-hero__media" aria-hidden="true">
        <img
          class="sector-hero__image"
          src="<?= Security::e(asset('img/redesign/sector-' . $sectorVisual . '.svg')) ?>"
          width="480"
          height="360"
          alt=""
          aria-hidden="true"
          loading="eager"
          decoding="async">
      </figure>
    <?php endif; ?>
  </div>
</header>

<article class="section section--page section--content">
  <div class="wrap">
    <div class="content-shell">
      <div class="content-main" data-outline>
        <div class="prose u-measure"><?= $outline['html'] ?></div>
      </div>

      <div class="content-aside">
        <?= partial('front/partials/toc', ['items' => $outline['items']]) ?>
        <?= partial('front/partials/related-links', [
            'items' => $relatedPages ?? [],
            'label' => __('other_sectors'),
        ]) ?>
        <?= partial('front/partials/aside-cta') ?>
      </div>
    </div>
  </div>
</article>

<?= partial('front/partials/faq', ['faqs' => $faqs]) ?>
<?= partial('front/partials/cta') ?>
