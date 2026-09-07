<?php
/**
 * Bolge haritasi ilce noktalari.
 *
 * Noktalar surukle-birak ile konumlandirilir; `map_x` ve `map_y` otomatik
 * hesaplanip gizli alanlara yazilir. JavaScript kapaliyken ayni degerler
 * sayi alanlarindan elle girilebilir.  DOCS.md 9.2, test F-16
 *
 * @var array $districts
 * @var array $pages
 */

declare(strict_types=1);

use Arcates\Core\Security;

$rows = $districts ?: [[
    'id' => 0, 'name' => '', 'map_x' => 500, 'map_y' => 95,
    'label_above' => 0, 'page_id' => null, 'sort' => 1, 'is_active' => 1,
]];
?>

<form method="post" action="<?= Security::e(admin_url('anasayfa/coast/ilceler')) ?>" data-dirty-guard>
  <?= csrf_field() ?>

  <section class="panel">
    <h2 class="panel__title">İlçe noktaları</h2>
    <p class="muted">
      Noktaları sürükleyerek konumlandırın; yatay ve dikey değerler otomatik
      hesaplanır. Fare kullanamıyorsanız aşağıdaki sayı alanlarından da
      girebilirsiniz. Yatay 0-1000, dikey 0-190 arasındadır.
    </p>

    <?php /* Surukle-birak tuvali; JavaScript yoksa yalnizca onizleme olarak
             kalir ve alttaki alanlar tek basina yeterlidir. */ ?>
    <div class="map-canvas" data-map-canvas>
      <svg viewBox="0 0 1000 190" class="map-canvas__svg" role="img"
           aria-label="Ilce noktalarinin harita uzerindeki konumu">
        <rect x="0" y="0" width="1000" height="190" class="map-canvas__bg"></rect>
        <?php foreach ($rows as $i => $district): ?>
          <g class="map-canvas__dot" data-map-dot="<?= (int) $i ?>"
             tabindex="0" role="button"
             aria-label="<?= Security::e(($district['name'] ?: 'Yeni nokta') . ' konumu') ?>">
            <circle cx="<?= (int) $district['map_x'] ?>" cy="<?= (int) $district['map_y'] ?>" r="11"></circle>
            <text x="<?= (int) $district['map_x'] ?>"
                  y="<?= (int) $district['map_y'] + ((int) $district['label_above'] === 1 ? -20 : 32) ?>">
              <?= Security::e($district['name'] ?: '—') ?>
            </text>
          </g>
        <?php endforeach; ?>
      </svg>
    </div>

    <div class="repeat" data-repeat="districts">
      <?php foreach ($rows as $i => $district): ?>
        <div class="repeat__row menu-row" data-map-row="<?= (int) $i ?>">
          <input type="hidden" name="districts[<?= (int) $i ?>][id]" value="<?= (int) ($district['id'] ?? 0) ?>">

          <div class="field">
            <label for="d_name_<?= (int) $i ?>">İlçe adı</label>
            <input type="text" id="d_name_<?= (int) $i ?>" name="districts[<?= (int) $i ?>][name]"
                   maxlength="60" value="<?= Security::e($district['name'] ?? '') ?>" data-map-name>
          </div>

          <div class="field">
            <label for="d_x_<?= (int) $i ?>">Yatay (0-1000)</label>
            <input type="number" id="d_x_<?= (int) $i ?>" name="districts[<?= (int) $i ?>][map_x]"
                   min="0" max="1000" value="<?= (int) ($district['map_x'] ?? 500) ?>" data-map-x>
          </div>

          <div class="field">
            <label for="d_y_<?= (int) $i ?>">Dikey (0-190)</label>
            <input type="number" id="d_y_<?= (int) $i ?>" name="districts[<?= (int) $i ?>][map_y]"
                   min="0" max="190" value="<?= (int) ($district['map_y'] ?? 95) ?>" data-map-y>
          </div>

          <div class="field">
            <label for="d_page_<?= (int) $i ?>">Bağlı sayfa</label>
            <select id="d_page_<?= (int) $i ?>" name="districts[<?= (int) $i ?>][page_id]">
              <option value="0">— yok —</option>
              <?php foreach ($pages as $page): ?>
                <option value="<?= (int) $page['id'] ?>"
                        <?= (int) ($district['page_id'] ?? 0) === (int) $page['id'] ? 'selected' : '' ?>>
                  <?= Security::e($page['title']) ?><?= $page['status'] === 'draft' ? ' (taslak)' : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label for="d_sort_<?= (int) $i ?>">Sıra</label>
            <input type="number" id="d_sort_<?= (int) $i ?>" name="districts[<?= (int) $i ?>][sort]"
                   value="<?= (int) ($district['sort'] ?? $i + 1) ?>">
          </div>

          <div class="field field--check">
            <label>
              <input type="checkbox" name="districts[<?= (int) $i ?>][label_above]" value="1"
                     <?= (int) ($district['label_above'] ?? 0) === 1 ? 'checked' : '' ?>>
              Etiket üstte
            </label>
            <label>
              <input type="checkbox" name="districts[<?= (int) $i ?>][is_active]" value="1"
                     <?= (int) ($district['is_active'] ?? 1) === 1 ? 'checked' : '' ?>>
              Etkin
            </label>
          </div>

          <button class="btn btn--ghost btn--sm" type="button" data-repeat-remove>Kaldır</button>
        </div>
      <?php endforeach; ?>
    </div>

    <button class="btn btn--ghost btn--sm" type="button" data-repeat-add="districts">Nokta ekle</button>
  </section>

  <div class="form__actions form__actions--sticky">
    <button class="btn btn--primary" type="submit">İlçe noktalarını kaydet</button>
  </div>
</form>
