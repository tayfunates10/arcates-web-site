<?php
declare(strict_types=1);

/**
 * Arcates R3 G-09 default social/OG image.
 *
 * The image is generated from approved brand assets only. No dynamic copy,
 * customer names, ratings, metrics or claims are rendered into the image.
 */

$wordmark = __DIR__ . '/img/logo-wordmark.png';
$fallback = __DIR__ . '/img/og-default.png';

header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400, stale-while-revalidate=604800');

$etagSeed = (is_file($wordmark) ? (string) filemtime($wordmark) : '0') . ':r3-social-v1';
$etag = '"' . sha1($etagSeed) . '"';
header('ETag: ' . $etag);
if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
    http_response_code(304);
    exit;
}

if (!extension_loaded('gd') || !is_file($wordmark)) {
    if (is_file($fallback)) {
        readfile($fallback);
        exit;
    }
    http_response_code(503);
    exit;
}

$width = 1200;
$height = 630;
$canvas = imagecreatetruecolor($width, $height);
if ($canvas === false) {
    http_response_code(503);
    exit;
}
imagealphablending($canvas, true);
imagesavealpha($canvas, true);

$navy = imagecolorallocate($canvas, 8, 20, 38);
$navy2 = imagecolorallocate($canvas, 10, 31, 58);
$blue = imagecolorallocate($canvas, 28, 123, 242);
$blueSoft = imagecolorallocatealpha($canvas, 127, 182, 255, 78);
$blueGhost = imagecolorallocatealpha($canvas, 28, 123, 242, 100);
$white = imagecolorallocate($canvas, 255, 255, 255);
$mist = imagecolorallocate($canvas, 237, 244, 255);
$line = imagecolorallocatealpha($canvas, 127, 182, 255, 104);

imagefilledrectangle($canvas, 0, 0, $width, $height, $navy);

// Subtle two-tone background field.
for ($y = 0; $y < $height; $y += 2) {
    $t = $y / max(1, $height - 1);
    $r = (int) round(8 + (10 - 8) * $t);
    $g = (int) round(20 + (31 - 20) * $t);
    $b = (int) round(38 + (58 - 38) * $t);
    $row = imagecolorallocate($canvas, $r, $g, $b);
    imagefilledrectangle($canvas, 0, $y, $width, min($height - 1, $y + 1), $row);
}

// R3 grid, intentionally decorative and very low contrast.
for ($x = 720; $x <= 1160; $x += 56) {
    imageline($canvas, $x, 74, $x, 560, $line);
}
for ($y = 98; $y <= 546; $y += 56) {
    imageline($canvas, 694, $y, 1166, $y, $line);
}

// Soft light wells.
imagefilledellipse($canvas, 1060, 84, 330, 330, $blueGhost);
imagefilledellipse($canvas, 820, 576, 260, 260, $blueGhost);

// White brand surface on the left.
$panelX = 74;
$panelY = 118;
$panelW = 650;
$panelH = 394;
$radius = 34;
imagefilledrectangle($canvas, $panelX + $radius, $panelY, $panelX + $panelW - $radius, $panelY + $panelH, $white);
imagefilledrectangle($canvas, $panelX, $panelY + $radius, $panelX + $panelW, $panelY + $panelH - $radius, $white);
imagefilledellipse($canvas, $panelX + $radius, $panelY + $radius, $radius * 2, $radius * 2, $white);
imagefilledellipse($canvas, $panelX + $panelW - $radius, $panelY + $radius, $radius * 2, $radius * 2, $white);
imagefilledellipse($canvas, $panelX + $radius, $panelY + $panelH - $radius, $radius * 2, $radius * 2, $white);
imagefilledellipse($canvas, $panelX + $panelW - $radius, $panelY + $panelH - $radius, $radius * 2, $radius * 2, $white);

$logo = @imagecreatefrompng($wordmark);
if ($logo !== false) {
    imagealphablending($logo, true);
    imagesavealpha($logo, true);
    $srcW = imagesx($logo);
    $srcH = imagesy($logo);
    $maxW = 510;
    $maxH = 180;
    $scale = min($maxW / max(1, $srcW), $maxH / max(1, $srcH));
    $dstW = (int) round($srcW * $scale);
    $dstH = (int) round($srcH * $scale);
    $dstX = $panelX + (int) round(($panelW - $dstW) / 2);
    $dstY = $panelY + (int) round(($panelH - $dstH) / 2);
    imagecopyresampled($canvas, $logo, $dstX, $dstY, 0, 0, $dstW, $dstH, $srcW, $srcH);
    imagedestroy($logo);
}

// Right-side connected Arcates scene; no labels or metrics.
imagesetthickness($canvas, 5);
imagearc($canvas, 928, 306, 310, 310, 214, 28, $blueSoft);
imagearc($canvas, 960, 320, 232, 232, 204, 38, $blue);
imagesetthickness($canvas, 1);

$nodes = [
    [820, 402, 18],
    [958, 220, 22],
    [1082, 378, 18],
];
foreach ($nodes as [$x, $y, $r]) {
    imagefilledellipse($canvas, $x, $y, $r * 2, $r * 2, $white);
    imageellipse($canvas, $x, $y, $r * 2, $r * 2, $blue);
    imagefilledellipse($canvas, $x, $y, (int) round($r * 0.72), (int) round($r * 0.72), $blue);
}

// Small neutral interface card to echo the R3 visual family.
$miniX = 804;
$miniY = 438;
$miniW = 292;
$miniH = 104;
imagefilledrectangle($canvas, $miniX, $miniY, $miniX + $miniW, $miniY + $miniH, $mist);
imagefilledrectangle($canvas, $miniX + 22, $miniY + 24, $miniX + 180, $miniY + 35, $blue);
imagefilledrectangle($canvas, $miniX + 22, $miniY + 50, $miniX + 250, $miniY + 60, $blueSoft);
imagefilledrectangle($canvas, $miniX + 22, $miniY + 72, $miniX + 208, $miniY + 82, $blueSoft);

imagepng($canvas, null, 7);
imagedestroy($canvas);
