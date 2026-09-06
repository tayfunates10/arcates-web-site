<?php
/**
 * SSS bolumu.
 *
 * `details`/`summary` kullanilir; JavaScript kapaliyken de acilip kapanir.
 * DOCS.md 7.1 kural 2, 11.2
 *
 * @var array  $faqs
 * @var string $faqTitle
 * @var string $faqDescription
 */

declare(strict_types=1);

use Arcates\Core\Security;

if (!($faqs ?? [])) {
    return;
}

$faqTitle       = $faqTitle ?? __('faq');
$faqDescription = $faqDescription ?? '';
?>
<section class="section section--faq" data-reveal-group>
  <div class="wrap">
    <header class="section__head">
      <h2 class="section__title" data-reveal><?= Security::e($faqTitle) ?></h2>
      <?php if ($faqDescription !== ''): ?>
        <p class="section__lead" data-reveal><?= Security::e($faqDescription) ?></p>
      <?php endif; ?>
    </header>

    <div class="faq">
      <?php foreach ($faqs as $faq): ?>
        <details class="faq__item" data-reveal>
          <summary class="faq__question"><?= Security::e($faq['question'] ?? '') ?></summary>
          <div class="faq__answer"><?= Arcates\Core\Security::sanitizeHtml((string) ($faq['answer'] ?? '')) ?></div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
