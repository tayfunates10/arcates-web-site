<?php
/**
 * SEO paneli.  DOCS.md 9.7
 *
 * @var string $robots
 * @var string $robotsPreview
 * @var int    $entryCount
 * @var array  $metaTable
 * @var int    $issueCount
 * @var array  $settings
 * @var array  $langs
 */

declare(strict_types=1);

use Arcates\Core\Security;
use Arcates\Core\Seo;
?>

<div class="cards">
  <div class="card card--stat">
    <span class="card__label">Site haritasındaki adres</span>
    <strong class="card__value"><?= Security::e((string) $entryCount) ?></strong>
  </div>
  <div class="card card--stat">
    <span class="card__label">Denetlenen sayfa çevirisi</span>
    <strong class="card__value"><?= Security::e((string) count($metaTable)) ?></strong>
  </div>
  <div class="card card--stat">
    <span class="card__label">Güçlü uyarı taşıyan</span>
    <strong class="card__value"><?= Security::e((string) $issueCount) ?></strong>
  </div>
</div>

<form method="post" action="<?= Security::e(admin_url('seo')) ?>" data-dirty-guard>
  <?= csrf_field() ?>

  <section class="panel panel--form">
    <h2 class="panel__title">Varsayılan meta</h2>

    <div class="field">
      <label for="meta_title_pattern">Başlık şablonu</label>
      <input type="text" id="meta_title_pattern" name="meta_title_pattern" maxlength="120"
             value="<?= Security::e((string) old('meta_title_pattern', $settings['meta_title_pattern'] ?? '%title% | %site%')) ?>">
      <span class="field__hint">
        Yer tutucular: <code>%title%</code>, <code>%site%</code>.
        Sayfada meta başlık girilmişse o kullanılır.
      </span>
    </div>

    <div class="field">
      <label for="meta_description">Varsayılan açıklama</label>
      <textarea id="meta_description" name="meta_description" maxlength="320" rows="3"
                data-counter="<?= Seo::DESCRIPTION_MAX ?>"><?= Security::e((string) old('meta_description', $settings['meta_description'] ?? '')) ?></textarea>
    </div>
  </section>

  <section class="panel panel--form">
    <h2 class="panel__title">Doğrulama ve ölçüm</h2>

    <div class="field">
      <label for="gsc_verification">Search Console doğrulama kodu</label>
      <input type="text" id="gsc_verification" name="gsc_verification" maxlength="200"
             value="<?= Security::e((string) old('gsc_verification', $settings['gsc_verification'] ?? '')) ?>">
      <span class="field__hint">
        Yalnızca <code>content</code> değerini yapıştırın; etiket şablon tarafından yazılır.
      </span>
    </div>

    <div class="field">
      <label for="analytics_code">Analytics ölçüm kimliği</label>
      <input type="text" id="analytics_code" name="analytics_code" maxlength="200"
             value="<?= Security::e((string) old('analytics_code', $settings['analytics_code'] ?? '')) ?>">
      <span class="field__hint">
        İçerik güvenlik politikası satır içi script yasaklar; bu değer yalnızca
        saklanır ve harici bir ölçüm betiği eklenmez. Şartname tek harici kaynak
        olarak Google Fonts'a izin verir (bölüm 2).
      </span>
    </div>
  </section>

  <section class="panel panel--form">
    <h2 class="panel__title">robots.txt</h2>

    <div class="field">
      <label for="robots_txt">Özel içerik</label>
      <textarea id="robots_txt" name="robots_txt" rows="8" maxlength="4000"><?= Security::e((string) old('robots_txt', $robots)) ?></textarea>
      <span class="field__hint">
        Boş bırakılırsa güvenli varsayılan üretilir. Ne girerseniz girin
        <code>Sitemap:</code> satırı eklenir.
      </span>
    </div>

    <details class="details">
      <summary>Şu an yayınlanan içerik</summary>
      <pre class="code-block"><?= Security::e($robotsPreview) ?></pre>
    </details>
  </section>

  <div class="form__actions form__actions--sticky">
    <button class="btn btn--primary" type="submit">SEO ayarlarını kaydet</button>
    <a class="btn btn--ghost" href="<?= Security::e(admin_url('seo/sitemap')) ?>" target="_blank" rel="noopener">
      Site haritasını gör
    </a>
    <a class="btn btn--ghost" href="/robots.txt" target="_blank" rel="noopener">robots.txt</a>
  </div>
</form>

<section class="panel">
  <h2 class="panel__title">Sayfaların meta durumu</h2>
  <p class="muted">
    İçerik skoru kaydı engellemez; eksikleri listeler. İlçe sayfalarında
    500 kelime altı ve %70 üzeri benzerlik güçlü uyarıdır.
  </p>

  <div class="table-scroll">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Sayfa</th>
          <th scope="col">Tür</th>
          <th scope="col">Dil</th>
          <th scope="col" class="num">Kelime</th>
          <th scope="col" class="num">Başlık</th>
          <th scope="col" class="num">Açıklama</th>
          <th scope="col">Yönerge</th>
          <th scope="col" class="num">Skor</th>
          <th scope="col">Uyarılar</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$metaTable): ?>
          <tr><td colspan="9" class="muted">Henüz sayfa yok.</td></tr>
        <?php endif; ?>

        <?php foreach ($metaTable as $row): ?>
          <?php
          $strong = false;
          foreach ($row['issues'] as $issue) {
              if ($issue['level'] === 'strong') {
                  $strong = true;
                  break;
              }
          }
          ?>
          <tr>
            <td>
              <a href="<?= Security::e(admin_url('sayfalar/' . $row['id'])) ?>"><?= Security::e($row['title']) ?></a>
              <br><code>/<?= Security::e($row['slug']) ?></code>
            </td>
            <td><?= Security::e($row['type']) ?></td>
            <td><?= Security::e(strtoupper($row['lang'])) ?></td>
            <td class="num"><?= Security::e((string) $row['words']) ?></td>
            <td class="num">
              <span class="tag tag--<?= $row['meta_length'] === 0 || $row['meta_length'] > Seo::TITLE_MAX ? 'warn' : 'ok' ?>">
                <?= Security::e((string) $row['meta_length']) ?>
              </span>
            </td>
            <td class="num">
              <span class="tag tag--<?= $row['desc_length'] === 0 || $row['desc_length'] > Seo::DESCRIPTION_MAX ? 'warn' : 'ok' ?>">
                <?= Security::e((string) $row['desc_length']) ?>
              </span>
            </td>
            <td><code><?= Security::e($row['robots']) ?></code></td>
            <td class="num">
              <span class="tag tag--<?= $strong ? 'off' : ($row['score'] >= 84 ? 'ok' : 'warn') ?>">
                <?= Security::e((string) $row['score']) ?>
              </span>
            </td>
            <td>
              <?php if (!$row['issues']): ?>
                <span class="muted">—</span>
              <?php else: ?>
                <ul class="issues issues--compact">
                  <?php foreach ($row['issues'] as $issue): ?>
                    <li class="issues__item issues__item--<?= Security::e($issue['level']) ?>">
                      <?= Security::e($issue['message']) ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
