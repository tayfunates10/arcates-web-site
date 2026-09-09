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

test('F-R3-02', 'Altı hizmet SVG görseli geçerli, 4:3 ve bütçe içindedir', function (): void {
    $keys = ['layout', 'cart', 'calendar', 'search', 'globe', 'shield'];
    $total = 0;

    foreach ($keys as $key) {
        $path = ARC_ROOT . '/public/assets/img/redesign/service-' . $key . '.svg';
        assertTrue(is_file($path), 'Hizmet SVG mevcut olmali: ' . $key);
        $svg = file_get_contents($path);
        assertTrue(is_string($svg), 'Hizmet SVG okunabilmeli');
        assertTrue(str_contains($svg, '<svg'), 'SVG kok etiketi olmali');
        assertTrue(str_contains($svg, 'width="480"') && str_contains($svg, 'height="360"'), 'SVG 4:3 intrinsic boyuta sahip olmali');
        assertTrue(str_contains($svg, 'viewBox="0 0 480 360"'), 'SVG 4:3 viewBox kullanmali');
        $bytes = filesize($path);
        assertTrue($bytes < 8 * 1024, 'Tek hizmet SVG 8 KB altinda olmali');
        $total += $bytes;
    }

    assertTrue($total < 32 * 1024, 'Tum hizmet SVG ailesi 32 KB altinda olmali');

    $template = file_get_contents(ARC_ROOT . '/views/front/partials/cards.php');
    assertTrue(is_string($template) && str_contains($template, 'service-card__image'), 'Hizmet kartlari yeni gorsel ailesini kullanmali');
    assertTrue(is_string($template) && str_contains($template, 'loading="lazy"'), 'Hizmet gorselleri lazy yuklenmeli');
});
