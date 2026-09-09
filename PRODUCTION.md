# Arcates Web Site — Canlı Yayın Kontrolü

Bu dosya `DOCS.md` şartnamesini değiştirmez; canlıya çıkışta uygulanacak kısa
operasyon kontrol listesidir.

## 1. Birleştirme kapısı

- GitHub CI yeşil olmalı.
- CI çıktısında atlanan test bulunmamalı.
- Tüm PHP dosyaları `php -l` kontrolünden geçmeli.
- Güvenlik testleri S-01…S-21 geçmeli.
- Ön yüz değişmişse gerçek Chromium kabul zinciri çalıştırılmalı.
- `php tools/preflight.php` engelleyici madde göstermemeli.

Repo/CI kapısının yeşil olması canlı hosting doğrulamasının yerine geçmez. Canlı
ortam doğrulaması için GitHub Actions içindeki **Live Acceptance** workflow'u
ayrıca gerçek HTTPS alan adına karşı çalıştırılır.

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

### 3.1 Güncelleme (var olan kurulum)

Yeni sürüm kurulmuş bir siteye yüklendiğinde:

```bash
php tools/migrate.php
```

Bekleyen göçler uygulanmadan yeni sürüm eksik çalışır. `db/schema.sql` yalnızca
sıfırdan kurulum içindir; kurulmuş bir siteye uygulanmaz.

Bu adım şema değişikliği olmasa da gerekir: `Settings::defaults()` yalnızca yeni
kurulumu besler, kurulmuş sitenin `settings` satırlarına dokunmaz. Değişen bir
varsayılanın canlıya ulaşması göç dosyasına bağlıdır (DOCS.md 8.6).

Göçten sonra Ayarlar ekranında adres, telefon ve e-posta gözle kontrol edilir;
göç elle girilmiş değeri korur, bu yüzden özelleştirilmiş bir alan bilerek eski
haliyle kalmış olabilir.

### 3.2 Canlı preflight

Göçten sonra gerçek hosting kullanıcısıyla:

```bash
php tools/preflight.php
```

çalıştırılır. Çıkış kodu `0` olmadan yayın kabulü tamamlanmış sayılmaz. Preflight
HTTPS, secure cookie, kurulum kilidi, yazılabilir klasörler, veritabanı, bekleyen
göçler, NAP, sitemap/robots, içerik ve yedek gibi makine tarafından denetlenebilir
maddeleri kontrol eder; e-posta, cron, Search Console ve Rich Results gibi
maddeleri ayrıca elle işaretler.

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

## 7. Canlı host Chromium kabulü

GitHub'da **Actions → Live Acceptance → Run workflow** açılır ve gerçek yayın
adresi `base_url` alanına tam HTTPS URL olarak girilir. `admin_path` üretimdeki
panel yoludur. Bilinen bir eski slug için 301 kanıtı isteniyorsa `redirect_from`
ve `redirect_to` birlikte girilir.

Workflow `tools/browser/live-check.mjs` dosyasını gerçek Chromium ile çalıştırır
ve production'a yazma yapmaz. Şunları otomatik doğrular:

- 390 px ve 1366 px temsilci rotalarda HTTP 200, tek H1, viewport containment ve yatay taşma,
- yönlendirme sonrası aynı HTTPS production origininde kalma,
- canonical origin ve path doğruluğu,
- `robots.txt` ve `sitemap.xml` production HTTPS origin tutarlılığı,
- favicon ve OG görselinin gerçek URL'den açılması,
- gerçek 404 cevabı ve 404 sayfası containment,
- isteğe bağlı eski slug için doğrudan 301 ve beklenen hedef.

Bu workflow form göndermez, panel oturumu açmaz, veritabanına yazmaz ve gerçek
e-posta teslimi, cron, yedek/geri yükleme, Search Console, Rich Results veya
Lighthouse/PageSpeed sonucunu kanıtlamaz.

## 8. Elle doğrulanacak yayın maddeleri

- İletişim formu gerçek e-posta kutusuna ulaşıyor.
- Spam klasörü kontrol edildi.
- Mobil 360 px ve masaüstü görünüm elle kontrol edildi.
- Klavye ile temel akış tamamlanabiliyor.
- 404 sayfası ve en az bir bilinen eski slug 301 yönlendirmesi canlı hostta çalışıyor.
- Favicon ve OG görseli gerçek URL'den açılıyor.
- En az bir veritabanı yedeği alındı ve ayrı bir test veritabanına geri yüklendi.
- Cron görevleri gerçek hosting kullanıcısıyla çalışıyor.
- `display_errors` kapalı.
- Panel güçlü ve benzersiz parola kullanıyor.
- Search Console doğrulandı ve sitemap gönderildi.
- Yapısal veri Rich Results testinden geçti.
- Üretim Lighthouse/PageSpeed sonucu gözden geçirildi.

## 9. Sürümleme

`VERSION` dosyası sürüm için tek kaynak olarak kabul edilir. Kod/CI kapıları
geçmiş fakat canlı hosting kontrol listesindeki maddeler tamamlanmamışsa sürüm
release-candidate biçiminde (`X.Y.Z-rcN`) kalır. Gerçek hostingte preflight,
**Live Acceptance** ve kalan elle doğrulamalar tamamlandıktan sonra aynı sürüm
ayrı bir release değişikliğinde stabil `X.Y.Z` biçimine yükseltilir.

Mevcut yayın adayı `VERSION` dosyasında `1.1.0-rc1` olarak tutulur; canlı kabul
tamamlanmadan stabil sürüme çevrilmez.
