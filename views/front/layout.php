<?php
/**
 * On yuz duzeni.
 *
 * Arapca sayfalarda `<html dir="rtl">` kullanilir.  DOCS.md 11.3
 *
 * @var string $content
 * @var array  $head
 * @var array  $_site
 * @var array  $_menu
 * @var array  $_footer
 * @var array  $_langs
 * @var string $_lang
 * @var string $_dir
 * @var string $body_class
 */

declare(strict_types=1);

use Arcates\Core\Security;

$isReferenceHome = str_contains((string) ($body_class ?? ''), 'is-reference-home');
?><!doctype html>
<html lang="<?= Security::e($_lang) ?>" dir="<?= Security::e($_dir) ?>">
<head>
<?= partial('front/partials/head', [
    'head'  => $head,
    '_lang' => $_lang,
    '_site' => $_site,
]) ?>
</head>
<body class="<?= Security::e($body_class ?? '') ?>">

<a class="skip-link" href="#icerik"><?= Security::e(__('skip_to_content')) ?></a>

<?= partial('front/partials/header', [
    '_menu'           => $_menu,
    '_langs'          => $_langs,
    '_lang'           => $_lang,
    '_site'           => $_site,
    '_alternates'     => $_alternates ?? [],
    'headerCta'       => $headerCta ?? null,
    'isReferenceHome' => $isReferenceHome,
]) ?>

<main id="icerik">
<?= $content ?>
</main>

<?= partial('front/partials/footer', [
    '_site'         => $_site,
    '_footer'       => $_footer,
    'footerContent' => $footerContent ?? [],
]) ?>

<script src="<?= Security::e(asset('js/site.js')) ?>" defer></script>
<script src="<?= Security::e(asset('js/redesign.js')) ?>" defer></script>
</body>
</html>
