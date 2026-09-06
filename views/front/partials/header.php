<?php
/**
 * Ust menu.  DOCS.md 5 (bolum 1)
 *
 * Menu ogeleri ve buton metni panelden gelir; sablona sabit metin gomulmez.
 *
 * @var array  $_menu
 * @var array  $_langs
 * @var string $_lang
 * @var array  $headerCta
 */

declare(strict_types=1);

use Arcates\Core\Lang;
use Arcates\Core\Security;

$headerCta   = $headerCta ?? null;
$_alternates = $_alternates ?? [];
?>
<header class="site-head" data-site-head>
  <?php /* Sayfa ilerleme cubugu; scroll'a bagli scaleX. DOCS.md 7.4 */ ?>
  <div class="progress" data-progress aria-hidden="true"><span class="progress__bar"></span></div>

  <div class="wrap site-head__inner">

    <a class="brand" href="<?= Security::e(url('/')) ?>">
      <span class="brand__mark" data-hero="mark" aria-hidden="true"></span>
      <span class="brand__name"><?= Security::e($_site['name'] ?? '') ?></span>
    </a>

    <nav class="site-nav" aria-label="<?= Security::e(__('menu')) ?>">
      <button class="site-nav__toggle" type="button" data-nav-toggle
              aria-expanded="false" aria-controls="site-nav-list">
        <span class="visually-hidden"><?= Security::e(__('open_menu')) ?></span>
        <span class="site-nav__bars" aria-hidden="true"></span>
      </button>

      <ul class="site-nav__list" id="site-nav-list">
        <?php foreach ($_menu as $item): ?>
          <li class="site-nav__item">
            <a class="site-nav__link" href="<?= Security::e($item['href']) ?>"
               <?= $item['target'] === '_blank' ? 'target="_blank" rel="noopener"' : '' ?>>
              <?= Security::e($item['label']) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <div class="site-head__side">
      <?php if (count($_langs) > 1): ?>
        <nav class="lang-switch" aria-label="<?= Security::e(__('change_language')) ?>">
          <?php foreach ($_langs as $code => $lang): ?>
            <?php if ($code === $_lang): ?>
              <span class="lang-switch__item is-current" aria-current="true"><?= Security::e(strtoupper($code)) ?></span>
            <?php else: ?>
              <?php /* Sayfanin o dildeki karsiligi varsa ona, yoksa o dilin
                       anasayfasina gidilir. DOCS.md 11.3 */ ?>
              <a class="lang-switch__item"
                 href="<?= Security::e($_alternates[$code] ?? url('/', $code)) ?>"
                 lang="<?= Security::e($code) ?>">
                <?= Security::e(strtoupper($code)) ?>
              </a>
            <?php endif; ?>
          <?php endforeach; ?>
        </nav>
      <?php endif; ?>

      <?php if ($headerCta !== null && ($headerCta['label'] ?? '') !== ''): ?>
        <a class="btn btn--primary btn--head" href="<?= Security::e(url((string) ($headerCta['url'] ?? '/iletisim'))) ?>">
          <?= Security::e($headerCta['label']) ?>
        </a>
      <?php endif; ?>
    </div>

  </div>
</header>
