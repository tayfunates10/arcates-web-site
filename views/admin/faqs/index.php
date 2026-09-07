<?php
/** SSS listesi.  DOCS.md 9.4 @var array $faqs */
declare(strict_types=1);
use Arcates\Core\Security;
?>
<div class="panel__head">
  <p class="muted"><?= Security::e((string) count($faqs)) ?> kayıt</p>
  <a class="btn btn--primary btn--sm" href="<?= Security::e(admin_url('sss/yeni')) ?>">Yeni SSS</a>
</div>

<section class="panel">
  <table class="table">
    <thead>
      <tr>
        <th scope="col">Soru</th>
        <th scope="col" class="num">Sıra</th>
        <th scope="col" class="num">Atanan sayfa</th>
        <th scope="col">Anasayfa</th>
        <th scope="col">Durum</th>
        <th scope="col"><span class="visually-hidden">İşlemler</span></th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$faqs): ?>
        <tr><td colspan="6" class="muted">Kayıt yok.</td></tr>
      <?php endif; ?>
      <?php foreach ($faqs as $row): ?>
        <tr>
          <td>
            <a href="<?= Security::e(admin_url('sss/' . (int) $row['id'])) ?>">
              <?= Security::e($row['question'] ?? '(çeviri yok)') ?>
            </a>
          </td>
          <td class="num"><?= (int) $row['sort'] ?></td>
          <td class="num"><?= (int) $row['page_count'] ?></td>
          <td>
            <span class="tag tag--<?= (int) $row['on_home'] > 0 ? 'ok' : 'draft' ?>">
              <?= (int) $row['on_home'] > 0 ? 'Evet' : 'Hayır' ?>
            </span>
          </td>
          <td>
            <span class="tag tag--<?= (int) $row['status'] === 1 ? 'ok' : 'off' ?>">
              <?= (int) $row['status'] === 1 ? 'Etkin' : 'Kapalı' ?>
            </span>
          </td>
          <td class="row-actions">
            <a class="btn btn--ghost btn--sm" href="<?= Security::e(admin_url('sss/' . (int) $row['id'])) ?>">Düzenle</a>
            <form method="post" action="<?= Security::e(admin_url('sss/' . (int) $row['id'] . '/sil')) ?>"
                  data-confirm="Bu kaydi silmek istiyor musunuz?">
              <?= csrf_field() ?>
              <button class="btn btn--danger btn--sm" type="submit">Sil</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
