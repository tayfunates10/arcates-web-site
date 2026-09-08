<?php
/** Referanslar — portfoy kayitlarindan son N tanesi. DOCS.md 5. */
declare(strict_types=1);
use Arcates\Core\Media;
use Arcates\Core\Security;
use Arcates\Core\Settings;
$content = $content ?? [];
$projects = $projects ?? [];
if (!$projects) return;
$cta = $content['cta'] ?? null;
?>
<section class="section section--loose" data-reveal-group>
  <div class="wrap">
    <header class="section__head">
      <?php if (($content['title'] ?? '') !== ''): ?><h2 class="section__title" data-reveal><?= Security::e($content['title']) ?></h2><?php endif; ?>
      <?php if (($content['description'] ?? '') !== ''): ?><p class="section__lead" data-reveal><?= Security::e($content['description']) ?></p><?php endif; ?>
    </header>
    <?= partial('front/partials/notice', ['text' => Settings::get('projects_notice', '')]) ?>
    <ul class="works">
      <?php foreach ($projects as $project): ?>
        <li class="work" data-reveal>
          <?php if (!empty($project['cover'])): ?>
            <div class="work__media"><img src="<?= Security::e(Media::url((string) $project['cover']['path'])) ?>" alt="<?= Security::e($project['cover']['alt'] ?? $project['title']) ?>" width="<?= (int) ($project['cover']['width'] ?? 768) ?>" height="<?= (int) ($project['cover']['height'] ?? 480) ?>" loading="lazy" decoding="async"></div>
          <?php endif; ?>
          <p class="work__meta"><?= Security::e($project['sector'] ?? '') ?><?php if (!empty($project['district'])): ?> · <?= Security::e($project['district']) ?><?php endif; ?></p>
          <h3 class="work__title"><a href="<?= Security::e(url('/referanslar/' . ($project['slug'] ?? ''))) ?>"><?= Security::e($project['title'] ?? $project['client_name'] ?? '') ?></a></h3>
          <?php if (!empty($project['excerpt'])): ?><p class="work__text"><?= Security::e($project['excerpt']) ?></p><?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
    <?php if ($cta !== null && ($cta['label'] ?? '') !== ''): ?>
      <p class="section__more" data-reveal><a class="btn btn--ghost" href="<?= Security::e(url((string) ($cta['url'] ?? '/referanslar'))) ?>"><?= Security::e($cta['label']) ?></a></p>
    <?php endif; ?>
  </div>
</section>
