<?php
/**
 * Referans formu.  DOCS.md 9.4
 * @var array|null $project @var array $translations @var array $gallery
 * @var array $langs @var array $media @var array $districts
 */
declare(strict_types=1);
use Arcates\Core\Lang;
use Arcates\Core\Security;

$isNew   = $project === null;
$action  = $isNew ? admin_url('referanslar/yeni') : admin_url('referanslar/' . (int) $project['id']);
$default = Lang::defaultCode();
?>
<form method="post" action="<?= Security::e($action) ?>" data-dirty-guard>
  <?= csrf_field() ?>

  <section class="panel panel--form">
    <h2 class="panel__title">Referans bilgileri</h2>

    <div class="grid grid--2">
      <div class="field">
        <label for="client_name">Müşteri adı</label>
        <input type="text" id="client_name" name="client_name" maxlength="150" required
               value="<?= Security::e((string) old('client_name', $project['client_name'] ?? '')) ?>">
        <?php if ($m = error_for('client_name')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
      </div>

      <div class="field">
        <label for="sector">Sektör</label>
        <input type="text" id="sector" name="sector" maxlength="80"
               value="<?= Security::e((string) old('sector', $project['sector'] ?? '')) ?>">
      </div>
    </div>

    <div class="grid grid--3">
      <div class="field">
        <label for="district">İlçe</label>
        <input type="text" id="district" name="district" maxlength="60" list="district-list"
               value="<?= Security::e((string) old('district', $project['district'] ?? '')) ?>">
        <datalist id="district-list">
          <?php foreach ($districts as $name): ?>
            <option value="<?= Security::e($name) ?>"></option>
          <?php endforeach; ?>
        </datalist>
        <span class="field__hint">İlçe sayfalarındaki referans listesi bu ada göre eşleşir.</span>
      </div>

      <div class="field">
        <label for="status">Durum</label>
        <select id="status" name="status">
          <?php $status = (string) old('status', $project['status'] ?? 'draft'); ?>
          <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Taslak</option>
          <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Yayında</option>
        </select>
      </div>

      <div class="field">
        <label for="sort">Sıra</label>
        <input type="number" id="sort" name="sort" value="<?= (int) old('sort', $project['sort'] ?? 0) ?>">
      </div>
    </div>

    <div class="field">
      <label for="live_url">Canlı site adresi</label>
      <input type="url" id="live_url" name="live_url" maxlength="255" placeholder="https://"
             value="<?= Security::e((string) old('live_url', $project['live_url'] ?? '')) ?>">
      <?php if ($m = error_for('live_url')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
    </div>

    <div class="field">
      <label for="cover_id">Kapak görseli</label>
      <select id="cover_id" name="cover_id">
        <option value="0">— yok —</option>
        <?php $cover = (int) old('cover_id', $project['cover_id'] ?? 0); ?>
        <?php foreach ($media as $file): ?>
          <option value="<?= (int) $file['id'] ?>" <?= $cover === (int) $file['id'] ? 'selected' : '' ?>>
            <?= Security::e($file['filename']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <fieldset class="fieldset">
      <legend>Galeri görselleri</legend>
      <div class="checkbox-grid">
        <?php foreach ($media as $file): ?>
          <label>
            <input type="checkbox" name="gallery[]" value="<?= (int) $file['id'] ?>"
                   <?= in_array((int) $file['id'], $gallery, true) ? 'checked' : '' ?>>
            <?= Security::e($file['filename']) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </fieldset>
  </section>

  <div class="tabs" data-tabs="lang">
    <?php foreach ($langs as $code => $lang): ?>
      <button class="tabs__button" type="button" role="tab" data-tab="<?= Security::e($code) ?>">
        <?= Security::e($lang['name']) ?>
      </button>
    <?php endforeach; ?>
  </div>

  <?php foreach ($langs as $code => $lang): ?>
    <?php $t = $translations[$code] ?? []; ?>
    <section class="panel panel--form" data-tab-panel="<?= Security::e($code) ?>" data-tab-group="lang">
      <h2 class="panel__title"><?= Security::e($lang['name']) ?></h2>

      <div class="field">
        <label for="title_<?= Security::e($code) ?>">Başlık</label>
        <input type="text" id="title_<?= Security::e($code) ?>" name="t[<?= Security::e($code) ?>][title]"
               maxlength="200" data-slug-source="slug_<?= Security::e($code) ?>"
               value="<?= Security::e($t['title'] ?? '') ?>">
        <?php if ($code === $default && ($m = error_for('title'))): ?>
          <span class="field__error"><?= Security::e($m) ?></span>
        <?php endif; ?>
      </div>

      <div class="field">
        <label for="slug_<?= Security::e($code) ?>">Adres</label>
        <input type="text" id="slug_<?= Security::e($code) ?>" name="t[<?= Security::e($code) ?>][slug]"
               maxlength="200" value="<?= Security::e($t['slug'] ?? '') ?>">
      </div>

      <div class="field">
        <label for="excerpt_<?= Security::e($code) ?>">Özet</label>
        <textarea id="excerpt_<?= Security::e($code) ?>" name="t[<?= Security::e($code) ?>][excerpt]"
                  rows="3" maxlength="400"><?= Security::e($t['excerpt'] ?? '') ?></textarea>
      </div>

      <div class="field">
        <label for="content_<?= Security::e($code) ?>">Yapılan işler</label>
        <textarea id="content_<?= Security::e($code) ?>" name="t[<?= Security::e($code) ?>][content]"
                  rows="14"><?= Security::e($t['content'] ?? '') ?></textarea>
      </div>

      <details class="details">
        <summary>SEO</summary>
        <div class="field">
          <label for="meta_title_<?= Security::e($code) ?>">Meta başlık</label>
          <input type="text" id="meta_title_<?= Security::e($code) ?>"
                 name="t[<?= Security::e($code) ?>][meta_title]" maxlength="180" data-counter="60"
                 value="<?= Security::e($t['meta_title'] ?? '') ?>">
        </div>
        <div class="field">
          <label for="meta_description_<?= Security::e($code) ?>">Meta açıklama</label>
          <textarea id="meta_description_<?= Security::e($code) ?>"
                    name="t[<?= Security::e($code) ?>][meta_description]" rows="3" maxlength="320"
                    data-counter="160"><?= Security::e($t['meta_description'] ?? '') ?></textarea>
        </div>
      </details>
    </section>
  <?php endforeach; ?>

  <div class="form__actions form__actions--sticky">
    <button class="btn btn--primary" type="submit"><?= $isNew ? 'Oluştur' : 'Kaydet' ?></button>
    <a class="btn btn--ghost" href="<?= Security::e(admin_url('referanslar')) ?>">Listeye dön</a>
  </div>
</form>
