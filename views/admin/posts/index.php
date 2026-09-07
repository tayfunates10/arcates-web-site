<?php
/** Blog listesi.  DOCS.md 9.4 @var array $posts @var string $search */
declare(strict_types=1);
use Arcates\Core\Security;
?>
<form class="filters" method="get" action="<?= Security::e(admin_url('blog')) ?>">
  <div class="field">
    <label for="ara">Ara</label>
    <input type="search" id="ara" name="ara" value="<?= Security::e($search) ?>" placeholder="Başlık, kategori">
  </div>
  <button class="btn btn--ghost btn--sm" type="submit">Filtrele</button>
  <a class="btn btn--primary btn--sm" href="<?= Security::e(admin_url('blog/yeni')) ?>">Yeni yazı</a>
</form>

<section class="panel">
  <div class="table-scroll">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Başlık</th>
          <th scope="col">Kategori</th>
          <th scope="col" class="num">Kelime</th>
          <th scope="col">Yayın tarihi</th>
          <th scope="col">Durum</th>
          <th scope="col"><span class="visually-hidden">İşlemler</span></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$posts): ?>
          <tr><td colspan="6" class="muted">Yazı yok.</td></tr>
        <?php endif; ?>
        <?php foreach ($posts as $row): ?>
          <?php $future = !empty($row['published_at']) && strtotime((string) $row['published_at']) > time(); ?>
          <tr>
            <td>
              <a href="<?= Security::e(admin_url('blog/' . (int) $row['id'])) ?>">
                <?= Security::e($row['title'] ?? '(çeviri yok)') ?>
              </a>
              <?php if (!empty($row['slug'])): ?><br><code>/blog/<?= Security::e($row['slug']) ?></code><?php endif; ?>
            </td>
            <td><?= Security::e($row['category'] ?? '—') ?></td>
            <td class="num"><?= (int) ($row['word_count'] ?? 0) ?></td>
            <td>
              <?= Security::e(format_date($row['published_at'], true) ?: '—') ?>
              <?php if ($future): ?><br><span class="tag tag--warn">ileri tarihli</span><?php endif; ?>
            </td>
            <td>
              <span class="tag tag--<?= $row['status'] === 'published' ? 'ok' : 'draft' ?>">
                <?= $row['status'] === 'published' ? 'Yayında' : 'Taslak' ?>
              </span>
            </td>
            <td class="row-actions">
              <a class="btn btn--ghost btn--sm" href="<?= Security::e(admin_url('blog/' . (int) $row['id'])) ?>">Düzenle</a>
              <form method="post" action="<?= Security::e(admin_url('blog/' . (int) $row['id'] . '/sil')) ?>"
                    data-confirm="Bu yaziyi silmek istiyor musunuz?">
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
