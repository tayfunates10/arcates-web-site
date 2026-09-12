<?php
/**
 * Arcates ana sayfa — referans görsel tabanlı koyu arayüz.
 * İçerik ve bağlantılar CMS verisinden beslenir; yalnız görsel düzen sabittir.
 *
 * @var array $sections
 * @var array $projects
 * @var array $posts
 * @var array $footerContent
 */

declare(strict_types=1);

use Arcates\Core\Media;
use Arcates\Core\Security;

$active = static function (string $key) use ($sections): ?array {
    $section = $sections[$key] ?? null;
    if ($section === null || !($section['is_active'] ?? false)) {
        return null;
    }
    return $section;
};

$hero     = $active('hero');
$services = $active('services');
$steps    = $active('steps');
$works    = $active('works');
$cta      = $active('cta');

$serviceCards = (array) ($services['content']['cards'] ?? []);
$stepItems    = (array) ($steps['content']['items'] ?? []);
$projectItems = array_slice($projects ?? [], 0, 3);
$postItems    = array_slice($posts ?? [], 0, 3);
$aboutText    = trim((string) ($footerContent['about'] ?? ''));

$icons = [
    'cube'     => '<path d="m12 3 9 5v8l-9 5-9-5V8l9-5Zm0 9 9-4M12 12 3 8m9 4v9"/>',
    'phone'    => '<rect x="6" y="2" width="12" height="20" rx="2"/><path d="M10 5h4m-3 14h2"/>',
    'bolt'     => '<path d="m13 2-9 12h7l-1 8 10-13h-8l1-7Z"/>',
    'headset'  => '<path d="M3 13v-1a9 9 0 0 1 18 0v1m0 5v1a3 3 0 0 1-3 3h-4"/><rect x="3" y="11" width="4" height="8" rx="2"/><rect x="17" y="11" width="4" height="8" rx="2"/>',
    'code'     => '<path d="m8 6-6 6 6 6m8-12 6 6-6 6M14 3l-4 18"/>',
    'pen'      => '<path d="m15 4 5 5M4 15 15 4a3.5 3.5 0 0 1 5 5L9 20l-7 2 2-7Zm0 0 5 5"/>',
    'rocket'   => '<path d="M14 5c3-3 7-3 7-3s0 4-3 7l-8 8-5-5 9-7Zm-6 4H4l-2 6 4-1m9 2v4l-6 2 1-5M6 18l-3 3"/><circle cx="16" cy="7" r="1.5"/>',
    'layout'   => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M9 9v11"/>',
    'cart'     => '<circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/><path d="M2 3h3l2.6 12.4a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 7H6"/>',
    'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
    'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/>',
    'globe'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.7 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.7-3.8-9S9.5 5.6 12 3Z"/>',
    'shield'   => '<path d="M12 3l7 3v5.5c0 4.3-2.9 8.3-7 9.5-4.1-1.2-7-5.2-7-9.5V6l7-3Z"/><path d="m9 12 2.2 2.2L15.5 10"/>',
];

/* Kahraman butonlarindaki oklar ve onay isaretleri. Referansta birincil
   buton duz bir ok, ikincil buton daire icinde ok tasiyor; onay satirinda
   cerceve degil duz bir tik var.

   Metin glifi (→ ↗ ✓) yerine satir ici SVG: glif fonta bagli, font
   yuklenmezse kutu olarak cizilir. Depoda bu donusum daha once bilgi
   kartlari ve hizmet ikonlari icin de yapildi. */
$heroMarks = [
    'arrow'        => '<svg class="ref-btn__mark" viewBox="0 0 16 12" fill="none" stroke="currentColor"'
        . ' stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
        . '<path d="M1 6h13M9.5 1.5 14 6l-4.5 4.5"/></svg>',
    'arrowCircled' => '<svg class="ref-btn__mark" viewBox="0 0 20 20" fill="none" stroke="currentColor"'
        . ' stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
        . '<circle cx="10" cy="10" r="8.25"/><path d="M6.5 10h7M10.5 7l3 3-3 3"/></svg>',
    'check'        => '<svg class="ref-trust__mark" viewBox="0 0 14 14" fill="none" stroke="currentColor"'
        . ' stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
        . '<path d="m2 7.5 3.2 3.2L12 3.8"/></svg>',
];

