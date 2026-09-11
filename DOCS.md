# Arcates Web Site — Proje Dokümantasyonu

**Depo:** `arcates-web-site`  
**Sürüm:** 1.0.0-rc1  
**Ürün:** Arcates Yazılım kurumsal sitesi + yönetim paneli  
**Hedef:** Edremit ve Balıkesir Körfez bölgesinde yerel aramalardan müşteri adayı toplamak

Bu dosya deponun kökünde `DOCS.md` olarak durur ve uygulamanın kanonik teknik dokümantasyonudur. Ayrıntılı yeniden tasarımın uygulama envanteri `REDESIGN-R0-INVENTORY.md`, R5 ana sayfa davranışı ise bu belgenin 5–7. bölümleriyle birlikte okunur.

> Bu depo `arcates-core` CMS ürününden ayrıdır. Burada üretilen ve genelleştirilebilir olan her modül, kararlı hale geldikten sonra core'a taşınır. Ters yönde kopyalama yapılmaz.

---

## İÇİNDEKİLER

1. Kapsam ve ilkeler
2. Teknoloji ve ortam
3. Klasör yapısı
4. Sayfa envanteri ve URL haritası
5. Anasayfa şartnamesi
6. Tasarım sistemi
7. Hareket ve etkileşim şartnamesi
8. Veritabanı şeması
9. Yönetim paneli
10. Güvenlik şartnamesi
11. SEO ve yapısal veri
12. Form ve dönüşüm takibi
13. Kurulum ve dağıtım
14. Test planı
15. Git akışı ve CI
16. Yapay zeka çalışma kuralları
17. Faz planı ve teslim listesi

---

## 1. KAPSAM VE İLKELER

### Kapsam içinde
Çok dilli (TR birincil, EN/DE/AR ikincil) kurumsal site, yönetilebilir anasayfa bölümleri, hizmet sayfaları, ilçe sayfaları, sektör sayfaları, örnek site/referans alanı, blog, SSS, teklif formu ve müşteri adayı takibi, gelişmiş SEO araçları, yönlendirme ve 404 yönetimi, ziyaretçi istatistiği, medya yönetimi ve yönetim paneli.

### Kapsam dışında (sürüm 1)
E-ticaret işlemi, ödeme, rezervasyon işlemi, üyelik ve yorum sistemi. Bunlar hizmet olarak anlatılabilir ancak Arcates kurumsal sitesinin kendi işlem modülü değildir.

### İlkeler
1. **Üretim bağımlılığı yok.** PHP uygulaması Composer/framework veya frontend build adımı gerektirmez. Playwright yalnız CI tarayıcı denetiminde geçici geliştirme bağımlılığıdır.
2. **İlerlemeli iyileştirme.** JavaScript kapalıyken tüm içerik görünür ve site kullanılabilir olmalı.
3. **Her veri doğrulanır, her çıktı kaçırılır.** İstisnasız.
4. **İş içeriği yönetilebilir.** Şablona müşteri, metrik veya gerçekmiş gibi görünen veri gömülmez. Arayüz etiketleri `lang/*.php` içinde tutulabilir.
5. **Hız, animasyondan önce gelir.** Bir efekt Core Web Vitals hedefini bozuyorsa efekt gider.
6. **Görsel sistem rol tabanlıdır.** Aynı renk ailesi tüm kartları rastgele renklendirmek için değil, arka plan/eylem/durum rollerini ayırmak için kullanılır.

---

## 2. TEKNOLOJİ VE ORTAM

| Bileşen | Gereksinim |
|---------|-----------|
| PHP | 8.1 minimum, 8.2 önerilen |
| Eklentiler | `pdo_mysql`, `mbstring`, `gd`, `json`, `fileinfo` |
| MySQL | 5.7+ / MariaDB 10.4+; CI MySQL 8.0 |
| Karakter seti | `utf8mb4` / `utf8mb4_unicode_ci` |
| Sunucu | Apache + `mod_rewrite`, cPanel paylaşımlı hosting |
| JS | Vanilla ES6; ortak `site.js` + küçük davranış katmanları |
| CSS | Derlemesiz katmanlı CSS: temel + ortak redesign + sayfaya özel katman |
| Zaman dilimi | `Europe/Istanbul` |

Medya varyant üretimi gerçek uygulamada GD kullandığı için **GD zorunludur**; yalnız Imagick bulunması yeterli kabul edilmez.

Harici kaynak yalnızca Google Fonts. Font yükleme `preconnect` + `display=swap` ile yapılır. Üçüncü taraf izleme scripti otomatik enjekte edilmez; dahili ziyaret ölçümü `visits`/`visits_daily` tablolarıyla yürür.

---

## 3. KLASÖR YAPISI

```text
arcates-web-site/
├── public/
│   ├── index.php
│   ├── .htaccess
│   ├── robots.php
│   ├── assets/
│   │   ├── css/
│   │   │   ├── site.css              # temel ön yüz sistemi
│   │   │   ├── redesign.css          # ortak yeniden tasarım katmanı
│   │   │   ├── home-redesign.css     # yalnız R5 anasayfa
│   │   │   ├── admin.css
│   │   │   └── admin-redesign.css
│   │   ├── js/
│   │   │   ├── site.js               # reveal/header/coast/TOC/form
│   │   │   ├── redesign.js           # ortak nav/form iyileştirmeleri
│   │   │   ├── admin.js
│   │   │   └── admin-redesign.js
│   │   └── img/
│   └── uploads/                       # git'te yok
│       └── .htaccess
├── app/
│   ├── Core/
│   ├── Models/
│   ├── Controllers/Front/
│   ├── Controllers/Admin/
│   └── helpers.php
├── views/
│   ├── front/
│   │   ├── layout.php
│   │   ├── home.php
│   │   ├── page.php
│   │   ├── service.php
│   │   ├── location.php
│   │   ├── sector.php
│   │   ├── project.php
│   │   ├── post.php
│   │   └── partials/
│   └── admin/
├── config/
│   ├── config.example.php
│   ├── config.php                    # git'te yok
│   └── routes.php
├── db/
│   ├── schema.sql
│   ├── migrations/
│   └── seed/
├── lang/  (tr.php, en.php, de.php, ar.php)
├── storage/  (logs, cache, backups)   # git'te yok
├── tests/
│   ├── run.php
│   ├── unit/
│   ├── security/
│   └── functional/
├── tools/
│   ├── backup.php
│   ├── purge_submissions.php
│   ├── rollup_visits.php
│   ├── preflight.php
│   └── browser/
├── .github/workflows/ci.yml
├── CLAUDE.md
├── DOCS.md
├── REDESIGN-R0-INVENTORY.md
├── PRODUCTION.md
├── CHANGELOG.md
├── VERSION
├── README.md
└── .gitignore
```

