<?php
/**
 * Dosya ayrintisi — alt metni, varyantlar, kullanim yeri.  DOCS.md 9.5
 *
 * @var array $media
 * @var array $usage
 * @var array $alts
 * @var array $langs
 */

declare(strict_types=1);

use Arcates\Core\Media;
use Arcates\Core\Security;

$altByLang = [];
foreach ($alts as $row) {
    $altByLang[$row['lang']] = $row;
}

$isImage = str_starts_with((string) $media['mime'], 'image/');
?>

<div class="grid grid--2">

  <section class="panel">
    <h2 class="panel__title">Önizleme</h2>

    <?php if ($isImage): ?>
      <img class="media-preview"
           src="<?= Security::e(Media::url((string) ($media['variants']['medium']['path'] ?? $media['path']))) ?>"
           alt="<?= Security::e($altByLang[Arcates\Core\Lang::defaultCode()]['alt'] ?? (string) $media['filename']) ?>"
           width="<?= (int) ($media['variants']['medium']['width'] ?? $media['width'] ?? 768) ?>"
           height="<?= (int) ($media['variants']['medium']['height'] ?? $media['height'] ?? 512) ?>">
    <?php else: ?>
      <p><a href="<?= Security::e(Media::url((string) $media['path'])) ?>" target="_blank" rel="noopener">
        <?= Security::e($media['filename']) ?>
      </a></p>
    <?php endif; ?>

    <ul class="system__list">
      <li><span>Dosya adı</span><span class="system__state"><?= Security::e($media['filename']) ?></span></li>
      <li><span>Tür</span><span class="system__state"><?= Security::e($media['mime']) ?></span></li>
      <li><span>Boyut</span><span class="system__state"><?= Security::e(format_bytes((int) $media['size'])) ?></span></li>
      <?php if (!empty($media['width'])): ?>
        <li><span>Ölçü</span><span class="system__state"><?= (int) $media['width'] ?>×<?= (int) $media['height'] ?></span></li>
      <?php endif; ?>
      <li><span>Adres</span><span class="system__state"><code><?= Security::e(Media::url((string) $media['path'])) ?></code></span></li>
    </ul>

    <?php if ($media['variants']): ?>
      <h3 class="panel__title">Varyantlar</h3>
      <table class="table">
        <thead><tr><th scope="col">Ad</th><th scope="col">Ölçü</th><th scope="col">Adres</th></tr></thead>
        <tbody>
          <?php foreach ($media['variants'] as $name => $variant): ?>
            <tr>
              <td><?= Security::e((string) $name) ?></td>
              <td><?= (int) ($variant['width'] ?? 0) ?>×<?= (int) ($variant['height'] ?? 0) ?></td>
              <td><code><?= Security::e(Media::url((string) ($variant['path'] ?? ''))) ?></code></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  <div>
    <section class="panel panel--form">
      <h2 class="panel__title">Alt metni</h2>
      <p class="muted">
        Alt metni ekran okuyucular ve arama motorları için gereklidir.
        Dekoratif görsellerde boş bırakın.
      </p>

      <form method="post" action="<?= Security::e(admin_url('medya/' . (int) $media['id'])) ?>">
        <?= csrf_field() ?>

        <?php foreach ($langs as $code => $lang): ?>
          <div class="field">
            <label for="alt_<?= Security::e($code) ?>">
              Alt metni — <?= Security::e($lang['name']) ?>
            </label>
            <input type="text" id="alt_<?= Security::e($code) ?>" name="alt[<?= Security::e($code) ?>]"
                   maxlength="255" value="<?= Security::e($altByLang[$code]['alt'] ?? '') ?>">
          </div>
        <?php endforeach; ?>

        <div class="form__actions">
          <button class="btn btn--primary" type="submit">Kaydet</button>
          <a class="btn btn--ghost" href="<?= Security::e(admin_url('medya')) ?>">Listeye dön</a>
        </div>
      </form>
    </section>

    <section class="panel">
      <h2 class="panel__title">Kullanım yeri</h2>

      <?php if (!$usage): ?>
        <p class="muted">Bu dosya hiçbir yerde kullanılmıyor.</p>
      <?php else: ?>
        <div class="notice notice--warning">
          Bu dosya <?= Security::e((string) count($usage)) ?> yerde kullanılıyor.
          Silerseniz ilgili sayfalarda görsel kaybolur.
        </div>
        <ul class="system__list">
          <?php foreach ($usage as $item): ?>
            <li>
              <span><?= Security::e($item['type']) ?></span>
              <span class="system__state"><?= Security::e($item['label']) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <form method="post" action="<?= Security::e(admin_url('medya/' . (int) $media['id'] . '/sil')) ?>"
            data-confirm="Bu dosyayi kalici olarak silmek istiyor musunuz?">
        <?= csrf_field() ?>

        <?php if ($usage): ?>
          <div class="field field--check">
            <label>
              <input type="checkbox" name="force" value="1">
              Kullanımda olduğunu biliyorum, yine de sil
            </label>
          </div>
        <?php endif; ?>

        <div class="form__actions">
          <button class="btn btn--danger" type="submit">Dosyayı sil</button>
        </div>
      </form>
    </section>
  </div>

</div>
