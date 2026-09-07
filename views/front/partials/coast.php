<?php
/**
 * Bolge haritasi.  DOCS.md 5 (bolum 5), 5.2, 7.4
 *
 * - SVG viewBox="0 0 1000 190", ilce noktalari `map_x` degerine gore yerlesir
 * - Her ilce noktasi ilgili ilce sayfasina baglantidir (ic link degeri tasir)
 * - SVG `role="img"` ve tum ilce adlarini iceren `aria-label` tasir
 *
 * @var array $content
 * @var array $config
 * @var array $districts Her biri: name, map_x, map_y, label_above, url
 */

declare(strict_types=1);

use Arcates\Core\Security;

$content   = $content ?? [];
$config    = $config ?? [];
$districts = $districts ?? [];

if (!$districts) {
    return;
}

$viewWidth  = (int) ($config['view_width'] ?? 1000);
$viewHeight = (int) ($config['view_height'] ?? 190);

$names = implode(', ', array_column($districts, 'name'));

/*
 * Kiyi cizgisi ilce noktalarindan gecen yumusak bir egridir. Noktalar
 * `map_x` degerine gore siralanir; egri her noktanin biraz altindan gecer.
 */
$sorted = $districts;
usort($sorted, static fn (array $a, array $b): int => (int) $a['map_x'] <=> (int) $b['map_x']);

$points = [];
foreach ($sorted as $district) {
    $points[] = [(int) $district['map_x'], (int) $district['map_y'] + 34];
}

$path = 'M 0 ' . ($points[0][1] + 8);
foreach ($points as $index => $point) {
    if ($index === 0) {
        $path .= ' L ' . $point[0] . ' ' . $point[1];
        continue;
    }
    $previous = $points[$index - 1];
    $midX     = (int) (($previous[0] + $point[0]) / 2);
    $path    .= ' Q ' . $midX . ' ' . $previous[1] . ' ' . $point[0] . ' ' . $point[1];
}
$path .= ' L ' . $viewWidth . ' ' . ($points[count($points) - 1][1] + 8);

$total = max(1, count($sorted));
?>
<section class="section coast" data-reveal-group>
  <div class="wrap">
    <header class="section__head">
      <?php if (($content['title'] ?? '') !== ''): ?>
        <h2 class="section__title" data-reveal><?= Security::e($content['title']) ?></h2>
      <?php endif; ?>
      <?php if (($content['description'] ?? '') !== ''): ?>
        <p class="section__lead" data-reveal><?= Security::e($content['description']) ?></p>
      <?php endif; ?>
    </header>

    <figure class="coast__figure" data-coast>
      <svg class="coast__svg"
           viewBox="0 0 <?= (int) $viewWidth ?> <?= (int) $viewHeight ?>"
           role="img"
           aria-label="<?= Security::e(__('locations') . ': ' . $names) ?>"
           preserveAspectRatio="xMidYMid meet">

        <path class="coast__path" d="<?= Security::e($path) ?>"></path>

        <?php foreach ($sorted as $index => $district): ?>
          <?php
          $x     = (int) $district['map_x'];
          $y     = (int) $district['map_y'];
          $above = (int) ($district['label_above'] ?? 0) === 1;
          $textY = $above ? $y - 18 : $y + 30;
          // Nokta, cizgi ilerlemesinin bu oranina gelince yanar. Esikler
          // 0-0.85 arasina yayilir; boylece figur ekranin ortasina geldiginde
          // son nokta da yanmis olur, tam ilerleme beklenmez.
          $at    = round((($index + 0.5) / $total) * 0.85, 3);
          ?>
          <g class="coast__dot" data-at="<?= Security::e((string) $at) ?>">
            <?php if (($district['url'] ?? '') !== ''): ?>
              <a href="<?= Security::e($district['url']) ?>">
                <circle cx="<?= $x ?>" cy="<?= $y ?>" r="9"></circle>
                <text x="<?= $x ?>" y="<?= $textY ?>"><?= Security::e($district['name']) ?></text>
              </a>
            <?php else: ?>
              <circle cx="<?= $x ?>" cy="<?= $y ?>" r="9"></circle>
              <text x="<?= $x ?>" y="<?= $textY ?>"><?= Security::e($district['name']) ?></text>
            <?php endif; ?>
          </g>
        <?php endforeach; ?>
      </svg>

      <?php /* SVG'ye erisemeyen kullanicilar icin metin karsiligi; ayni zamanda
               ic link degeri tasir. DOCS.md 5.2, 11.1 */ ?>
      <figcaption class="visually-hidden"><?= Security::e(__('locations')) ?></figcaption>
    </figure>

    <ul class="coast__list">
      <?php foreach ($sorted as $district): ?>
        <?php if (($district['url'] ?? '') === '') { continue; } ?>
        <li>
          <a href="<?= Security::e($district['url']) ?>"><?= Security::e($district['name']) ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
