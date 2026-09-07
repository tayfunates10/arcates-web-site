<?php
/**
 * Görünür metnin Türkçe yazımı.
 *
 * Arayüz dizeleri, şablon metinleri ve başlangıç içeriği aksanlı harflerle
 * yazılır; adresler (slug) ASCII kalır. DOCS.md 4 (anahtar kelimeler ve URL
 * haritası), 11.3 (arayüz dizeleri)
 */

declare(strict_types=1);

/** Türkçe harflerin ASCII karşılığı. */
function arc_fold(string $text): string
{
    return strtr($text, [
        'ç' => 'c', 'Ç' => 'C', 'ğ' => 'g', 'Ğ' => 'G', 'ı' => 'i', 'İ' => 'I',
        'ö' => 'o', 'Ö' => 'O', 'ş' => 's', 'Ş' => 'S', 'ü' => 'u', 'Ü' => 'U',
    ]);
}

test('F-P13-a', 'Arayüz dizeleri aksanlı harf taşır', function (): void {
    $tr = require ARC_ROOT . '/lang/tr.php';

    assertSame('Menü', $tr['menu'], 'Menü dizesi aksanlı olmalı');
    assertSame('Tümü', $tr['all'], 'Tümü dizesi aksanlı olmalı');
    assertSame('Bölgeler', $tr['locations'], 'Bölgeler dizesi aksanlı olmalı');
    assertSame('Sektörler', $tr['sectors'], 'Sektörler dizesi aksanlı olmalı');
    assertSame('İletişim', $tr['contact'], 'İletişim dizesi aksanlı olmalı');

    $withMark = 0;
    foreach ($tr as $value) {
        if (is_string($value) && arc_fold($value) !== $value) {
            $withMark++;
        }
    }
    assertTrue($withMark >= 30, "lang/tr.php en az 30 aksanlı dize içermeli — bulunan: {$withMark}");
});

test('F-P13-b', 'Almanca dizeler umlaut taşır, ae/oe/ue yazımı kalmaz', function (): void {
    $de = require ARC_ROOT . '/lang/de.php';

    assertSame('Menü', $de['menu'], 'Menue yerine Menü olmalı');
    assertSame('Über uns', $de['about'], 'Ueber yerine Über olmalı');
    assertSame('Öffnungszeiten', $de['opening_hours'], 'Oeffnungszeiten yerine Öffnungszeiten olmalı');

    // "Neueste" gibi dogru yazimlar ue icerir; bu yuzden yalnizca bilinen
    // ASCII karsiliklari aranir.
    $translit = [
        'Menue', 'oeffnen', 'schliessen', 'Laedt', 'Haeufige', 'Ueber',
        'Veroeffentlicht', 'Aehnliche', 'Beitraege', 'Eintraege', 'Gewuenschte',
        'waehlen', 'pruefen', 'Kuerze', 'Oeffnungszeiten', 'geprueft', 'zurueck',
    ];
    $all = implode(' | ', array_filter($de, 'is_string'));
    foreach ($translit as $word) {
        assertNotContains($word, $all, "de.php icinde {$word} kalmamali");
    }
});

test('F-P13-c', 'Tohum başlıkları bölüm 4 anahtar kelimeleriyle örtüşür', function (): void {
    $pages = require ARC_ROOT . '/db/seed/pages.php';
    $index = [];
    foreach ($pages as $page) {
        $index[$page['slug']] = $page;
    }

    assertContains('tasarım', mb_strtolower($index['web-tasarim']['title'], 'UTF-8'), 'Hizmet başlığı "tasarım" içermeli');
    assertContains('fiyat', mb_strtolower($index['fiyatlar']['title'], 'UTF-8'), 'Fiyat sayfası başlığı');

    $locations = require ARC_ROOT . '/db/seed/locations.php';
    foreach ($locations as $row) {
        $title = mb_strtolower($row['title'], 'UTF-8');
        assertContains('tasarım', $title, "{$row['slug']} başlığı 'tasarım' içermeli");
    }
});

test('F-P13-d', 'Adresler ASCII kalır', function (): void {
    foreach (['pages', 'locations', 'sectors', 'projects'] as $file) {
        foreach (require ARC_ROOT . '/db/seed/' . $file . '.php' as $row) {
            $slug = (string) ($row['slug'] ?? '');
            assertTrue(
                $slug !== '' && preg_match('/^[a-z0-9-]+$/', $slug) === 1,
                "{$file}: slug yalnizca kucuk harf, rakam ve tire icermeli — {$slug}"
            );
        }
    }
});

test('F-P13-e', 'SQL anahtar kelimeleri ve regex bayrakları bozulmamış', function (): void {
    $files = [];
    $walk  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ARC_ROOT . '/app'));
    foreach ($walk as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }

    foreach ($files as $path) {
        $src = (string) file_get_contents($path);
        $name = basename($path);
        assertNotContains('İŞ NULL', $src, "{$name}: SQL 'IS NULL' bozulmus");
        assertNotContains('İŞ NOT NULL', $src, "{$name}: SQL 'IS NOT NULL' bozulmus");
        assertNotContains('ÖN DUPLICATE', $src, "{$name}: SQL 'ON DUPLICATE' bozulmus");
        assertNotContains("#iş'", $src, "{$name}: regex bayragi bozulmus");
    }
});
