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

test('U-08', 'Media uzantı beyaz listesi .php dosyasını reddeder', function (): void {
    arc_test_config();

    $php = arc_fake_upload('test.php', '<?php echo "merhaba"; ?>');
    $result = Media::validate($php);

    assertFalse($result['ok'], 'PHP dosyası reddedilmeli');
    assertContains('kabul edilmiyor', $result['error']);

    @unlink($php['tmp_name']);
});

test('S-05', 'test.php yüklemesi reddedilir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $php    = arc_fake_upload('test.php', '<?php system($_GET["c"]); ?>');
    $result = Media::store($php, null);

    assertFalse($result['ok'], 'PHP dosyası kaydedilmemeli');
    assertSame(0, $result['id'], 'Kayıt oluşmamalı');

    @unlink($php['tmp_name']);
    arc_logout_test();
});

test('S-06', 'resim.php.jpg yüklemesi MIME kontrolünde reddedilir', function (): void {
    arc_test_config();

    // Uzantisi .jpg ama icerigi PHP.
    $disguised = arc_fake_upload('resim.php.jpg', '<?php echo "kötü"; ?>');
    $result    = Media::validate($disguised);

    assertFalse($result['ok'], 'Çift uzantılı dosya reddedilmeli');

    @unlink($disguised['tmp_name']);

    // Uzantisi .jpg, icerigi gercekten PNG — MIME uyusmuyor.
    $wrongMime = arc_fake_upload('resim.jpg', arc_png(20, 20));
    $check     = Media::validate($wrongMime);

    assertFalse($check['ok'], 'İçeriği uzantısıyla uyuşmayan dosya reddedilmeli');
    assertContains('uyuşmuyor', $check['error']);

    @unlink($wrongMime['tmp_name']);
});

test('F-06', 'Görsel yüklenince thumb, medium, large ve webp üretilir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    if (!extension_loaded('gd') || !function_exists('imagewebp')) {
        skip('GD veya WebP desteği yok.');
    }

    $upload = arc_fake_upload('sahil.png', arc_png(1800, 1200), 'image/png');
    $result = Media::store($upload, null, ['tr' => 'Edremit sahili']);

    assertTrue($result['ok'], 'Yükleme başarılı olmalı: ' . $result['error']);

    $media = Media::find($result['id']);
    assertTrue($media !== null, 'Kayıt oluşmalı');

    // Ad kullanicidan gelmez.
    assertNotContains('sahil', (string) $media['filename'], 'Dosya adı kullanıcının verdiği ad olmamalı');
    assertTrue((bool) preg_match('#^\d{4}/\d{2}/[0-9a-f]{16}\.png$#', (string) $media['path']), 'Yol YYYY/MM/rastgele.png olmalı: ' . $media['path']);

    assertSame(1800, (int) $media['width'], 'Genişlik kaydedilmeli');
    assertSame(1200, (int) $media['height'], 'Yükseklik kaydedilmeli');

    foreach (['thumb', 'medium', 'large', 'webp'] as $variant) {
        assertTrue(isset($media['variants'][$variant]), "Varyant üretilmeli: {$variant}");
        $path = Media::uploadRoot() . '/' . $media['variants'][$variant]['path'];
        assertTrue(is_file($path), "Varyant dosyası diskte olmalı: {$variant}");
        assertContains('.webp', $media['variants'][$variant]['path'], 'Varyantlar WebP olmalı');
    }

    assertSame(320, (int) $media['variants']['thumb']['width'], 'thumb genişliği 320 olmalı');
    assertSame(768, (int) $media['variants']['medium']['width'], 'medium genişliği 768 olmalı');
    assertSame(1600, (int) $media['variants']['large']['width'], 'large genişliği 1600 olmalı');

    // Yukseklik oran korunarak hesaplanmali.
    assertSame(213, (int) $media['variants']['thumb']['height'], 'Oran korunmalı');

    assertSame('Edremit sahili', Media::alt($result['id'], 'tr'), 'Alt metni kaydedilmeli');

    Media::delete($result['id']);
    @unlink($upload['tmp_name']);
    arc_logout_test();
});

