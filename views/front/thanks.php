<?php
/**
 * Tesekkur sayfasi.
 *
 * Ayri URL olmalidir ki donusum olculebilsin; `noindex` tasir.
 * DOCS.md 12
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<header class="page-hero page-hero--simple section section--tight">
  <div class="wrap wrap--text">
    <h1 class="page__title"><?= Security::e(__('form_success')) ?></h1>
    <p class="page__lead u-measure-lead">
      <?= Security::e($_site['name'] ?? '') ?> ekibi en kısa sürede size dönüş yapacak.
    </p>
  </div>
</header>

<section class="section section--page section--thanks">
  <div class="wrap wrap--text">
    <div class="thanks">
      <span class="state-mark state-mark--success" aria-hidden="true">
        <span class="state-mark__glyph"></span>
      </span>

      <div class="thanks__content">
        <div class="system__actions">
          <a class="btn btn--primary" href="<?= Security::e(url('/')) ?>"><?= Security::e(__('back_to_home')) ?></a>
          <a class="btn btn--ghost" href="<?= Security::e(url('/referanslar')) ?>"><?= Security::e(__('projects')) ?></a>
        </div>
      </div>
    </div>
  </div>
</section>
