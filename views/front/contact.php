<?php
/**
 * Iletisim sayfasi — teklif formu ilk ekranda gorunur. DOCS.md 12
 *
 * @var array $page
 * @var array $faqs
 * @var array $crumbs
 * @var array $services
 * @var array $errors
 * @var array $old
 * @var array $flash
 * @var array $_site
 */

declare(strict_types=1);

use Arcates\Core\Security;

$phone  = (string) ($_site['phone'] ?? '');
$digits = preg_replace('/\D+/', '', $phone) ?? '';
if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
    $digits = '9' . $digits;
} elseif (strlen($digits) === 10 && str_starts_with($digits, '5')) {
    $digits = '90' . $digits;
}
?>

<?= partial('front/partials/breadcrumbs', ['crumbs' => $crumbs]) ?>

<header class="page-hero page-hero--contact section section--tight">
  <div class="wrap">
    <h1 class="page__title"><?= Security::e($page['title']) ?></h1>
    <?php if (!empty($page['excerpt'])): ?>
      <p class="page__lead u-measure-lead"><?= Security::e($page['excerpt']) ?></p>
    <?php endif; ?>
  </div>
</header>

<section class="section section--page section--contact section--content">
  <div class="wrap">
    <div class="contact-shell">
      <aside class="contact-card" aria-label="<?= Security::e(__('request_quote')) ?>">
        <?= partial('front/partials/form', [
            'services'   => $services,
            'errors'     => $errors,
            'old'        => $old,
            'flash'      => $flash,
            'returnPath' => (string) $page['slug'],
        ]) ?>

        <div class="contact-card__details">
          <p class="contact-card__label"><?= Security::e(__('contact_details')) ?></p>
          <ul class="contact-list">
            <?php if ($phone !== ''): ?>
              <li><span><?= Security::e(__('phone')) ?></span><a href="tel:<?= Security::e(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= Security::e($phone) ?></a></li>
            <?php endif; ?>
            <?php if (($_site['email'] ?? '') !== ''): ?>
              <li><span><?= Security::e(__('email')) ?></span><a href="mailto:<?= Security::e($_site['email']) ?>"><?= Security::e($_site['email']) ?></a></li>
            <?php endif; ?>
            <?php if (($_site['street'] ?? '') !== '' || ($_site['district'] ?? '') !== ''): ?>
              <li><span><?= Security::e(__('address')) ?></span><strong><?= Security::e(trim(($_site['street'] ?? '') . ' ' . ($_site['district'] ?? '') . ' ' . ($_site['city'] ?? ''))) ?></strong></li>
            <?php endif; ?>
            <?php if (($_site['hours'] ?? []) !== []): ?>
              <li class="contact-list__hours">
                <span><?= Security::e(__('opening_hours')) ?></span>
                <div>
                  <?php foreach ($_site['hours'] as $slot): ?>
                    <strong><?= Security::e(($slot['days'] ?? '') . ' ' . ($slot['opens'] ?? '') . '–' . ($slot['closes'] ?? '')) ?></strong>
                  <?php endforeach; ?>
                </div>
              </li>
            <?php endif; ?>
          </ul>
          <?php if ($digits !== ''): ?>
            <a class="btn btn--ghost contact-card__whatsapp" href="https://wa.me/<?= Security::e($digits) ?>" target="_blank" rel="noopener">
              <?= Security::e(__('whatsapp')) ?> · <?= Security::e(__('write_on_whatsapp')) ?>
            </a>
          <?php endif; ?>
        </div>
      </aside>

      <article class="contact-copy">
        <div class="prose u-measure">
          <?= Security::sanitizeHtml((string) ($page['content'] ?? '')) ?>
        </div>
      </article>
    </div>
  </div>
</section>

<?= partial('front/partials/faq', ['faqs' => $faqs]) ?>
