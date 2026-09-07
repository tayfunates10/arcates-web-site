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
        'client' => 'Ornek Korfez Otel', 'sector' => 'Konaklama', 'district' => 'Akcay',
        'title' => 'Akcay pansiyon rezervasyon sitesi',
        'slug' => 'akcay-pansiyon-rezervasyon-sitesi',
        'excerpt' => 'Komisyonsuz dogrudan rezervasyon ve Almanca yayin.',
        'content' => '<h2>Ihtiyac</h2><p>Doluluğunun tamamini platformlardan saglayan '
            . 'bir pansiyon, komisyon yukunu azaltmak istiyordu.</p>'
            . '<h2>Yapilanlar</h2><ul><li>Musaitlik takvimi ve donem bazli fiyatlandirma</li>'
            . '<li>Kapora ile dogrudan rezervasyon</li><li>Almanca ve Ingilizce yayin</li>'
            . '<li>Sezon disi uzun konaklama sayfasi</li></ul>',
    ],
    [
        'client' => 'Ornek Zeytin Kooperatifi', 'sector' => 'Zeytinyagi', 'district' => 'Burhaniye',
        'title' => 'Kooperatif zeytinyagi e-ticaret magazasi',
        'slug' => 'burhaniye-kooperatif-e-ticaret',
        'excerpt' => 'Hasat takvimine gore on siparis alan magaza.',
        'content' => '<h2>Ihtiyac</h2><p>Uye ureticilerin urununu kendi markasiyla '
            . 'satmak isteyen bir kooperatif.</p>'
            . '<h2>Yapilanlar</h2><ul><li>Litre ve kilogram varyantlariyla stok takibi</li>'
            . '<li>Hasat donemi on siparisi ve bekleme listesi</li>'
            . '<li>Bolgeye gore kargo ucreti</li><li>Uretici tanitim sayfalari</li></ul>',
    ],
    [
        'client' => 'Ornek Dis Poliklinigi', 'sector' => 'Saglik', 'district' => 'Edremit',
        'title' => 'Edremit poliklinik kurumsal sitesi',
        'slug' => 'edremit-poliklinik-kurumsal-site',
        'excerpt' => 'Randevu formu ve hizmet bazli sayfalar.',
        'content' => '<h2>Ihtiyac</h2><p>Harita sonuclarinda gorunen ama sitesi olmadigi '
            . 'icin karsilastirmada geride kalan bir poliklinik.</p>'
            . '<h2>Yapilanlar</h2><ul><li>Her tedavi icin ayri sayfa ve SSS</li>'
            . '<li>Randevu formu ve calisma saatleri</li>'
            . '<li>Google Isletme Profili ile birebir ayni iletisim bilgileri</li></ul>',
    ],
    [
        'client' => 'Ornek Emlak Ofisi', 'sector' => 'Emlak', 'district' => 'Altinoluk',
        'title' => 'Altinoluk emlak ofisi bolge rehberi',
        'slug' => 'altinoluk-emlak-bolge-rehberi',
        'excerpt' => 'Ilan listelemek yerine bolgeyi anlatan site.',
        'content' => '<h2>Ihtiyac</h2><p>Portal bagimliligini azaltmak isteyen bir '
            . 'emlak ofisi.</p>'
            . '<h2>Yapilanlar</h2><ul><li>Her mahalle icin bolge rehberi sayfasi</li>'
            . '<li>Aidat, kis doluluğu ve mesafe bilgileri</li>'
            . '<li>Ingilizce sayfalarda tapu ve oturma izni anlatimi</li></ul>',
    ],
    [
        'client' => 'Ornek Butik Otel', 'sector' => 'Konaklama', 'district' => 'Ayvalik',
        'title' => 'Cunda butik otel coklu dil sitesi',
        'slug' => 'ayvalik-butik-otel-coklu-dil',
        'excerpt' => 'Uc dilde gercek icerik ve hizli galeri.',
        'content' => '<h2>Ihtiyac</h2><p>Yabanci misafir orani yuksek bir butik otel, '
            . 'otomatik ceviriyle gorunur olamiyordu.</p>'
            . '<h2>Yapilanlar</h2><ul><li>Turkce, Ingilizce ve Almanca ayri icerik</li>'
            . '<li>WebP galeri; mobilde hizli yukleme</li>'
            . '<li>Oda bazli gercek fotograf ve ayrinti anlatimi</li></ul>',
    ],
    [
        'client' => 'Ornek Tarim Bayii', 'sector' => 'Tarim', 'district' => 'Havran',
        'title' => 'Havran tarim bayii urun sitesi',
        'slug' => 'havran-tarim-bayii-urun-sitesi',
        'excerpt' => 'Mevsime gore urun sayfalari ve sade yonetim.',
        'content' => '<h2>Ihtiyac</h2><p>Sulama ve hasat sezonlarinda arama trafigini '
            . 'yakalamak isteyen bir tarim bayii.</p>'
            . '<h2>Yapilanlar</h2><ul><li>Sezon oncesi yayinlanan urun sayfalari</li>'
            . '<li>Hafif tasarim; dusuk baglanti hizinda hizli acilma</li>'
            . '<li>Telefondan yonetilebilen panel</li></ul>',
    ],
    [
        'client' => 'Ornek Apart', 'sector' => 'Konaklama', 'district' => 'Gomec',
        'title' => 'Gomec apart uzun konaklama sitesi',
        'slug' => 'gomec-apart-uzun-konaklama',
        'excerpt' => 'Haftalik ve aylik kiralama icin ayri sayfalar.',
        'content' => '<h2>Ihtiyac</h2><p>Sezon disinda bos kalan apart daireler icin '
            . 'uzun donem misafir.</p>'
            . '<h2>Yapilanlar</h2><ul><li>Gunluk ve uzun donem icin ayri sayfalar</li>'
            . '<li>Mutfak donanimi, internet hizi ve market mesafesi bilgileri</li>'
            . '<li>Sezon oncesi hatirlatma icin musteri listesi</li></ul>',
    ],
    [
        'client' => 'Ornek Makine Sanayi', 'sector' => 'Sanayi', 'district' => 'Balikesir',
        'title' => 'Balikesir uretim firmasi teknik sitesi',
        'slug' => 'balikesir-uretim-teknik-site',
        'excerpt' => 'Ingilizce teknik icerik ve indirilebilir dosyalar.',
        'content' => '<h2>Ihtiyac</h2><p>Ihracat yapan bir uretim firmasi, teknik '
            . 'bilgiyi karsi tarafa hizli ulastirmak istiyordu.</p>'
            . '<h2>Yapilanlar</h2><ul><li>Urun kodu ve kapasite bazli liste</li>'
            . '<li>Indirilebilir teknik dosyalar</li>'
            . '<li>Ingilizce teknik terimlerle yazilmis ayri icerik</li></ul>',
    ],
];