`app/`, `config/`, `storage/`, `views/` web kökü dışındadır. `public/uploads/` içinde PHP çalıştırılması ayrıca `.htaccess` ile kapatılır.

### .gitignore
```text
/config/config.php
/public/uploads/*
!/public/uploads/.htaccess
/storage/logs/*
/storage/cache/*
/storage/backups/*
!/storage/**/.gitkeep
*.log
*.sql
*.sql.gz
.DS_Store
.idea/
.vscode/
```

---

## 4. SAYFA ENVANTERİ VE URL HARİTASI

### 4.1 Ana sayfalar
| URL | Şablon | Hedef arama |
|-----|--------|-------------|
| `/` | `home` | edremit web tasarım, balıkesir web tasarım |
| `/hakkimizda` | `page` | arcates yazılım |
| `/iletisim` | `page` | — |
| `/fiyatlar` | `page` | web sitesi fiyatları |
| `/referanslar` | `page` | — |
| `/referanslar/{slug}` | `project` | — |
| `/blog`, `/blog/{slug}` | `post` | uzun kuyruk |
| `/sss` | `page` | — |
| `/kvkk`, `/gizlilik-politikasi` | `page` | yasal bilgilendirme; mevcut yayın kararı `index,follow` |

### 4.2 Hizmet sayfaları — `service`
`/web-tasarim`, `/e-ticaret-sitesi`, `/rezervasyon-sistemi`, `/seo-hizmeti`, `/coklu-dil-web-sitesi`, `/web-sitesi-bakim`

### 4.3 İlçe sayfaları — `location`
`/edremit-web-tasarim`, `/akcay-web-tasarim`, `/altinoluk-web-tasarim`, `/burhaniye-web-tasarim`, `/havran-web-tasarim`, `/ayvalik-web-tasarim`, `/gomec-web-tasarim`, `/balikesir-web-tasarim`

### 4.4 Sektör sayfaları — `sector`
`/otel-pansiyon-web-sitesi`, `/zeytinyagi-e-ticaret-sitesi`, `/restoran-kafe-qr-menu`, `/emlak-web-sitesi`, `/nakliyat-web-sitesi`, `/tabela-matbaa-web-sitesi`

### 4.5 Sistem
`/sitemap.xml`, `/robots.txt`, `/panel/*`

### 4.6 Dil önekleri
Varsayılan dil (TR) öneksizdir. Diğer diller `/en/...`, `/de/...`, `/ar/...` önekiyle çalışır. Yalnız gerçekten etkin ve çevirisi bulunan dil bağlantıları üst menü/hreflang setine girer. Arapça `dir="rtl"` kullanır.

### 4.7 Kritik kural — ilçe sayfaları
Aynı metnin ilçe adı değiştirilerek çoğaltılması doorway page riski taşır. Her ilçe sayfası en az **500 kelime özgün metin**, o ilçeye ait en az bir gerçek/örnek çalışma ve o ilçeye özel SSS içermelidir. Panel benzerlik ve içerik eksiklerini yayın öncesinde uyarır.

---

## 5. ANASAYFA ŞARTNAMESİ — R5

Anasayfa sabit sıralı bölümlerden oluşur. Her bölüm panelden açılıp kapatılabilir; içerik bölümleri yönetilebilir. Ziyaretçi DOM sırası ile paneldeki sabit bölüm sırası aynıdır.

| # | Bölüm | Anahtar | Davranış / kaynak |
|---|-------|---------|-------------------|
| 1 | Üst menü | `header` | Menü ağacı + teklif CTA |
| 2 | Kahraman | `hero` | 3 satır H1, açıklama, 2 CTA, web+mobil yazılım sahnesi |
| 3 | Sektör grubu | `strip` | Yayındaki gerçek `sector` sayfalarına statik bağlantılar; DB etiketi yalnız fallback |
| 4 | Hizmet kartları | `services` | 6 kart; tek mavi görsel aile, ikon/başlık/metin/URL |
| 5 | Süreç | `steps` | 1–2–3 bağlı tek yüzey |
| 6 | Örnek siteler | `works` | 1 büyük öne çıkan + destek kartları, mevcut proje verisi |
| 7 | Hizmet bölgeleri | `coast` | Temsili bağlantı grafiği + gerçek ilçe URL'leri + HTML link listesi |
| 8 | SSS | `faq` | Masaüstünde iki kolonlu `details/summary` |
| 9 | Çağrı bandı | `cta` | Koyu CTA; teklif + NAP telefondan üretilen WhatsApp |
| 10 | Alt bilgi | `footer` | NAP, çalışma saatleri, bağlantılar |

**Üst menü (bölüm 1).** Menü masaüstünde ekranın ortasında durur; marka solda, dil seçici ve çağrı butonu sağdadır. 940 px altında açılır kutuya döner ve ortalama uygulanmaz. Açık sayfanın öğesi işaretlenir: masaüstünde alt çizgi, açılır menüde sol şerit, her ikisinde `aria-current="page"`. Aktif öğe sayfanın canonical adresine göre belirlenir; bir öğenin altındaki adres açıkken üst öğe de açık kalır. Menünün ilk öğesi "Ana Sayfa"dır ve anasayfada işaret onun üzerinde durur. Testler F-HD-a…h, F-P18-a…c.

*Ölçüler.* `public/assets/css/reference-header.css` menünün son katmanıdır ve `head.php` içinde sayfaya özel stillerden **sonra** yüklenir; önce yüklenirse `.is-reference-home` önekli kurallar (ağırlık 0,2,0) menüyü geri ezer. Dosyadaki her değer referans görselden ölçüldü: görseldeki macOS trafik ışığı noktası 7 px (gerçekte 12 css px), yani görsel 0.583 oranında küçültülmüş ve tasarımın kendi genişliği ≈1863 px. Değerler `vw` cinsinden yazılır, böylece 1863 px'te referansla birebir olur, dar ekranda orantılı küçülür. Ölçülen renkler: pasif öğe `#CFDFF2`, açık sayfa `#5AC8F5`, alt çizgi `#3988BC`, çağrı butonu `#0E78FE`, bant zemini `#000A1A`.

