<?php
/**
 * Bulten onay ve cikis sonucu sayfasi.  DOCS.md 4.1, 12
 *
 * Metinler dil dosyasindan gelir; sablona sabit metin gomulmez.
 * Sayfa anahtar tasiyan bir adresten acildigi icin `noindex,nofollow`
 * isaretlenir (denetleyicide).
 *
 * @var string $baslik
 * @var string $metin
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<section class="section newsletter-result">
  <div class="wrap">
    <h1 class="newsletter-result__title"><?= Security::e($baslik) ?></h1>
    <p class="newsletter-result__text"><?= Security::e($metin) ?></p>
    <p>
      <a class="btn btn--primary" href="<?= Security::e(url('/')) ?>">
        <?= Security::e(__('back_to_home')) ?>
      </a>
    </p>
  </div>
</section>
