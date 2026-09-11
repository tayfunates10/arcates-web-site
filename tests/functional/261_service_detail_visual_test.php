<?php
declare(strict_types=1);

use Arcates\Core\Lang;
use Arcates\Core\View;

test('F-LIVE-05', 'Hizmet detaylari dogru G-02 gorselini kullanir ve bilinmeyen slug guvenle kalir', function (): void {
    arc_need_db();
    Lang::use('tr');
    $visuals = [
        'web-tasarim' => 'layout', 'e-ticaret-sitesi' => 'cart',
        'rezervasyon-sistemi' => 'calendar', 'seo-hizmeti' => 'search',
        'coklu-dil-web-sitesi' => 'globe', 'web-sitesi-bakim' => 'shield',
        'custom-service' => null,
    ];
    foreach ($visuals as $slug => $key) {
        $html = View::render('front/service', [
            'page' => ['slug' => $slug, 'title' => 'A & B', 'excerpt' => '<test>', 'content' => '<h2>Kapsam</h2><p>Aciklama.</p>'],
            'faqs' => [], 'relatedPages' => [], 'crumbs' => [],
        ]);
        assertSame(1, substr_count($html, '<h1'));
        assertContains('A &amp; B', $html);
        assertContains('&lt;test&gt;', $html);
        assertContains('Kapsam', $html);
        if ($key === null) {
            assertNotContains('service-hero__media', $html, 'Bilinmeyen slug kirik gorsel uretmez');
            assertNotContains('service-hero__grid', $html, 'Gorselsiz sayfada bos sag kolon yok');
        } else {
            assertContains('service-' . $key . '.svg', $html);
            assertContains('width="480" height="360" alt=""', $html);
            assertContains('loading="eager"', $html);
        }
    }
});
