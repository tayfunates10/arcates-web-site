<?php
/**
 * Sektor seridi.  DOCS.md 5 (bolum 3), 7.2
 *
 * Kesintisiz donmesi icin etiket grubu iki kez basilir; animasyon -%50
 * kaydiginda ilk grup tam olarak ikincinin yerine gelir ve sicrama olmaz.
 * Bu yuzden kopya sunucudan gelir; JavaScript kapaliyken de duzgun doner.
 *
 * @var array $content
 * @var array $config
 */

declare(strict_types=1);

use Arcates\Core\Security;

$content = $content ?? [];
$tags    = array_values(array_filter(
    (array) ($content['tags'] ?? []),
    static fn ($tag): bool => trim((string) $tag) !== ''
));

if (!$tags) {
    return;
}
?>
<section class="strip" aria-label="<?= Security::e(__('sectors')) ?>">
  <div class="strip__track" data-strip>
    <div class="strip__group">
      <?php foreach ($tags as $tag): ?>
        <span class="strip__item"><?= Security::e($tag) ?></span>
      <?php endforeach; ?>
    </div>
    <div class="strip__group" aria-hidden="true">
      <?php foreach ($tags as $tag): ?>
        <span class="strip__item"><?= Security::e($tag) ?></span>
      <?php endforeach; ?>
    </div>
  </div>
</section>
