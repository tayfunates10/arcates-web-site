<?php
/**
 * Cagri bandi. Anasayfada panelden gelen icerikle, ic sayfalarda ayarlardaki
 * varsayilanlarla calisir.  DOCS.md 5 (bolum 9)
 *
 * @var array $cta
 */

declare(strict_types=1);

use Arcates\Core\Security;
use Arcates\Core\Settings;

$cta = $cta ?? [];

$title = (string) ($cta['title'] ?? '');
$text  = (string) ($cta['text'] ?? '');

// Ic sayfalarda bolum icerigi gecilmezse ayarlardaki isletme adiyla
// olusturulmus kisa bir bant gosterilir.
if ($title === '') {
    $title = (string) Settings::get('cta_title', 'Projenizi konusalim');
}
if ($text === '') {
    $text = (string) Settings::get('cta_text', '');
}

$primary   = $cta['cta1'] ?? ['label' => __('form_submit'), 'url' => '/iletisim'];
$secondary = $cta['cta2'] ?? null;
?>
<section class="section section--cta" data-reveal-group>
  <div class="wrap">
    <div class="cta">
      <h2 class="cta__title" data-reveal><?= Security::e($title) ?></h2>
      <?php if ($text !== ''): ?>
        <p class="cta__text" data-reveal><?= Security::e($text) ?></p>
      <?php endif; ?>

      <div class="cta__actions" data-reveal>
        <?php if (($primary['label'] ?? '') !== ''): ?>
          <a class="btn btn--primary" href="<?= Security::e(url((string) ($primary['url'] ?? '/iletisim'))) ?>">
            <?= Security::e($primary['label']) ?>
          </a>
        <?php endif; ?>

        <?php if ($secondary !== null && ($secondary['label'] ?? '') !== ''): ?>
          <a class="btn btn--ghost btn--on-dark" href="<?= Security::e(url((string) ($secondary['url'] ?? '/'))) ?>">
            <?= Security::e($secondary['label']) ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