// Only trusted local paths are rendered; CMS icon names never become SVG markup.
$renderIcon = static function (string $key) use ($icons): string {
    $path = $icons[$key] ?? $icons['layout'];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $path . '</svg>';
};
$processIcons = count($stepItems) === 3 ? ['search', 'code', 'rocket'] : ['search', 'pen', 'code', 'rocket'];
?>

<div class="ref-home">
<?php if ($hero !== null): ?>
  <?php
  $heroContent = (array) ($hero['content'] ?? []);
  $heroLines = array_values(array_filter([
      (string) ($heroContent['line1'] ?? ''),
      (string) ($heroContent['line2'] ?? ''),
      (string) ($heroContent['line3'] ?? ''),
  ], static fn (string $value): bool => trim($value) !== ''));
  $heroCta1 = $heroContent['cta1'] ?? null;
  $heroCta2 = $heroContent['cta2'] ?? null;
  ?>
  <section class="ref-hero">
    <div class="wrap ref-hero__grid">
      <div class="ref-hero__copy">
        <?php if (($heroContent['badge'] ?? '') !== ''): ?>
          <p class="ref-kicker"><?= Security::e((string) $heroContent['badge']) ?></p>
        <?php endif; ?>

        <h1 class="ref-hero__title">
          <?php foreach ($heroLines as $index => $line): ?>
            <span<?= $index === count($heroLines) - 1 ? ' class="ref-accent"' : '' ?>><?= Security::e($line) ?></span>
          <?php endforeach; ?>
        </h1>

        <?php if (($heroContent['description'] ?? '') !== ''): ?>
          <p class="ref-hero__lead"><?= Security::e((string) $heroContent['description']) ?></p>
        <?php endif; ?>

        <div class="ref-hero__actions">
          <?php if ($heroCta1 !== null && ($heroCta1['label'] ?? '') !== ''): ?>
            <a class="ref-btn ref-btn--primary" href="<?= Security::e(url((string) ($heroCta1['url'] ?? '/iletisim'))) ?>">
              <?= Security::e((string) $heroCta1['label']) ?><?= $heroMarks['arrow'] ?>
            </a>
          <?php endif; ?>
          <?php if ($heroCta2 !== null && ($heroCta2['label'] ?? '') !== ''): ?>
            <a class="ref-btn ref-btn--ghost" href="<?= Security::e(url((string) ($heroCta2['url'] ?? '/referanslar'))) ?>">
              <?= Security::e((string) $heroCta2['label']) ?><?= $heroMarks['arrowCircled'] ?>
            </a>
          <?php endif; ?>
        </div>

        <ul class="ref-trust" aria-label="<?= Security::e(__('service_benefits')) ?>">
          <li><?= $heroMarks['check'] ?><?= Security::e(__('free_consultation')) ?></li>
          <li><?= $heroMarks['check'] ?><?= Security::e(__('quick_reply')) ?></li>
          <li><?= $heroMarks['check'] ?><?= Security::e(__('tailored_solution')) ?></li>
        </ul>
      </div>

      <?php
      // Sahne tamamen dekoratiftir: metni panelden gelir, olcum/musteri verisi
      // uretmez. Bos birakilan her parca hic basilmaz.
      $scene      = (array) ($heroContent['scene'] ?? []);
      $sceneCard  = (array) ($scene['card'] ?? []);
      $sceneChips = array_values(array_filter(
          (array) ($scene['chips'] ?? []),
          static fn ($chip): bool => is_array($chip) && trim((string) ($chip['value'] ?? '')) !== ''
      ));
      $sceneRail  = array_values(array_filter(
          (array) ($scene['rail'] ?? []),
          static fn ($item): bool => is_array($item) && trim((string) ($item['label'] ?? '')) !== ''
      ));
      ?>
      <div class="ref-hero__visual" aria-hidden="true">
        <span class="ref-hero__halo"></span>
        <img src="<?= Security::e(asset('img/reference/hero-laptop.webp')) ?>"
             width="1448" height="1086" alt="" loading="eager" decoding="async">

        <?php if (($sceneCard['title'] ?? '') !== ''): ?>
          <div class="ref-scene-card">
            <strong><?= Security::e((string) $sceneCard['title']) ?></strong>
            <?php if (($sceneCard['text'] ?? '') !== ''): ?>
              <span><?= Security::e((string) $sceneCard['text']) ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php if ($sceneChips !== []): ?>
          <ul class="ref-scene-chips">
            <?php foreach (array_slice($sceneChips, 0, 2) as $index => $chip): ?>
              <li class="ref-scene-chip ref-scene-chip--<?= (int) ($index + 1) ?>">
                <strong><?= Security::e((string) $chip['value']) ?></strong>
                <?php if (($chip['label'] ?? '') !== ''): ?>
                  <small><?= Security::e((string) $chip['label']) ?></small>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <?php if ($sceneRail !== []): ?>
          <ul class="ref-scene-rail">
            <?php foreach (array_slice($sceneRail, 0, 5) as $item): ?>
              <li>
                <span class="ref-scene-rail__icon"><?= $renderIcon((string) ($item['icon'] ?? 'layout')) ?></span>
                <?= Security::e((string) $item['label']) ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <p class="ref-hero__scribble"><?= Security::e(__('technology_for_limits')) ?></p>
      </div>
    </div>
  </section>

  <section class="ref-metrics" aria-label="<?= Security::e(__('core_capabilities')) ?>">
    <div class="wrap ref-metrics__grid">
      <article class="ref-metric"><span class="ref-metric__icon" aria-hidden="true"><?= $renderIcon('cube') ?></span><p><strong><?= Security::e(__('custom_design')) ?></strong><small><?= Security::e(__('custom_design_short')) ?></small></p></article>
      <article class="ref-metric"><span class="ref-metric__icon" aria-hidden="true"><?= $renderIcon('phone') ?></span><p><strong><?= Security::e(__('mobile_first')) ?></strong><small><?= Security::e(__('mobile_first_short')) ?></small></p></article>
      <article class="ref-metric"><span class="ref-metric__icon" aria-hidden="true"><?= $renderIcon('bolt') ?></span><p><strong><?= Security::e(__('speed_and_seo')) ?></strong><small><?= Security::e(__('speed_and_seo_short')) ?></small></p></article>
      <article class="ref-metric"><span class="ref-metric__icon" aria-hidden="true"><?= $renderIcon('headset') ?></span><p><strong><?= Security::e(__('continuous_support')) ?></strong><small><?= Security::e(__('continuous_support_short')) ?></small></p></article>
    </div>
  </section>
