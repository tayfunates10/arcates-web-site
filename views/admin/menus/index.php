<?php
/**
 * Menu duzenleyici.  DOCS.md 8.5, 9.3
 *
 * @var string $menuKey
 * @var array  $keys
 * @var array  $items
 * @var array  $langs
 * @var array  $pages
 */

declare(strict_types=1);

use Arcates\Core\Security;

$rows = $items ?: [[
    'id' => 0, 'parent_id' => null, 'page_id' => null, 'url' => '',
    'target' => '_self', 'sort' => 1, 'labels' => [],
]];
?>

<div class="tabs">
  <?php foreach ($keys as $key => $label): ?>
    <a class="tabs__button<?= $menuKey === $key ? ' is-current' : '' ?>"
       href="<?= Security::e(admin_url('menuler') . '?menu=' . $key) ?>">
      <?= Security::e($label) ?>
    </a>
  <?php endforeach; ?>
</div>

<form method="post" action="<?= Security::e(admin_url('menuler')) ?>" data-dirty-guard>
  <?= csrf_field() ?>
  <input type="hidden" name="menu" value="<?= Security::e($menuKey) ?>">

  <section class="panel">
    <p class="muted">
      Sıra sayısı küçükten büyüğe dizilir. Bir ögeyi kaldırmak için tüm dillerdeki
      etiketlerini boşaltıp kaydedin.
    </p>

    <div class="menu-rows repeat" data-repeat="menu">
      <?php foreach ($rows as $i => $row): ?>
        <div class="menu-row repeat__row">
          <input type="hidden" name="items[<?= (int) $i ?>][id]" value="<?= (int) ($row['id'] ?? 0) ?>">

          <?php foreach ($langs as $code => $lang): ?>
            <div class="field">
              <label for="label_<?= (int) $i ?>_<?= Security::e($code) ?>">
                Etiket (<?= Security::e($code) ?>)
              </label>
              <input type="text" id="label_<?= (int) $i ?>_<?= Security::e($code) ?>"
                     name="items[<?= (int) $i ?>][labels][<?= Security::e($code) ?>]" maxlength="120"
                     value="<?= Security::e($row['labels'][$code] ?? '') ?>">
            </div>
          <?php endforeach; ?>

          <div class="field">
            <label for="page_<?= (int) $i ?>">Sayfa</label>
            <select id="page_<?= (int) $i ?>" name="items[<?= (int) $i ?>][page_id]">
              <option value="0">— elle adres —</option>
              <?php foreach ($pages as $page): ?>
                <option value="<?= (int) $page['id'] ?>"
                        <?= (int) ($row['page_id'] ?? 0) === (int) $page['id'] ? 'selected' : '' ?>>
                  <?= Security::e($page['title']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label for="url_<?= (int) $i ?>">Adres</label>
            <input type="text" id="url_<?= (int) $i ?>" name="items[<?= (int) $i ?>][url]" maxlength="255"
                   value="<?= Security::e($row['url'] ?? '') ?>" placeholder="/iletisim">
          </div>

          <div class="field">
            <label for="sort_<?= (int) $i ?>">Sıra</label>
            <input type="number" id="sort_<?= (int) $i ?>" name="items[<?= (int) $i ?>][sort]"
                   value="<?= (int) ($row['sort'] ?? $i + 1) ?>">
          </div>

          <div class="field">
            <label for="target_<?= (int) $i ?>">Hedef</label>
            <select id="target_<?= (int) $i ?>" name="items[<?= (int) $i ?>][target]">
              <option value="_self" <?= ($row['target'] ?? '_self') === '_self' ? 'selected' : '' ?>>Aynı sekme</option>
              <option value="_blank" <?= ($row['target'] ?? '') === '_blank' ? 'selected' : '' ?>>Yeni sekme</option>
            </select>
          </div>

          <button class="btn btn--ghost btn--sm" type="button" data-repeat-remove>Kaldır</button>
        </div>
      <?php endforeach; ?>
    </div>

    <button class="btn btn--ghost btn--sm" type="button" data-repeat-add="menu">Öge ekle</button>
  </section>

  <div class="form__actions form__actions--sticky">
    <button class="btn btn--primary" type="submit">Menüyü kaydet</button>
  </div>
</form>
