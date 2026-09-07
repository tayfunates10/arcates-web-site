<?php
/**
 * Sayfa listesi — tur filtresi ve dil sekmeleri.  DOCS.md 9.3
 *
 * @var array  $pages
 * @var array  $types
 * @var string $type
 * @var string $search
 * @var string $lang
 * @var array  $langs
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<form class="filters" method="get" action="<?= Security::e(admin_url('sayfalar')) ?>">
  <div class="field">
    <label for="tur">Tür</label>
    <select id="tur" name="tur">
      <option value="">Tümü</option>
      <?php foreach ($types as $key => $label): ?>
        <option value="<?= Security::e($key) ?>" <?= $type === $key ? 'selected' : '' ?>>
          <?= Security::e($label) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="field">
    <label for="dil">Dil</label>
    <select id="dil" name="dil">
      <?php foreach ($langs as $code => $item): ?>
        <option value="<?= Security::e($code) ?>" <?= $lang === $code ? 'selected' : '' ?>>
          <?= Security::e($item['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="field">
    <label for="ara">Ara</label>
    <input type="search" id="ara" name="ara" value="<?= Security::e($search) ?>" placeholder="Başlık veya adres">
  </div>

  <button class="btn btn--ghost btn--sm" type="submit">Filtrele</button>
  <a class="btn btn--primary btn--sm" href="<?= Security::e(admin_url('sayfalar/yeni')) ?>">Yeni sayfa</a>
</form>

<section class="panel">
  <div class="table-scroll">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Başlık</th>
          <th scope="col">Tür</th>
          <th scope="col">Adres</th>
          <th scope="col" class="num">Kelime</th>
          <th scope="col">Durum</th>
          <th scope="col">Güncelleme</th>
          <th scope="col"><span class="visually-hidden">İşlemler</span></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$pages): ?>
          <tr><td colspan="7" class="muted">Bu filtreye uyan sayfa yok.</td></tr>
        <?php endif; ?>

        <?php foreach ($pages as $page): ?>
          <?php
          $isLocation = $page['type'] === 'location';
          $words      = (int) ($page['word_count'] ?? 0);
          $thin       = $isLocation ? $words < 500 : $words < 300;
          ?>
          <tr>
            <td>
              <a href="<?= Security::e(admin_url('sayfalar/' . (int) $page['id'])) ?>">
                <?= Security::e($page['title'] ?? '(bu dilde çeviri yok)') ?>
              </a>
            </td>
            <td><?= Security::e($types[$page['type']] ?? $page['type']) ?></td>
            <td>
              <?php if (!empty($page['slug'])): ?>
                <code>/<?= Security::e($page['slug']) ?></code>
              <?php else: ?>
                <span class="muted">—</span>
              <?php endif; ?>
            </td>
            <td class="num">
              <span class="tag tag--<?= $thin ? 'warn' : 'ok' ?>"><?= Security::e((string) $words) ?></span>
            </td>
            <td>
              <span class="tag tag--<?= $page['status'] === 'published' ? 'ok' : 'draft' ?>">
                <?= $page['status'] === 'published' ? 'Yayında' : 'Taslak' ?>
              </span>
            </td>
            <td><?= Security::e(format_date($page['updated_at'])) ?></td>
            <td class="row-actions">
              <?php if ($page['status'] === 'published' && !empty($page['slug'])): ?>
                <a class="btn btn--ghost btn--sm" target="_blank" rel="noopener"
                   href="<?= Security::e(($lang === Arcates\Core\Lang::defaultCode() ? '' : '/' . $lang) . '/' . $page['slug']) ?>">Gör</a>
              <?php endif; ?>

              <form method="post" action="<?= Security::e(admin_url('sayfalar/' . (int) $page['id'] . '/durum')) ?>">
                <?= csrf_field() ?>
                <button class="btn btn--ghost btn--sm" type="submit">
                  <?= $page['status'] === 'published' ? 'Taslağa al' : 'Yayınla' ?>
                </button>
              </form>

              <form method="post" action="<?= Security::e(admin_url('sayfalar/' . (int) $page['id'] . '/sil')) ?>"
                    data-confirm="Bu sayfayi silmek istiyor musunuz? Islem geri alinamaz.">
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