<?php endif; ?>

<?php if ($services !== null && $serviceCards !== []): ?>
  <section class="ref-section ref-services" id="hizmetler">
    <div class="wrap">
      <header class="ref-section__head">
        <div><h2><?= Security::e(__('our_services')) ?></h2><p><?= Security::e((string) ($services['content']['description'] ?? '')) ?></p></div>
        <a href="<?= Security::e(url('/web-tasarim')) ?>"><?= Security::e(__('all_services')) ?> <span aria-hidden="true">→</span></a>
      </header>
      <ul class="ref-services__grid">
        <?php foreach ($serviceCards as $card): ?>
          <?php $iconKey = isset($icons[(string) ($card['icon'] ?? '')]) ? (string) $card['icon'] : 'layout'; ?>
          <li>
            <a class="ref-service" href="<?= Security::e(url((string) ($card['url'] ?? '/'))) ?>">
              <span class="ref-service__icon" aria-hidden="true"><?= $renderIcon($iconKey) ?></span>
              <strong><?= Security::e((string) ($card['title'] ?? '')) ?></strong>
              <span class="ref-service__text"><?= Security::e((string) ($card['text'] ?? '')) ?></span>
              <span class="ref-service__more"><?= Security::e(__('more_details')) ?> →</span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
<?php endif; ?>

