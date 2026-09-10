<?php
/** Ortak kabuk sayfaya gore kaybolmamali; bilincli bos deger korunmali. */
declare(strict_types=1);

use Arcates\Core\Lang;
use Arcates\Core\Response;
use Arcates\Models\HomeSection;

test('F-LIVE-01', 'Ic sayfalar paneldeki ortak header ve footer icerigini kullanir', function (): void {
    arc_need_db();
    Lang::use('tr');
    $header = HomeSection::content('header', 'tr');
    $footer = HomeSection::content('footer', 'tr');
    $controller = new class extends Arcates\Controllers\Front\Controller {
        public function preview(array $data = []): Response
        {
            return $this->render('front/thanks', $data);
        }
    };
    try {
        HomeSection::saveContent('header', 'tr', ['cta' => ['label' => 'LIVE-HEADER-CTA', 'url' => '/iletisim']]);
        HomeSection::saveContent('footer', 'tr', [
            'about' => 'LIVE-FOOTER-ABOUT',
            'columns' => [['title' => 'LIVE-FOOTER-COLUMN', 'links' => [['label' => 'LIVE-LEGAL', 'url' => '/kvkk']]]],
        ]);
        $html = $controller->preview()->body();
        assertContains('LIVE-HEADER-CTA', $html);
        assertContains('LIVE-FOOTER-ABOUT', $html);
        assertContains('LIVE-FOOTER-COLUMN', $html);
        assertContains('LIVE-LEGAL', $html);

        $empty = $controller->preview(['headerCta' => null, 'footerContent' => []])->body();
        assertNotContains('LIVE-HEADER-CTA', $empty, 'Acik null header tercihi korunur');
        assertNotContains('LIVE-FOOTER-ABOUT', $empty, 'Acik bos footer tercihi korunur');
    } finally {
        HomeSection::saveContent('header', 'tr', $header);
        HomeSection::saveContent('footer', 'tr', $footer);
    }
});
