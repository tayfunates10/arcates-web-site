<?php
/**
 * Ana sayfa — tam yeniden tasarim sabit akisi.
 *
 * header → hero → strip → services → steps → works → coast → faq → cta → footer
 * Her bolum panelden acilip kapatilabilir; surukle-birak sayfa olusturucu yoktur.
 *
 * @var array $sections
 * @var array $projects
 * @var array $faqs
 * @var array $districts
 * @var array $sectors
 */

declare(strict_types=1);

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
  <?= partial('front/partials/strip', [
      'content' => $strip['content'],
      'config'  => $strip['config'],
      'sectors' => $sectors ?? [],
  ]) ?>
<?php endif; ?>

<?php if ($services = $active('services')): ?>
  <?= partial('front/partials/cards', ['content' => $services['content']]) ?>
<?php endif; ?>

<?php if ($steps = $active('steps')): ?>
  <?= partial('front/partials/steps', ['content' => $steps['content']]) ?>
<?php endif; ?>

<?php if ($works = $active('works')): ?>
  <?= partial('front/partials/works', ['content' => $works['content'], 'projects' => $projects]) ?>
<?php endif; ?>

<?php if ($coast = $active('coast')): ?>
  <?= partial('front/partials/coast', [
      'content'   => $coast['content'],
      'config'    => $coast['config'],
      'districts' => $districts,
  ]) ?>
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
