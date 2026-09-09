# Arcates — R9 production kabul kaydı

## Durum

- **Repo/CI: TAMAMLANDI** — R8 exact `main` merge commitinde test ve gerçek Chromium kapıları yeşil.
- **Canli host: BEKLIYOR** — gerçek hosting preflight, canlı Chromium ve elle operasyon kontrolleri henüz bu kayıt tarafından doğrulanmış sayılmaz.
- `VERSION`: `1.1.0-rc1` — canlı kabul tamamlanmadan stabil sürüme yükseltilmez.

R9 yeni bir tasarım fazı değildir. Amaç, repo/CI kanıtı ile gerçek production kanıtını birbirinden ayırmak ve yayın kabulünü tekrar çalıştırılabilir hale getirmektir.

## 1. Kaynak durum

R9 başlangıç noktası:

- `main`: `00c21bc8b8125607062696ff1d9902a7b0ad0180`
- R8 merge: PR #10
- `main` CI #247: test success + browser success
- test paketi: 238/238, 0 hata, 0 atlanan
- R8 browser kapanışı: `TUM R8 NIHAI KABUL DENETIMLERI GECTI`

Bu kanıtlar yeniden tasarımın repo/CI düzeyinde yayın adayı olduğunu gösterir; gerçek sunucudaki yapılandırma ve harici servisleri doğrulamaz.

## 2. R9 ile eklenen canlı host kapısı

R9 iki parça ekler:

1. `tools/browser/live-check.mjs`
2. GitHub Actions **Live Acceptance** workflow'u

Workflow manuel çalışır ve gerçek `https://` production adresini input olarak alır. Yalnız HTTP GET istekleri yapar; form göndermez, panelde oturum açmaz ve ayar/içerik mutasyonu yapmaz. Bununla birlikte uygulamanın normal ölçüm davranışı bu istekleri kaydedebilir: public ziyaretler istatistik oluşturabilir, bilinmeyen rota `not_found` sayacını, bilinen redirect kontrolü ise redirect isabet sayacını artırabilir. Bu sınırlı operasyonel telemetri R9 smoke testinin beklenen yan etkisidir.

Otomatik canlı kontroller:

- 390 px ve 1366 px temsilci public rotalar,
- HTTP 200, tek H1, yatay taşma ve viewport containment,
- HTTPS ve tek production origin,
- canonical origin + path,
- `robots.txt` sitemap ve panel engeli,
- `sitemap.xml` URL origin tutarlılığı,
- favicon ve OG görselinin gerçek URL'den açılması,
- bilinmeyen rotanın gerçek HTTP 404 vermesi,
- isteğe bağlı bilinen eski slug için 301 ve beklenen hedef.

## 3. Var olan kurulumu production'a güncelleme sırası

Canlı sunucuda işlem sırası:

1. Veritabanı ve dosya yedeği alınır.
2. Onaylanmış `main` sürümü deploy edilir.
3. Var olan kurulumda göçler uygulanır:

```bash
php tools/migrate.php
```

4. Production yapılandırmasıyla preflight çalıştırılır:

```bash
php tools/preflight.php
```

5. Preflight engelleyicileri sıfırlanır; özellikle HTTPS/base URL, secure cookie, `installed.lock`, yazılabilir klasörler, DB bağlantısı, bekleyen göç, NAP, sitemap/robots ve yedek kontrol edilir.
6. Göç sonrası Ayarlar ekranında NAP/adres/telefon/e-posta gözle doğrulanır.
7. GitHub Actions → **Live Acceptance** gerçek HTTPS alan adına karşı çalıştırılır.
8. Bilinen eski bir slug varsa `redirect_from` ve `redirect_to` verilerek gerçek 301 kanıtı alınır.
9. Aşağıdaki elle maddeler tamamlanır.

`db/schema.sql` mevcut production veritabanına uygulanmaz; yalnız sıfırdan kurulum içindir.

## 4. Canlı ortamda hâlâ elle doğrulanacaklar

### İletişim ve operasyon

- **gercek e-posta**: iletişim formu gerçek hedef kutuya ulaşmalı ve spam klasörü kontrol edilmeli.
- **cron**: backup, submission purge ve visit rollup görevleri gerçek hosting kullanıcısıyla çalışmalı.
- **yedek**: en az bir gerçek production yedeği alınmalı; ayrı test veritabanında geri yükleme doğrulanmalı.
- Panel için güçlü ve benzersiz yönetici parolası kullanılmalı.
- `display_errors` production'da kapalı olmalı.
- Document root gerçekten `public/` olmalı; `storage/` ve `public/uploads/` izinleri gerçek sunucuda doğrulanmalı.

### Yerel SEO ve arama

- NAP, Google İşletme Profili ile karşılaştırılmalı.
- **Search Console** mülkiyeti doğrulanmalı ve production sitemap gönderilmeli.
- Yapısal veri **Rich Results** testinden gerçek production URL'leriyle geçirilmeli.

### Performans ve cihaz

- Production **Lighthouse** / PageSpeed mobil ve masaüstü sonuçları alınmalı.
- 360 px telefon görünümü ve masaüstü görünümü gerçek cihaz/tarayıcıda kısa göz kontrolünden geçirilmeli.
- Klavye ile temel public akış gerçek browserda denenmeli.

## 5. Yayın kabul kararı

Stabil sürüme geçiş için birlikte gerekli kapılar:

- repo CI yeşil,
- canlı sunucuda `php tools/migrate.php` tamam,
- canlı sunucuda `php tools/preflight.php` engelleyici yok,
- GitHub **Live Acceptance** yeşil,
- gerçek e-posta, cron ve yedek/geri yükleme doğrulanmış,
- Search Console ve Rich Results tamamlanmış,
- production Lighthouse/PageSpeed gözden geçirilmiş,
- kalan `PRODUCTION.md` elle maddeleri tamamlanmış.

Bunlardan biri eksikse `VERSION` release-candidate olarak kalır. Tümü doğrulandıktan sonra `1.1.0-rc1` ayrı bir release commit/PR'ında stabil `1.1.0` sürümüne yükseltilebilir.

## 6. Kapsam sınırı

R9'un repo tarafında bir canlı-host test aracı bulunması, aracın production'a karşı çalıştırıldığı anlamına gelmez. `base_url` gerçek yayın adresiyle **Live Acceptance** başarıyla çalıştırılmadan ve elle maddeler kapanmadan “production doğrulandı” denmez.