*Koyu yüzey kuralı.* Menü koyu bir bant; `--fg` (açık tema metni, `#062244`) burada kullanılamaz, kontrast 1.1:1'e düşer. Aynı hata açma düğmesinin çubuklarındaydı: `site.css` çubukları `var(--fg)` ile boyuyordu ve düğme 1.2:1 kontrastla pratikte görünmüyordu. Menü katmanındaki her renk bant zeminine karşı en az 4.5:1 olmalı; F-HD-b ve F-HD-g bunu hesaplayarak doğrular.

### 5.1 Kahraman bölümü
- Arka plan ana koyu rol `#081426`, ikincil koyu yüzey `#10233D` ailesidir.
- `H1` panelden gelen üç satırı kullanır; vurgulu üçüncü satır metin olarak DOM'da kalır, görsele dönüştürülmez.
- Sağ sahne **temsili yazılım arayüzü**dür: masaüstü web ekranı, mobil ekran ve Arcates işareti/bağlantı yayı. Sahte müşteri, sahte puan, sahte trafik veya performans metriği gösterilmez.
- Sahne bilgi taşımadığı için `aria-hidden="true"`; içindeki dekoratif logo `<img alt="" aria-hidden="true">` kullanır.
- H1 görünürlüğü JS'ye bağlı değildir. `html.js` sadece kısa giriş animasyonunu iyileştirir.

**Sahne katmanları (`hero.scene`).** Sahnenin üzerinde panelden beslenen üç dekoratif katman bulunur: bir kart (başlık + metin), en çok iki rozet (`value` + `label`) ve en çok beş maddelik yetkinlik rayı (`label` + ikon). Üçü de `ref-hero__visual` içindedir, yani `aria-hidden` kapsamındadır; boş bırakılan katman hiç basılmaz.

Katmanların metni **işletmenin arkasında durabileceği** ifadelerle sınırlıdır. Doğrulanamayan sayaç, memnuniyet oranı, tamamlanan proje adedi ya da sunulmayan bir hizmet yazılmaz — bu, yukarıdaki "sahte müşteri, sahte puan, sahte metrik gösterilmez" kuralının sahneye uzantısıdır ve test **F-HS-b** ile korunur. Ray maddeleri yayındaki hizmet listesinden gelir (F-HS-a).

### 5.2 Sektör ve hizmet bölgeleri
- `strip` adı veri tabanında geriye uyumluluk için korunur; ön yüzde artık kayan şerit değildir.
- `HomeController`, `Page::listing('sector')` sonucundan yalnız `status=published`, başlık ve slug'ı bulunan sektörleri alır. Bunlar gerçek URL taşıyan statik kartlardır.
- Yayında sektör sayfası yoksa paneldeki `tags` listesi statik etiket olarak basılır; sahte URL üretilmez.
- Bölge SVG'si **coğrafi sınır haritası değildir**. Görünür açıklama ve figcaption bunu açıkça belirtir.
- SVG `viewBox="0 0 1000 190"`, `role="img"`, açıklayıcı `aria-label` taşır. İlçe noktaları ilgili yayınlanmış ilçe sayfasına bağlanır. Aynı bağlantılar HTML listesinde de bulunur.

### 5.3 Hizmetler, süreç, örnek siteler ve SSS
- Hizmet kartları masaüstünde 3×2; renk varyantı içerikten gelse bile R5 sunumu tek mavi aileyle tutarlıdır.
- Süreç üç ayrı kart hissi yerine aynı büyük yüzey üzerinde 1–2–3 akışı oluşturur.
- Örnek sitelerde ilk kayıt öne çıkar; sonraki kayıtlar destek kartıdır. Gerçek görsel yoksa sahte ekran görüntüsü üretilmez, nötr boş durum kullanılır.
- `projects_notice` gerçek müşteri işi olmayan örneklerin durumunu açıklamaya devam eder.
- SSS native `details/summary` kullanır; JS olmadan da açılır/kapanır.

### 5.4 CTA ve WhatsApp
- Birincil CTA panel içeriğinden gelir.
- WhatsApp hedefi yalnız `Settings::get('nap_phone')` değerinden üretilir; şablonda telefon sabitlenmez.
- TR numarası `0XXXXXXXXXX` veya `5XXXXXXXXX` biçimindeyse `wa.me` için ülke koduna normalize edilir.
- Telefon boşsa paneldeki ikinci CTA fallback olarak kullanılabilir.

### 5.5 Responsive davranış
- 940px altında hero ve büyük iki kolonlu alanlar tek kolona geçer.
- 720px altında hero sahnesi yaklaşık 300px yüksekliğe sıkışır; sektörler 2 kolon, hizmetler ve örnek-site vitrini tek kolon olur.
- 480px altında sektör grubu da tek kolona düşebilir.
- Bölge grafiği kendi kutusunda yatay kayabilir; belge seviyesinde yatay taşma oluşamaz.
- RTL'de hero sahnesi ve sıra bağları mantıksal olarak terslenir; içerik okunabilirliği korunur.

---

## 6. TASARIM SİSTEMİ

Tasarım üç katmandır:

1. `site.css`: temel tokenlar, layout, tipografi ve geriye uyumlu bileşenler.
2. `redesign.css`: tüm ziyaretçi sitesi için ortak R1/R4 yüzey, kontrol, header/footer/form rolleri.
3. `home-redesign.css`: yalnız `is-redesign-home` altında R5 anasayfa kompozisyonu.

Panel kendi `admin.css` + `admin-redesign.css` katmanını kullanır. CSS'te build adımı yoktur.

### 6.1 Ana roller
| Rol | Değer / aile | Kullanım |
|-----|---------------|----------|
| Ana koyu | `#081426` | hero, güçlü CTA, navigasyon bağlamı |
| Koyu yüzey | `#10233D` | koyu yüzey içi kart/derinlik |
| Marka mavi | `#0B4FA8` | birincil eylem |
| Parlak mavi | `#1C7BF2` | vurgu/focus/bağlantı |
| Açık mavi | `#7FB6FF` | düşük yoğunluklu vurgu |
| Açık zemin | `#EDF4FF`, `#F7FAFF`, beyaz | içerik yüzeyleri |
| Durum renkleri | yeşil/sarı/kırmızı | yalnız başarı/uyarı/hata |

