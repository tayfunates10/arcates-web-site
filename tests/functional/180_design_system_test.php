<?php
/**
 * TASARIM-PLANI.md kabul kapilari — T1/T2/T3/T4.
 */

declare(strict_types=1);

use Arcates\Core\ContentOutline;

function arc_front_template_files(): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
        ARC_ROOT . '/views/front',
        FilesystemIterator::SKIP_DOTS
    ));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

test('F-T1-a', 'Spacing olcegi token disinda ham px kullanmaz', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/site.css');
    $afterTokens = strstr($css, '@layer reset');
    assertTrue(is_string($afterTokens), 'Token katmanindan sonraki CSS bulunmali');

    preg_match_all('/(?:margin(?:-[a-z]+)?|padding(?:-[a-z]+)?|gap|row-gap|column-gap)\s*:[^;]*\b\d+(?:\.\d+)?px\b/i', $afterTokens, $matches);
    assertSame([], $matches[0] ?? [], 'Spacing degerleri var(--sp-*) veya bagil birimlerden gelmeli');
});

test('F-T1-b', 'Site CSS important kullanmaz', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/site.css');
    assertNotContains('!important', $css, 'Katman sirasi ozgulluk savasini ortadan kaldirmali');
});

test('F-T1-c', 'CSS katman sirasi ve sekiz katman tanimlidir', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/site.css');
    assertContains('@layer tokens, reset, base, layout, components, pages, motion, utilities;', $css);
    foreach (['tokens', 'reset', 'base', 'layout', 'components', 'pages', 'motion', 'utilities'] as $layer) {
        assertContains('@layer ' . $layer . ' {', $css, 'Eksik CSS katmani: ' . $layer);
    }
});

test('F-T2-a', 'On yuz sablonlarinda satir ici style niteligi yoktur', function (): void {
    foreach (arc_front_template_files() as $file) {
        $source = (string) file_get_contents($file);
        assertFalse(
            preg_match('/\sstyle\s*=\s*["\']/i', $source) === 1,
            'Satir ici style bulundu: ' . str_replace(ARC_ROOT . '/', '', $file)
        );
    }
});

test('F-T3-a', 'Icerik basliklari guvenli TOC ve benzersiz capa uretir', function (): void {
    $outline = ContentOutline::prepare('<h2>Hizmet Süreci</h2><p>Metin</p><h3>İlk Adım</h3><h2>Hizmet Süreci</h2>');
    assertCount(3, $outline['items']);
    assertContains('id="hizmet-sureci"', $outline['html']);
    assertContains('id="ilk-adim"', $outline['html']);
    assertContains('id="hizmet-sureci-2"', $outline['html']);

    foreach (['location.php', 'service.php', 'sector.php', 'post.php'] as $template) {
        $source = (string) file_get_contents(ARC_ROOT . '/views/front/' . $template);
        assertContains("front/partials/toc", $source, 'TOC eksik: ' . $template);
        assertContains('data-outline', $source, 'Baslik gozlem alani eksik: ' . $template);
    }
});

test('F-T3-b', 'Ic sayfa yan sutunu 940 altinda icerigin ustune tasinir', function (): void {
    $css = (string) file_get_contents(ARC_ROOT . '/public/assets/css/site.css');
    assertContains('@media (max-width: 58.75rem)', $css, '940 breakpointi rem karsiligiyla bulunmali');
    assertContains('.content-aside, .article-aside { order: -1; }', $css, 'Yan sutun mobilde once gelmeli');
});

test('F-T4-a', 'Iletisim formu DOM siralamasinda metinden once gelir', function (): void {
    $source = (string) file_get_contents(ARC_ROOT . '/views/front/contact.php');
    $form = strpos($source, "front/partials/form");
    $copy = strpos($source, 'contact-copy');
    assertTrue($form !== false && $copy !== false && $form < $copy, 'Form mobilde ilk ekrana uygun DOM sirasinda olmali');
});

test('F-T4-b', 'Planlanan JS modulleri bagimsiz baslatilir', function (): void {
    $js = (string) file_get_contents(ARC_ROOT . '/public/assets/js/site.js');
    foreach (['initToc()', 'initStickyCta()', 'initFormState()'] as $call) {
        assertContains($call, $js, 'Eksik JS modulu: ' . $call);
    }
    assertContains('data-reveal-step', $js, 'Reveal kademesi veri niteligiyle desteklenmeli');
});
