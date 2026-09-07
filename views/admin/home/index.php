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
    Bölüm sırası sabittir. Her bölüm açılıp kapatılabilir; kapatılan bölüm
    on yüzde hiç basılmaz.
  </p>

  <table class="table">
    <thead>
      <tr>
        <th scope="col">Sıra</th>
        <th scope="col">Bölüm</th>
        <th scope="col">Durum</th>
        <th scope="col"><span class="visually-hidden">İşlemler</span></th>
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
              <?= $section['is_active'] ? 'Açık' : 'Kapalı' ?>
            </span>
          </td>
          <td class="row-actions">
            <a class="btn btn--ghost btn--sm" href="<?= Security::e(admin_url('anasayfa/' . $key)) ?>">Düzenle</a>
            <form method="post" action="<?= Security::e(admin_url('anasayfa/' . $key . '/durum')) ?>">
              <?= csrf_field() ?>
              <button class="btn btn--ghost btn--sm" type="submit">
                <?= $section['is_active'] ? 'Kapat' : 'Aç' ?>
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section class="panel">
  <h2 class="panel__title">Önizleme</h2>
  <p class="muted">Anasayfayı yeni sekmede açarak değişiklikleri görebilirsiniz.</p>
  <a class="btn btn--ghost btn--sm" href="/" target="_blank" rel="noopener">Anasayfayı aç</a>
</section>
