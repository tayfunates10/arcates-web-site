<?php
/**
 * Referans ana sayfa isleme, veri dogrulugu ve erisilebilirlik testleri.
 */
declare(strict_types=1);

use Arcates\Controllers\Front\HomeController;
use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Models\HomeSection;
use Arcates\Models\Project;

function arc_home(string $lang = 'tr'): Arcates\Core\Response
{
    Lang::use($lang);
    return (new HomeController())->index(Request::make('GET', $lang === 'tr' ? '/' : '/' . $lang), []);
}

test('F-REF-01', 'Referans anasayfa temel bolumleri ve stil katmaniyla islenir', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();

    $response = arc_home();
    assertSame(200, $response->status());
    $body = $response->body();

    foreach ([
        'ref-home',
        'ref-hero',
        'ref-metrics',
        'ref-services',
        'ref-process',
        'ref-why',
        'ref-final-cta',
        'site-foot',
    ] as $needle) {
        assertContains($needle, $body);
    }

    assertContains('css/reference-home.css', $body, 'Referans stili yalniz anasayfaya eklenmeli');
    assertNotContains('css/home-redesign.css', $body, 'Eski anasayfa katmani yeni referansla birlikte yuklenmemeli');
});

test('O-01', 'Anasayfada tek H1 bulunur ve baslik hiyerarsisi atlamaz', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();
    $body = arc_home()->body();

    assertSame(1, substr_count($body, '<h1'));
    preg_match_all('/<h([1-6])\b/', $body, $matches);
    $levels = array_map('intval', $matches[1]);
    $previous = 0;
    foreach ($levels as $level) {
        if ($previous > 0) {
            assertTrue($level <= $previous + 1, "Baslik seviyesi atlanmis: h{$previous} sonrasi h{$level}");
        }
        $previous = $level;
    }
});

test('E-REF-02', 'Dekoratif hero gorseli erisilebilirlik agacindan gizlenir ve tum img etiketleri alt tasir', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();
    $body = arc_home()->body();

    assertContains('<div class="ref-hero__visual" aria-hidden="true">', $body);
    assertContains('img/reference/hero-laptop.webp', $body);
    assertContains('alt=""', $body, 'Dekoratif hero gorseli bos alt metniyle isaretlenmeli');
    assertSame(0, Arcates\Core\Seo::countImagesWithoutAlt($body));
});

test('F-REF-03', 'Hero CMS basligi degisince referans arayuze aninda yansir', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();

    $content = HomeSection::content('hero', 'tr');
    $content['line3'] = 'yepyeni bir baslik satiri';
    HomeSection::saveContent('hero', 'tr', $content);

    $body = arc_home()->body();
    assertContains('yepyeni bir baslik satiri', $body);
    assertContains('ref-accent', $body);
    assertContains('ref-hero__visual', $body);
});

test('F-REF-04', 'Hizmet bolumu panelden kapatilinca referans kartlari gorunmez', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();

    assertContains('ref-services', arc_home()->body());
    HomeSection::toggle('services');
    assertNotContains('ref-services', arc_home()->body());
    HomeSection::toggle('services');
    assertContains('ref-services', arc_home()->body());
});

test('F-REF-05', 'Vitrin referanstaki uc kartlik siniri korur ve gercek proje verisini kullanir', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    $db->run('DELETE FROM projects');

    $id = Project::save(
        [
            'client_name' => 'Referans Test',
            'sector' => 'Yazilim',
            'district' => 'Edremit',
            'live_url' => '',
            'status' => 'published',
            'sort' => 1,
        ],
        ['tr' => [
            'title' => 'Gercek CMS Referansi',
            'slug' => 'gercek-cms-referansi',
            'excerpt' => 'Referans karti test verisi.',
            'content' => '<p>Gercek proje verisi.</p>',
            'robots' => 'index,follow',
        ]]
    );

    $body = arc_home()->body();
    $count = substr_count($body, '<li class="ref-project">');
    assertSame(1, $count, 'Yayinlanmis CMS projesi vitrinde gorunmeli');
    assertContains('Gercek CMS Referansi', $body);
    assertTrue($count <= 3, 'Referans masaustu duzeni en fazla uc proje karti gostermeli');
    assertNotContains('Sahte Proje', $body);

    $db->run('DELETE FROM project_translations WHERE project_id = :id', [':id' => $id]);
    $db->run('DELETE FROM projects WHERE id = :id', [':id' => $id]);
});

test('F-REF-06', 'Referans gorselleri yerel optimize WebP dosyalari olarak bulunur', function (): void {
    foreach ([
        ARC_ROOT . '/public/assets/img/reference/hero-laptop.webp',
        ARC_ROOT . '/public/assets/img/reference/about-team.webp',
        ARC_ROOT . '/public/assets/img/reference/cta-mountain.webp',
    ] as $path) {
        assertTrue(is_file($path), 'Referans gorseli eksik: ' . basename($path));
        $bytes = filesize($path);
        assertTrue($bytes !== false && $bytes > 0 && $bytes < 80 * 1024, 'Referans WebP hafif olmali: ' . basename($path));
    }
});

test('A-REF-07', 'Mobil referans duzeni kartlari ve sureci tek sutuna indirir', function (): void {
    $path = ARC_ROOT . '/public/assets/css/reference-home.css';
    assertTrue(is_file($path), 'Referans CSS dosyasi bulunmali');
    $css = (string) file_get_contents($path);

    assertContains('@media(max-width:640px)', $css);
    assertContains('.ref-services__grid{grid-template-columns:1fr', $css);
    assertContains('.ref-process__list{grid-template-columns:1fr', $css);
    assertContains('.ref-posts__grid{grid-template-columns:1fr', $css);
    assertContains('@media(prefers-reduced-motion:reduce)', $css);
    assertNotContains('url(http', strtolower($css), 'Referans stilinde harici gorsel kaynagi kullanilmamali');
});

test('SEO-REF-08', 'Gorunmeyen FAQ icin yapilandirilmis veri uretilmez', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();
    $body = arc_home()->body();

    assertNotContains('FAQPage', $body, 'Sayfada gorunmeyen FAQ schema basilmamali');
    assertContains('ProfessionalService', $body, 'Ana hizmet schema korunmali');
});
