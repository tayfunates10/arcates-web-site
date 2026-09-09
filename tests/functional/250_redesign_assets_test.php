<?php
declare(strict_types=1);

test('F-R3-01', 'Hero WebP varyantları çözülebilir, orantılı ve bütçe içindedir', function (): void {
    foreach ([640, 1280] as $width) {
        $path = ARC_ROOT . '/public/assets/img/redesign/hero-' . $width . '.webp';
        assertTrue(is_file($path), 'Hero varyanti mevcut olmali');
        $size = getimagesize($path);
        assertTrue($size !== false, 'Gorsel basligi okunabilmeli');
        assertSame($width, $size[0]);
        assertSame((int) ($width * 3 / 4), $size[1]);
        assertSame('image/webp', $size['mime']);
        $image = imagecreatefromwebp($path);
        assertTrue($image !== false, 'Gorsel tam olarak cozulebilmeli');
        imagedestroy($image);
        assertTrue(filesize($path) < 220 * 1024, 'Hero transfer butcesi asilmamali');
    }
});
