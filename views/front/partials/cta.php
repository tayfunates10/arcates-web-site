<?php
/**
 * Son cagri — R5. Baslik/metin panel veya ayarlardan; telefon tek kaynak olan
 * NAP ayarindan gelir. Telefon varsa ikinci eylem WhatsApp'tir.
 *
 * @var array $cta
 */
declare(strict_types=1);
use Arcates\Core\Security;
use Arcates\Core\Settings;

$cta = $cta ?? [];
$title = (string) ($cta['title'] ?? '');
$text = (string) ($cta['text'] ?? '');
if ($title === '') $title = (string) Settings::get('cta_title', 'Projenizi konuşalım');
if ($text === '') $text = (string) Settings::get('cta_text', '');

$primary = $cta['cta1'] ?? ['label' => __('form_submit'), 'url' => '/iletisim'];
$fallbackSecondary = $cta['cta2'] ?? null;
$phone = (string) Settings::get('nap_phone', '');
$digits = preg_replace('/\D+/', '', $phone) ?? '';
if (strlen($digits) === 11 && str_starts_with($digits, '0')) $digits = '9' . $digits;
elseif (strlen($digits) === 10 && str_starts_with($digits, '5')) $digits = '90' . $digits;
?>
<section class="section section--cta home-cta" data-reveal-group>
  <div class="wrap">
    <div class="cta">
      <div class="cta__copy">
        <h2 class="cta__title" data-reveal><?= Security::e($title) ?></h2>
        <?php if ($text !== ''): ?><p class="cta__text" data-reveal><?= Security::e($text) ?></p><?php endif; ?>
      </div>

      <div class="cta__actions" data-reveal>
        <?php if (($primary['label'] ?? '') !== ''): ?>
          <a class="btn btn--primary" href="<?= Security::e(url((string) ($primary['url'] ?? '/iletisim#teklif-formu'))) ?>"><?= Security::e($primary['label']) ?></a>
        <?php endif; ?>

        <?php if ($digits !== ''): ?>
          <a class="btn btn--ghost btn--on-dark" href="https://wa.me/<?= Security::e($digits) ?>" target="_blank" rel="noopener">
            <?= Security::e(__('whatsapp')) ?> · <?= Security::e(__('write_on_whatsapp')) ?>
          </a>
        <?php elseif ($fallbackSecondary !== null && ($fallbackSecondary['label'] ?? '') !== ''): ?>
          <a class="btn btn--ghost btn--on-dark" href="<?= Security::e(url((string) ($fallbackSecondary['url'] ?? '/'))) ?>"><?= Security::e($fallbackSecondary['label']) ?></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