Eski çok renkli kart paleti yeni ana sayfada dekor amacıyla kullanılmaz.

### 6.2 Kontrol ve yüzey geometrisi
- Buton/input benzeri kontroller: yaklaşık **14px** radius ailesi.
- Büyük kart/panel/CTA yüzeyleri: yaklaşık **22px** radius ailesi.
- Minimum mobil dokunma alanı 44px; ana kontroller 48px hedeflenir.
- Birincil eylem dolu mavi, ikincil eylem çerçeveli/ghost; her bölümde bir baskın CTA olur.

### 6.3 Tipografi
Başlık `Sora` (600/700/800), gövde `Plus Jakarta Sans` (400/500/600). Üçüncü font eklenmez. H1/H2 ölçekleri responsive `clamp()` ile küçülür; mobilde satır kırılması güvenlidir.

### 6.4 Kontrast ve odak
Metin/zemin normal metinde WCAG AA 4.5:1 hedefler. `:focus-visible` halkası tüm bağlantı, buton ve form kontrollerinde görünür olmalıdır. Renk tek başına durum anlatmaz.

---

## 7. HAREKET VE ETKİLEŞİM ŞARTNAMESİ

### 7.1 Değişmez kurallar
1. Animasyon hedefi yalnız `transform`, `opacity`; bölge çizgisi için ayrıca `stroke-dashoffset` olabilir. Layout özelliği (`width`, `height`, `top`, `left`, `margin`, `padding`) animasyonu yasaktır.
2. Gizli başlangıç durumu yalnız `html.js` altında tanımlanır. JS çalışmazsa hiçbir içerik gizli kalmaz.
3. `prefers-reduced-motion: reduce` tüm giriş/reveal hareketini kapatır ve son durumu gösterir.
4. Scroll dinleyicileri `{passive:true}` + `requestAnimationFrame` ile sınırlandırılır.
5. Reveal `IntersectionObserver` kullanır; destek yoksa `revealAll()` çalışır.
6. Görünmüş öğe `unobserve` edilir; geri kaydırmada yeniden oynatılmaz.
7. **R5 anasayfada sonsuz animasyon yoktur.** Eski soyut şekil süzülmesi, hero parallax ve kayan sektör şeridi kaldırılmıştır.
8. Hareket içerik hiyerarşisini destekler; dekoratif hareket kullanıcıyı bekletmez.

### 7.2 R5 hareket süreleri
| Öge | Süre / gecikme |
|-----|----------------|
| H1 satırı | 600ms, satır gecikmeleri 0 / 70 / 140ms |
| Hero açıklaması | 440ms, yaklaşık 160ms gecikme |
| Hero eylemleri | 440ms, yaklaşık 210ms gecikme |
| Web/mobil/mark sahnesi | 700ms, yaklaşık 100 / 180 / 240ms gecikme |
| Genel reveal | yaklaşık 460ms |
| Görünürlük | `threshold: 0.15`, `rootMargin: 0px 0px -8% 0px` |
| Header küçülme eşiği | 24px |

Hero içerik ve sahnesi **1.05 saniye içinde** yerleşmiş olmalıdır. Eski 1.4 saniyelik `HERO_SETTLE`, dekoratif şekil kuyruğu ve `PARALLAX_LIMIT` yoktur.

### 7.3 Scroll'a bağlı hareketler
- **İlerleme çubuğu:** sayfa ilerlemesine göre `scaleX`.
- **Sabit üst menü:** 24px sonrası küçük/stuck duruma geçer.
- **Reveal:** içerik viewport'a girdikçe tek seferlik görünür olur.
- **Hizmet bölgeleri:** figür konumuna göre 0–1 ilerleme; `stroke-dashoffset` güncellenir ve ilçe noktaları çizgi geçtikçe `is-lit` olur.
- **TOC:** iç sayfa başlıkları görünürlüğe göre `aria-current="location"` alır.
- **Sticky CTA:** son CTA görünürken tekrarlayan yan kart baskılanır.

### 7.4 Mobil ve görünürlük
Mobilde içerik sırf animasyon için aşağı itilmez. Hero sahnesi CSS ile küçülür. Sektör grubu statiktir; sekmenin arka plana alınmasıyla yönetilecek animasyon durumu yoktur.

### 7.5 Performans sınırı
Scroll sırasında pahalı layout animasyonu yoktur. Ana sayfa görsel katmanı R3 G-01 özgün raster sahnesini kullanır; 640/1280 genişlikli WebP varyantları sırasıyla 14.606/34.574 bayttır. PNG ana kaynak tarayıcıya yüklenmez. Sahne metin, müşteri veya performans verisi içermez. R8 kabul turunda LCP/CLS/INP ve ilk yük bütçesi ayrıca ölçülür.

---

## 8. VERİTABANI ŞEMASI

Tüm tablolar `InnoDB`, `utf8mb4_unicode_ci`. Çeviri deseni: ana tablo dilden bağımsız alanları, `_translations` tablosu dile bağlı alanları tutar. Tam ve güncel kurulum kaynağı `db/schema.sql` dosyasıdır; aşağıdaki şema dokümanı temel sözleşmeyi özetler.

### 8.1 Sistem
```sql
CREATE TABLE settings (
  `key` VARCHAR(100) PRIMARY KEY,
  `value` LONGTEXT NULL,
  autoload TINYINT(1) NOT NULL DEFAULT 1,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','editor') NOT NULL DEFAULT 'editor',
  status TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE login_attempts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip VARBINARY(16) NOT NULL,
  email VARCHAR(190) NULL,
  success TINYINT(1) NOT NULL DEFAULT 0,
  attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ip_time (ip, attempted_at)
) ENGINE=InnoDB;

CREATE TABLE activity_log (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(60) NOT NULL,
  entity VARCHAR(60) NULL,
  entity_id INT UNSIGNED NULL,
  detail TEXT NULL,
  ip VARBINARY(16) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_time (user_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE languages (
  code CHAR(2) PRIMARY KEY,
  name VARCHAR(60) NOT NULL,
  direction ENUM('ltr','rtl') NOT NULL DEFAULT 'ltr',
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort SMALLINT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE migrations (
  filename VARCHAR(190) PRIMARY KEY,
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

### 8.2 İçerik
```sql
CREATE TABLE pages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parent_id INT UNSIGNED NULL,
  type ENUM('page','service','location','sector') NOT NULL DEFAULT 'page',
  template VARCHAR(60) NOT NULL DEFAULT 'page',
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  cover_id INT UNSIGNED NULL,
  district VARCHAR(60) NULL,
  sort SMALLINT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_type_status (type, status)
) ENGINE=InnoDB;

