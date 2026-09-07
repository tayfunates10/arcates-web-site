<?php
/**
 * Anasayfa bolum listesi.  DOCS.md 9.2
 *
 * Sira sabittir (surum 1); her bolum icin ac/kapat anahtari ve duzenleme
 * ekrani bulunur.
 *
 * @var array $sections
 * @var array $labels
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<section class="panel">
  <p class="muted">
    Bolum sirasi sabittir. Her bolum acilip kapatilabilir; kapatilan bolum
    on yuzde hic basilmaz.
  </p>

  <table class="table">
    <thead>
      <tr>
        <th scope="col">Sira</th>
        <th scope="col">Bolum</th>
        <th scope="col">Durum</th>
        <th scope="col"><span class="visually-hidden">Islemler</span></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($sections as $key => $section): ?>
        <tr>
          <td class="num"><?= (int) $section['sort'] ?></td>
          <td>
            <a href="<?= Security::e(admin_url('anasayfa/' . $key)) ?>">
              <?= Security::e($labels[$key] ?? $key) ?>
            </a>
            <br><code><?= Security::e($key) ?></code>
          </td>
          <td>
            <span class="tag tag--<?= $section['is_active'] ? 'ok' : 'off' ?>">
              <?= $section['is_active'] ? 'Acik' : 'Kapali' ?>
            </span>
          </td>
          <td class="row-actions">
            <a class="btn btn--ghost btn--sm" href="<?= Security::e(admin_url('anasayfa/' . $key)) ?>">Duzenle</a>
            <form method="post" action="<?= Security::e(admin_url('anasayfa/' . $key . '/durum')) ?>">
              <?= csrf_field() ?>
              <button class="btn btn--ghost btn--sm" type="submit">
                <?= $section['is_active'] ? 'Kapat' : 'Ac' ?>
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section class="panel">
  <h2 class="panel__title">Onizleme</h2>
  <p class="muted">Anasayfayi yeni sekmede acarak degisiklikleri gorebilirsiniz.</p>
  <a class="btn btn--ghost btn--sm" href="/" target="_blank" rel="noopener">Anasayfayi ac</a>
</section>
