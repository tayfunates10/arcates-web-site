<?php
/**
 * Sektor baglanti grubu — R5.
 *
 * Sonsuz kayan dekor kaldirildi. Yayinlanmis sektor sayfalari gercek URL'leri
 * ile listelenir. Sektor sayfasi bulunamazsa paneldeki etiketler yalnizca
 * metin olarak gosterilir; var olmayan URL uretilmez.
 *
 * @var array $content
 * @var array $sectors
 */
declare(strict_types=1);

use Arcates\Core\Security;

$content = $content ?? [];
$sectors = $sectors ?? [];
$tags = array_values(array_filter(
    (array) ($content['tags'] ?? []),
    static fn ($tag): bool => trim((string) $tag) !== ''
));

if (!$sectors && !$tags) return;

$icons = [
    'otel-pansiyon-web-sitesi' => '<path d="M4 20V8h16v12M4 13h16M8 8V4h8v4M8 16h2M14 16h2"/>',
    'zeytinyagi-e-ticaret-sitesi' => '<path d="M8 7h8l2 13H6L8 7ZM10 7V4h4v3M9 12h6"/>',
    'restoran-kafe-qr-menu' => '<path d="M5 4v16M5 9h4V4M15 4v16M15 4c3 2 4 5 2 8h-2"/>',
    'emlak-web-sitesi' => '<path d="m3 11 9-7 9 7M6 10v10h12V10M10 20v-6h4v6"/>',
    'nakliyat-web-sitesi' => '<path d="M3 7h11v10H3zM14 10h4l3 4v3h-7M7 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM18 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>',
    'tabela-matbaa-web-sitesi' => '<path d="M5 4h14v10H5zM8 18h8M12 14v4M8 8h8M8 11h5"/>',
];
$defaultIcon = '<path d="M4 5h16v14H4zM8 9h8M8 13h5"/>';
?>
<section class="section sector-links" aria-labelledby="sector-links-title">
  <div class="wrap">
    <div class="sector-links__head">
      <p class="sector-links__label" id="sector-links-title"><?= Security::e(__('sectors')) ?></p>
    </div>

    <ul class="sector-links__grid">
      <?php if ($sectors): ?>
        <?php foreach ($sectors as $sector): ?>
          <?php $slug = (string) ($sector['slug'] ?? ''); ?>
          <li>
            <a class="sector-link" href="<?= Security::e(url('/' . ltrim($slug, '/'))) ?>">
              <span class="sector-link__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" focusable="false">
                  <?= $icons[$slug] ?? $defaultIcon ?>
                </svg>
              </span>
              <span><?= Security::e($sector['title'] ?? '') ?></span>
              <span class="sector-link__arrow" aria-hidden="true">→</span>
            </a>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <?php foreach ($tags as $tag): ?>
          <li class="sector-link sector-link--static">
            <span class="sector-link__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" focusable="false"><?= $defaultIcon ?></svg>
            </span>
            <span><?= Security::e($tag) ?></span>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </div>
</section>
