# Arcates Web Site — Canlı Yayın Kontrolü

Bu dosya `DOCS.md` şartnamesini değiştirmez; canlıya çıkışta uygulanacak kısa
operasyon kontrol listesidir.

## 1. Birleştirme kapısı

- GitHub CI yeşil olmalı.
- CI çıktısında atlanan test bulunmamalı.
- Tüm PHP dosyaları `php -l` kontrolünden geçmeli.
- Güvenlik testleri S-01…S-21 geçmeli.
- Ön yüz değişmişse `tools/browser/animation-check.mjs` gerçek Chromium ile çalıştırılmalı.
- `php tools/preflight.php` engelleyici madde göstermemeli.

## 2. Yetki ve kişisel veri

- `admin`: tüm yönetim işlevlerine erişebilir.
- `editor`: yalnız içerik, medya, SEO görünümü ve istatistik görünümü gibi içerik odaklı yetkilere sahiptir.
- Form kayıtları, CSV dışa aktarma, yönlendirme/404 yönetimi, kullanıcılar, ayarlar, yedekleme ve işlem günlüğü admin'e özeldir.
- CSV dışa aktarmada kullanıcı kontrollü hücreler spreadsheet formula enjeksiyonuna karşı güvenli hale getirilir.

## 3. Sunucu

- PHP 8.1+; 8.2 önerilir.
- `pdo_mysql`, `mbstring`, `gd`, `json`, `fileinfo` açık olmalı.
- Document root doğrudan `public/` olmalı.
- HTTPS zorunlu ve tek canonical host kullanılmalı.
- `storage/` ile `public/uploads/` yazılabilir olmalı.
- `config/config.php` web kökü dışında kalmalı.
- `/install` tamamlandıktan sonra `storage/installed.lock` mevcut olmalı.

## 4. Cron

```cron
0 3 * * * php /home/kullanici/arcates-web-site/tools/backup.php
15 3 * * * php /home/kullanici/arcates-web-site/tools/purge_submissions.php
30 3 * * * php /home/kullanici/arcates-web-site/tools/rollup_visits.php
```

Cron kurulduktan sonra üç komut da en az bir kez elle çalıştırılıp çıkış kodu kontrol edilir.

## 5. SEO ve içerik

- `sitemap.xml` gerçek yayınlanmış URL'leri içermeli.
- `robots.txt` sitemap satırı ve panel engeli içermeli.
- Canonical host HTTPS ve tek host olmalı.
- KVKK ve Gizlilik Politikası mevcut uygulama kararı gereği `index,follow` yayınlanır.
- İlçe sayfaları 500+ kelime ve %70 altı benzerlik şartını geçmeli.
- Örnek projeler gerçek müşteri işi değilse `projects_notice` açıkça görünmeli.
- NAP bilgileri Google İşletme Profili ile elle karşılaştırılmalı.
- Search Console doğrulaması ve sitemap gönderimi elle doğrulanmalı.
- Yapısal veri Rich Results testinden elle geçirilmelidir.

## 6. Ölçüm

Arcates'in dahili ziyaret ve dönüşüm istatistikleri temel ölçüm sistemidir.
`analytics_code` alanının dolu olması tek başına Google Analytics entegrasyonu
anlamına gelmez; mevcut CSP altında harici Analytics betiği otomatik eklenmez.
Google Analytics kullanılacaksa ayrı entegrasyon ve gizlilik değerlendirmesi yapılmalıdır.

## 7. Elle doğrulanacak yayın maddeleri

- İletişim formu gerçek e-posta kutusuna ulaşıyor.
- Spam klasörü kontrol edildi.
- Mobil 360 px ve masaüstü görünüm elle kontrol edildi.
- Klavye ile temel akış tamamlanabiliyor.
- 404 sayfası ve eski slug 301 yönlendirmesi canlı hostta çalışıyor.
- Favicon ve OG görseli gerçek URL'den açılıyor.
- En az bir veritabanı yedeği alındı ve ayrı bir test veritabanına geri yüklendi.
- Cron görevleri gerçek hosting kullanıcısıyla çalışıyor.
- `display_errors` kapalı.
- Panel güçlü ve benzersiz parola kullanıyor.

## 8. Sürümleme

Tüm otomatik kontroller ve yukarıdaki elle kontroller tamamlanmadan `VERSION`
`1.0.0` yapılmaz. Kod/CI kapıları geçip yalnız canlı-host elle kontrolleri kaldığında
`1.0.0-rc1`; tüm maddeler doğrulandığında `1.0.0` kullanılır.
