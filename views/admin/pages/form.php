<?php
/**
 * Sayfa formu — dil sekmeleri, tam SEO paneli, icerik skoru, Google onizlemesi.
 * DOCS.md 9.3, 9.6
 *
 * @var array|null $page
 * @var array      $translations
 * @var array      $types
 * @var array      $langs
 * @var array|null $score
 * @var array      $pages
 */

declare(strict_types=1);

use Arcates\Core\Lang;
use Arcates\Core\Security;
use Arcates\Core\Seo;

$isNew  = $page === null;
$action = $isNew ? admin_url('sayfalar/yeni') : admin_url('sayfalar/' . (int) $page['id']);
$default = Lang::defaultCode();

$robotsOptions = [
    'index,follow'     => 'Dizine girsin, bağlantıları izlesin',
    'noindex,follow'   => 'Dizine girmesin, bağlantıları izlesin',
    'index,nofollow'   => 'Dizine girsin, bağlantıları izlemesin',
    'noindex,nofollow' => 'Dizine girmesin, bağlantıları izlemesin',
];
?>

<form method="post" action="<?= Security::e($action) ?>" data-dirty-guard>
  <?= csrf_field() ?>

  <section class="panel panel--form">
    <h2 class="panel__title">Sayfa ayarları</h2>

    <div class="grid grid--3">
      <div class="field">
        <label for="type">Tür</label>
        <select id="type" name="type">
          <?php $type = (string) old('type', $page['type'] ?? 'page'); ?>
          <?php foreach ($types as $key => $label): ?>
            <option value="<?= Security::e($key) ?>" <?= $type === $key ? 'selected' : '' ?>>
              <?= Security::e($label) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <span class="field__hint">İlçe sayfaları için en az 500 kelime özgün metin gerekir.</span>
      </div>

      <div class="field">
        <label for="status">Durum</label>
        <select id="status" name="status">
          <?php $status = (string) old('status', $page['status'] ?? 'draft'); ?>
          <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Taslak</option>
          <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Yayında</option>
        </select>
      </div>

      <div class="field">
        <label for="sort">Sıra</label>
        <input type="number" id="sort" name="sort" value="<?= Security::e((string) old('sort', $page['sort'] ?? 0)) ?>">
      </div>
    </div>

    <div class="grid grid--2">
      <div class="field">
        <label for="district">İlçe adı</label>
        <input type="text" id="district" name="district" maxlength="60"
               value="<?= Security::e((string) old('district', $page['district'] ?? '')) ?>">
        <span class="field__hint">Yalnızca ilçe sayfalarında kullanılır; referansları bu ada göre eşleşir.</span>
      </div>

      <div class="field">
        <label for="parent_id">Üst sayfa</label>
        <select id="parent_id" name="parent_id">
          <option value="0">— yok —</option>
          <?php $parent = (int) old('parent_id', $page['parent_id'] ?? 0); ?>
          <?php foreach ($pages as $option): ?>
            <option value="<?= (int) $option['id'] ?>" <?= $parent === (int) $option['id'] ? 'selected' : '' ?>>
              <?= Security::e($option['title']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </section>

  <?php if ($score !== null): ?>
    <section class="panel">
      <h2 class="panel__title">İçerik skoru — <?= Security::e((string) $score['score']) ?>/100</h2>
      <?php if (!$score['issues']): ?>
        <p class="muted">Denetimlerin tümü geçti.</p>
      <?php else: ?>
        <ul class="issues">
          <?php foreach ($score['issues'] as $issue): ?>
            <li class="issues__item issues__item--<?= Security::e($issue['level']) ?>">
              <span class="tag tag--<?= $issue['level'] === 'strong' ? 'off' : 'warn' ?>">
                <?= $issue['level'] === 'strong' ? 'Güçlü uyarı' : 'Uyarı' ?>
              </span>
              <?= Security::e($issue['message']) ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <p class="muted">İçerik skoru kaydı engellemez; yalnızca eksikleri listeler.</p>
      <?php endif; ?>
    </section>
  <?php endif; ?>

  <div class="tabs" data-tabs="lang">
    <?php foreach ($langs as $code => $lang): ?>
      <button class="tabs__button" type="button" role="tab" data-tab="<?= Security::e($code) ?>">
        <?= Security::e($lang['name']) ?>
        <?php if ($code === $default): ?><span class="tabs__badge">varsayılan</span><?php endif; ?>
      </button>
    <?php endforeach; ?>
  </div>

  <?php foreach ($langs as $code => $lang): ?>
    <?php $t = $translations[$code] ?? []; ?>
    <section class="panel panel--form" data-tab-panel="<?= Security::e($code) ?>" data-tab-group="lang">

      <h2 class="panel__title"><?= Security::e($lang['name']) ?> içeriği</h2>

      <?php if ($code !== $default): ?>
        <p class="muted">
          Başlığı boş bırakırsanız bu dil yayınlanmaz ve <code>hreflang</code> setine girmez.
        </p>
      <?php endif; ?>

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
        <label for="slug_<?= Security::e($code) ?>">Adres (slug)</label>
        <input type="text" id="slug_<?= Security::e($code) ?>" name="t[<?= Security::e($code) ?>][slug]"
               maxlength="200" value="<?= Security::e($t['slug'] ?? '') ?>">
        <span class="field__hint">
          Adres değişirse eski adresten yenisine <strong>301 yönlendirmesi otomatik oluşur</strong>.
        </span>
      </div>

      <div class="field">
        <label for="excerpt_<?= Security::e($code) ?>">Özet</label>
        <textarea id="excerpt_<?= Security::e($code) ?>" name="t[<?= Security::e($code) ?>][excerpt]"
                  maxlength="400" rows="3"><?= Security::e($t['excerpt'] ?? '') ?></textarea>
      </div>

      <div class="field">
        <label for="content_<?= Security::e($code) ?>">İçerik</label>
        <textarea id="content_<?= Security::e($code) ?>" name="t[<?= Security::e($code) ?>][content]"
                  rows="18"><?= Security::e($t['content'] ?? '') ?></textarea>
        <span class="field__hint">
          İzin verilen etiketler: p, h2-h6, strong, em, ul, ol, li, a, img, blockquote, table.
          Sayfa başlığı zaten H1 üretir; içerikte H2 ile başlayın.
        </span>
      </div>

      <details class="details">
        <summary>SEO ayarları</summary>

        <div class="field">
          <label for="meta_title_<?= Security::e($code) ?>">Meta başlık</label>
          <input type="text" id="meta_title_<?= Security::e($code) ?>"
                 name="t[<?= Security::e($code) ?>][meta_title]" maxlength="180"
                 data-counter="<?= Seo::TITLE_MAX ?>"
                 value="<?= Security::e($t['meta_title'] ?? '') ?>">
        </div>

        <div class="field">
          <label for="meta_description_<?= Security::e($code) ?>">Meta açıklama</label>
          <textarea id="meta_description_<?= Security::e($code) ?>"
                    name="t[<?= Security::e($code) ?>][meta_description]" maxlength="320" rows="3"
                    data-counter="<?= Seo::DESCRIPTION_MAX ?>"><?= Security::e($t['meta_description'] ?? '') ?></textarea>
        </div>

        <div class="field">
          <label for="robots_<?= Security::e($code) ?>">Arama motoru yönergesi</label>
          <select id="robots_<?= Security::e($code) ?>" name="t[<?= Security::e($code) ?>][robots]">
            <?php $robots = (string) ($t['robots'] ?? 'index,follow'); ?>
            <?php foreach ($robotsOptions as $key => $label): ?>
              <option value="<?= Security::e($key) ?>" <?= $robots === $key ? 'selected' : '' ?>>
                <?= Security::e($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="canonical_<?= Security::e($code) ?>">Canonical adres</label>
          <input type="text" id="canonical_<?= Security::e($code) ?>"
                 name="t[<?= Security::e($code) ?>][canonical]" maxlength="255"
                 value="<?= Security::e($t['canonical'] ?? '') ?>">
          <span class="field__hint">Boş bırakılırsa sayfanın kendi adresi kullanılır.</span>
        </div>

        <div class="field">
          <label for="schema_type_<?= Security::e($code) ?>">Yapısal veri türü</label>
          <input type="text" id="schema_type_<?= Security::e($code) ?>"
                 name="t[<?= Security::e($code) ?>][schema_type]" maxlength="40"
                 value="<?= Security::e($t['schema_type'] ?? '') ?>">
          <span class="field__hint">Boş bırakılırsa sayfa türüne göre seçilir (Service, ProfessionalService).</span>
        </div>

        <?php if (!empty($t['title'])): ?>
          <div class="serp" aria-label="Google sonuc onizlemesi">
            <span class="serp__url"><?= Security::e(url('/' . ($t['slug'] ?? ''), $code)) ?></span>
            <span class="serp__title"><?= Security::e(str_limit((string) ($t['meta_title'] ?: $t['title']), 60, '…')) ?></span>
            <span class="serp__desc"><?= Security::e(str_limit((string) ($t['meta_description'] ?: ($t['excerpt'] ?? '')), 155)) ?></span>
          </div>
        <?php endif; ?>
      </details>
    </section>
  <?php endforeach; ?>

  <div class="form__actions form__actions--sticky">
    <button class="btn btn--primary" type="submit"><?= $isNew ? 'Sayfayı oluştur' : 'Kaydet' ?></button>
    <a class="btn btn--ghost" href="<?= Security::e(admin_url('sayfalar')) ?>">Listeye dön</a>
  </div>
</form>
