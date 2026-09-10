<?php
declare(strict_types=1);

test('F-R3-06', 'Proje cercevesi yalniz gercek yuklenmis medya icin kullanilir', function (): void {
    $list = (string) file_get_contents(ARC_ROOT . '/views/front/projects.php');
    $detail = (string) file_get_contents(ARC_ROOT . '/views/front/project.php');
    $legacyHome = (string) file_get_contents(ARC_ROOT . '/views/front/partials/works.php');
    $referenceHome = (string) file_get_contents(ARC_ROOT . '/views/front/home.php');

    foreach ([$list, $detail, $legacyHome] as $template) {
        assertContains("!empty(\$project['cover'])", $template, 'Proje gorsel cercevesi cover verisine bagli olmali');
        assertContains('project-shot', $template, 'Gercek proje medyasi R3 cercevesi kullanmali');
        assertNotContains('editorial-cover', $template, 'Proje icin sahte editoryal ekran uretilmemeli');
    }

    assertContains("!empty(\$project['cover'])", $referenceHome, 'Referans ana sayfa proje medyasini gercek cover verisine baglamali');
    assertContains('ref-project__media', $referenceHome);
    assertNotContains('editorial-cover', $referenceHome, 'Referans proje karti sahte kapak uretmemeli');
    assertContains('project-gallery__frame', $detail, 'Gercek galeri medyasi sunum yuzeyi kullanmali');
});

test('F-R3-07', 'Blog kapagi gercek medyayi onceleyip metinsiz fallback kullanir', function (): void {
    $list = (string) file_get_contents(ARC_ROOT . '/views/front/posts.php');
    $detail = (string) file_get_contents(ARC_ROOT . '/views/front/post.php');
    $partial = (string) file_get_contents(ARC_ROOT . '/views/front/partials/editorial-cover.php');
    $referenceHome = (string) file_get_contents(ARC_ROOT . '/views/front/home.php');

    assertContains("if (!empty(\$post['cover']))", $list);
    assertContains("if (!empty(\$post['cover']))", $detail);
    assertContains("partial('front/partials/editorial-cover'", $list);
    assertContains("partial('front/partials/editorial-cover'", $detail);
    assertContains('aria-hidden="true"', $partial, 'Fallback kapak dekoratif olmali');
    assertNotContains('<img', $partial, 'Fallback harici veya sahte raster kullanmamali');
    assertNotContains('<text', strtolower($partial), 'Fallback gorselin icine metin gommemeli');

    assertContains("!empty(\$post['cover'])", $referenceHome, 'Referans ana sayfa blog kapagini gercek cover verisine baglamali');
    assertContains('ref-post__media', $referenceHome);
});

test('F-R3-067', 'Eski G-06 G-07 stili ic sayfalarda kalir; referans anasayfa kendi stilini kullanir', function (): void {
    $cssPath = ARC_ROOT . '/public/assets/css/r3-project-blog-media.css';
    assertTrue(is_file($cssPath), 'R3 proje/blog medya CSS dosyasi bulunmali');
    $bytes = filesize($cssPath);
    assertTrue($bytes !== false && $bytes < 18 * 1024, 'R3 proje/blog medya CSS 18 KB altinda olmali');

    $home = (string) file_get_contents(ARC_ROOT . '/app/Controllers/Front/HomeController.php');
    $projects = (string) file_get_contents(ARC_ROOT . '/app/Controllers/Front/ProjectController.php');
    $posts = (string) file_get_contents(ARC_ROOT . '/app/Controllers/Front/PostController.php');
    assertContains('css/reference-home.css', $home, 'Referans anasayfa kendi stil katmanini yuklemeli');
    assertNotContains('css/r3-project-blog-media.css', $home, 'Eski anasayfa medya stili referans sayfada yuklenmemeli');
    assertContains('css/r3-project-blog-media.css', $projects, 'Proje ic sayfasi R3 medya stilini korumali');
    assertContains('css/r3-project-blog-media.css', $posts, 'Blog ic sayfasi R3 medya stilini korumali');

    $css = strtolower((string) file_get_contents($cssPath));
    assertNotContains('url(http', $css, 'R3 medya stili harici kaynak kullanmamali');
    assertContains('@media (prefers-reduced-motion: reduce)', $css, 'Hareket azaltma davranisi korunmali');
});
