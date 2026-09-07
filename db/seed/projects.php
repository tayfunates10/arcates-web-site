<?php
/**
 * Referans kayitlari.  DOCS.md 4.7, 9.4
 *
 * Bolum 4.7: her ilce sayfasi o ilceye ait EN AZ BIR referans veya ornek
 * icermelidir. Asagidaki liste sekiz ilcenin her biri icin en az bir kayit
 * tasir; `district` alani ilce sayfasindaki `district` degeriyle eslesir.
 *
 * Bu kayitlar ornek icerik olarak isaretlidir ve yayin oncesi teslim
 * listesindeki "demo icerik temizlendi" maddesi geregi canliya cikmadan once
 * gercek musteri kayitlariyla degistirilir (bolum 17).
 */

declare(strict_types=1);

return [
    [
        'client' => 'Örnek Körfez Otel', 'sector' => 'Konaklama', 'district' => 'Akçay',
        'title' => 'Akçay pansiyon rezervasyon sitesi',
        'slug' => 'akcay-pansiyon-rezervasyon-sitesi',
        'excerpt' => 'Komisyonsuz doğrudan rezervasyon ve Almanca yayın.',
        'content' => '<h2>İhtiyaç</h2><p>Doluluğunun tamamını platformlardan sağlayan '
            . 'bir pansiyon, komisyon yükünü azaltmak istiyordu.</p>'
            . '<h2>Yapılanlar</h2><ul><li>Müsaitlik takvimi ve dönem bazlı fiyatlandırma</li>'
            . '<li>Kapora ile doğrudan rezervasyon</li><li>Almanca ve İngilizce yayın</li>'
            . '<li>Sezon dışı uzun konaklama sayfası</li></ul>',
    ],
    [
        'client' => 'Örnek Zeytin Kooperatifi', 'sector' => 'Zeytinyağı', 'district' => 'Burhaniye',
        'title' => 'Kooperatif zeytinyağı e-ticaret mağazası',
        'slug' => 'burhaniye-kooperatif-e-ticaret',
        'excerpt' => 'Hasat takvimine göre on sipariş alan mağaza.',
        'content' => '<h2>İhtiyaç</h2><p>Üye üreticilerin ürününü kendi markasıyla '
            . 'satmak isteyen bir kooperatif.</p>'
            . '<h2>Yapılanlar</h2><ul><li>Litre ve kilogram varyantlarıyla stok takibi</li>'
            . '<li>Hasat dönemi on siparişi ve bekleme listesi</li>'
            . '<li>Bölgeye göre kargo ücreti</li><li>Üretici tanıtım sayfaları</li></ul>',
    ],
    [
        'client' => 'Örnek Dış Polikliniği', 'sector' => 'Sağlık', 'district' => 'Edremit',
        'title' => 'Edremit poliklinik kurumsal sitesi',
        'slug' => 'edremit-poliklinik-kurumsal-site',
        'excerpt' => 'Randevu formu ve hizmet bazlı sayfalar.',
        'content' => '<h2>İhtiyaç</h2><p>Harita sonuçlarında görünen ama sitesi olmadığı '
            . 'için karşılaştırmada geride kalan bir poliklinik.</p>'
            . '<h2>Yapılanlar</h2><ul><li>Her tedavi için ayrı sayfa ve SSS</li>'
            . '<li>Randevu formu ve çalışma saatleri</li>'
            . '<li>Google İşletme Profili ile birebir aynı iletişim bilgileri</li></ul>',
    ],
    [
        'client' => 'Örnek Emlak Ofisi', 'sector' => 'Emlak', 'district' => 'Altınoluk',
        'title' => 'Altınoluk emlak ofisi bölge rehberi',
        'slug' => 'altinoluk-emlak-bolge-rehberi',
        'excerpt' => 'İlan listelemek yerine bölgeyi anlatan site.',
        'content' => '<h2>İhtiyaç</h2><p>Portal bağımlılığını azaltmak isteyen bir '
            . 'emlak ofisi.</p>'
            . '<h2>Yapılanlar</h2><ul><li>Her mahalle için bölge rehberi sayfası</li>'
            . '<li>Aidat, kış doluluğu ve mesafe bilgileri</li>'
            . '<li>İngilizce sayfalarda tapu ve oturma izni anlatımı</li></ul>',
    ],
    [
        'client' => 'Örnek Butik Otel', 'sector' => 'Konaklama', 'district' => 'Ayvalık',
        'title' => 'Cunda butik otel çoklu dil sitesi',
        'slug' => 'ayvalik-butik-otel-coklu-dil',
        'excerpt' => 'Üç dilde gerçek içerik ve hızlı galeri.',
        'content' => '<h2>İhtiyaç</h2><p>Yabancı misafir oranı yüksek bir butik otel, '
            . 'otomatik çeviriyle görünür olamıyordu.</p>'
            . '<h2>Yapılanlar</h2><ul><li>Türkçe, İngilizce ve Almanca ayrı içerik</li>'
            . '<li>WebP galeri; mobilde hızlı yükleme</li>'
            . '<li>Oda bazlı gerçek fotoğraf ve ayrıntı anlatımı</li></ul>',
    ],
    [
        'client' => 'Örnek Tarım Bayii', 'sector' => 'Tarım', 'district' => 'Havran',
        'title' => 'Havran tarım bayii ürün sitesi',
        'slug' => 'havran-tarim-bayii-urun-sitesi',
        'excerpt' => 'Mevsime göre ürün sayfaları ve sade yönetim.',
        'content' => '<h2>İhtiyaç</h2><p>Sulama ve hasat sezonlarında arama trafiğini '
            . 'yakalamak isteyen bir tarım bayii.</p>'
            . '<h2>Yapılanlar</h2><ul><li>Sezon öncesi yayınlanan ürün sayfaları</li>'
            . '<li>Hafif tasarım; düşük bağlantı hızında hızlı açılma</li>'
            . '<li>Telefondan yönetilebilen panel</li></ul>',
    ],
    [
        'client' => 'Örnek Apart', 'sector' => 'Konaklama', 'district' => 'Gömeç',
        'title' => 'Gömeç apart uzun konaklama sitesi',
        'slug' => 'gomec-apart-uzun-konaklama',
        'excerpt' => 'Haftalık ve aylık kiralama için ayrı sayfalar.',
        'content' => '<h2>İhtiyaç</h2><p>Sezon dışında boş kalan apart daireler için '
            . 'uzun dönem misafir.</p>'
            . '<h2>Yapılanlar</h2><ul><li>Günlük ve uzun dönem için ayrı sayfalar</li>'
            . '<li>Mutfak donanımı, internet hızı ve market mesafesi bilgileri</li>'
            . '<li>Sezon öncesi hatırlatma için müşteri listesi</li></ul>',
    ],
    [
        'client' => 'Örnek Makine Sanayi', 'sector' => 'Sanayi', 'district' => 'Balıkesir',
        'title' => 'Balıkesir üretim firması teknik sitesi',
        'slug' => 'balikesir-uretim-teknik-site',
        'excerpt' => 'İngilizce teknik içerik ve indirilebilir dosyalar.',
        'content' => '<h2>İhtiyaç</h2><p>İhracat yapan bir üretim firması, teknik '
            . 'bilgiyi karşı tarafa hızlı ulaştırmak istiyordu.</p>'
            . '<h2>Yapılanlar</h2><ul><li>Ürün kodu ve kapasite bazlı liste</li>'
            . '<li>İndirilebilir teknik dosyalar</li>'
            . '<li>İngilizce teknik terimlerle yazılmış ayrı içerik</li></ul>',
    ],
];
