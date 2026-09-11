<?php
/**
 * Ayarlar ekrani.  DOCS.md 9.11, 11.2
 *
 * NAP bilgileri Google Isletme Profili ile birebir ayni yazilmalidir.
 *
 * @var array $fields
 * @var array $settings
 * @var array $hours
 * @var array $social
 * @var array $languages
 */

declare(strict_types=1);

use Arcates\Core\Security;

$value = static function (string $key) use ($settings): string {
    return (string) old($key, $settings[$key] ?? '');
};
?>

<form method="post" action="<?= Security::e(admin_url('ayarlar')) ?>">
  <?= csrf_field() ?>

  <section class="panel panel--form">
    <h2 class="panel__title">Site</h2>

    <?php foreach (['site_name', 'site_tagline', 'meta_title_pattern', 'meta_description'] as $key): ?>
      <div class="field">
        <label for="<?= Security::e($key) ?>"><?= Security::e($fields[$key][0]) ?></label>
        <input type="text" id="<?= Security::e($key) ?>" name="<?= Security::e($key) ?>"
               maxlength="<?= Security::e((string) $fields[$key][1]) ?>"
               value="<?= Security::e($value($key)) ?>">
        <?php if ($key === 'meta_title_pattern'): ?>
          <span class="field__hint">Kullanılabilir yer tutucular: <code>%title%</code>, <code>%site%</code>.</span>
        <?php endif; ?>
        <?php if ($m = error_for($key)): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
      </div>
    <?php endforeach; ?>

    <div class="field">
      <label for="projects_notice"><?= Security::e($fields['projects_notice'][0]) ?></label>
      <textarea id="projects_notice" name="projects_notice" rows="3"
                maxlength="<?= Security::e((string) $fields['projects_notice'][1]) ?>"><?= Security::e($value('projects_notice')) ?></textarea>
      <span class="field__hint">Örnek proje kayıtlarının gerçek müşteri işi olmadığını ziyaretçiye açıkça belirtir.</span>
      <?php if ($m = error_for('projects_notice')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
    </div>

    <div class="field">
      <label for="default_lang">Varsayılan dil</label>
      <select id="default_lang" name="default_lang">
        <?php $current = (string) old('default_lang', $settings['default_lang'] ?? 'tr'); ?>
        <?php foreach ($languages as $code => $lang): ?>
          <option value="<?= Security::e($code) ?>" <?= $current === $code ? 'selected' : '' ?>>
            <?= Security::e($lang['name']) ?> (<?= Security::e($code) ?>)
          </option>
        <?php endforeach; ?>
      </select>
      <span class="field__hint">Varsayılan dil adres önekiyle yayınlanmaz.</span>
    </div>

    <div class="field">
      <span class="field__label">Yayındaki diller</span>
      <ul class="switch-list">
        <?php foreach ($allLanguages as $code => $lang): ?>
          <?php $isDefault = $code === $current; ?>
          <li>
            <label class="switch-list__item">
              <input type="checkbox" name="active_langs[]" value="<?= Security::e($code) ?>"
                     <?= ((int) $lang['is_active'] === 1 || $isDefault) ? 'checked' : '' ?>
                     <?= $isDefault ? 'disabled' : '' ?>>
              <span><?= Security::e($lang['name']) ?> (<?= Security::e($code) ?>)</span>
              <?php if ($isDefault): ?><span class="badge">varsayılan</span><?php endif; ?>
            </label>
          </li>
        <?php endforeach; ?>
      </ul>
      <span class="field__hint">
        Kapalı dil üst menüde görünmez ve <code>hreflang</code> setine girmez.
        Bir dili, çevirileri girildikten sonra açın; yoksa ziyaretçi Türkçe içeriğe düşer.
      </span>
    </div>
  </section>

  <section class="panel panel--form">
    <h2 class="panel__title">İşletme bilgileri (NAP)</h2>
    <p class="muted">
      Bu alanlar yapısal veride <code>ProfessionalService</code> olarak yayınlanır ve
      Google İşletme Profili ile <strong>birebir aynı</strong> yazılmalıdır.
    </p>

    <div class="grid grid--2">
      <?php foreach (['nap_name', 'nap_phone', 'nap_email', 'nap_street', 'nap_district', 'nap_city', 'nap_postcode', 'nap_country', 'nap_lat', 'nap_lng'] as $key): ?>
        <div class="field">
          <label for="<?= Security::e($key) ?>"><?= Security::e($fields[$key][0]) ?></label>
          <input type="text" id="<?= Security::e($key) ?>" name="<?= Security::e($key) ?>"
                 maxlength="<?= Security::e((string) $fields[$key][1]) ?>"
                 value="<?= Security::e($value($key)) ?>">
          <?php if ($m = error_for($key)): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="panel panel--form">
    <h2 class="panel__title">Çalışma saatleri</h2>
    <p class="muted">Gün biçimi schema.org standardındadır: <code>Mo-Fr</code>, <code>Sa</code>, <code>Su</code>. Yapısal veri bu kodu olduğu gibi kullanır; ziyaretçi sayfada sayfanın dilindeki gün adını görür (<code>Mo-Fr</code> → <em>Pzt–Cum</em>). Kod dışında bir metin yazarsanız aynen görünür.</p>

    <div class="repeat" data-repeat="hours">
      <?php $rows = $hours ?: [['days' => '', 'opens' => '', 'closes' => '']]; ?>
      <?php foreach ($rows as $i => $row): ?>
        <div class="repeat__row">
          <div class="field">
            <label for="hours_days_<?= (int) $i ?>">Günler</label>
            <input type="text" id="hours_days_<?= (int) $i ?>" name="hours_days[]" maxlength="40"
                   value="<?= Security::e($row['days'] ?? '') ?>">
          </div>
          <div class="field">
            <label for="hours_opens_<?= (int) $i ?>">Açılış</label>
            <input type="time" id="hours_opens_<?= (int) $i ?>" name="hours_opens[]"
                   value="<?= Security::e($row['opens'] ?? '') ?>">
          </div>
          <div class="field">
            <label for="hours_closes_<?= (int) $i ?>">Kapanış</label>
            <input type="time" id="hours_closes_<?= (int) $i ?>" name="hours_closes[]"
                   value="<?= Security::e($row['closes'] ?? '') ?>">
          </div>
          <button class="btn btn--ghost btn--sm" type="button" data-repeat-remove>Kaldır</button>
        </div>
      <?php endforeach; ?>
    </div>
    <button class="btn btn--ghost btn--sm" type="button" data-repeat-add="hours">Satır ekle</button>
  </section>

  <section class="panel panel--form">
    <h2 class="panel__title">Sosyal hesaplar</h2>

    <div class="repeat" data-repeat="social">
      <?php $rows = $social ?: [['label' => '', 'url' => '']]; ?>
      <?php foreach ($rows as $i => $row): ?>
        <div class="repeat__row">
          <div class="field">
            <label for="social_label_<?= (int) $i ?>">Ad</label>
            <input type="text" id="social_label_<?= (int) $i ?>" name="social_label[]" maxlength="60"
                   value="<?= Security::e($row['label'] ?? '') ?>">
          </div>
          <div class="field field--wide">
            <label for="social_url_<?= (int) $i ?>">Adres</label>
            <input type="url" id="social_url_<?= (int) $i ?>" name="social_url[]" maxlength="255"
                   value="<?= Security::e($row['url'] ?? '') ?>">
          </div>
          <button class="btn btn--ghost btn--sm" type="button" data-repeat-remove>Kaldır</button>
        </div>
      <?php endforeach; ?>
    </div>
    <button class="btn btn--ghost btn--sm" type="button" data-repeat-add="social">Satır ekle</button>
  </section>

  <section class="panel panel--form">
    <h2 class="panel__title">Bakım modu</h2>

    <div class="field field--check">
      <label>
        <input type="checkbox" name="maintenance_mode" value="1"
               <?= (string) ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
        Bakım modunu aç
      </label>
      <span class="field__hint">Ziyaretçiler bakım sayfasını görür; oturum açmış yöneticiler siteyi normal görür.</span>
    </div>

    <div class="field">
      <label for="maintenance_text">Bakım mesajı</label>
      <input type="text" id="maintenance_text" name="maintenance_text" maxlength="320"
             value="<?= Security::e($value('maintenance_text')) ?>">
    </div>
  </section>

  <div class="form__actions form__actions--sticky">
    <button class="btn btn--primary" type="submit">Ayarları kaydet</button>
  </div>
</form>
