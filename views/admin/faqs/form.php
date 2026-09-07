<?php
/**
 * SSS formu — soru, cevap, hangi sayfalara atanacagi.  DOCS.md 9.4
 * @var array|null $faq @var array $translations @var array $assigned
 * @var bool $onHome @var array $langs @var array $pages
 */
declare(strict_types=1);
use Arcates\Core\Lang;
use Arcates\Core\Security;

$isNew   = $faq === null;
$action  = $isNew ? admin_url('sss/yeni') : admin_url('sss/' . (int) $faq['id']);
$default = Lang::defaultCode();
?>
<form method="post" action="<?= Security::e($action) ?>" data-dirty-guard>
  <?= csrf_field() ?>

  <section class="panel panel--form">
    <div class="grid grid--2">
      <div class="field">
        <label for="sort">Sıra</label>
        <input type="number" id="sort" name="sort" value="<?= (int) old('sort', $faq['sort'] ?? 0) ?>">
      </div>

      <div class="field field--check">
        <label>
          <input type="checkbox" name="status" value="1" <?= (int) ($faq['status'] ?? 1) === 1 ? 'checked' : '' ?>>
          Etkin
        </label>
        <label>
          <input type="checkbox" name="on_home" value="1" <?= $onHome ? 'checked' : '' ?>>
          Anasayfada göster
        </label>
      </div>
    </div>

    <fieldset class="fieldset">
      <legend>Atanacak sayfalar</legend>
      <p class="field__hint">
        Seçilen sayfalarda bu soru gösterilir ve <code>FAQPage</code> yapısal
        verisine girer. İlçe ve hizmet sayfalarında en az bir SSS önerilir.
      </p>
      <div class="checkbox-grid">
        <?php foreach ($pages as $page): ?>
          <label>
            <input type="checkbox" name="pages[]" value="<?= (int) $page['id'] ?>"
                   <?= in_array((int) $page['id'], $assigned, true) ? 'checked' : '' ?>>
            <?= Security::e($page['title']) ?>
            <span class="muted">(<?= Security::e($page['type']) ?>)</span>
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
        <label for="question_<?= Security::e($code) ?>">Soru</label>
        <input type="text" id="question_<?= Security::e($code) ?>"
               name="t[<?= Security::e($code) ?>][question]" maxlength="300"
               value="<?= Security::e($t['question'] ?? '') ?>">
        <?php if ($code === $default && ($m = error_for('question'))): ?>
          <span class="field__error"><?= Security::e($m) ?></span>
        <?php endif; ?>
      </div>

      <div class="field">
        <label for="answer_<?= Security::e($code) ?>">Cevap</label>
        <textarea id="answer_<?= Security::e($code) ?>" name="t[<?= Security::e($code) ?>][answer]"
                  rows="8"><?= Security::e($t['answer'] ?? '') ?></textarea>
      </div>
    </section>
  <?php endforeach; ?>

  <div class="form__actions form__actions--sticky">
    <button class="btn btn--primary" type="submit"><?= $isNew ? 'Oluştur' : 'Kaydet' ?></button>
    <a class="btn btn--ghost" href="<?= Security::e(admin_url('sss')) ?>">Listeye dön</a>
  </div>
</form>
