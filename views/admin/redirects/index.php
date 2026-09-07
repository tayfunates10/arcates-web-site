<?php
/**
 * Yonlendirmeler ve 404 kayitlari.  DOCS.md 9.8
 *
 * @var array  $redirects
 * @var array  $notFound
 * @var string $search
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<section class="panel panel--form">
  <h2 class="panel__title">Yeni yonlendirme</h2>

  <form method="post" action="<?= Security::e(admin_url('yonlendirmeler')) ?>">
    <?= csrf_field() ?>

    <div class="grid grid--3">
      <div class="field">
        <label for="from_path">Kaynak adres</label>
        <input type="text" id="from_path" name="from_path" maxlength="255" required
               placeholder="/eski-adres" value="<?= Security::e((string) old('from_path')) ?>">
        <?php if ($m = error_for('from_path')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
      </div>

      <div class="field">
        <label for="to_path">Hedef adres</label>
        <input type="text" id="to_path" name="to_path" maxlength="255" required
               placeholder="/yeni-adres" value="<?= Security::e((string) old('to_path')) ?>">
        <?php if ($m = error_for('to_path')): ?><span class="field__error"><?= Security::e($m) ?></span><?php endif; ?>
      </div>

      <div class="field">
        <label for="code">Kod</label>
        <select id="code" name="code">
          <option value="301">301 — kalici</option>
          <option value="302">302 — gecici</option>
          <option value="307">307 — gecici, yontem korunur</option>
          <option value="308">308 — kalici, yontem korunur</option>
        </select>
      </div>
    </div>

    <p class="muted">
      Dongu olusturan kayitlar kabul edilmez: <code>/a → /b</code> varken
      <code>/b → /a</code> eklenemez.
    </p>

    <div class="form__actions">
      <button class="btn btn--primary" type="submit">Yonlendirme ekle</button>
    </div>
  </form>
</section>

<section class="panel">
  <div class="panel__head">
    <h2 class="panel__title"><?= Security::e((string) count($redirects)) ?> yonlendirme</h2>

    <form class="filters" method="get" action="<?= Security::e(admin_url('yonlendirmeler')) ?>">
      <div class="field">
        <label for="ara">Ara</label>
        <input type="search" id="ara" name="ara" value="<?= Security::e($search) ?>" placeholder="Adres">
      </div>
      <button class="btn btn--ghost btn--sm" type="submit">Filtrele</button>
    </form>
  </div>

  <div class="table-scroll">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Kaynak</th>
          <th scope="col">Hedef</th>
          <th scope="col">Kod</th>
          <th scope="col" class="num">Isabet</th>
          <th scope="col">Son isabet</th>
          <th scope="col"><span class="visually-hidden">Islemler</span></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$redirects): ?>
          <tr><td colspan="6" class="muted">Yonlendirme yok.</td></tr>
        <?php endif; ?>

        <?php foreach ($redirects as $row): ?>
          <tr>
            <td><code><?= Security::e($row['from_path']) ?></code></td>
            <td><code><?= Security::e($row['to_path']) ?></code></td>
            <td><?= (int) $row['code'] ?></td>
            <td class="num"><?= (int) $row['hits'] ?></td>
            <td><?= Security::e(format_date($row['last_hit_at'], true) ?: '—') ?></td>
            <td class="row-actions">
              <form method="post" action="<?= Security::e(admin_url('yonlendirmeler/' . (int) $row['id'] . '/sil')) ?>"
                    data-confirm="Bu yonlendirmeyi silmek istiyor musunuz?">
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

<section class="panel">
  <h2 class="panel__title">Bulunamayan adresler</h2>
  <p class="muted">
    Ziyaretcilerin gelip 404 aldigi adresler. Tek tikla yonlendirmeye
    cevirebilirsiniz.
  </p>

  <div class="table-scroll">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Adres</th>
          <th scope="col" class="num">Istek</th>
          <th scope="col">Son gorulme</th>
          <th scope="col">Geldigi yer</th>
          <th scope="col">Yonlendir</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$notFound): ?>
          <tr><td colspan="5" class="muted">Kirik adres yok.</td></tr>
        <?php endif; ?>

        <?php foreach ($notFound as $row): ?>
          <tr>
            <td><code><?= Security::e($row['path']) ?></code></td>
            <td class="num"><?= (int) $row['hits'] ?></td>
            <td><?= Security::e(format_date($row['last_seen'], true)) ?></td>
            <td class="wrap-anywhere"><?= Security::e(str_limit((string) ($row['referrer'] ?? ''), 60) ?: '—') ?></td>
            <td>
              <form class="inline-form" method="post"
                    action="<?= Security::e(admin_url('yonlendirmeler/404/' . (int) $row['id'])) ?>">
                <?= csrf_field() ?>
                <label class="visually-hidden" for="to_<?= (int) $row['id'] ?>">Hedef adres</label>
                <input type="text" id="to_<?= (int) $row['id'] ?>" name="to_path" maxlength="255"
                       placeholder="/hedef-adres" required>
                <button class="btn btn--ghost btn--sm" type="submit">Cevir</button>
              </form>

              <form method="post" action="<?= Security::e(admin_url('yonlendirmeler/404/' . (int) $row['id'] . '/sil')) ?>">
                <?= csrf_field() ?>
                <button class="btn btn--ghost btn--sm" type="submit">Yoksay</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