<?php if ($works !== null && $projectItems !== []): ?>
  <section class="ref-section ref-projects">
    <div class="wrap">
      <header class="ref-section__head">
        <div><h2><?= Security::e(__('featured_projects')) ?></h2><p><?= Security::e((string) ($works['content']['description'] ?? '')) ?></p></div>
        <a href="<?= Security::e(url('/referanslar')) ?>"><?= Security::e(__('all_projects')) ?> <span aria-hidden="true">→</span></a>
      </header>
      <ul class="ref-projects__grid">
        <?php foreach ($projectItems as $project): ?>
          <li class="ref-project">
            <a href="<?= Security::e(url('/referanslar/' . (string) ($project['slug'] ?? ''))) ?>">
              <div class="ref-project__media">
                <?php if (!empty($project['cover'])): ?>
                  <img src="<?= Security::e(Media::url((string) $project['cover']['path'])) ?>"
                       alt="<?= Security::e((string) ($project['cover']['alt'] ?? $project['title'] ?? '')) ?>"
                       width="<?= (int) ($project['cover']['width'] ?? 768) ?>" height="<?= (int) ($project['cover']['height'] ?? 480) ?>" loading="lazy" decoding="async">
                <?php else: ?>
                  <span class="ref-project__fallback" aria-hidden="true"><i></i><i></i><i></i></span>
                <?php endif; ?>
              </div>
              <div class="ref-project__body">
                <?php if (($project['sector'] ?? '') !== ''): ?><span class="ref-chip"><?= Security::e((string) $project['sector']) ?></span><?php endif; ?>
                <h3><?= Security::e((string) ($project['title'] ?? $project['client_name'] ?? '')) ?></h3>
                <?php if (($project['excerpt'] ?? '') !== ''): ?><p><?= Security::e((string) $project['excerpt']) ?></p><?php endif; ?>
                <span class="ref-project__arrow" aria-hidden="true">→</span>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
<?php endif; ?>

<section class="ref-section ref-duo-section">
  <div class="wrap ref-duo">
    <?php if ($steps !== null && $stepItems !== []): ?>
      <div class="ref-process">
        <header class="ref-section__head ref-section__head--local">
          <div><h2><?= Security::e(__('how_we_work')) ?></h2><p><?= Security::e((string) ($steps['content']['description'] ?? __('transparent_process'))) ?></p></div>
        </header>
        <ol class="ref-process__list">
          <?php foreach ($stepItems as $index => $step): ?>
            <li>
              <span class="ref-process__dot" aria-hidden="true"><?= $renderIcon((string) ($step['icon'] ?? $processIcons[$index] ?? 'layout')) ?></span>
              <div><span class="ref-process__number" aria-hidden="true"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><h3><?= Security::e((string) ($step['title'] ?? '')) ?></h3><p><?= Security::e((string) ($step['text'] ?? '')) ?></p></div>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>
    <?php endif; ?>

    <?php if ($serviceCards !== []): ?>
      <aside class="ref-why">
        <header class="ref-section__head ref-section__head--local"><div><h2><?= Security::e(__('why_arcates')) ?></h2><p><?= Security::e(__('why_arcates_short')) ?></p></div></header>
        <div class="ref-why__body">
          <ul>
            <?php foreach (array_slice($serviceCards, 0, 5) as $card): ?>
              <li><span aria-hidden="true"><?= $renderIcon((string) ($card['icon'] ?? 'layout')) ?></span><?= Security::e((string) ($card['title'] ?? '')) ?></li>
            <?php endforeach; ?>
          </ul>
          <div class="ref-why__statement">
            <img src="<?= Security::e(asset('img/logo-mark-light.png')) ?>" alt="" width="80" height="80" loading="lazy" decoding="async">
            <strong><?= Security::e(__('more_than_software')) ?></strong>
            <?php if ($aboutText !== ''): ?><p><?= Security::e($aboutText) ?></p><?php endif; ?>
          </div>
        </div>
      </aside>
    <?php endif; ?>
  </div>
</section>

