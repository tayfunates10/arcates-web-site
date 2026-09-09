<?php
/** R9 production/live acceptance contract. */
declare(strict_types=1);

function arc_r9_file(string $path): string
{
    return (string) file_get_contents(ARC_ROOT . '/' . $path);
}

test('F-R9-01', 'R9 canli kabul workflowu yalniz manuel ve HTTPS hedefle calisir', function (): void {
    $workflow = arc_r9_file('.github/workflows/live-acceptance.yml');
    assertContains('workflow_dispatch:', $workflow);
    assertContains('base_url:', $workflow);
    assertContains('node tools/browser/live-check.mjs', $workflow);
    assertContains('actions/checkout@v6', $workflow);
    assertContains('actions/upload-artifact@v6', $workflow);
});

test('F-R9-02', 'R9 canli Chromium denetimi responsive canonical robots sitemap varlik 404 ve redirect kapilarini tasir', function (): void {
    $browser = arc_r9_file('tools/browser/live-check.mjs');
    foreach ([
        "base.protocol !== 'https:'",
        "{ width: 390, height: 844 }",
        "{ width: 1366, height: 900 }",
        "link[rel=\"canonical\"]",
        "'/robots.txt'",
        "'/sitemap.xml'",
        "'__arcates-r9-live-404__'",
        'state.mainLeft !== null && state.mainLeft >= -1',
        'state.h1Right !== null && state.h1Right <= width + 1',
        'maxRedirects: 0',
        'response.status() === 301',
    ] as $needle) {
        assertContains($needle, $browser, 'Eksik R9 canli kabul kriteri: ' . $needle);
    }
});

test('F-R9-03', 'Production kontrol listesi surumu VERSION dosyasindan takip eder ve eski 1.0.0 sabitini tasimaz', function (): void {
    $production = arc_r9_file('PRODUCTION.md');
    assertContains('VERSION', $production);
    assertContains('Live Acceptance', $production);
    assertTrue(!str_contains($production, '`1.0.0`'), 'PRODUCTION.md eski 1.0.0 stabil sabitini tasimamali');
    assertTrue(!str_contains($production, '`1.0.0-rc1`'), 'PRODUCTION.md eski 1.0.0-rc1 sabitini tasimamali');
});

test('F-R9-04', 'R8 teslim kaydi exact main merge ve CI 247 kanitini saklar', function (): void {
    $doc = arc_r9_file('REDESIGN-R8-ACCEPTANCE.md');
    foreach ([
        '00c21bc8b8125607062696ff1d9902a7b0ad0180',
        'CI #247',
        '238/238',
        'TUM R8 NIHAI KABUL DENETIMLERI GECTI',
    ] as $needle) {
        assertContains($needle, $doc, 'Eksik R8 main kapanis kaniti: ' . $needle);
    }
});

test('F-R9-05', 'R9 teslim kaydi otomatik ve elle production kapilarini ayirir', function (): void {
    $doc = arc_r9_file('RELEASE-R9-PRODUCTION.md');
    foreach ([
        'Repo/CI: TAMAMLANDI',
        'Canli host: BEKLIYOR',
        'php tools/migrate.php',
        'php tools/preflight.php',
        'Live Acceptance',
        'gercek e-posta',
        'cron',
        'yedek',
        'Search Console',
        'Rich Results',
        'Lighthouse',
    ] as $needle) {
        assertContains($needle, $doc, 'Eksik R9 teslim kaydi: ' . $needle);
    }
});
