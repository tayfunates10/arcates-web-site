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

<section class="section section--thanks">
  <div class="wrap wrap--text">
    <div class="thanks">
      <span class="thanks__mark" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
             stroke-linecap="round" stroke-linejoin="round" focusable="false">
          <path d="m4 12.5 5.2 5.2L20 6.9"></path>
        </svg>
      </span>

      <h1 class="page__title"><?= Security::e(__('form_success')) ?></h1>

      <p class="page__lead">
        <?= Security::e($_site['name'] ?? '') ?> ekibi en kisa surede size donus yapacak.
      </p>

      <div class="system__actions">
        <a class="btn btn--primary" href="<?= Security::e(url('/')) ?>"><?= Security::e(__('back_to_home')) ?></a>
        <a class="btn btn--ghost" href="<?= Security::e(url('/referanslar')) ?>"><?= Security::e(__('projects')) ?></a>
      </div>
    </div>
  </div>
</section>