test('F-06b', 'Kaynaktan büyük varyant üretilmez', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    if (!extension_loaded('gd') || !function_exists('imagewebp')) {
        skip('GD veya WebP desteği yok.');
    }

    $upload = arc_fake_upload('kucuk.png', arc_png(400, 300), 'image/png');
    $result = Media::store($upload, null);

    assertTrue($result['ok'], 'Yükleme başarılı olmalı');

    $media = Media::find($result['id']);
    assertSame(320, (int) $media['variants']['thumb']['width'], 'thumb küçültülmeli');
    assertSame(400, (int) $media['variants']['medium']['width'], 'medium kaynaktan büyük olmamalı');
    assertSame(400, (int) $media['variants']['large']['width'], 'large kaynaktan büyük olmamalı');

    Media::delete($result['id']);
    @unlink($upload['tmp_name']);
    arc_logout_test();
});

test('F-07', 'Kullanımdaki görseli silmek uyarı verir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $upload = arc_fake_upload('kapak.png', arc_png(600, 400), 'image/png');
    $result = Media::store($upload, null);
    assertTrue($result['ok'], 'Yükleme başarılı olmalı');

    $mediaId = $result['id'];

    $db->run('DELETE FROM pages');
    $pageId = $db->insert('pages', ['type' => 'page', 'template' => 'page', 'status' => 'published', 'cover_id' => $mediaId]);
    $db->insert('page_translations', [
        'page_id' => $pageId, 'lang' => 'tr', 'title' => 'Kapaklı sayfa', 'slug' => 'kapakli-sayfa',
    ]);

    $usage = Media::usage($mediaId);
    assertGreaterThan(0, count($usage), 'Kullanım yeri bulunmalı');
    assertSame('Sayfa kapağı', $usage[0]['type']);
    assertSame('Kapaklı sayfa', $usage[0]['label']);

    // Panel onaysiz silmeyi reddeder.
    $response = (new Arcates\Controllers\Admin\MediaController())->destroy(
        Arcates\Core\Request::make('POST', admin_url('medya/' . $mediaId . '/sil'), ['_token' => Security::csrfToken()]),
        ['id' => $mediaId]
    );

    assertSame(302, $response->status());
    assertTrue(Media::find($mediaId) !== null, 'Onaysız silme gerçekleşmemeli');

    // Onayli silme calisir.
    (new Arcates\Controllers\Admin\MediaController())->destroy(
        Arcates\Core\Request::make('POST', admin_url('medya/' . $mediaId . '/sil'), [
            '_token' => Security::csrfToken(),
            'force'  => '1',
        ]),
        ['id' => $mediaId]
    );

    assertSame(null, Media::find($mediaId), 'Onaylı silme gerçekleşmeli');

    $db->run('DELETE FROM pages');
    @unlink($upload['tmp_name']);
    arc_logout_test();
});

test('S-17b', 'Yüklenen SVG temizlenerek diske yazılır', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 60" onload="alert(1)">'
        . '<script>alert(2)</script><circle cx="50" cy="30" r="20" fill="#1C7BF2"/></svg>';

    $upload = arc_fake_upload('logo.svg', $svg, 'image/svg+xml');
    $result = Media::store($upload, null);

    assertTrue($result['ok'], 'SVG yüklenebilmeli: ' . $result['error']);

    $media   = Media::find($result['id']);
    $written = (string) file_get_contents(Media::uploadRoot() . '/' . $media['path']);

    assertNotContains('<script', $written, 'Diske yazılan dosyada script kalmamalı');
    assertNotContains('onload', $written, 'Diske yazılan dosyada olay niteliği kalmamalı');
    assertContains('<circle', $written, 'Zararsız içerik korunmalı');

    assertSame(100, (int) $media['width'], 'viewBox genişliği okunmalı');
    assertSame(60, (int) $media['height'], 'viewBox yüksekliği okunmalı');

    Media::delete($result['id']);
    @unlink($upload['tmp_name']);
    arc_logout_test();
});
