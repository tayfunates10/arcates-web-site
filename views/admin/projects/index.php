<?php
/** Referans listesi.  DOCS.md 9.4 @var array $projects @var string $search */
declare(strict_types=1);
use Arcates\Core\Security;
?>
<form class="filters" method="get" action="<?= Security::e(admin_url('referanslar')) ?>">
  <div class="field">
    <label for="ara">Ara</label>
    <input type="search" id="ara" name="ara" value="<?= Security::e($search) ?>" placeholder="Musteri, baslik, ilce">
  </div>
  <button class="btn btn--ghost btn--sm" type="submit">Filtrele</button>
  <a class="btn btn--primary btn--sm" href="<?= Security::e(admin_url('referanslar/yeni')) ?>">Yeni referans</a>
</form>

<section class="panel">
  <div class="table-scroll">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Baslik</th>
          <th scope="col">Musteri</th>
          <th scope="col">Sektor</th>
          <th scope="col">Ilce</th>
          <th scope="col">Durum</th>
          <th scope="col"><span class="visually-hidden">Islemler</span></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$projects): ?>
          <tr><td colspan="6" class="muted">Referans yok.</td></tr>
        <?php endif; ?>
        <?php foreach ($projects as $row): ?>
          <tr>
            <td>
              <a href="<?= Security::e(admin_url('referanslar/' . (int) $row['id'])) ?>">
                <?= Security::e($row['title'] ?? '(ceviri yok)') ?>
              </a>
              <?php if (!empty($row['slug'])): ?><br><code>/referanslar/<?= Security::e($row['slug']) ?></code><?php endif; ?>
            </td>
            <td><?= Security::e($row['client_name']) ?></td>
            <td><?= Security::e($row['sector'] ?? '—') ?></td>
            <td><?= Security::e($row['district'] ?? '—') ?></td>
            <td>
              <span class="tag tag--<?= $row['status'] === 'published' ? 'ok' : 'draft' ?>">
                <?= $row['status'] === 'published' ? 'Yayinda' : 'Taslak' ?>
              </span>
            </td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="<?= Security::e(admin_url('referanslar/' . (int) $row['id'])) ?>">Duzenle</a>
              <form method="post" action="<?= Security::e(admin_url('referanslar/' . (int) $row['id'] . '/sil')) ?>"
                    data-confirm="Bu referansi silmek istiyor musunuz?">
                <?= csrf_field() ?>
                <button class="btn btn--danger btn--sm" type="submit">Sil</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
