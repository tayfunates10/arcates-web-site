<?php
/** UI sadeleştirme regresyon testleri. */
declare(strict_types=1);

test('F-UI-01', 'Sayfa ve ana hero basliklarinin ustunde etiket kalmaz', function (): void {
    $files = [
        'views/front/partials/hero.php',
        'views/front/location.php',
        'views/front/service.php',
        'views/front/sector.php',
        'views/front/contact.php',
        'views/front/posts.php',
        'views/front/post.php',
        'views/front/projects.php',
        'views/front/project.php',
    ];

    foreach ($files as $file) {
        $source = (string) file_get_contents(ARC_ROOT . '/' . $file);
        assertNotContains('page__eyebrow', $source, $file . ' ust baslik etiketi icermemeli');
        if ($file === 'views/front/partials/hero.php') {
            assertNotContains('hero__badge', $source, 'Ana hero rozet/ust baslik icermemeli');
        }
    }
});

test('F-UI-02', 'Surec numaralari sifir dolgusu olmadan yazilir', function (): void {
    $steps = (string) file_get_contents(ARC_ROOT . '/views/front/partials/steps.php');
    assertNotContains('str_pad', $steps, 'Surec numaralari 01, 02 biciminde uretilmemeli');
    assertContains('(int) ($index + 1)', $steps, 'Surec numaralari 1, 2, 3 biciminde yazilmali');
});