<?php if ($aboutText !== '' || $postItems !== []): ?>
  <section class="ref-section ref-editorial">
    <div class="wrap ref-editorial__grid">
      <?php if ($aboutText !== ''): ?>
        <article class="ref-about">
          <header class="ref-section__head ref-section__head--local">
            <div><h2><?= Security::e(__('who_we_are')) ?></h2><p><?= Security::e(__('human_centered_technology')) ?></p></div>
            <a href="<?= Security::e(url('/hakkimizda')) ?>"><?= Security::e(__('about')) ?> <span aria-hidden="true">→</span></a>
          </header>
          <div class="ref-about__body">
            <img src="<?= Security::e(asset('img/reference/about-team.webp')) ?>" alt="<?= Security::e(__('team_image_alt')) ?>" width="1672" height="941" loading="lazy" decoding="async">
            <div><p><?= Security::e($aboutText) ?></p><a class="ref-btn ref-btn--ghost ref-btn--small" href="<?= Security::e(url('/hakkimizda')) ?>"><?= Security::e(__('learn_more_about_us')) ?> →</a></div>
          </div>
        </article>
      <?php endif; ?>

      <?php if ($postItems !== []): ?>
        <article class="ref-posts">
          <header class="ref-section__head ref-section__head--local">
            <div><h2><?= Security::e(__('latest_articles')) ?></h2><p><?= Security::e(__('latest_articles_short')) ?></p></div>
            <a href="<?= Security::e(url('/blog')) ?>"><?= Security::e(__('all_posts')) ?> <span aria-hidden="true">→</span></a>
          </header>
          <ul class="ref-posts__grid">
            <?php foreach ($postItems as $post): ?>
              <li class="ref-post">
                <a href="<?= Security::e(url('/blog/' . (string) ($post['slug'] ?? ''))) ?>">
                  <div class="ref-post__media">
                    <?php if (!empty($post['cover'])): ?>
                      <img src="<?= Security::e(Media::url((string) $post['cover']['path'])) ?>" alt="<?= Security::e((string) ($post['cover']['alt'] ?? $post['title'] ?? '')) ?>" width="<?= (int) ($post['cover']['width'] ?? 768) ?>" height="<?= (int) ($post['cover']['height'] ?? 480) ?>" loading="lazy" decoding="async">
                    <?php else: ?>
                      <span aria-hidden="true">&lt;/&gt;</span>
                    <?php endif; ?>
                  </div>
                  <div class="ref-post__body">
                    <?php if (($post['category'] ?? '') !== ''): ?><span class="ref-chip"><?= Security::e((string) $post['category']) ?></span><?php endif; ?>
                    <h3><?= Security::e((string) ($post['title'] ?? '')) ?></h3>
                    <?php if (($post['published_at'] ?? '') !== ''): ?><time datetime="<?= Security::e((string) $post['published_at']) ?>"><?= Security::e(date('d.m.Y', strtotime((string) $post['published_at']))) ?></time><?php endif; ?>
                    <span class="ref-post__arrow" aria-hidden="true">→</span>
                  </div>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </article>
      <?php endif; ?>
    </div>
  </section>
<?php endif; ?>

<?php if ($cta !== null): ?>
  <?php $ctaContent = (array) ($cta['content'] ?? []); $primary = $ctaContent['cta1'] ?? null; ?>
  <section class="ref-final-cta">
    <div class="wrap">
      <div class="ref-final-cta__card">
        <div class="ref-final-cta__copy">
          <p class="ref-kicker"><?= Security::e(__('turn_ideas_into_reality')) ?></p>
          <h2><?= Security::e((string) ($ctaContent['title'] ?? '')) ?></h2>
          <?php if (($ctaContent['text'] ?? '') !== ''): ?><p><?= Security::e((string) $ctaContent['text']) ?></p><?php endif; ?>
        </div>
        <?php if ($primary !== null && ($primary['label'] ?? '') !== ''): ?>
          <div class="ref-final-cta__action"><a class="ref-btn ref-btn--primary" href="<?= Security::e(url((string) ($primary['url'] ?? '/iletisim'))) ?>"><?= Security::e((string) $primary['label']) ?> <span aria-hidden="true">→</span></a><small>✓ <?= Security::e(__('free_consultation')) ?></small></div>
        <?php endif; ?>
      </div>
    </div>
  </section>
<?php endif; ?>
</div>
