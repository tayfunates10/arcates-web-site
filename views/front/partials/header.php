<?php
/**
 * Ust menu.  DOCS.md 5 (bolum 1), 5.3, 7.4
 *
 * Menu ogeleri ve buton metni panelden gelir; sablona sabit metin gomulmez.
 *
 * Duzen: marka + acma dugmesi + panel. Panel masaustunde satir icinde durur,
 * 940 px altinda acilir kapanir kutuya doner. Boylece dar ekranda marka, dil
 * secici ve buton ayni satira sikismaz ve yatay kaydirma olusmaz. (Test A-08)
 *
 * @var array  $_menu
 * @var array  $_langs
 * @var string $_lang
 * @var array  $_site
 * @var array  $_alternates
 * @var array  $headerCta
 * @var bool   $isReferenceHome
 */

declare(strict_types=1);

use Arcates\Core\Security;

$headerCta       = $headerCta ?? null;
$_alternates     = $_alternates ?? [];
$isReferenceHome = $isReferenceHome ?? false;

/* Cagri butonundaki ok. Dekoratiftir: ekran okuyucu "Teklif Al ok" diye
   okumasin diye `aria-hidden`, `focusable="false"`. Harici dosya yok. */
$ctaArrow = '<svg class="btn__arrow" viewBox="0 0 16 12" aria-hidden="true" focusable="false">'
    . '<path d="M1 6h13M9.5 1.5 14 6l-4.5 4.5" fill="none" stroke="currentColor"'
    . ' stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>
<header class="site-head" data-site-head>
  <?php /* Sayfa ilerleme cubugu; scroll'a bagli scaleX. DOCS.md 7.4 */ ?>
  <div class="progress" data-progress aria-hidden="true"><span class="progress__bar"></span></div>

  <div class="wrap site-head__inner">

    <a class="brand" href="<?= Security::e(url('/')) ?>"
       aria-label="<?= Security::e((string) ($_site['name'] ?? 'Arcates Yazilim')) ?>">
      <span class="brand__mark" aria-hidden="true"></span>
      <span class="brand__name"><?= Security::e($_site['name'] ?? '') ?></span>
    </a>

    <?php if ($isReferenceHome && $headerCta !== null && ($headerCta['label'] ?? '') !== ''): ?>
      <a class="btn btn--primary btn--head ref-mobile-head-cta"
         href="<?= Security::e(url((string) ($headerCta['url'] ?? '/iletisim'))) ?>">
        <?= Security::e($headerCta['label']) ?><?= $ctaArrow ?>
      </a>
    <?php endif; ?>

    <button class="site-nav__toggle" type="button" data-nav-toggle
            data-open-label="<?= Security::e(__('open_menu')) ?>"
            data-close-label="<?= Security::e(__('close_menu')) ?>"
            aria-label="<?= Security::e(__('open_menu')) ?>"
            aria-expanded="false" aria-controls="site-head-panel">
      <span class="visually-hidden"><?= Security::e(__('open_menu')) ?></span>
      <span class="site-nav__bars" aria-hidden="true"></span>
    </button>

    <div class="site-head__panel" id="site-head-panel">

      <nav class="site-nav" aria-label="<?= Security::e(__('menu')) ?>">
        <ul class="site-nav__list">
          <?php foreach ($_menu as $item): ?>
            <?php $isCurrent = (bool) ($item['is_current'] ?? false); ?>
            <li class="site-nav__item">
              <a class="site-nav__link<?= $isCurrent ? ' is-current' : '' ?>"
                 href="<?= Security::e($item['href']) ?>"
                 <?= $isCurrent ? 'aria-current="page"' : '' ?>
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

        <?php /* Referansta cagri butonunun solunda bir arama dugmesi var.
                 Arkasinda gercek bir arama olmadan konmamisti; `/ara`
                 modulu geldigi icin artik gercek bir hedefi var. */ ?>
        <a class="site-head__search" href="<?= Security::e(url('/ara')) ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
               stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
            <circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/>
          </svg>
          <span class="visually-hidden"><?= Security::e(__('search')) ?></span>
        </a>

        <?php if ($headerCta !== null && ($headerCta['label'] ?? '') !== ''): ?>
          <a class="btn btn--primary btn--head" href="<?= Security::e(url((string) ($headerCta['url'] ?? '/iletisim'))) ?>">
            <?= Security::e($headerCta['label']) ?><?= $ctaArrow ?>
          </a>
        <?php endif; ?>
      </div>

    </div>
  </div>
</header>
