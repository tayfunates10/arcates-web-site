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
/*
 * Paylasim gorseli: sayfaya ozel gorsel yoksa varsayilan marka gorseli
 * kullanilir. Boylece her sayfa paylasimda gorselli cikar.
 * DOCS.md 17 (yayin oncesi teslim listesi)
 */
$ogImage = (string) ($head['og_image'] ?? '');
if ($ogImage === '') {
    $ogImage = path_url('/assets/img/og-default.png');
}
?>
<meta property="og:image" content="<?= Security::e($ogImage) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">

<?php $verification = (string) Settings::get('gsc_verification', ''); ?>
<?php if ($verification !== ''): ?>
<meta name="google-site-verification" content="<?= Security::e($verification) ?>">
<?php endif; ?>

<link rel="icon" href="/assets/img/favicon-32.png" sizes="32x32">
<link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">

<?php /* Google Fonts — preconnect + display=swap. DOCS.md 2, 6 */ ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap">

<link rel="stylesheet" href="<?= Security::e(asset('css/site.css')) ?>">

<?php /* Yapisal veri. Uydurma yorum veya AggregateRating yazilmaz. DOCS.md 11.2 */ ?>
<?php foreach ($head['schemas'] ?? [] as $schema): ?>
<script type="application/ld+json"><?= Security::json($schema) ?></script>
<?php endforeach; ?>

<?php
/*
 * Tum gizli baslangic durumlari `html.js` sinifi altinda tanimlidir.
 * Bu satir ici script calismazsa hicbir icerik gizli kalmaz.
 * DOCS.md 7.1 kural 2 — icerik guvenlik politikasindaki karma bu govdeden
 * uretilir, bu yuzden metin Security::JS_FLAG_SCRIPT ile birebir aynidir.
 */
?>
<script><?= Security::JS_FLAG_SCRIPT ?></script>
