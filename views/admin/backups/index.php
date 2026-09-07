<?php
/**
 * Yedekleme ekrani.  DOCS.md 9.11
 *
 * @var array $backups
 * @var int   $keep
 * @var bool  $writable
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<?php if (!$writable): ?>
  <div class="notice notice--error">
    <code>storage/backups/</code> klasoru yazilabilir degil. Yedek alinamaz.
  </div>
<?php endif; ?>

<section class="panel">
  <div class="panel__head">
    <div>
      <h2 class="panel__title">Yedekler</h2>
      <p class="muted">
        Son <?= (int) $keep ?> yedek tutulur, eskiler otomatik silinir.
        Gunluk yedek <code>tools/backup.php</code> gorevi ile alinir.
      </p>
    </div>

    <form method="post" action="<?= Security::e(admin_url('yedekleme/al')) ?>">
      <?= csrf_field() ?>
      <button class="btn btn--primary btn--sm" type="submit" <?= $writable ? '' : 'disabled' ?>>
        Simdi yedek al
      </button>
    </form>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th scope="col">Dosya</th>
        <th scope="col">Tarih</th>
        <th scope="col" class="num">Boyut</th>
        <th scope="col"><span class="visually-hidden">Islemler</span></th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$backups): ?>
        <tr><td colspan="4" class="muted">Henuz yedek yok.</td></tr>
      <?php endif; ?>

      <?php foreach ($backups as $backup): ?>
        <tr>
          <td><code><?= Security::e($backup['filename']) ?></code></td>
          <td><?= Security::e(format_date($backup['created'], true)) ?></td>
          <td class="num"><?= Security::e(format_bytes($backup['size'])) ?></td>
          <td class="row-actions">
            <a class="btn btn--ghost btn--sm"
               href="<?= Security::e(admin_url('yedekleme/indir/' . rawurlencode($backup['filename']))) ?>">Indir</a>

            <form method="post" action="<?= Security::e(admin_url('yedekleme/sil/' . rawurlencode($backup['filename']))) ?>"
                  data-confirm="Bu yedegi silmek istiyor musunuz?">
              <?= csrf_field() ?>
              <button class="btn btn--danger btn--sm" type="submit">Sil</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php if ($backups): ?>
  <section class="panel panel--form">
    <h2 class="panel__title">Geri yukleme</h2>

    <div class="notice notice--warning">
      Geri yukleme mevcut tum verinin yerine yedektekini koyar. Islem oncesi
      otomatik olarak yeni bir yedek alinir, boylece geri donus yolu acik kalir.
    </div>

    <?php foreach ($backups as $backup): ?>
      <form class="restore-row" method="post"
            action="<?= Security::e(admin_url('yedekleme/geri-yukle/' . rawurlencode($backup['filename']))) ?>"
            data-confirm="<?= Security::e($backup['filename']) ?> yedegi geri yuklenecek. Emin misiniz?">
        <?= csrf_field() ?>
        <code><?= Security::e($backup['filename']) ?></code>
        <label>
          <input type="checkbox" name="confirm" value="1">
          Onayliyorum
        </label>
        <button class="btn btn--danger btn--sm" type="submit">Geri yukle</button>
      </form>
    <?php endforeach; ?>
  </section>
<?php endif; ?>
