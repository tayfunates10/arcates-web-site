<?php
/**
 * Ic sayfalarda kullanilan yapiskan donusum karti.
 * Tum iletisim bilgileri ortak NAP verisinden gelir.
 *
 * @var array $_site
 */

declare(strict_types=1);

use Arcates\Core\Security;

$phone = trim((string) ($_site['phone'] ?? ''));
$tel   = preg_replace('/[^0-9+]/', '', $phone) ?? '';
$digits = preg_replace('/\D+/', '', $phone) ?? '';
if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
    $digits = '9' . $digits;
} elseif (strlen($digits) === 10 && str_starts_with($digits, '5')) {
    $digits = '90' . $digits;
}
?>
<aside class="aside-cta" data-sticky-cta>
  <p class="aside-cta__eyebrow"><?= Security::e(__('request_quote')) ?></p>
  <p class="aside-cta__text"><?= Security::e(__('quote_card_text')) ?></p>

  <div class="aside-cta__actions">
    <a class="btn btn--primary" href="<?= Security::e(url('/iletisim') . '#teklif-formu') ?>">
      <?= Security::e(__('form_submit')) ?>
    </a>

    <?php if ($phone !== ''): ?>
      <a class="aside-cta__link" href="tel:<?= Security::e($tel) ?>">
        <span><?= Security::e(__('phone')) ?></span>
        <strong><?= Security::e($phone) ?></strong>
      </a>
    <?php endif; ?>

    <?php if ($digits !== ''): ?>
      <a class="aside-cta__link" href="https://wa.me/<?= Security::e($digits) ?>" target="_blank" rel="noopener">
        <span><?= Security::e(__('whatsapp')) ?></span>
        <strong><?= Security::e(__('write_on_whatsapp')) ?></strong>
      </a>
    <?php endif; ?>
  </div>
</aside>
