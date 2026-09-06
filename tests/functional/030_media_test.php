<?php
/**
 * Medya yukleme, varyantlar ve silme uyarisi.
 * DOCS.md 9.5, 10.6 — testler F-06, F-07, U-08, S-05, S-06
 */

declare(strict_types=1);

use Arcates\Core\Media;
use Arcates\Core\Security;

/** Gecici bir PNG uretir ve $_FILES benzeri dizi dondurur. */
function arc_fake_upload(string $name, string $contents, ?string $mime = null): array
{
    $tmp = tempnam(sys_get_temp_dir(), 'arcupload');
    file_put_contents($tmp, $contents);

    return [
        'name'     => $name,
        'type'     => $mime ?? 'application/octet-stream',
        'tmp_name' => $tmp,
        'error'    => UPLOAD_ERR_OK,
        'size'     => strlen($contents),
    ];
}

/** Belirtilen olcude gercek bir PNG uretir. */
function arc_png(int $width = 1200, int $height = 800): string
{
    $image = imagecreatetruecolor($width, $height);
    $blue  = imagecolorallocate($image, 11, 79, 168);
    imagefilledrectangle($image, 0, 0, $width, $height, $blue);

    ob_start();
    imagepng($image);
    $data = (string) ob_get_clean();
    imagedestroy($image);

    return $data;
}

test('U-08', 'Media uzanti beyaz listesi .php dosyasini reddeder', function (): void {
    arc_test_config();

    $php = arc_fake_upload('test.php', '<?php echo "merhaba"; ?>');
    $result = Media::validate($php);

    assertFalse($result['ok'], 'PHP dosyasi reddedilmeli');
    assertContains('kabul edilmiyor', $result['error']);

    @unlink($php['tmp_name']);
});

test('S-05', 'test.php yuklemesi reddedilir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $php    = arc_fake_upload('test.php', '<?php system($_GET["c"]); ?>');
    $result = Media::store($php, null);

    assertFalse($result['ok'], 'PHP dosyasi kaydedilmemeli');
    assertSame(0, $result['id'], 'Kayit olusmamali');

    @unlink($php['tmp_name']);
    arc_logout_test();
});

test('S-06', 'resim.php.jpg yuklemesi MIME kontrolunde reddedilir', function (): void {
    arc_test_config();

    // Uzantisi .jpg ama icerigi PHP.
    $disguised = arc_fake_upload('resim.php.jpg', '<?php echo "kotu"; ?>');
    $result    = Media::validate($disguised);

    assertFalse($result['ok'], 'Cift uzantili dosya reddedilmeli');

    @unlink($disguised['tmp_name']);

    // Uzantisi .jpg, icerigi gercekten PNG — MIME uyusmuyor.
    $wrongMime = arc_fake_upload('resim.jpg', arc_png(20, 20));
    $check     = Media::validate($wrongMime);

    assertFalse($check['ok'], 'Icerigi uzantisiyla uyusmayan dosya reddedilmeli');
    assertContains('uyusmuyor', $check['error']);

    @unlink($wrongMime['tmp_name']);
});

test('F-06', 'Gorsel yuklenince thumb, medium, large ve webp uretilir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    if (!extension_loaded('gd') || !function_exists('imagewebp')) {
        skip('GD veya WebP destegi yok.');
    }

    $upload = arc_fake_upload('sahil.png', arc_png(1800, 1200), 'image/png');
    $result = Media::store($upload, null, ['tr' => 'Edremit sahili']);

    assertTrue($result['ok'], 'Yukleme basarili olmali: ' . $result['error']);

    $media = Media::find($result['id']);
    assertTrue($media !== null, 'Kayit olusmali');

    // Ad kullanicidan gelmez.
    assertNotContains('sahil', (string) $media['filename'], 'Dosya adi kullanicinin verdigi ad olmamali');
    assertTrue((bool) preg_match('#^\d{4}/\d{2}/[0-9a-f]{16}\.png$#', (string) $media['path']), 'Yol YYYY/MM/rastgele.png olmali: ' . $media['path']);

    assertSame(1800, (int) $media['width'], 'Genislik kaydedilmeli');
    assertSame(1200, (int) $media['height'], 'Yukseklik kaydedilmeli');

    foreach (['thumb', 'medium', 'large', 'webp'] as $variant) {
        assertTrue(isset($media['variants'][$variant]), "Varyant uretilmeli: {$variant}");
        $path = Media::uploadRoot() . '/' . $media['variants'][$variant]['path'];
        assertTrue(is_file($path), "Varyant dosyasi diskte olmali: {$variant}");
        assertContains('.webp', $media['variants'][$variant]['path'], 'Varyantlar WebP olmali');
    }

    assertSame(320, (int) $media['variants']['thumb']['width'], 'thumb genisligi 320 olmali');
    assertSame(768, (int) $media['variants']['medium']['width'], 'medium genisligi 768 olmali');
    assertSame(1600, (int) $media['variants']['large']['width'], 'large genisligi 1600 olmali');

    // Yukseklik oran korunarak hesaplanmali.
    assertSame(213, (int) $media['variants']['thumb']['height'], 'Oran korunmali');

    assertSame('Edremit sahili', Media::alt($result['id'], 'tr'), 'Alt metni kaydedilmeli');

    Media::delete($result['id']);
    @unlink($upload['tmp_name']);
    arc_logout_test();
});

