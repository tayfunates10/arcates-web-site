<?php
/** CSV dışa aktarmada spreadsheet formula enjeksiyonu korunur. */

declare(strict_types=1);

use Arcates\Models\Submission;

test('S-21', 'CSV hücreleri formula enjeksiyonuna karşı güvenlidir', function (): void {
    $csv = Submission::toCsv([[
        'id' => 1,
        'created_at' => '2026-09-07 12:00:00',
        'status' => 'new',
        'name' => '=2+2',
        'email' => '+SUM(A1:A2)',
        'phone' => '@cmd',
        'service' => '-1+1',
        'message' => '=HYPERLINK("https://example.invalid")',
        'source_url' => '/iletisim',
        'referrer' => '=WEBSERVICE("https://example.invalid")',
        'utm' => ['utm_source' => '@source', 'utm_medium' => '+medium', 'utm_campaign' => '-campaign'],
        'lang' => 'tr',
        'kvkk_consent' => 1,
        'note' => '=1+1',
    ]]);

    $lines = preg_split('/\r?\n/', trim($csv));
    assertTrue(is_array($lines) && count($lines) >= 2, 'CSV veri satırı üretmeli');
    $cells = str_getcsv($lines[1]);

    foreach ([3, 4, 5, 6, 7, 9, 10, 11, 12, 15] as $index) {
        assertTrue(
            isset($cells[$index]) && str_starts_with($cells[$index], "'"),
            'Riskli hücre apostrof ile başlamalı: sütun ' . $index
        );
    }

    assertSame('/iletisim', $cells[8], 'Normal hücreler değiştirilmemeli');
    assertSame('tr', $cells[13], 'Normal dil hücresi değiştirilmemeli');
});
