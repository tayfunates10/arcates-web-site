<?php
/**
 * Teklif formu.  DOCS.md 12, 10.8, 10.9
 *
 * Alanlar: ad, telefon, e-posta, ilgilenilen hizmet, mesaj, KVKK onayi,
 * honeypot, zaman damgasi, CSRF token.
 *
 * Her alan `label` ile baglidir (test E-06); onay kutusu onceden isaretli
 * degildir (DOCS.md 10.9).
 *
 * @var array  $services
 * @var array  $errors
 * @var array  $old
 * @var array  $flash
 * @var string $returnPath
 */

declare(strict_types=1);

use Arcates\Controllers\Front\ContactController;
use Arcates\Core\Security;

$services   = $services ?? [];
$errors     = $errors ?? [];
$old        = $old ?? [];
$flash      = $flash ?? [];
$returnPath = $returnPath ?? 'iletisim';

$errorTargets = [
    'name'    => ['form_name', __('form_name')],
    'phone'   => ['form_phone', __('form_phone')],
    'email'   => ['form_email', __('form_email')],
    'service' => ['form_service', __('form_service')],
    'message' => ['form_message', __('form_message')],
    'kvkk'    => ['form_kvkk', __('form_kvkk')],
];
?>
<section class="section section--form" id="teklif-formu">
  <div class="wrap wrap--text">

    <?php foreach ($flash as $item): ?>
      <div class="notice notice--<?= Security::e($item['type']) ?>" role="status">
        <?= Security::e($item['message']) ?>
      </div>
    <?php endforeach; ?>

    <?php if ($errors): ?>
      <div class="form-error-summary" role="alert" tabindex="-1" data-error-summary>
        <strong><?= Security::e(__('form_error_title')) ?></strong>
        <p><?= Security::e(__('form_error_text')) ?></p>
        <ul>
          <?php foreach ($errorTargets as $key => [$target, $label]): ?>
            <?php if (!isset($errors[$key])) continue; ?>
            <li><a href="#<?= Security::e($target) ?>"><?= Security::e($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form class="form" method="post" action="<?= Security::e(url('/iletisim')) ?>" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="_return" value="<?= Security::e($returnPath) ?>">
      <input type="hidden" name="_source" value="<?= Security::e($_SERVER['REQUEST_URI'] ?? '/') ?>">

      <?php /* Formun acilis zamani; 3 saniyeden hizli gonderim reddedilir.
               DOCS.md 10.8, test S-14 */ ?>
      <input type="hidden" name="_opened" value="<?= Security::e((string) time()) ?>">

      <?php /* Honeypot. Gercek kullanicilar gormez ve doldurmaz.
               DOCS.md 10.8, test S-15 */ ?>
      <div class="form__trap" aria-hidden="true">
        <label for="<?= Security::e(ContactController::HONEYPOT) ?>">Bu alanı boş bırakın</label>
        <input type="text" id="<?= Security::e(ContactController::HONEYPOT) ?>"
               name="<?= Security::e(ContactController::HONEYPOT) ?>"
               tabindex="-1" autocomplete="off">
      </div>

      <div class="form__grid">
        <div class="field<?= isset($errors['name']) ? ' is-invalid' : '' ?>">
          <label for="form_name"><?= Security::e(__('form_name')) ?> *</label>
          <input type="text" id="form_name" name="name" maxlength="150" required
                 autocomplete="name" value="<?= Security::e($old['name'] ?? '') ?>"
                 <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="form_name_error"' : '' ?>>
          <?php if (isset($errors['name'])): ?>
            <span class="field__error" id="form_name_error"><?= Security::e($errors['name']) ?></span>
          <?php endif; ?>
        </div>

        <div class="field<?= isset($errors['phone']) ? ' is-invalid' : '' ?>">
          <label for="form_phone"><?= Security::e(__('form_phone')) ?> *</label>
          <input type="tel" id="form_phone" name="phone" maxlength="40" required
                 autocomplete="tel" inputmode="tel" value="<?= Security::e($old['phone'] ?? '') ?>"
                 <?= isset($errors['phone']) ? 'aria-invalid="true" aria-describedby="form_phone_error"' : '' ?>>
          <?php if (isset($errors['phone'])): ?>
            <span class="field__error" id="form_phone_error"><?= Security::e($errors['phone']) ?></span>
          <?php endif; ?>
        </div>
      </div>

      <div class="form__grid">
        <div class="field<?= isset($errors['email']) ? ' is-invalid' : '' ?>">
          <label for="form_email"><?= Security::e(__('form_email')) ?> *</label>
          <input type="email" id="form_email" name="email" maxlength="190" required
                 autocomplete="email" value="<?= Security::e($old['email'] ?? '') ?>"
                 <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="form_email_error"' : '' ?>>
          <?php if (isset($errors['email'])): ?>
            <span class="field__error" id="form_email_error"><?= Security::e($errors['email']) ?></span>
          <?php endif; ?>
        </div>

        <div class="field<?= isset($errors['service']) ? ' is-invalid' : '' ?>">
          <label for="form_service"><?= Security::e(__('form_service')) ?></label>
          <select id="form_service" name="service"
                  <?= isset($errors['service']) ? 'aria-invalid="true" aria-describedby="form_service_error"' : '' ?>>
            <option value=""><?= Security::e(__('form_choose')) ?></option>
            <?php foreach ($services as $service): ?>
              <option value="<?= Security::e($service) ?>"
                      <?= ($old['service'] ?? '') === $service ? 'selected' : '' ?>>
                <?= Security::e($service) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['service'])): ?>
            <span class="field__error" id="form_service_error"><?= Security::e($errors['service']) ?></span>
          <?php endif; ?>
        </div>
      </div>

      <div class="field<?= isset($errors['message']) ? ' is-invalid' : '' ?>">
        <label for="form_message"><?= Security::e(__('form_message')) ?> *</label>
        <textarea id="form_message" name="message" rows="6" maxlength="4000" required
                  <?= isset($errors['message']) ? 'aria-invalid="true" aria-describedby="form_message_error"' : '' ?>><?= Security::e($old['message'] ?? '') ?></textarea>
        <?php if (isset($errors['message'])): ?>
          <span class="field__error" id="form_message_error"><?= Security::e($errors['message']) ?></span>
        <?php endif; ?>
      </div>

      <?php /* Onceden isaretli olmayan onay kutusu. DOCS.md 10.9 */ ?>
      <div class="field field--check<?= isset($errors['kvkk']) ? ' is-invalid' : '' ?>">
        <label for="form_kvkk">
          <input type="checkbox" id="form_kvkk" name="kvkk" value="1" required
                 <?= !empty($old['kvkk']) ? 'checked' : '' ?>
                 <?= isset($errors['kvkk']) ? 'aria-invalid="true" aria-describedby="form_kvkk_error"' : '' ?>>
          <span>
            <a href="<?= Security::e(url('/kvkk')) ?>" target="_blank" rel="noopener">
              <?= Security::e(__('form_kvkk')) ?>
            </a>
          </span>
        </label>
        <?php if (isset($errors['kvkk'])): ?>
          <span class="field__error" id="form_kvkk_error"><?= Security::e($errors['kvkk']) ?></span>
        <?php endif; ?>
      </div>

      <div class="form__actions">
        <button class="btn btn--primary" type="submit"
                data-loading-label="<?= Security::e(__('form_sending')) ?>"><?= Security::e(__('form_submit')) ?></button>
      </div>
    </form>

  </div>
</section>