CREATE TABLE page_translations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  page_id INT UNSIGNED NOT NULL,
  lang CHAR(2) NOT NULL,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL,
  excerpt VARCHAR(400) NULL,
  content LONGTEXT NULL,
  meta_title VARCHAR(180) NULL,
  meta_description VARCHAR(320) NULL,
  og_image_id INT UNSIGNED NULL,
  canonical VARCHAR(255) NULL,
  robots VARCHAR(40) NOT NULL DEFAULT 'index,follow',
  schema_type VARCHAR(40) NULL,
  word_count SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  UNIQUE KEY uq_lang_slug (lang, slug),
  INDEX idx_page_lang (page_id, lang)
) ENGINE=InnoDB;
```

`projects`, `posts`, `faqs` aynı ana kayıt + çeviri desenini izler. İlişkilerin tam foreign-key tanımı için `db/schema.sql` kaynak kabul edilir.

### 8.3 Anasayfa bölümleri
```sql
CREATE TABLE home_sections (
  `key` VARCHAR(40) PRIMARY KEY,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort SMALLINT NOT NULL DEFAULT 0,
  config JSON NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE home_section_translations (
  section_key VARCHAR(40) NOT NULL,
  lang CHAR(2) NOT NULL,
  content JSON NOT NULL,
  PRIMARY KEY (section_key, lang)
) ENGINE=InnoDB;

CREATE TABLE districts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL,
  map_x SMALLINT NOT NULL,
  map_y SMALLINT NOT NULL,
  label_above TINYINT(1) NOT NULL DEFAULT 0,
  page_id INT UNSIGNED NULL,
  sort SMALLINT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;
```

R5 sabit anahtar sırası: `header, hero, strip, services, steps, works, coast, faq, cta, footer`. Eski kurulumdaki `sort` değerleri farklı olsa bile `HomeSection::all()` bu kanonik sırayı üretir. Yeni seed de aynı sırayı yazar. `strip` için hız konfigürasyonu artık üretilmez; sektör grubu statiktir.

### 8.4 Medya, form, SEO, istatistik
Ana tablolar: `media`, `media_translations`, `submissions`, `redirects`, `not_found`, `visits`, `visits_daily`. `visits` ham kayıtları saklama süresi sonunda günlük tabloya toplanır.

### 8.5 Menü
`menu_items` + `menu_item_translations` kullanılır; `main` ve `footer` ağaçları dil bazlı etiket taşır.

### 8.6 Göç kuralı
`schema.sql` sıfırdan kurulum içindir. Sonraki şema veya kurulu-site varsayılan değişiklikleri `db/migrations/YYYY_MM_DD_NNNN_aciklama.sql` ile yapılır.

Varsayılan değer göçü yalnız eski varsayılanın aynen durduğu veya alanın boş/eksik olduğu satıra dokunur; elle girilen işletme verisi koşulsuz `UPDATE` ile ezilemez. Göçler idempotent olmalıdır.

---

## 9. YÖNETİM PANELİ

Panel yolu `config.php` ile değiştirilebilir, varsayılan `/panel`.

### 9.1 Pano
Son 30 gün ziyaretçi grafiği, yeni form sayısı, dönüşüm hunisi, en çok ziyaret edilen sayfalar, son 404'ler, taslak içerik ve sistem durumu.

### 9.2 Anasayfa yöneticisi
Bölüm listesi R5 sabit sırasını kullanır; her bölüm açılıp kapatılabilir. Kahraman metni/CTA'ları, sektör fallback etiketleri, hizmet içerikleri, süreç, örnek-site bölümü, bölge başlığı/açıklaması, SSS başlığı/açıklaması, CTA ve footer içerikleri yönetilebilir. Bölge noktaları `map_x`/`map_y` ile konumlandırılır.

> `strip` anahtarı geriye uyumluluk için korunur. R5 ön yüzde kayma hızı kullanılmaz. R7 panel yeniden tasarımında eski hız kontrolleri arayüzden tamamen kaldırılacaktır; kayıtlı eski `speed` değerleri ön yüzde etkisizdir.

**Kayıpsız kayıt.** Bölüm kaydı, içeriği beyaz listeden yeniden kurar. Formda alanı bulunmayan bir içerik alanı (örneğin `hero.scene`) bu sırada silinmez: `readContent()` saklanan içeriği alır ve gönderilmeyen alanı olduğu gibi bırakır. Gönderilen değer her zaman temizlenip sınırlanır. Test F-HS-f.

### 9.3 Sayfalar
Tür filtresi (sayfa / hizmet / ilçe / sektör), dil sekmeleri, tam SEO paneli, içerik skoru ve sonuç önizlemesi. **Slug değişince otomatik 301** oluşturulur.

### 9.4 Örnek siteler, blog, SSS
Örnek site: müşteri/örnek adı, sektör, ilçe, canlı site alanı, kapak/galeri ve içerik. Gerçek müşteri işleri girilene kadar `projects_notice` bunun örnek çalışma olduğunu açıklar. Blog ileri tarihli yayın destekler. SSS kayıtları sayfalara atanabilir.

### 9.5 Medya
Çoklu yükleme, alt metin, varyant bilgisi, kullanım yeri ve güvenli silme. Görsel işleme GD ile yapılır.

### 9.6 İçerik skoru
Kaydı engellemez; kelime sayısı, H1, meta, alt, iç link, slug, sayfaya bağlı SSS/referans ve ilçe benzerliği gibi eksikleri listeler. İlçe için 500 kelime, diğer içerik için 300 kelime eşiği kullanılır; ilçe benzerliği %70 ve üzeri güçlü uyarıdır.

### 9.7 SEO
`robots.txt`, sitemap, meta şablonu, Search Console doğrulama alanı ve sayfa meta durumları. `analytics_code` alanının dolu olması kendi başına üçüncü taraf analytics'in enjekte edildiği anlamına gelmez; mevcut üretim kararı dahili `visits` ölçümüdür.

### 9.8 Yönlendirmeler ve 404
Yönlendirme listesi, elle ekleme, döngü kontrolü, 404 listesi ve 404'ü yönlendirmeye dönüştürme. Bu alanlar admin yetkisindedir.

### 9.9 Formlar
Kayıt listesi, durum, not, kaynak/referrer/UTM, CSV dışa aktarma ve saklama süresi temizliği. Kişisel form kayıtları admin-only'dir.

### 9.10 İstatistik
Günlük ziyaret/görüntüleme, sayfalar, kaynaklar, cihaz/dil dağılımı ve aylık CSV. Bot kayıtları grafiğe girmez.

### 9.11 Kullanıcılar, ayarlar, yedekleme
Admin tam yetkili; editor içerik alanlarıyla sınırlıdır. Ayarlar NAP, sosyal, saatler, dil, bakım ve e-posta içerir. Yedekleme admin-only'dir; son 10 yedek ve geri yükleme desteklenir.

---

## 10. GÜVENLİK ŞARTNAMESİ

### 10.1 Veritabanı
Tüm sorgular hazırlanmış ifade. PDO `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES=false`. Değişken tablo/sütun adı yalnız beyaz listeden gelebilir.

### 10.2 Çıktı
Ekrana basılan kullanıcı/veri içeriği `Security::e()` ile kaçırılır. Zengin metin izin listesiyle temizlenir. JSON güvenli HEX bayraklarıyla üretilir.

### 10.3 Oturum
Çerez `HttpOnly`, `SameSite=Lax`; canlı HTTPS'te `secure=true`. Girişte session ID yenilenir, işlemsizlik süresi uygulanır, çıkışta oturum temizlenir.

### 10.4 Şifre ve giriş
`password_hash` / `password_verify`, minimum 10 karakter. IP ve e-posta bazlı başarısız giriş kilidi; kullanıcı var/yok ayrımı sızdırılmaz.

### 10.5 CSRF
Her POST formunda `_token`; doğrulama `hash_equals` tabanlıdır. Başarısız istek 419 döner.

### 10.6 Dosya yükleme
Uzantı + MIME beyaz listesi, 5MB sınırı, rastgele dosya adı, SVG script/event temizliği, yükleme klasöründe PHP çalıştırma yasağı.

### 10.7 Yapılandırma ve başlıklar
Canlıda hata gösterimi kapalı, log açık. `config.php` ve kaynak klasörler web kökü dışında. `/install` kurulum kilidiyle kapanır. Güvenlik başlıkları CSP dahil uygulanır.

### 10.8 Form ve spam
Honeypot, en az 3 saniye form süresi, IP başına saatlik sınır ve sunucu tarafı validasyon. CSRF/rate-limit/honeypot tasarımla kaldırılmaz.

### 10.9 KVKK
Aydınlatma ve gizlilik sayfaları mevcut içerik/seed kararında `index,follow` olabilir; form onayı önceden işaretli değildir. `submissions` saklama süresi ve `purge_submissions.php` ile otomatik temizlik vardır.

### 10.10 Sunucu kuralları
HTTPS ve canonical host tek yöne 301; directory listing kapalı; hassas uzantılar ve upload içi PHP engelli.

---

## 11. SEO VE YAPISAL VERİ

### 11.1 Her sayfada
Tek H1, anlamlı H2/H3 sırası, title/description, canonical, gerektiğinde breadcrumb, doğal iç linkler, görünür CTA. Arama motoru için içerik gizlenmez.

### 11.2 Şema tipleri
| Sayfa | Şema |
|-------|------|
| Anasayfa, iletişim | `ProfessionalService` |
| Hizmet | `Service` |
| İlçe | `Service` + `areaServed` |
| SSS olan sayfa | `FAQPage` |
| Örnek site/referans detay | `CreativeWork` |
| Blog yazısı | `Article` |
| İç sayfalar | `BreadcrumbList` |

Uydurma yorum/puan veya `AggregateRating` eklenmez. NAP şeması gerçek işletme verisini kullanır.

**Çalışma saatleri — iki biçim.** `opening_hours` ayarı schema.org gün kodunu saklar (`Mo-Fr`, `Sa`, `Mo,We,Fr`); `openingHoursSpecification.dayOfWeek` bu kodu **ham** kullanır, çevrilmez. Ziyaretçiye gösterilen metin ise `opening_days()` yardımcısından geçer ve sayfanın dilindeki gün adına döner (`Mo-Fr` → `Pzt–Cum`). Tanınmayan bir belirteç olduğu gibi geçer, böylece panele yazılan serbest metin (`Hafta içi`) bozulmaz. Şablonda gün kodu doğrudan basılmaz; test **F-OD-e** bunu denetler.

### 11.3 Çok dil
Yalnız etkin ve gerçek karşılığı olan diller `hreflang` setine girer; `x-default` varsayılan dile gider. Arapça `dir="rtl"` ve mantıksal yön davranışı kullanır.

### 11.4 Teknik
`sitemap.xml` dinamik, taslak/noindex içerik dışarıda. Görsellerde width/height ve uygun lazy stratejisi. Hero/LCP içeriği lazy ile geciktirilmez. Türkçe slug dönüşümü güvenli ASCII üretir.

---

## 12. FORM VE DÖNÜŞÜM TAKİBİ

Teklif formu: ad, telefon, e-posta, hizmet, mesaj, KVKK, honeypot, açılış zamanı ve CSRF.

Sunucu hataları alanlarla `aria-invalid` + `aria-describedby` üzerinden bağlıdır; hata özeti odaklanabilir. Başarılı gönderimde çift gönderim kilidi ve yerelleştirilmiş gönderiliyor etiketi vardır.

Kayıt sırasında `source_url`, referrer, UTM, dil, IP ve user-agent saklanır. Teşekkür sayfası `/tesekkurler` ve `noindex`tir.

---

## 13. KURULUM VE DAĞITIM

### İlk kurulum
1. Repo alınır.
2. `config/config.example.php` → `config/config.php`; DB ve ortam ayarları girilir.
3. `/install`: gereksinimler, şema, ilk admin, varsayılan içerik.
4. `storage/installed.lock` oluşturulur.
5. `storage/` ve `public/uploads/` izinleri doğrulanır.
6. Üretimde `php tools/preflight.php` çalıştırılır.

### cPanel / cron
Document root `public/` olmalıdır. PHP 8.1+ ve GD etkin olmalıdır.

```cron
0 3 * * * php /home/kullanici/arcates-web-site/tools/backup.php
15 3 * * * php /home/kullanici/arcates-web-site/tools/purge_submissions.php
30 3 * * * php /home/kullanici/arcates-web-site/tools/rollup_visits.php
```

Canlı yayın için ayrıca `PRODUCTION.md` kontrol listesi geçerlidir.

---

## 14. TEST PLANI

### 14.1 Çalıştırıcı ve CI
Yerel: `php tests/run.php`. CI MySQL 8.0 servisinde tüm PHP dosyalarını `php -l` ile tarar. CI ortamında **ATLANDI** kabul edilmez. PHP/MySQL job başarıya ulaşmadan browser job başlamaz.

Browser job CI veritabanını kurar, seed eder, PHP built-in server açar ve Playwright/Chromium ile `animation-check.mjs` + `design-check.mjs` çalıştırır.

### 14.2 Birim testleri (U)
Slug/kaçış/validator/router/database/SEO/hreflang/sitemap/bot gibi çekirdek işlevler `tests/unit` ve ilgili functional testlerde doğrulanır.

### 14.3 Güvenlik testleri (S)
CSRF, XSS/output escaping, upload MIME/uzantı, SVG temizliği, install lock, login rate limit, rol erişimi, editor privacy, redirect admin-only ve CSV formula injection test edilir.

### 14.4 İşlevsel testler (F)
Sayfa yayın/taslak/slug-301, dil/RTL, medya, menü, anasayfa bölüm aç-kapat, hero içerik güncelleme, ilçe linkleri, form, sitemap, redirect/404, örnek site/blog/SSS, istatistik/yedekleme ve seed/göç sözleşmeleri kapsam dahilindedir.

R5 ek sözleşmeleri:
- `F-P5-a`: R5 temel bölümleri işlenir.
- `F-P5-b`: sabit sıra `hero → strip → services → steps → works → coast → faq → cta`.
- `F-R5-01`: sektör grubu yalnız yayınlanmış gerçek sektör sayfalarına bağlantı verir.
- `F-R5-02`: WhatsApp hedefi yalnız NAP telefonundan üretilir.

### 14.5 Hareket testleri (A)
| ID | Senaryo | Beklenen |
|----|---------|----------|
| A-01 | JavaScript kapalı | tüm içerik görünür |
| A-02 | `prefers-reduced-motion: reduce` | reveal/hero hareketi kapalı, son durum görünür |
| A-03 | R5 hero açılışı | web+mobil sahne ve içerik 1.05s içinde yerleşir; eski shape/parallax yok |
| A-04 | Bölge grafiği scroll | çizgi ilerler, noktalar yanar |
| A-05 | Geri kaydırma | reveal yeniden kapanmaz/oynamaz |
| A-06 | Sektör alanı | tab/visibility animasyonu yok; statik grup |
| A-07 | Resize | bölge çizgi uzunluğu tekrar ölçülür |
| A-08 | 360px | belge yatay taşmaz, hero sahnesi viewport içinde, hizmetler tek kolon |
| A-09 | IntersectionObserver yok | tüm reveal öğeleri görünür |
| A-10 | Sektörler | statik, gerçek URL'li, sonsuz marquee yok |

### 14.6 Performans hedefleri (P)
| ID | Ölçüm | Hedef |
|----|-------|-------|
| P-01 | Anasayfa yükleme masaüstü | < 2 sn hedef |
| P-02 | Lighthouse/PageSpeed | mobil ≥90 hedef, masaüstü daha yüksek |
| P-03 | CLS | ≤0.1 |
| P-04 | Scroll | akıcı; tasarım browser testi ≥50 fps alt sınırını gözler |
| P-05 | SQL | N+1 yok; gereksiz sorgu yok |
| P-06 | İlk yük | mümkün olduğunca küçük; R8 bütçesiyle ölçülür |
| P-07 | LCP | ≤2.5 sn |
| P-08 | INP | ≤200 ms hedef |

### 14.7 Erişilebilirlik (E)
Klavye tam gezinme, görünür focus, anlamlı alt/dekoratif aria-hidden, AA kontrast, tek H1 ve sıra, form label/hata bağı, SVG bölge açıklaması, mobile touch hedefleri, RTL ve %200 zoom R8'de birlikte doğrulanır.

### 14.8 SEO (O)
Tek H1, meta, canonical, yapılandırılmış veri, robots/sitemap, benzerlik, kırık iç link ve canonical host yönlendirmeleri test edilir.

### 14.9 Kabul kapısı
Bir modül şu şartlar sağlanmadan `main`e girmez:
- Tüm ilgili PHP/MySQL testleri geçer, atlanan test yoktur.
- Ön yüz değiştiyse gerçek Chromium browser job geçer.
- `php -l` tüm PHP dosyalarında temizdir.
- JS kapalı ve reduced-motion durumları korunur.
- Güvenlik/SEO/veri sözleşmesinde regresyon yoktur.
- `CHANGELOG.md` ve `DOCS.md` günceldir.
- PR merge edilebilir durumdadır; merge sonrası `main` CI tekrar doğrulanır.

---

## 15. GİT AKIŞI VE CI

### Dallar
`main` her an yayın adayıdır. Büyük redesign işleri modül branch/PR'larında yürütülür (`redesign/r0-r4-foundation`, `redesign/r5-home`, devamında R3/R6/R7/R8).

### Commit biçimi
```text
feat(redesign): rebuild home hero
fix(redesign): align section order
test(browser): validate R5 home behavior
docs(redesign): align R5 contract
```

### PR kontrol listesi
- [ ] Kapsam tek modül/fazda tutuldu
- [ ] Testler eklendi ve geçti
- [ ] Güvenlik/SEO sözleşmesi korunuyor
- [ ] `DOCS.md` + `CHANGELOG.md` güncel
- [ ] Üretim bağımlılığı eklenmedi
- [ ] JS kapalı/reduced-motion çalışıyor
- [ ] Browser CI yeşil

### `.github/workflows/ci.yml`
Gerçek workflow kanoniktir. Özet:
1. MySQL 8.0 service.
2. PHP 8.2 + `pdo_mysql, mbstring, gd, fileinfo`.
3. Tüm PHP dosyalarında syntax kontrolü.
4. `tests/run.php`; skip/ATLANDI varsa fail.
5. Test artifact.
6. Test başarılıysa ikinci MySQL ile browser ortamı.
7. Seed + installed lock.
8. Playwright Chromium.
9. `animation-check.mjs` ve `design-check.mjs`.
10. Browser log artifact.

---

## 16. YAPAY ZEKA ÇALIŞMA KURALLARI

1. `DOCS.md`, ilgili tasarım planı ve mevcut dosyayı okumadan değiştirme.
2. Büyük işi modül/branch/PR olarak böl.
3. Var olan güvenlik, route ve veri sözleşmesini sırf tasarım için bozma.
4. Üretim bağımlılığı/framework ekleme; CI geliştirme aracı ayrı tutulabilir.
5. Şema değişikliği migration ister; canlı varsayılan değişikliği de gerektiğinde göç ister.
6. Her SQL hazırlıklı; çıktı escape; POST CSRF.
7. Hareket bölüm 7 sözleşmesine uyar; reduced-motion zorunludur.
8. Sahte müşteri, puan, performans veya canlı veri üretme.
9. Modül bitince test, dokümantasyon ve changelog birlikte güncellenir.
10. CI kırılırsa artifact/log okunmadan tahminle merge edilmez.
11. PR ancak iki job da yeşilken merge edilir; sonra `main` tekrar doğrulanır.

---

## 17. FAZ PLANI VE TESLİM LİSTESİ

### Mevcut yeniden tasarım uygulama sırası
| Faz | İçerik | Bitiş şartı |
|-----|--------|-------------|
| R0 | Envanter ve bağımlılık/URL/veri haritası | kayıt tamam |
| R1 | Görsel sistem ve semantik roller | foundation testleri |
| R4 | Ortak header/footer/buton/form/panel kabuğu | foundation browser + PHP CI |
| R5 | Ana sayfa kompozisyonu | R5 PHP + gerçek Chromium + docs |
| R3 | Özgün G-01…G-10 görsel varlık ailesi | kayıt, boyut/alt/yerleşim kabulü |
| R6 | Hizmet/ilçe/sektör/blog/proje/iletişim/genel iç sayfalar | aile bazlı browser kabulü |
| R7 | Tüm panel/sistem ekranları | rol + CRUD + responsive/a11y kabulü |
| R8 | 320–1920px, RTL, %200 zoom, keyboard, reduced-motion, performance | tam yayın kapısı |

### Yayın öncesi teslim listesi
- [ ] SSL ve `https` yönlendirmesi çalışıyor
- [ ] `www` tercihi tek yönde sabit
- [ ] `display_errors` kapalı
- [ ] `/install` erişilemez
- [ ] Panel şifresi güçlü
- [ ] Gerçek form gönderimi ve e-posta teslimi kontrol edildi
- [ ] Spam klasörü kontrol edildi
- [ ] `sitemap.xml` Search Console'a gönderildi
- [ ] Dahili ziyaret ölçümü çalışıyor; üçüncü taraf analytics ancak ayrıca gerçekten entegre edildiyse “bağlandı” sayılır
- [ ] Yapısal veri Rich Results testinden geçti
- [ ] 404 düzgün
- [ ] Favicon ve OG görseli var
- [ ] Sahte/demo iddia yok; örnek çalışma notu doğru
- [ ] NAP Google İşletme Profili ile birebir
- [ ] Backup, submission purge ve visit rollup cron'ları kuruldu
- [ ] Bir gerçek backup geri yükleme denemesi yapıldı
- [ ] `php tools/preflight.php` üretimde geçti
- [ ] Tüm PHP/MySQL ve gerçek Chromium CI yeşil
- [ ] R8 viewport/RTL/%200 zoom/keyboard/reduced-motion/performance matrisi geçti


### Yeniden tasarım devam kaydı — 9 Eylül 2026

R0/R1/R4 foundation, R5, R6 ve R7 kodları main dalındadır; R8 otomatik kabul matrisi eklenmiştir. `00c21bc` kaynak sürümünün CI sonucu başarılıdır. R8 bütün özgün görsellerin veya canlı yayın kontrollerinin tamamlandığı anlamına gelmez. R3 G-01 hero sahnesi bu modülde eklenir; kalan görseller ve üretim doğrulamaları `REDESIGN-R3-ASSETS.md` kaydında açık tutulur.


### Canlı arayüz denetimi — 10 Eylül 2026

`LIVE-AUDIT-2026-09-10.md` mevcut üretim bulgularını ve yayın sınırlarını kaydeder. Header CTA ve footer içeriği tüm ön yüz controller'larında aynı panel kaynağından gelir. Eksik form gönderimi alan hatasını açıklar; buton yalnız devam eden gönderimde kilitlenir. Koyu zemindeki ikincil eylem rengi ortak CSS katmanındadır. Eksik `projects_notice` anahtarı varsayılana düşer, mevcut boş değer değiştirilmez. F-LIVE-01 ve inner/design browser kontrolleri bu davranışları korur.

HEAD, GET rotasını kullanır; POST handler çalıştırmaz ve ziyaret sayacına eklenmez. U-12f ve LIVE-04 HTTP testleri izleme uyumluluğunu korur.


### Referans ana sayfa ikon düzeltmesi — 11 Eylül 2026

Güncel `is-reference-home` sayfası `reference-home.css`, `reference-parity.css`,
`reference-parity-hotfix.css`, `reference-parity-final.css` sırasını kullanır.
Bilgi, hizmet, süreç ve avantaj ikonları güvenilir yerel SVG yollarından üretilir;
CMS ikon anahtarı izin verilen çizimler arasından seçilir, bilinmeyen anahtar
`layout` ikonuna düşer. İkonlar dekoratiftir, odak almaz; anlam görünür metindedir.
Işık iki sabit drop-shadow katmanıdır; hover, JS veya animasyona bağlı değildir.
CMS süreç sayısı korunur; masaüstü kolon sayısı gerçek öğe sayısına uyarlanır.
Proje kartları tablet dahil metin solda/görsel sağda düzenini korur.
`reference-parity-check.mjs` 320/390/768/1024/1440px ekran görüntülerini ve
JS kapalı/reduced-motion ikon görünürlüğünü denetler. Bu kontroller piksel
birebirliği veya canlı cPanel dağıtımı kabulü değildir. Elle A-01, A-02,
A-08 ve E (klavye, %200 zoom) kontrolleri uygulanmalıdır.