test('F-06b', 'Kaynaktan buyuk varyant uretilmez', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    if (!extension_loaded('gd') || !function_exists('imagewebp')) {
        skip('GD veya WebP destegi yok.');
    }

    $upload = arc_fake_upload('kucuk.png', arc_png(400, 300), 'image/png');
    $result = Media::store($upload, null);

    assertTrue($result['ok'], 'Yukleme basarili olmali');

    $media = Media::find($result['id']);
    assertSame(320, (int) $media['variants']['thumb']['width'], 'thumb kucultulmeli');
    assertSame(400, (int) $media['variants']['medium']['width'], 'medium kaynaktan buyuk olmamali');
    assertSame(400, (int) $media['variants']['large']['width'], 'large kaynaktan buyuk olmamali');

    Media::delete($result['id']);
    @unlink($upload['tmp_name']);
    arc_logout_test();
});

test('F-07', 'Kullanimdaki gorseli silmek uyari verir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $upload = arc_fake_upload('kapak.png', arc_png(600, 400), 'image/png');
    $result = Media::store($upload, null);
    assertTrue($result['ok'], 'Yukleme basarili olmali');

    $mediaId = $result['id'];

    $db->run('DELETE FROM pages');
    $pageId = $db->insert('pages', ['type' => 'page', 'template' => 'page', 'status' => 'published', 'cover_id' => $mediaId]);
    $db->insert('page_translations', [
        'page_id' => $pageId, 'lang' => 'tr', 'title' => 'Kapakli sayfa', 'slug' => 'kapakli-sayfa',
    ]);

    $usage = Media::usage($mediaId);
    assertGreaterThan(0, count($usage), 'Kullanim yeri bulunmali');
    assertSame('Sayfa kapagi', $usage[0]['type']);
    assertSame('Kapakli sayfa', $usage[0]['label']);

    // Panel onaysiz silmeyi reddeder.
    $response = (new Arcates\Controllers\Admin\MediaController())->destroy(
        Arcates\Core\Request::make('POST', admin_url('medya/' . $mediaId . '/sil'), ['_token' => Security::csrfToken()]),
        ['id' => $mediaId]
    );

    assertSame(302, $response->status());
    assertTrue(Media::find($mediaId) !== null, 'Onaysiz silme gerceklesmemeli');

    // Onayli silme calisir.
    (new Arcates\Controllers\Admin\MediaController())->destroy(
        Arcates\Core\Request::make('POST', admin_url('medya/' . $mediaId . '/sil'), [
            '_token' => Security::csrfToken(),
            'force'  => '1',
        ]),
        ['id' => $mediaId]
    );

    assertSame(null, Media::find($mediaId), 'Onayli silme gerceklesmeli');

    $db->run('DELETE FROM pages');
    @unlink($upload['tmp_name']);
    arc_logout_test();
});

test('S-17b', 'Yuklenen SVG temizlenerek diske yazilir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 60" onload="alert(1)">'
        . '<script>alert(2)</script><circle cx="50" cy="30" r="20" fill="#1C7BF2"/></svg>';

    $upload = arc_fake_upload('logo.svg', $svg, 'image/svg+xml');
    $result = Media::store($upload, null);

    assertTrue($result['ok'], 'SVG yuklenebilmeli: ' . $result['error']);

    $media   = Media::find($result['id']);
    $written = (string) file_get_contents(Media::uploadRoot() . '/' . $media['path']);

    assertNotContains('<script', $written, 'Diske yazilan dosyada script kalmamali');
    assertNotContains('onload', $written, 'Diske yazilan dosyada olay niteligi kalmamali');
    assertContains('<circle', $written, 'Zararsiz icerik korunmali');

    assertSame(100, (int) $media['width'], 'viewBox genisligi okunmali');
    assertSame(60, (int) $media['height'], 'viewBox yuksekligi okunmali');

    Media::delete($result['id']);
    @unlink($upload['tmp_name']);
    arc_logout_test();
});
