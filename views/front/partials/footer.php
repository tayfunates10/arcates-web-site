<?php
/**
 * Alt bilgi — NAP bilgileri, calisma saatleri, baglantilar.
 * DOCS.md 5 (bolum 10), 11.2
 *
 * @var array $_site
 * @var array $_footer
 * @var array $footerContent
 */

declare(strict_types=1);

use Arcates\Core\Security;

$footerContent = $footerContent ?? [];
$columns       = $footerContent['columns'] ?? [];
$legal         = $footerContent['legal'] ?? [];
?>
<footer class="site-foot">
  <div class="wrap site-foot__inner">

    <div class="site-foot__brand">
      <span class="brand__name"><?= Security::e($_site['name'] ?? '') ?></span>
      <?php if (($footerContent['about'] ?? '') !== ''): ?>
        <p class="site-foot__about"><?= Security::e($footerContent['about']) ?></p>
      <?php endif; ?>

      <?php if (($_site['social'] ?? []) !== []): ?>
        <ul class="site-foot__social" aria-label="<?= Security::e(__('follow_us')) ?>">
          <?php foreach ($_site['social'] as $link): ?>
            <li>
              <a href="<?= Security::e($link['url'] ?? '') ?>" rel="noopener me" target="_blank">
                <?= Security::e($link['label'] ?? '') ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <?php foreach ($columns as $column): ?>
      <nav class="site-foot__col" aria-label="<?= Security::e($column['title'] ?? '') ?>">
        <h2 class="site-foot__title"><?= Security::e($column['title'] ?? '') ?></h2>
        <ul>
          <?php foreach ($column['links'] ?? [] as $link): ?>
            <li>
              <a href="<?= Security::e(url((string) ($link['url'] ?? '/'))) ?>">
                <?= Security::e($link['label'] ?? '') ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    <?php endforeach; ?>

    <?php /* NAP — Google Isletme Profili ile birebir ayni. DOCS.md 11.2 */ ?>
    <div class="site-foot__col site-foot__nap">
      <h2 class="site-foot__title"><?= Security::e(__('contact')) ?></h2>

      <address class="nap">
        <?php if (($_site['street'] ?? '') !== '' || ($_site['district'] ?? '') !== ''): ?>
          <span class="nap__line">
            <?= Security::e(trim(($_site['street'] ?? '') . ' ' . ($_site['district'] ?? '') . ' ' . ($_site['city'] ?? ''))) ?>
          </span>
        <?php endif; ?>

        <?php if (($_site['phone'] ?? '') !== ''): ?>
          <a class="nap__line" href="tel:<?= Security::e(preg_replace('/[^0-9+]/', '', (string) $_site['phone'])) ?>">
            <?= Security::e($_site['phone']) ?>
          </a>
        <?php endif; ?>

        <?php if (($_site['email'] ?? '') !== ''): ?>
          <a class="nap__line" href="mailto:<?= Security::e($_site['email']) ?>">
            <?= Security::e($_site['email']) ?>
          </a>
        <?php endif; ?>
      </address>

      <?php if (($_site['hours'] ?? []) !== []): ?>
        <h2 class="site-foot__title site-foot__title--sub"><?= Security::e(__('opening_hours')) ?></h2>
        <ul class="hours">
          <?php foreach ($_site['hours'] as $slot): ?>
            <li>
              <span><?= Security::e($slot['days'] ?? '') ?></span>
              <span><?= Security::e(($slot['opens'] ?? '') . '–' . ($slot['closes'] ?? '')) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

  </div>

  <div class="wrap site-foot__base">
    <p class="site-foot__legal">
      © <?= Security::e(date('Y')) ?> <?= Security::e($_site['name'] ?? '') ?>. <?= Security::e(__('all_rights')) ?>
    </p>

    <?php if ($legal !== [] || $_footer !== []): ?>
      <ul class="site-foot__links">
        <?php foreach ($_footer as $item): ?>
          <li><a href="<?= Security::e($item['href']) ?>"><?= Security::e($item['label']) ?></a></li>
        <?php endforeach; ?>
        <?php foreach ($legal as $link): ?>
          <li>
            <a href="<?= Security::e(url((string) ($link['url'] ?? '/'))) ?>">
              <?= Security::e($link['label'] ?? '') ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</footer>
