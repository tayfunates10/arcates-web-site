<?php
/**
 * Request birim testleri.  DOCS.md 14.2 (U-15), 9.10, 12
 */

declare(strict_types=1);

use Arcates\Core\Request;

test('U-15', 'Googlebot bot olarak isaretlenir', function (): void {
    $bot = Request::make('GET', '/', [], [], [
        'HTTP_USER_AGENT' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
    ]);
    assertSame('bot', $bot->device());

    $bing = Request::make('GET', '/', [], [], ['HTTP_USER_AGENT' => 'Mozilla/5.0 (compatible; bingbot/2.0)']);
    assertSame('bot', $bing->device());

    $empty = Request::make('GET', '/', [], [], ['HTTP_USER_AGENT' => '']);
    assertSame('bot', $empty->device(), 'Bos tarayici imzasi bot sayilmali');
});

test('U-15b', 'Masaustu, mobil ve tablet ayrimi', function (): void {
    $desktop = Request::make('GET', '/', [], [], [
        'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36',
    ]);
    assertSame('desktop', $desktop->device());

    $mobile = Request::make('GET', '/', [], [], [
        'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605 Mobile/15E148',
    ]);
    assertSame('mobile', $mobile->device());

    $tablet = Request::make('GET', '/', [], [], [
        'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605 Safari/604.1',
    ]);
    assertSame('tablet', $tablet->device());
});

test('U-15c', 'UTM parametreleri toplanir', function (): void {
    $request = Request::make('GET', '/edremit-web-tasarim', [], [
        'utm_source'   => 'google',
        'utm_medium'   => 'cpc',
        'utm_campaign' => 'edremit-2026',
        'baska'        => 'deger',
    ]);

    $utm = $request->utm();
    assertSame('google', $utm['utm_source']);
    assertSame('cpc', $utm['utm_medium']);
    assertSame('edremit-2026', $utm['utm_campaign']);
    assertFalse(isset($utm['baska']), 'Beklenmeyen parametre alinmamali');
});

test('U-15d', 'Yol normallestirmesi', function (): void {
    assertSame('/hakkimizda', Request::make('GET', '/hakkimizda/')->path());
    assertSame('/', Request::make('GET', '/')->path());
    assertSame('/blog/yazi', Request::make('GET', 'blog/yazi')->path());
    assertSame(['blog', 'yazi'], Request::make('GET', '/blog/yazi')->segments());
});
