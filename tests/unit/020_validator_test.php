<?php
/**
 * Validator birim testleri.  DOCS.md 14.2 (U-04, U-05), 10.8
 */

declare(strict_types=1);

use Arcates\Core\Validator;

test('U-04', 'Gecersiz e-posta reddedilir', function (): void {
    $v = new Validator(['email' => 'gecersiz-adres']);
    $v->email('email');
    assertTrue($v->fails(), 'Gecersiz e-posta basarisiz olmali');

    $ok = new Validator(['email' => 'bilgi@arcates.com']);
    $ok->email('email');
    assertTrue($ok->passes(), 'Gecerli e-posta gecmeli');
});

test('U-05', 'Bos dize zorunlu alanda reddedilir', function (): void {
    $v = new Validator(['name' => '   ']);
    $v->required('name');
    assertTrue($v->fails(), 'Yalnizca bosluk iceren deger reddedilmeli');

    $missing = new Validator([]);
    $missing->required('name');
    assertTrue($missing->fails(), 'Eksik alan reddedilmeli');

    $ok = new Validator(['name' => 'Arcates']);
    $ok->required('name');
    assertTrue($ok->passes(), 'Dolu alan gecmeli');
});

test('U-05b', 'Uzunluk, sayi ve aralik kurallari', function (): void {
    $v = new Validator(['title' => str_repeat('a', 201)]);
    $v->max('title', 200);
    assertTrue($v->fails(), 'Uzun deger reddedilmeli');

    $v2 = new Validator(['sort' => 'abc']);
    $v2->integer('sort');
    assertTrue($v2->fails(), 'Sayi olmayan deger reddedilmeli');

    $v3 = new Validator(['code' => 999]);
    $v3->between('code', 300, 308);
    assertTrue($v3->fails(), 'Aralik disi deger reddedilmeli');
});

test('U-05c', 'Sifre en az 10 karakter olmali', function (): void {
    arc_test_config();

    $short = new Validator(['password' => 'kisa123']);
    $short->password('password');
    assertTrue($short->fails(), 'Kisa sifre reddedilmeli');

    $ok = new Validator(['password' => 'guclu-sifre-2026']);
    $ok->password('password');
    assertTrue($ok->passes(), 'Uzun sifre gecmeli');
});

test('U-05d', 'KVKK onay kutusu isaretlenmeden gecmez', function (): void {
    $v = new Validator([]);
    $v->accepted('kvkk');
    assertTrue($v->fails(), 'Isaretlenmemis onay reddedilmeli');

    $ok = new Validator(['kvkk' => '1']);
    $ok->accepted('kvkk');
    assertTrue($ok->passes(), 'Isaretli onay gecmeli');
});

test('U-05e', 'Slug kurali Turkce karakteri reddeder', function (): void {
    $v = new Validator(['slug' => 'edremit-tasarım']);
    $v->slug('slug');
    assertTrue($v->fails(), 'Turkce karakterli slug reddedilmeli');

    $ok = new Validator(['slug' => 'edremit-web-tasarim']);
    $ok->slug('slug');
    assertTrue($ok->passes(), 'Temiz slug gecmeli');
});
