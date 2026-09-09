<?php
declare(strict_types=1);

/**
 * Arcates R3 G-09 exact-size favicon endpoint.
 *
 * Only fixed, documented sizes are accepted. The source artwork remains the
 * approved Arcates raster mark; no alternate logo geometry is introduced.
 */

$allowed = [16, 48];
$size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
if (!is_int($size) || !in_array($size, $allowed, true)) {
    http_response_code(404);
    exit;
}

$source = $size <= 16
    ? __DIR__ . '/img/favicon-32.png'
    : __DIR__ . '/img/logo-mark.png';

if (!extension_loaded('gd') || !is_file($source)) {
    http_response_code(503);
    exit;
}

$etag = '"' . sha1((string) filemtime($source) . ':' . $size . ':r3-icon-v1') . '"';
header('Content-Type: image/png');
header('Cache-Control: public, max-age=604800, stale-while-revalidate=604800');
header('ETag: ' . $etag);
if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
    http_response_code(304);
    exit;
}

$src = @imagecreatefrompng($source);
if ($src === false) {
    http_response_code(503);
    exit;
}

$srcW = imagesx($src);
$srcH = imagesy($src);
$dst = imagecreatetruecolor($size, $size);
if ($dst === false) {
    imagedestroy($src);
    http_response_code(503);
    exit;
}

imagealphablending($dst, false);
imagesavealpha($dst, true);
$transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
imagefill($dst, 0, 0, $transparent);
imagecopyresampled($dst, $src, 0, 0, 0, 0, $size, $size, $srcW, $srcH);

imagepng($dst, null, 9);
imagedestroy($dst);
imagedestroy($src);
