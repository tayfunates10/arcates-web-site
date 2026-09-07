<?php
/**
 * Blog yazisi formu.  DOCS.md 9.4
 * @var array|null $post @var array $translations @var array $langs
 * @var array $media @var array $categories
 */
declare(strict_types=1);
use Arcates\Core\Lang;
use Arcates\Core\Security;

$isNew   = $post === null;
$action  = $isNew ? admin_url('blog/yeni') : admin_url('blog/' . (int) $post['id']);
$default = Lang::defaultCode();
$publishedAt = (string) ($post['published_at'] ?? date('Y-m-d H:i:s'));
?>
<form method="post" action="<?= Security::e($action) ?>" data-dirty-guard>
  <?= csrf_field() ?>

  <section class="panel panel--form">
    <h2 class="panel__title">Yazı ayarları</h2>

    <div class="grid grid--3">
      <div class="field">
        <label for="category">Kategori</label>
        <input type="text" id="category" name="category" maxlength="80" list="category-list"
               value="<?= Security::e((string) old('category', $post['category'] ?? '')) ?>">
        <datalist id="category-list">
          <?php foreach ($categories as $category): ?>
            <option value="<?= Security::e($category) ?>"></option>
          <?php endforeach; ?>
        </datalist>
      </div>

      <div class="field">
        <label for="status">Durum</label>
        <select id="status" name="status">
          <?php $status = (string) old('status', $post['status'] ?? 'draft'); ?>
          <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Taslak</option>
          <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Yayında</option>
        </select>
      </div>

      <div class="field">
        <label for="published_at">Yayın tarihi</label>
        <input type="datetime-local" id="published_at" name="published_at"
               value="<?= Security::e(date('Y-m-d\TH:i', strtotime($publishedAt))) ?>">
        <span class="field__hint">İleri tarih girerseniz yazı o tarihte görünür olur.</span>
      </div>
    </div>

    <div class="field">
      <label for="cover_id">Kapak görseli</label>
      <select id="cover_id" name="cover_id">
        <option value="0">— yok —</option>
        <?php $cover = (int) old('cover_id', $post['cover_id'] ?? 0); ?>
        <?php foreach ($media as $file): ?>
          <option value="<?= (int) $file['id'] ?>" <?= $cover === (int) $file['id'] ? 'selected' : '' ?>>
            <?= Security::e($file['filename']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
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
        <span class="field__hint">Adres değişirse eski adresten yenisine 301 otomatik oluşur.</span>
      </div>

      <div class="field">
        <label for="excerpt_<?= Security::e($code) ?>">Özet</label>
        <textarea id="excerpt_<?= Security::e($code) ?>" name="t[<?= Security::e($code) ?>][excerpt]"
                  rows="3" maxlength="400"><?= Security::e($t['excerpt'] ?? '') ?></textarea>
      </div>

      <div class="field">
        <label for="content_<?= Security::e($code) ?>">İçerik</label>
        <textarea id="content_<?= Security::e($code) ?>" name="t[<?= Security::e($code) ?>][content]"
                  rows="18"><?= Security::e($t['content'] ?? '') ?></textarea>
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
    <a class="btn btn--ghost" href="<?= Security::e(admin_url('blog')) ?>">Listeye dön</a>
  </div>
</form>
