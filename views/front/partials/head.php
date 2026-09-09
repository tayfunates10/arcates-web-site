<?php
/**
 * <head> icerigi.
 *
 * Harici kaynak yalnizca Google Fonts'tur; preconnect ve display=swap ile
 * yuklenir. Baska ucuncu taraf script eklenmez.  DOCS.md 2, 11.1, 11.2
 *
 * @var array  $head
 * @var string $_lang
 * @var array  $_site
 */

declare(strict_types=1);

use Arcates\Core\Security;
use Arcates\Core\Settings;

$head = $head ?? [];
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#081426">

<title><?= Security::e($head['title'] ?? '') ?></title>
<meta name="description" content="<?= Security::e($head['description'] ?? '') ?>">
<meta name="robots" content="<?= Security::e($head['robots'] ?? 'index,follow') ?>">
<link rel="canonical" href="<?= Security::e($head['canonical'] ?? url('/')) ?>">

<?php /* hreflang seti; karsiligi olmayan dil listeye girmez. DOCS.md 11.3 */ ?>
<?php foreach ($head['hreflang'] ?? [] as $alternate): ?>
<link rel="alternate" hreflang="<?= Security::e($alternate['hreflang']) ?>" href="<?= Security::e($alternate['href']) ?>">
<?php endforeach; ?>

<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= Security::e($_site['name'] ?? '') ?>">
<meta property="og:title" content="<?= Security::e($head['title'] ?? '') ?>">
<meta property="og:description" content="<?= Security::e($head['description'] ?? '') ?>">
<meta property="og:url" content="<?= Security::e($head['canonical'] ?? url('/')) ?>">
<meta property="og:locale" content="<?= Security::e($_lang ?? 'tr') ?>">
<?php
$ogImage = (string) ($head['og_image'] ?? '');
if ($ogImage === '') {
    $ogImage = path_url('/assets/social-card.php');
}
$ogAlt = trim((string) ($_site['name'] ?? 'Arcates Yazılım'));
?>
<meta property="og:image" content="<?= Security::e($ogImage) ?>">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?= Security::e($ogAlt) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= Security::e($head['title'] ?? '') ?>">
<meta name="twitter:description" content="<?= Security::e($head['description'] ?? '') ?>">
<meta name="twitter:image" content="<?= Security::e($ogImage) ?>">

<?php $verification = (string) Settings::get('gsc_verification', ''); ?>
<?php if ($verification !== ''): ?>
<meta name="google-site-verification" content="<?= Security::e($verification) ?>">
<?php endif; ?>

<link rel="icon" type="image/png" href="/assets/brand-icon.php?size=16" sizes="16x16">
<link rel="icon" type="image/png" href="/assets/img/favicon-32.png" sizes="32x32">
<link rel="icon" type="image/png" href="/assets/brand-icon.php?size=48" sizes="48x48">
<link rel="icon" type="image/png" href="/assets/img/logo-mark.png" sizes="192x192">
<link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png" sizes="180x180">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap">

<link rel="stylesheet" href="<?= Security::e(asset('css/site.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/redesign.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/inner-redesign.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/inner-performance.css')) ?>">
<link rel="stylesheet" href="<?= Security::e(asset('css/r3-cta-status.css')) ?>">
<?php foreach ((array) ($head['styles'] ?? []) as $style): ?>
<link rel="stylesheet" href="<?= Security::e(asset((string) $style)) ?>">
<?php endforeach; ?>

<?php foreach ($head['schemas'] ?? [] as $schema): ?>
<script type="application/ld+json"><?= Security::json($schema) ?></script>
<?php endforeach; ?>

<script><?= Security::JS_FLAG_SCRIPT ?></script>
