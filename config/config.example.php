<?php
/**
 * Arcates Web Site — yapilandirma ornegi
 *
 * Bu dosyayi `config/config.php` olarak kopyalayin ve degerleri doldurun.
 * `config/config.php` surum kontrolune girmez (bkz. .gitignore).
 *
 * DOCS.md 2, 10.7, 13
 */

declare(strict_types=1);

return [

    'app' => [
        // Sondaki egik cizgi olmadan tam adres.
        'base_url'   => 'https://arcatesyazilim.com',
        // 'production' veya 'local'
        'env'        => 'production',
        // Canlida daima false. DOCS.md 10.7
        'debug'      => false,
        'timezone'   => 'Europe/Istanbul',
        'name'       => 'Arcates Yazilim',
        // Panel yolu degistirilebilir. DOCS.md 9
        'admin_path' => 'panel',
    ],

    'db' => [
        'host'    => 'localhost',
        'port'    => 3306,
        'name'    => 'arcates',
        'user'    => 'arcates',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    'lang' => [
        // Varsayilan dil oneksizdir. DOCS.md 4.6
        'default'   => 'tr',
        'available' => ['tr', 'en', 'de', 'ar'],
    ],

    'mail' => [
        // 'mail' (PHP mail) veya 'log' (storage/logs/mail.log)
        'method'    => 'mail',
        'from'      => 'site@arcatesyazilim.com',
        'from_name' => 'Arcates Yazilim',
        // Form bildirimlerinin gidecegi adres
        'to'        => 'info@arcatesyazilim.com',
    ],

    'security' => [
        'session_name'    => 'arcsid',
        // Saniye. 2 saat islemsizlikte oturum duser. DOCS.md 10.3
        'session_idle'    => 7200,
        // DOCS.md 10.4
        'login_max_tries' => 5,
        'login_lock'      => 900,
        'password_min'    => 10,
        // DOCS.md 10.8 — saatte IP basina form gonderimi
        'form_max_hourly' => 5,
        // Formun en az bu kadar saniye acik kalmis olmasi gerekir
        'form_min_seconds' => 3,
        // Cerezlerde secure bayragi. Yerel http gelistirmede false yapin.
        'cookie_secure'   => true,
    ],

    'upload' => [
        // Bayt. 5 MB. DOCS.md 10.6
        'max_size'    => 5 * 1024 * 1024,
        'allowed_ext' => ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'pdf'],
        'variants'    => [
            'thumb'  => 320,
            'medium' => 768,
            'large'  => 1600,
        ],
    ],

    'privacy' => [
        // Gun. Suresi dolan form kayitlari otomatik silinir. DOCS.md 10.9
        'submission_retention_days' => 730,
        // Gun. Ham ziyaret kayitlari bu suredan sonra gunluge toplanir. DOCS.md 8.4
        'visit_retention_days'      => 90,
    ],
];
