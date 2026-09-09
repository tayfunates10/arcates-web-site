<?php
/** Hizmet bolgeleri — R3 G-05 erisilebilir baglanti sahnesi + HTML baglantilar. */
declare(strict_types=1);
use Arcates\Core\Security;
$content = $content ?? [];
$config = $config ?? [];
$districts = $districts ?? [];
if (!$districts) return;

$viewWidth = (int) ($config['view_width'] ?? 1000);
$viewHeight = (int) ($config['view_height'] ?? 190);
$names = implode(', ', array_column($districts, 'name'));
$sorted = $districts;
usort($sorted, static fn (array $a, array $b): int => (int) $a['map_x'] <=> (int) $b['map_x']);
$points = [];
foreach ($sorted as $district) $points[] = [(int) $district['map_x'], (int) $district['map_y'] + 34];
$path = 'M 0 ' . ($points[0][1] + 8);
foreach ($points as $index => $point) {
    if ($index === 0) { $path .= ' L ' . $point[0] . ' ' . $point[1]; continue; }
    $previous = $points[$index - 1];
    $midX = (int) (($previous[0] + $point[0]) / 2);
    $path .= ' Q ' . $midX . ' ' . $previous[1] . ' ' . $point[0] . ' ' . $point[1];
}
$path .= ' L ' . $viewWidth . ' ' . ($points[count($points) - 1][1] + 8);
$total = max(1, count($sorted));
?>
<section class="section section--loose coast home-coast" data-reveal-group>
  <div class="wrap">
    <div class="coast__intro">
      <header class="section__head">
        <?php if (($content['title'] ?? '') !== ''): ?><h2 class="section__title" data-reveal><?= Security::e($content['title']) ?></h2><?php endif; ?>
        <?php if (($content['description'] ?? '') !== ''): ?><p class="section__lead" data-reveal><?= Security::e($content['description']) ?></p><?php endif; ?>
      </header>
      <p class="coast__note" data-reveal><?= Security::e(__('area_graphic_note')) ?></p>
    </div>

    <figure class="coast__figure" data-coast>
      <svg class="coast__svg" viewBox="0 0 <?= $viewWidth ?> <?= $viewHeight ?>" role="img"
           aria-label="<?= Security::e(__('locations') . ': ' . $names) ?>" preserveAspectRatio="xMidYMid meet">
        <defs>
          <linearGradient id="coast-route-gradient" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#7FB6FF"></stop>
            <stop offset=".5" stop-color="#1C7BF2"></stop>
            <stop offset="1" stop-color="#9DD5FF"></stop>
          </linearGradient>
          <pattern id="coast-grid" width="48" height="48" patternUnits="userSpaceOnUse">
            <path d="M48 0H0V48" fill="none" stroke="rgba(127,182,255,.09)" stroke-width="1"></path>
          </pattern>
        </defs>
        <rect class="coast__backdrop" x="0" y="0" width="<?= $viewWidth ?>" height="<?= $viewHeight ?>" rx="22"></rect>
        <rect class="coast__grid" x="0" y="0" width="<?= $viewWidth ?>" height="<?= $viewHeight ?>" rx="22" fill="url(#coast-grid)"></rect>
        <path class="coast__route-shadow" d="<?= Security::e($path) ?>"></path>
        <path class="coast__path" d="<?= Security::e($path) ?>" stroke="url(#coast-route-gradient)"></path>
        <?php foreach ($sorted as $index => $district): ?>
          <?php
          $x = (int) $district['map_x']; $y = (int) $district['map_y'];
          $above = (int) ($district['label_above'] ?? 0) === 1;
          $textY = $above ? $y - 18 : $y + 30;
          $at = round((($index + 0.5) / $total) * 0.85, 3);
          ?>
          <g class="coast__dot" data-at="<?= Security::e((string) $at) ?>">
            <?php if (($district['url'] ?? '') !== ''): ?>
              <a href="<?= Security::e($district['url']) ?>"><circle cx="<?= $x ?>" cy="<?= $y ?>" r="9"></circle><text x="<?= $x ?>" y="<?= $textY ?>"><?= Security::e($district['name']) ?></text></a>
            <?php else: ?>
              <circle cx="<?= $x ?>" cy="<?= $y ?>" r="9"></circle><text x="<?= $x ?>" y="<?= $textY ?>"><?= Security::e($district['name']) ?></text>
            <?php endif; ?>
          </g>
        <?php endforeach; ?>
      </svg>
      <figcaption class="visually-hidden"><?= Security::e(__('area_graphic_note')) ?></figcaption>
    </figure>

    <ul class="coast__list" aria-label="<?= Security::e(__('locations')) ?>">
      <?php foreach ($sorted as $district): ?>
        <?php if (($district['url'] ?? '') === '') continue; ?>
        <li><a href="<?= Security::e($district['url']) ?>"><span><?= Security::e($district['name']) ?></span><span aria-hidden="true">→</span></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
