<?php
/**
 * Anasayfa bolumu duzenleme.  DOCS.md 9.2
 *
 * Kahraman bolumunde canli onizleme; bolge haritasinda ilce noktalari
 * surukle-birak ile konumlandirilir.
 *
 * @var string $key
 * @var string $label
 * @var array  $section
 * @var array  $contents
 * @var array  $langs
 * @var array  $districts
 * @var array  $pages
 */

declare(strict_types=1);

use Arcates\Core\Lang;
use Arcates\Core\Security;

$default = Lang::defaultCode();
$config  = $section['config'] ?? [];

/** Alan adi kisayolu: c[dil][alan] */
$field = static fn (string $lang, string $name): string => 'c[' . $lang . '][' . $name . ']';
?>

<p><a class="btn btn--ghost btn--sm" href="<?= Security::e(admin_url('anasayfa')) ?>">← Bolum listesi</a></p>

<form method="post" action="<?= Security::e(admin_url('anasayfa/' . $key)) ?>" data-dirty-guard>
  <?= csrf_field() ?>

  <section class="panel panel--form">
    <div class="field field--check">
      <label>
        <input type="checkbox" name="is_active" value="1" <?= ($section['is_active'] ?? true) ? 'checked' : '' ?>>
        Bu bolum anasayfada gosterilsin
      </label>
    </div>

    <?php if ($key === 'strip'): ?>
      <div class="grid grid--2">
        <div class="field">
          <label for="speed">Tam tur suresi (saniye)</label>
          <input type="number" id="speed" name="config[speed]" min="10" max="120"
                 value="<?= (int) ($config['speed'] ?? 34) ?>">
          <span class="field__hint">Sartnamedeki deger 34 saniyedir.</span>
        </div>
        <div class="field">
          <label for="mobile_speed">Mobil hiz (%)</label>
          <input type="number" id="mobile_speed" name="config[mobile_speed_percent]" min="30" max="100"
                 value="<?= (int) ($config['mobile_speed_percent'] ?? 70) ?>">
          <span class="field__hint">Mobilde serit bu oranda yavaslar.</span>
        </div>
      </div>
    <?php elseif ($key === 'works' || $key === 'faq'): ?>
      <div class="field">
        <label for="limit">Gosterilecek kayit sayisi</label>
        <input type="number" id="limit" name="config[limit]" min="1" max="24"
               value="<?= (int) ($config['limit'] ?? 6) ?>">
      </div>
    <?php elseif ($key === 'services'): ?>
      <div class="field">
        <label for="columns">Sutun sayisi</label>
        <input type="number" id="columns" name="config[columns]" min="2" max="4"
               value="<?= (int) ($config['columns'] ?? 3) ?>">
      </div>
    <?php endif; ?>
  </section>

  <div class="tabs" data-tabs="lang">
    <?php foreach ($langs as $code => $lang): ?>
      <button class="tabs__button" type="button" role="tab" data-tab="<?= Security::e($code) ?>">
        <?= Security::e($lang['name']) ?>
        <?php if ($code === $default): ?><span class="tabs__badge">varsayilan</span><?php endif; ?>
      </button>
    <?php endforeach; ?>
  </div>

  <?php foreach ($langs as $code => $lang): ?>
    <?php $c = $contents[$code] ?? []; ?>
    <section class="panel panel--form" data-tab-panel="<?= Security::e($code) ?>" data-tab-group="lang">
      <h2 class="panel__title"><?= Security::e($label) ?> — <?= Security::e($lang['name']) ?></h2>

      <?php if ($key === 'header'): ?>
        <div class="grid grid--2">
          <div class="field">
            <label for="cta_label_<?= Security::e($code) ?>">Buton metni</label>
            <input type="text" id="cta_label_<?= Security::e($code) ?>"
                   name="<?= Security::e($field($code, 'cta][label')) ?>" maxlength="60"
                   value="<?= Security::e($c['cta']['label'] ?? '') ?>">
          </div>
          <div class="field">
            <label for="cta_url_<?= Security::e($code) ?>">Buton hedefi</label>
            <input type="text" id="cta_url_<?= Security::e($code) ?>"
                   name="<?= Security::e($field($code, 'cta][url')) ?>" maxlength="255"
                   value="<?= Security::e($c['cta']['url'] ?? '') ?>">
          </div>
        </div>

      <?php elseif ($key === 'hero'): ?>
        <div class="field">
          <label for="badge_<?= Security::e($code) ?>">Rozet metni</label>
          <input type="text" id="badge_<?= Security::e($code) ?>" name="<?= Security::e($field($code, 'badge')) ?>"
                 maxlength="120" value="<?= Security::e($c['badge'] ?? '') ?>" data-preview="badge">
        </div>

        <?php foreach ([1, 2, 3] as $line): ?>
          <div class="field">
            <label for="line<?= $line ?>_<?= Security::e($code) ?>">
              Baslik satiri <?= $line ?><?= $line === 3 ? ' (degrade renkli)' : '' ?>
            </label>
            <input type="text" id="line<?= $line ?>_<?= Security::e($code) ?>"
                   name="<?= Security::e($field($code, 'line' . $line)) ?>" maxlength="120"
                   value="<?= Security::e($c['line' . $line] ?? '') ?>" data-preview="line<?= $line ?>">
          </div>
        <?php endforeach; ?>

        <div class="field">
          <label for="description_<?= Security::e($code) ?>">Aciklama</label>
          <textarea id="description_<?= Security::e($code) ?>" name="<?= Security::e($field($code, 'description')) ?>"
                    maxlength="600" rows="4" data-preview="description"><?= Security::e($c['description'] ?? '') ?></textarea>
        </div>

        <?php foreach (['cta1' => 'Birinci buton', 'cta2' => 'Ikinci buton'] as $cta => $ctaLabel): ?>
          <div class="grid grid--2">
            <div class="field">
              <label for="<?= Security::e($cta) ?>_label_<?= Security::e($code) ?>"><?= Security::e($ctaLabel) ?> metni</label>
              <input type="text" id="<?= Security::e($cta) ?>_label_<?= Security::e($code) ?>"
                     name="<?= Security::e($field($code, $cta . '][label')) ?>" maxlength="60"
                     value="<?= Security::e($c[$cta]['label'] ?? '') ?>" data-preview="<?= Security::e($cta) ?>">
            </div>
            <div class="field">
              <label for="<?= Security::e($cta) ?>_url_<?= Security::e($code) ?>"><?= Security::e($ctaLabel) ?> hedefi</label>
              <input type="text" id="<?= Security::e($cta) ?>_url_<?= Security::e($code) ?>"
                     name="<?= Security::e($field($code, $cta . '][url')) ?>" maxlength="255"
                     value="<?= Security::e($c[$cta]['url'] ?? '') ?>">
            </div>
          </div>
        <?php endforeach; ?>

        <?php if ($code === $default): ?>
          <?php /* Canli onizleme. DOCS.md 9.2 */ ?>
          <div class="hero-preview" data-hero-preview aria-live="polite">
            <span class="hero-preview__badge" data-preview-out="badge"><?= Security::e($c['badge'] ?? '') ?></span>
            <span class="hero-preview__title">
              <span data-preview-out="line1"><?= Security::e($c['line1'] ?? '') ?></span>
              <span data-preview-out="line2"><?= Security::e($c['line2'] ?? '') ?></span>
              <span class="hero-preview__accent" data-preview-out="line3"><?= Security::e($c['line3'] ?? '') ?></span>
            </span>
            <span class="hero-preview__text" data-preview-out="description"><?= Security::e($c['description'] ?? '') ?></span>
            <span class="hero-preview__actions">
              <span class="hero-preview__btn" data-preview-out="cta1"><?= Security::e($c['cta1']['label'] ?? '') ?></span>
              <span class="hero-preview__btn hero-preview__btn--ghost" data-preview-out="cta2"><?= Security::e($c['cta2']['label'] ?? '') ?></span>
            </span>
          </div>
        <?php endif; ?>

      <?php elseif ($key === 'strip'): ?>
        <p class="muted">Her satir bir etiket. Bos satirlar kaydedilmez.</p>
        <div class="repeat" data-repeat="tags-<?= Security::e($code) ?>">
          <?php $tags = $c['tags'] ?? ['']; ?>
          <?php foreach ($tags as $i => $tag): ?>
            <div class="repeat__row">
              <div class="field field--wide">
                <label for="tag_<?= Security::e($code) ?>_<?= (int) $i ?>">Etiket</label>
                <input type="text" id="tag_<?= Security::e($code) ?>_<?= (int) $i ?>"
                       name="c[<?= Security::e($code) ?>][tags][]" maxlength="60"
                       value="<?= Security::e($tag) ?>">
              </div>
              <button class="btn btn--ghost btn--sm" type="button" data-repeat-remove>Kaldir</button>
            </div>
          <?php endforeach; ?>
        </div>
        <button class="btn btn--ghost btn--sm" type="button" data-repeat-add="tags-<?= Security::e($code) ?>">Etiket ekle</button>

      <?php elseif ($key === 'services'): ?>
        <div class="field">
          <label for="title_<?= Security::e($code) ?>">Bolum basligi</label>
          <input type="text" id="title_<?= Security::e($code) ?>" name="<?= Security::e($field($code, 'title')) ?>"
                 maxlength="160" value="<?= Security::e($c['title'] ?? '') ?>">
        </div>
        <div class="field">
          <label for="desc_<?= Security::e($code) ?>">Bolum aciklamasi</label>
          <textarea id="desc_<?= Security::e($code) ?>" name="<?= Security::e($field($code, 'description')) ?>"
                    maxlength="400" rows="3"><?= Security::e($c['description'] ?? '') ?></textarea>
        </div>

        <h3 class="panel__title">Kartlar</h3>
        <div class="repeat" data-repeat="cards-<?= Security::e($code) ?>">
          <?php $cards = $c['cards'] ?? [['icon' => 'layout', 'color' => 'blue', 'title' => '', 'text' => '', 'url' => '']]; ?>
          <?php foreach ($cards as $i => $card): ?>
            <div class="repeat__row menu-row">
              <div class="field">
                <label for="card_icon_<?= Security::e($code) ?>_<?= (int) $i ?>">Ikon</label>
                <select id="card_icon_<?= Security::e($code) ?>_<?= (int) $i ?>"
                        name="c[<?= Security::e($code) ?>][cards][<?= (int) $i ?>][icon]">
                  <?php foreach (['layout' => 'Duzen', 'cart' => 'Sepet', 'calendar' => 'Takvim', 'search' => 'Arama', 'globe' => 'Kure', 'shield' => 'Kalkan'] as $icon => $iconLabel): ?>
                    <option value="<?= Security::e($icon) ?>" <?= ($card['icon'] ?? '') === $icon ? 'selected' : '' ?>>
                      <?= Security::e($iconLabel) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field">
                <label for="card_color_<?= Security::e($code) ?>_<?= (int) $i ?>">Renk</label>
                <select id="card_color_<?= Security::e($code) ?>_<?= (int) $i ?>"
                        name="c[<?= Security::e($code) ?>][cards][<?= (int) $i ?>][color]">
                  <?php foreach (['blue', 'coral', 'cyan', 'mint', 'violet', 'sun'] as $colour): ?>
                    <option value="<?= Security::e($colour) ?>" <?= ($card['color'] ?? '') === $colour ? 'selected' : '' ?>>
                      <?= Security::e($colour) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field">
                <label for="card_title_<?= Security::e($code) ?>_<?= (int) $i ?>">Baslik</label>
                <input type="text" id="card_title_<?= Security::e($code) ?>_<?= (int) $i ?>"
                       name="c[<?= Security::e($code) ?>][cards][<?= (int) $i ?>][title]" maxlength="120"
                       value="<?= Security::e($card['title'] ?? '') ?>">
              </div>
              <div class="field field--wide">
                <label for="card_text_<?= Security::e($code) ?>_<?= (int) $i ?>">Metin</label>
                <input type="text" id="card_text_<?= Security::e($code) ?>_<?= (int) $i ?>"
                       name="c[<?= Security::e($code) ?>][cards][<?= (int) $i ?>][text]" maxlength="320"
                       value="<?= Security::e($card['text'] ?? '') ?>">
              </div>
              <div class="field">
                <label for="card_url_<?= Security::e($code) ?>_<?= (int) $i ?>">Baglanti</label>
                <input type="text" id="card_url_<?= Security::e($code) ?>_<?= (int) $i ?>"
                       name="c[<?= Security::e($code) ?>][cards][<?= (int) $i ?>][url]" maxlength="255"
                       value="<?= Security::e($card['url'] ?? '') ?>">
              </div>
              <button class="btn btn--ghost btn--sm" type="button" data-repeat-remove>Kaldir</button>
            </div>
          <?php endforeach; ?>
        </div>
        <button class="btn btn--ghost btn--sm" type="button" data-repeat-add="cards-<?= Security::e($code) ?>">Kart ekle</button>

      <?php elseif ($key === 'steps'): ?>
        <div class="field">
          <label for="title_<?= Security::e($code) ?>">Bolum basligi</label>
          <input type="text" id="title_<?= Security::e($code) ?>" name="<?= Security::e($field($code, 'title')) ?>"
                 maxlength="160" value="<?= Security::e($c['title'] ?? '') ?>">
        </div>

        <div class="repeat" data-repeat="steps-<?= Security::e($code) ?>">
          <?php $items = $c['items'] ?? [['title' => '', 'text' => '']]; ?>
          <?php foreach ($items as $i => $item): ?>
            <div class="repeat__row menu-row">
              <div class="field">
                <label for="step_title_<?= Security::e($code) ?>_<?= (int) $i ?>">Adim basligi</label>
                <input type="text" id="step_title_<?= Security::e($code) ?>_<?= (int) $i ?>"
                       name="c[<?= Security::e($code) ?>][items][<?= (int) $i ?>][title]" maxlength="120"
                       value="<?= Security::e($item['title'] ?? '') ?>">
              </div>
              <div class="field field--wide">
                <label for="step_text_<?= Security::e($code) ?>_<?= (int) $i ?>">Adim metni</label>
                <input type="text" id="step_text_<?= Security::e($code) ?>_<?= (int) $i ?>"
                       name="c[<?= Security::e($code) ?>][items][<?= (int) $i ?>][text]" maxlength="400"
                       value="<?= Security::e($item['text'] ?? '') ?>">
              </div>
              <button class="btn btn--ghost btn--sm" type="button" data-repeat-remove>Kaldir</button>
            </div>
          <?php endforeach; ?>
        </div>
        <button class="btn btn--ghost btn--sm" type="button" data-repeat-add="steps-<?= Security::e($code) ?>">Adim ekle</button>

      <?php else: ?>
        <?php /* coast, works, faq, cta, footer icin ortak metin alanlari */ ?>
        <div class="field">
          <label for="title_<?= Security::e($code) ?>">Baslik</label>
          <input type="text" id="title_<?= Security::e($code) ?>" name="<?= Security::e($field($code, 'title')) ?>"
                 maxlength="160" value="<?= Security::e($c['title'] ?? '') ?>">
        </div>

        <div class="field">
          <label for="text_<?= Security::e($code) ?>">
            <?= $key === 'cta' ? 'Metin' : ($key === 'footer' ? 'Kisa tanim' : 'Aciklama') ?>
          </label>
          <textarea id="text_<?= Security::e($code) ?>" rows="3" maxlength="400"
                    name="<?= Security::e($field($code, $key === 'cta' ? 'text' : ($key === 'footer' ? 'about' : 'description'))) ?>"><?php
            echo Security::e($c[$key === 'cta' ? 'text' : ($key === 'footer' ? 'about' : 'description')] ?? '');
          ?></textarea>
        </div>

        <?php if (in_array($key, ['works', 'cta'], true)): ?>
          <?php $ctaKeys = $key === 'cta' ? ['cta1' => 'Birinci buton', 'cta2' => 'Ikinci buton'] : ['cta' => 'Buton']; ?>
          <?php foreach ($ctaKeys as $ctaKey => $ctaLabel): ?>
            <div class="grid grid--2">
              <div class="field">
                <label for="<?= Security::e($ctaKey) ?>_label_<?= Security::e($code) ?>"><?= Security::e($ctaLabel) ?> metni</label>
                <input type="text" id="<?= Security::e($ctaKey) ?>_label_<?= Security::e($code) ?>"
                       name="<?= Security::e($field($code, $ctaKey . '][label')) ?>" maxlength="60"
                       value="<?= Security::e($c[$ctaKey]['label'] ?? '') ?>">
              </div>
              <div class="field">
                <label for="<?= Security::e($ctaKey) ?>_url_<?= Security::e($code) ?>"><?= Security::e($ctaLabel) ?> hedefi</label>
                <input type="text" id="<?= Security::e($ctaKey) ?>_url_<?= Security::e($code) ?>"
                       name="<?= Security::e($field($code, $ctaKey . '][url')) ?>" maxlength="255"
                       value="<?= Security::e($c[$ctaKey]['url'] ?? '') ?>">
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      <?php endif; ?>
    </section>
  <?php endforeach; ?>

  <div class="form__actions form__actions--sticky">
    <button class="btn btn--primary" type="submit">Bolumu kaydet</button>
    <a class="btn btn--ghost" href="<?= Security::e(admin_url('anasayfa')) ?>">Listeye don</a>
  </div>
</form>

<?php if ($key === 'coast'): ?>
  <?= partial('admin/home/districts', ['districts' => $districts, 'pages' => $pages]) ?>
<?php endif; ?>
