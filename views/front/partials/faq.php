<?php
/** SSS — R5 iki kolon; details/summary davranisi korunur. */
declare(strict_types=1);
use Arcates\Core\Security;
if (!($faqs ?? [])) return;
$faqTitle = $faqTitle ?? __('faq');
$faqDescription = $faqDescription ?? '';
?>
<section class="section section--loose section--faq home-faq" data-reveal-group>
  <div class="wrap faq-layout">
    <header class="section__head faq-layout__head">
      <h2 class="section__title" data-reveal><?= Security::e($faqTitle) ?></h2>
      <?php if ($faqDescription !== ''): ?><p class="section__lead" data-reveal><?= Security::e($faqDescription) ?></p><?php endif; ?>
    </header>

    <div class="faq faq--home">
      <?php foreach ($faqs as $faq): ?>
        <details class="faq__item" data-reveal>
          <summary class="faq__question"><span><?= Security::e($faq['question'] ?? '') ?></span><span class="faq__state" aria-hidden="true"></span></summary>
          <div class="faq__answer"><?= Arcates\Core\Security::sanitizeHtml((string) ($faq['answer'] ?? '')) ?></div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
