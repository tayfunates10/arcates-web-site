<?php
/**
 * Anasayfa.
 *
 * Sabit sirali bolumler; her biri panelden acilip kapatilabilir.
 * Sira degistirilemez (surum 1).  DOCS.md 5
 *
 * | # | Bolum          | Anahtar   |
 * |---|----------------|-----------|
 * | 1 | Ust menu       | header    |  (layout icinde)
 * | 2 | Kahraman       | hero      |
 * | 3 | Sektor seridi  | strip     |
 * | 4 | Hizmet kartlari| services  |
 * | 5 | Bolge haritasi | coast     |
 * | 6 | Surec          | steps     |
 * | 7 | Referanslar    | works     |
 * | 8 | SSS            | faq       |
 * | 9 | Cagri bandi    | cta       |
 * |10 | Alt bilgi      | footer    |  (layout icinde)
 *
 * @var array $sections
 * @var array $projects
 * @var array $faqs
 * @var array $districts
 */

declare(strict_types=1);

/** Bolum etkinse icerigini dondurur, degilse null. DOCS.md 9.2, test F-14 */
$active = static function (string $key) use ($sections): ?array {
    $section = $sections[$key] ?? null;
    if ($section === null || !$section['is_active']) {
        return null;
    }
    return $section;
};
?>

<?php if ($hero = $active('hero')): ?>
  <?= partial('front/partials/hero', ['content' => $hero['content']]) ?>
<?php endif; ?>

<?php if ($strip = $active('strip')): ?>
  <?= partial('front/partials/strip', ['content' => $strip['content'], 'config' => $strip['config']]) ?>
<?php endif; ?>

<?php if ($services = $active('services')): ?>
  <?= partial('front/partials/cards', ['content' => $services['content']]) ?>
<?php endif; ?>

<?php if ($coast = $active('coast')): ?>
  <?= partial('front/partials/coast', [
      'content'   => $coast['content'],
      'config'    => $coast['config'],
      'districts' => $districts,
  ]) ?>
<?php endif; ?>

<?php if ($steps = $active('steps')): ?>
  <?= partial('front/partials/steps', ['content' => $steps['content']]) ?>
<?php endif; ?>

<?php if ($works = $active('works')): ?>
  <?= partial('front/partials/works', ['content' => $works['content'], 'projects' => $projects]) ?>
<?php endif; ?>

<?php if ($faq = $active('faq')): ?>
  <?= partial('front/partials/faq', [
      'faqs'           => $faqs,
      'faqTitle'       => (string) ($faq['content']['title'] ?? __('faq')),
      'faqDescription' => (string) ($faq['content']['description'] ?? ''),
  ]) ?>
<?php endif; ?>

<?php if ($cta = $active('cta')): ?>
  <?= partial('front/partials/cta', ['cta' => $cta['content']]) ?>
<?php endif; ?>
