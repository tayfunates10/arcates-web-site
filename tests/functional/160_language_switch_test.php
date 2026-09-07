<?php
/**
 * Dil yayin anahtari.
 *
 * Cevirisi girilmemis bir dil acik olsaydi ust menude gorunur, ziyaretci
 * tiklayinca Turkce icerige duserdi. Bu yuzden yalnizca varsayilan dil acik
 * baslar; isletme cevirileri girdikten sonra panelden aciyor.
 * DOCS.md 4.6, 9, 11.3
 */

declare(strict_types=1);

use Arcates\Controllers\Admin\SettingController;
use Arcates\Controllers\Front\HomeController;
use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Security;

test('F-P16-a', 'Tohum yalnızca varsayılan dili açık bırakır', function (): void {
    $src = (string) file_get_contents(ARC_ROOT . '/app/Core/Seeder.php');

    assertContains("'code' => 'tr', 'name' => 'Türkçe',  'direction' => 'ltr', 'is_default' => 1, 'is_active' => 1", $src, 'Türkçe açık olmalı');
    foreach (['en', 'de', 'ar'] as $code) {
        assertTrue(
            preg_match("/'code' => '{$code}'.*'is_active' => 0/", $src) === 1,
            "{$code} tohumda kapalı başlamalı"
        );
    }
});

test('F-P16-b', 'Kapalı dil üst menüde ve hreflang setinde görünmez', function (): void {
    arc_need_db();
    arc_activate_langs(['tr']);
    Lang::use('tr');

    $home = (new HomeController())->index(Request::make('GET', '/'), []);
    assertSame(200, $home->status(), 'Anasayfa açılmalı');

    $body = $home->body();
    assertNotContains('lang-switch', $body, 'Tek dil varken seçici basılmamalı');
    assertNotContains('rel="alternate"', $body, 'Karşılığı olmayan dil için hreflang bağlantısı verilmemeli');
    assertNotContains('/en/', $body, 'Kapalı dilin adresi geçmemeli');
});

test('F-P16-c', 'Dil açılınca seçici geri gelir', function (): void {
    arc_need_db();
    arc_activate_langs(['tr', 'en']);
    Lang::use('tr');

    assertTrue(Lang::exists('en'), 'Açılan dil etkin sayılmalı');

    $home = (new HomeController())->index(Request::make('GET', '/'), []);
    $body = $home->body();
    assertContains('lang-switch', $body, 'İki dil varken seçici görünmeli');
    assertContains('lang="en"', $body, 'Açılan dil seçicide olmalı');

    arc_activate_langs(['tr']);
});

test('F-P16-d', 'Panel dil anahtarını kaydeder, varsayılan dil kapatılamaz', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');
    arc_activate_langs(['tr']);

    $post = [
        '_token'       => Security::csrfToken(),
        'site_name'    => 'Arcates Yazılım',
        'nap_name'     => 'Arcates Yazılım',
        'default_lang' => 'tr',
        'active_langs' => ['de'],
    ];

    $response = (new SettingController())->update(Request::make('POST', admin_url('ayarlar'), $post), []);
    assertSame(302, $response->status(), 'Kayıt sonrası yönlendirme dönmeli');

    Lang::reset();
    $langs = Lang::allLanguages();
    assertSame(1, (int) $langs['de']['is_active'], 'İşaretlenen dil açılmalı');
    assertSame(1, (int) $langs['tr']['is_active'], 'Varsayılan dil listede olmasa da açık kalmalı');
    assertSame(0, (int) $langs['en']['is_active'], 'İşaretlenmeyen dil kapanmalı');

    arc_activate_langs(['tr']);
    arc_logout_test();
});
