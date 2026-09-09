<?php
/**
 * R3 G-07 — blog icin soyut, metinsiz editoryal kapak.
 * Gercek yuklenmis kapak varsa bu partial kullanilmaz.
 *
 * @var string $category
 * @var string $class
 */
declare(strict_types=1);

use Arcates\Core\Security;

$category = (string) ($category ?? '');
$class = trim((string) ($class ?? ''));
$variant = Security::slug($category);
if ($variant === '') $variant = 'genel';
$classes = trim('editorial-cover editorial-cover--' . $variant . ' ' . $class);
?>
<div class="<?= Security::e($classes) ?>" aria-hidden="true">
  <span class="editorial-cover__halo"></span>
  <span class="editorial-cover__panel editorial-cover__panel--back"></span>
  <span class="editorial-cover__panel editorial-cover__panel--front"></span>
  <span class="editorial-cover__symbol"></span>
  <span class="editorial-cover__chip editorial-cover__chip--a"></span>
  <span class="editorial-cover__chip editorial-cover__chip--b"></span>
</div>
