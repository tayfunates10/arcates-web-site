# Arcates Web Site — Proje Dokümantasyonu

**Depo:** `arcates-web-site`
**Sürüm:** 1.0.0-draft
**Ürün:** Arcates Yazılım kurumsal sitesi + yönetim paneli
**Hedef:** Edremit ve Balıkesir Körfez bölgesinde yerel aramalardan müşteri adayı toplamak

Bu dosya deponun kökünde `DOCS.md` olarak durur. Claude Code her oturuma bu dosyayı okuyarak başlar ve sıradaki fazdan devam eder.

> Bu depo `arcates-core` CMS ürününden ayrıdır. Burada üretilen ve genelleştirilebilir olan her modül, kararlı hale geldikten sonra core'a taşınır. Ters yönde kopyalama yapılmaz.

---

## İÇİNDEKİLER

1. Kapsam ve ilkeler
2. Teknoloji ve ortam
3. Klasör yapısı
4. Sayfa envanteri ve URL haritası
5. Anasayfa şartnamesi (bölüm bölüm)
6. Tasarım belirteçleri
7. Animasyon şartnamesi
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
Çok dilli (TR birincil, EN/DE/AR ikincil) kurumsal site, yönetilebilir anasayfa bölümleri, hizmet sayfaları, ilçe sayfaları, sektör sayfaları, referans/portföy, blog, SSS, teklif formu ve müşteri adayı takibi, gelişmiş SEO araçları, yönlendirme ve 404 yönetimi, ziyaretçi istatistiği, medya yönetimi, yönetim paneli.

### Kapsam dışında (sürüm 1)
E-ticaret, ödeme, rezervasyon, üyelik, yorum sistemi.

### İlkeler
1. **Bağımlılık yok.** Composer, framework, npm, derleme adımı yok.
2. **İlerlemeli iyileştirme.** JavaScript kapalıyken tüm içerik görünür ve site kullanılabilir olmalı.
3. **Her veri doğrulanır, her çıktı kaçırılır.** İstisnasız.
4. **Anasayfa dahil her metin panelden düzenlenebilir.** Şablona gömülü metin bırakılmaz.
5. **Hız, animasyondan önce gelir.** Bir efekt Core Web Vitals hedefini bozuyorsa efekt gider.

---

## 2. TEKNOLOJİ VE ORTAM

| Bileşen | Gereksinim |
|---------|-----------|
| PHP | 8.1 minimum, 8.2 önerilen |
| Eklentiler | `pdo_mysql`, `mbstring`, `gd` veya `imagick`, `json`, `fileinfo` |
| MySQL | 5.7+ / MariaDB 10.4+ |
| Karakter seti | `utf8mb4` / `utf8mb4_unicode_ci` |
| Sunucu | Apache + `mod_rewrite`, cPanel paylaşımlı hosting |
| JS | Vanilla ES6, tek dosya |
| CSS | Tek dosya, CSS değişkenleri |
| Zaman dilimi | `Europe/Istanbul` |

Harici kaynak yalnızca Google Fonts. Font yükleme `preconnect` + `display=swap` ile yapılır. Üçüncü taraf başka script eklenmez.

---

## 3. KLASÖR YAPISI

```
arcates-web-site/
├── public/
│   ├── index.php                 # tek giriş noktası
│   ├── .htaccess
│   ├── robots.php                # /robots.txt yönlendirilir
│   ├── assets/
│   │   ├── css/site.css
│   │   ├── css/admin.css
│   │   ├── js/site.js            # animasyon motoru
│   │   ├── js/admin.js
│   │   └── img/
│   └── uploads/                  # git'te yok
│       └── .htaccess
├── app/
│   ├── Core/                     # App, Database, Router, Request, Response,
│   │                             # Session, Auth, Security, Validator, View,
│   │                             # Lang, Settings, Media, Seo, Mailer, Logger
│   ├── Models/
│   ├── Controllers/
│   │   ├── Front/
│   │   └── Admin/
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
│   │   ├── 404.php
│   │   └── partials/
│   │       ├── head.php
│   │       ├── header.php
│   │       ├── footer.php
│   │       ├── hero.php
│   │       ├── strip.php
│   │       ├── cards.php
│   │       ├── coast.php
│   │       ├── steps.php
│   │       ├── works.php
│   │       ├── faq.php
│   │       └── cta.php
│   └── admin/
├── config/
│   ├── config.example.php
│   ├── config.php                # git'te yok
│   └── routes.php
├── db/
│   ├── schema.sql
│   └── migrations/
├── lang/  (tr.php, en.php, de.php, ar.php)
├── storage/  (logs, cache, backups)   # git'te yok
├── tests/
│   ├── run.php
│   ├── unit/
│   ├── security/
│   └── functional/
├── tools/  (backup.php, rollup_visits.php, deploy.sh)
├── .github/workflows/ci.yml
├── CLAUDE.md
├── DOCS.md
├── CHANGELOG.md
├── VERSION
├── README.md
└── .gitignore
```

`app/`, `config/`, `storage/`, `views/` web kökü dışındadır. Hosting buna izin vermiyorsa her birine `Require all denied` içeren `.htaccess` konur.

### .gitignore
```
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
| `/blog` , `/blog/{slug}` | `post` | uzun kuyruk |
| `/sss` | `page` | — |
| `/kvkk` , `/gizlilik-politikasi` | `page` | `noindex` |

### 4.2 Hizmet sayfaları — `service`
`/web-tasarim`, `/e-ticaret-sitesi`, `/rezervasyon-sistemi`, `/seo-hizmeti`, `/coklu-dil-web-sitesi`, `/web-sitesi-bakim`

### 4.3 İlçe sayfaları — `location`
`/edremit-web-tasarim`, `/akcay-web-tasarim`, `/altinoluk-web-tasarim`, `/burhaniye-web-tasarim`, `/havran-web-tasarim`, `/ayvalik-web-tasarim`, `/gomec-web-tasarim`, `/balikesir-web-tasarim`

### 4.4 Sektör sayfaları — `sector`
`/otel-pansiyon-web-sitesi`, `/zeytinyagi-e-ticaret-sitesi`, `/restoran-kafe-qr-menu`, `/emlak-web-sitesi`, `/nakliyat-web-sitesi`, `/tabela-matbaa-web-sitesi`

### 4.5 Sistem
`/sitemap.xml`, `/robots.txt`, `/panel/*`

### 4.6 Dil önekleri
Varsayılan dil (TR) öneksizdir. Diğer diller `/en/...`, `/de/...`, `/ar/...` önekiyle çalışır. İlçe sayfaları yalnızca TR'de yayınlanır; diğer dillerde `hreflang` verilmez.

### 4.7 Kritik kural — ilçe sayfaları
Aynı metnin ilçe adı değiştirilerek çoğaltılması Google tarafından **doorway page** olarak değerlendirilir ve tüm sayfaları birden değersizleştirir. Her ilçe sayfası en az **500 kelime özgün metin**, o ilçeye ait en az bir referans veya örnek, ve o ilçeye özel SSS içermelidir. Panel bu şartı sağlamayan sayfayı yayınlarken uyarı gösterir (bkz. 9.6).

---

## 5. ANASAYFA ŞARTNAMESİ

Anasayfa sabit sıralı bölümlerden oluşur. Her bölüm panelden açılıp kapatılabilir ve içeriği düzenlenebilir. Sıra değiştirilemez (sürüm 1).

| # | Bölüm | Anahtar | Yönetilebilir alanlar |
|---|-------|---------|----------------------|
| 1 | Üst menü | `header` | Menü öğeleri, buton metni ve hedefi |
| 2 | Kahraman | `hero` | Rozet metni, 3 satır başlık, vurgulu satır, açıklama, 2 buton |
| 3 | Sektör şeridi | `strip` | Etiket listesi, kayma hızı |
| 4 | Hizmet kartları | `services` | 6 kart: ikon, renk, başlık, metin, bağlantı |
| 5 | Bölge haritası | `coast` | Başlık, açıklama, ilçe listesi (ad + x konumu + hedef URL) |
| 6 | Süreç | `steps` | 3 adım: başlık, metin |
| 7 | Referanslar | `works` | Portföy kayıtlarından son N tanesi |
| 8 | SSS | `faq` | Anasayfaya atanmış SSS kayıtları |
| 9 | Çağrı bandı | `cta` | Başlık, metin, 2 buton |
| 10 | Alt bilgi | `footer` | NAP bilgileri, çalışma saatleri, bağlantılar |

### 5.1 Kahraman bölümü teknik notları
- `H1` üç satırdan oluşur, üçüncü satır degrade renklidir. Degrade metin **arama motorunda okunabilir olmalıdır**: `background-clip:text` kullanılır, görsel kullanılmaz.
- Sağdaki şekil kümesi tamamen dekoratiftir, `aria-hidden="true"` taşır ve içeriğe bilgi eklemez.
- `H1` LCP ögesidir. Görünürlüğü JavaScript'e bağlı olamaz; animasyon CSS ile sayfa yüklenirken başlar.

### 5.2 Bölge haritası teknik notları
- SVG `viewBox="0 0 1000 190"`, ilçe noktaları `data-x` değerine göre yerleşir.
- Her ilçe noktası ilgili ilçe sayfasına bağlantı olmalıdır (iç link değeri taşır).
- SVG `role="img"` ve tüm ilçe adlarını içeren `aria-label` taşır.

### 5.3 Mobil davranış
- 940px altında ızgara tek sütuna düşer, şekil kümesi başlığın altına geçer.
- 720px altında şekil kümesi yüksekliği %60'a iner, süzülme animasyonları kapanır (pil ve akıcılık için).
- Sektör şeridi mobilde de akar ancak hızı %70'e düşer.

---

## 6. TASARIM BELİRTEÇLERİ

`public/assets/css/site.css` başında CSS değişkeni olarak tanımlanır. Şablonda sabit renk kodu yazılmaz.

| Değişken | Değer | Kullanım |
|----------|-------|----------|
| `--navy` | `#062244` | Ana metin, koyu bant |
| `--blue` | `#0B4FA8` | Birincil marka rengi |
| `--blue-bright` | `#1C7BF2` | Vurgu, bağlantı, degrade |
| `--blue-soft` | `#7FB6FF` | Degrade ucu |
| `--cyan` | `#1FC4E0` | İkincil vurgu |
| `--mist` | `#EDF4FF` | Açık zemin |
| `--mist-2` | `#F7FAFF` | Kahraman zemini |
| `--white` | `#FFFFFF` | Zemin |
| `--sun` | `#FFC13D` | Şekil, kart aksanı |
| `--coral` | `#FF6B57` | Şekil, kart aksanı |
| `--violet` | `#7C5CFF` | Şekil, kart aksanı |
| `--mint` | `#25C989` | Durum, kart aksanı |
| `--ink-soft` | `#4A6588` | İkincil metin |
| `--line` | `rgba(11,79,168,.14)` | Kenarlık |

**Tipografi:** Başlık `Sora` (600/700/800), gövde `Plus Jakarta Sans` (400/500/600). İkisi de tam Türkçe karakter desteği sunar. Üçüncü bir yazı tipi eklenmez.

**Kontrast kuralı:** `--ink-soft` beyaz üzerinde AA seviyesini geçer. Renkli zemin üzerinde beyaz metin kullanılacaksa kontrast oranı 4.5:1 altına düşmemelidir. Yeni renk eklenirse test E-04 ile doğrulanır.

---

## 7. ANİMASYON ŞARTNAMESİ

### 7.1 Değişmez kurallar
1. Yalnızca `transform` ve `opacity` animasyonu yapılır. `width`, `height`, `top`, `left`, `margin` animasyonu yasaktır.
2. Tüm gizli başlangıç durumları `html.js` sınıfı altında tanımlanır. Bu sınıf `<head>` içindeki satır içi script ile eklenir. **JavaScript çalışmazsa hiçbir içerik gizli kalmaz.**
3. `prefers-reduced-motion: reduce` tanımlıysa tüm animasyonlar kapanır, son durum gösterilir.
4. Scroll dinleyicileri `{passive:true}` ile bağlanır ve `requestAnimationFrame` ile sınırlandırılır.
5. Görünürlük tespiti `IntersectionObserver` ile yapılır; desteklenmiyorsa tüm ögeler görünür duruma alınır.
6. Bir öge bir kez göründükten sonra `unobserve` edilir; geri kaydırmada tekrar oynatılmaz.
7. Sonsuz döngü animasyonu yalnızca dekoratif şekillerde ve sektör şeridinde bulunur; metin üzerinde döngü animasyonu yoktur.

### 7.2 Hareket belirteçleri
| Belirteç | Değer |
|----------|-------|
| Yumuşatma | `cubic-bezier(.16,1,.3,1)` |
| Başlık maskesi süresi | 1000 ms |
| Kartlar için giriş süresi | 800 ms |
| Kademe aralığı | 80 ms (kartlar), 140 ms (adımlar) |
| Şekil giriş süresi | 950 ms |
| Süzülme döngüsü | 7 s / 9 s |
| Şerit tam tur | 34 s |
| Görünürlük eşiği | `threshold: 0.15`, `rootMargin: 0px 0px -8% 0px` |

### 7.3 Açılış sırası (anasayfa)
| Sıra | Öge | Gecikme |
|------|-----|---------|
| 1 | Logo işareti döner | 0 ms |
| 2 | Başlık satırı 1 | 80 ms |
| 3 | Başlık satırı 2 | 180 ms |
| 4 | Başlık satırı 3 (degrade) | 280 ms |
| 5 | Kart şekli | 240 ms |
| 6 | Daire, halka | 420–520 ms |
| 7 | Açıklama metni | 560 ms |
| 8 | Butonlar | 660 ms |
| 9 | Kare, üçgen, hap | 640–860 ms |
| 10 | Grafik çizgisi çizilir | 1150 ms |

Toplam açılış 1.4 saniyede tamamlanır. Bu süre uzatılmaz; kullanıcı içeriği beklemiş hissetmemelidir.

**Uygulama notu.** 1.4 saniye *içerik* için geçerlidir: rozet, üç başlık satırı, açıklama ve
butonlar bölüm 7.2'deki sürelerle en geç 1460 ms'te yerine oturur. Dekoratif şekiller ve
grafik çizgisi, yine 7.2'deki 950 ms giriş süresi ve yukarıdaki gecikmelerle bir miktar
sonra tamamlanır; bunlar `aria-hidden` taşıyan süslemelerdir ve kullanıcı onları beklemez.
Bu iki tablo (7.2 süreleri ve 7.3 gecikmeleri) değiştirilmeden korunur.

### 7.4 Scroll'a bağlı hareketler
- **İlerleme çubuğu:** sayfa ilerlemesine göre `scaleX`.
- **Sabit üst menü:** 24px sonrası küçülür ve alt çizgi kazanır.
- **Bölge haritası:** figürün konumuna göre 0–1 arası ilerleme hesaplanır, `stroke-dashoffset` buna göre ayarlanır, ilçe noktaları çizgi geçtikçe yanar.
- **Şekil sürüklenmesi:** `data-depth` değerine göre farklı hızda kayar, yalnızca ilk ekran yüksekliğinin 1.3 katı içinde çalışır.

### 7.5 Performans sınırı
Animasyonlar açıkken masaüstünde 60 fps korunmalıdır. Test P-04 ile ölçülür. Düşen kare varsa önce süzülme döngüleri, sonra şekil sürüklenmesi kapatılır.

---

## 8. VERİTABANI ŞEMASI

Tüm tablolar `InnoDB`, `utf8mb4_unicode_ci`. Çeviri deseni: ana tablo dilden bağımsız alanları, `_translations` tablosu dile bağlı alanları tutar.

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
  INDEX idx_user_time (user_id, created_at),
  CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
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
  district VARCHAR(60) NULL,          -- location sayfaları için
  sort SMALLINT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_type_status (type, status),
  CONSTRAINT fk_page_parent FOREIGN KEY (parent_id) REFERENCES pages(id) ON DELETE SET NULL
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
  INDEX idx_page_lang (page_id, lang),
  CONSTRAINT fk_pt_page FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

`services`, `projects`, `posts`, `faqs` aynı deseni izler:

```sql
CREATE TABLE projects (              -- referanslar
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  client_name VARCHAR(150) NOT NULL,
  sector VARCHAR(80) NULL,
  district VARCHAR(60) NULL,
  live_url VARCHAR(255) NULL,
  cover_id INT UNSIGNED NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  sort SMALLINT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE faqs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sort SMALLINT NOT NULL DEFAULT 0,
  status TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE faq_page (              -- SSS'yi sayfaya bağlar
  faq_id INT UNSIGNED NOT NULL,
  page_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (faq_id, page_id),
  CONSTRAINT fk_fp_faq FOREIGN KEY (faq_id) REFERENCES faqs(id) ON DELETE CASCADE,
  CONSTRAINT fk_fp_page FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### 8.3 Anasayfa bölümleri
```sql
CREATE TABLE home_sections (
  `key` VARCHAR(40) PRIMARY KEY,     -- hero, strip, services, coast, steps, works, faq, cta
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort SMALLINT NOT NULL DEFAULT 0,
  config JSON NULL,                  -- dilden bağımsız ayarlar (renk, hız, adet)
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE home_section_translations (
  section_key VARCHAR(40) NOT NULL,
  lang CHAR(2) NOT NULL,
  content JSON NOT NULL,             -- {"badge":"...","line1":"...","cta1":{"label":"","url":""}}
  PRIMARY KEY (section_key, lang),
  CONSTRAINT fk_hst_section FOREIGN KEY (section_key) REFERENCES home_sections(`key`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE districts (             -- bölge haritası noktaları
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

### 8.4 Medya, form, SEO, istatistik
```sql
CREATE TABLE media (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  filename VARCHAR(190) NOT NULL,
  path VARCHAR(255) NOT NULL,
  mime VARCHAR(80) NOT NULL,
  size INT UNSIGNED NOT NULL,
  width SMALLINT UNSIGNED NULL,
  height SMALLINT UNSIGNED NULL,
  variants JSON NULL,
  user_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE media_translations (
  media_id INT UNSIGNED NOT NULL,
  lang CHAR(2) NOT NULL,
  alt VARCHAR(255) NULL,
  title VARCHAR(255) NULL,
  PRIMARY KEY (media_id, lang),
  CONSTRAINT fk_mt_media FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE submissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  form_key VARCHAR(60) NOT NULL DEFAULT 'contact',
  name VARCHAR(150) NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(40) NULL,
  service VARCHAR(80) NULL,
  message TEXT NULL,
  source_url VARCHAR(255) NULL,
  referrer VARCHAR(255) NULL,
  utm JSON NULL,
  lang CHAR(2) NULL,
  kvkk_consent TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('new','contacted','quoted','won','lost') NOT NULL DEFAULT 'new',
  note TEXT NULL,
  ip VARBINARY(16) NULL,
  user_agent VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_status_time (status, created_at)
) ENGINE=InnoDB;

CREATE TABLE redirects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  from_path VARCHAR(255) NOT NULL UNIQUE,
  to_path VARCHAR(255) NOT NULL,
  code SMALLINT NOT NULL DEFAULT 301,
  hits INT UNSIGNED NOT NULL DEFAULT 0,
  last_hit_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE not_found (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  path VARCHAR(255) NOT NULL UNIQUE,
  hits INT UNSIGNED NOT NULL DEFAULT 1,
  referrer VARCHAR(255) NULL,
  last_seen DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE visits (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  path VARCHAR(255) NOT NULL,
  session_hash CHAR(64) NOT NULL,
  referrer VARCHAR(255) NULL,
  device ENUM('desktop','mobile','tablet','bot') NOT NULL DEFAULT 'desktop',
  lang CHAR(2) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_time (created_at),
  INDEX idx_path_time (path, created_at)
) ENGINE=InnoDB;

CREATE TABLE visits_daily (
  day DATE NOT NULL,
  path VARCHAR(255) NOT NULL,
  views INT UNSIGNED NOT NULL DEFAULT 0,
  sessions INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (day, path)
) ENGINE=InnoDB;
```

`visits` tablosu hızla büyür. Günlük cron 90 günden eski kayıtları `visits_daily`'ye toplar ve siler.

### 8.5 Menü
```sql
CREATE TABLE menu_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  menu_key VARCHAR(40) NOT NULL,     -- main, footer
  parent_id INT UNSIGNED NULL,
  page_id INT UNSIGNED NULL,
  url VARCHAR(255) NULL,
  target ENUM('_self','_blank') NOT NULL DEFAULT '_self',
  sort SMALLINT NOT NULL DEFAULT 0,
  INDEX idx_menu (menu_key, sort)
) ENGINE=InnoDB;

CREATE TABLE menu_item_translations (
  menu_item_id INT UNSIGNED NOT NULL,
  lang CHAR(2) NOT NULL,
  label VARCHAR(120) NOT NULL,
  PRIMARY KEY (menu_item_id, lang),
  CONSTRAINT fk_mit_item FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### 8.6 Göç kuralı
`schema.sql` yalnızca sıfırdan kurulum içindir, elle düzenlenmez. Her şema değişikliği `db/migrations/YYYY_MM_DD_NNNN_aciklama.sql` olarak eklenir ve `migrations` tablosuna yazılır.

---

## 9. YÖNETİM PANELİ

Panel yolu `config.php` ile değiştirilebilir, varsayılan `/panel`.

### 9.1 Pano
Son 30 gün ziyaretçi grafiği, yeni form sayısı, dönüşüm hunisi (yeni → arandı → teklif → kazanıldı), en çok ziyaret edilen 10 sayfa, son 404 kayıtları, taslak içerik sayısı, sistem durumu (PHP sürümü, disk, son yedek tarihi).

### 9.2 Anasayfa yöneticisi
Bölüm listesi; her bölüm için aç/kapat anahtarı ve düzenleme ekranı. Kahraman bölümünde canlı önizleme. Bölge haritasında ilçe noktaları sürükle-bırak ile konumlandırılır, `map_x`/`map_y` otomatik hesaplanır.

### 9.3 Sayfalar
Tür filtresi (sayfa / hizmet / ilçe / sektör), dil sekmeleri, ağaç sıralama, tam SEO paneli, içerik skoru, Google sonuç önizlemesi.
**Slug değişince otomatik 301 kaydı oluşturulur; bu davranış kapatılamaz.**

### 9.4 Referanslar, blog, SSS
Referans: müşteri adı, sektör, ilçe, canlı site linki, görseller, yapılan işler.
Blog: kategori, kapak, yayın tarihi, ileri tarihli yayın.
SSS: soru, cevap, hangi sayfalara atanacağı.

**Uygulama notu — örnek site sunumu.** Gerçek müşteri işleri yayına girene
kadar `project` kayıtları ön yüzde "Örnek Siteler" olarak sunulur. Adres
bölüm 4'teki gibi `/referanslar` ve `/referanslar/{slug}` olarak kalır; böylece
gerçek işler eklendiğinde yönlendirme gerekmez. Ayarlardaki **Örnek site notu**
(`projects_notice`) liste, detay, anasayfa bloğu ve ilçe sayfasındaki blokta
görünür ve bu kayıtların teslim edilmiş müşteri işi olmadığını açıkça söyler.
Gerçek işler yayına alındığında bu ayar boşaltılır, not kendiliğinden kaybolur.

### 9.5 Medya
Çoklu yükleme, ızgara görünüm, alt metin alanı (boşsa uyarı rozeti), varyant bilgisi, kullanım yeri gösterimi, kullanımdaki dosya için silme uyarısı.

### 9.6 İçerik skoru
Kaydı engellemez, eksikleri listeler.

| Kontrol | Eşik | Sayfa türü |
|---------|------|-----------|
| Kelime sayısı | < 300 uyarı | tümü |
| Kelime sayısı | < 500 **güçlü uyarı** | `location` |
| İlçeye özel referans | yoksa uyarı | `location` |
| Sayfaya atanmış SSS | yoksa uyarı | `location`, `service` |
| H1 sayısı | 1 değilse uyarı | tümü |
| Meta başlık | boş veya > 60 karakter | tümü |
| Meta açıklama | boş veya > 160 karakter | tümü |
| Alt metni eksik görsel | > 0 | tümü |
| İç link | < 2 | tümü |
| Slug | Türkçe karakter veya boşluk | tümü |
| Benzerlik | Başka bir `location` sayfasıyla %70+ örtüşme | `location` |

Son satır kritiktir: ilçe sayfalarının birbirine benzemesini yayın öncesinde yakalar.

### 9.7 SEO
`robots.txt` düzenleyici, sitemap durumu ve yeniden üretme, varsayılan meta şablonu (`%title% | %site%`), Search Console ve Analytics kod alanı, tüm sayfaların meta durumu tablosu.

### 9.8 Yönlendirmeler ve 404
Yönlendirme listesi ve elle ekleme, döngü kontrolü, 404 listesi ve tek tıkla yönlendirmeye dönüştürme.

### 9.9 Formlar
Kayıt listesi, durum etiketleri, not alanı, kaynak sayfa ve referrer, CSV dışa aktarma, saklama süresi dolan kayıtları toplu silme.

### 9.10 İstatistik
Günlük ziyaretçi ve görüntüleme, en çok girilen sayfalar, referans kaynakları, cihaz ve dil dağılımı, aylık CSV rapor. `user_agent` bot imzası taşıyorsa `device='bot'` işaretlenir ve grafiklere girmez.

### 9.11 Kullanıcılar, ayarlar, yedekleme
Rol yönetimi (admin tam yetkili, editör yalnızca içerik), şifre değiştirme, işlem günlüğü.
Ayarlar: site adı, NAP, sosyal hesaplar, çalışma saatleri, varsayılan dil, bakım modu, e-posta.
Yedekleme: elle yedek alma, son 10 yedek, indirme, günlük otomatik yedek.

---

## 10. GÜVENLİK ŞARTNAMESİ

### 10.1 Veritabanı
Tüm sorgular hazırlanmış ifade. PDO `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES=false`. SQL içine değişken birleştirilmez. Tablo/sütun adı değişkenden gelecekse beyaz listeden geçer.

### 10.2 Çıktı
Ekrana basılan her değer `Security::e()` ile kaçırılır. Zengin metin yalnızca admin girer ve izin verilen etiket listesiyle temizlenir. JSON çıktısı `JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT` ile üretilir.

### 10.3 Oturum
```php
session_set_cookie_params([
  'lifetime' => 0, 'path' => '/',
  'secure' => true, 'httponly' => true, 'samesite' => 'Lax'
]);
session_name('arcsid');
```
Girişte `session_regenerate_id(true)`. 2 saat işlemsizlikte düşer. Çıkışta veri silinir, çerez geçmişe alınır.

### 10.4 Şifre ve giriş
`password_hash` / `password_verify`, minimum 10 karakter. Aynı IP'den 5 başarısız denemede 15 dakika kilit, e-posta bazlı ayrı sayaç. Kullanıcı var/yok ayrımı sızdırılmaz.

### 10.5 CSRF
Her POST formunda `_token`, kontrol `hash_equals` ile. Başarısızsa 419 ve `activity_log` kaydı.

### 10.6 Dosya yükleme
| Kontrol | Kural |
|---------|-------|
| Uzantı | `jpg, jpeg, png, webp, gif, svg, pdf` |
| MIME | `finfo_file` ile doğrulanır, uzantıyla eşleşmeli |
| Boyut | 5 MB |
| Ad | `bin2hex(random_bytes(8))`, kullanıcı adı kullanılmaz |
| SVG | `<script>`, `on*` nitelikleri, `xlink:href` temizlenir |
| Klasör | `uploads/YYYY/MM/`, PHP çalıştırma kapalı |

### 10.7 Yapılandırma ve başlıklar
Canlıda `display_errors=0`, `log_errors=1`. `config.php` web kökü dışında. `/install` kurulumdan sonra `storage/installed.lock` ile kapanır. `.git` web'den erişilemez.
Başlıklar: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, `Content-Security-Policy` (panelde satır içi script yok).

### 10.8 Form ve spam
Honeypot alanı, form açılış zaman damgası (3 saniyeden hızlı gönderim reddedilir), IP başına saatte 5 gönderim sınırı, sunucu tarafı doğrulama.

### 10.9 KVKK
Aydınlatma metni ve gizlilik politikası sayfaları (`noindex` değil, indekslenir), formda önceden işaretli olmayan onay kutusu, `submissions` saklama süresi ayarı ve otomatik temizlik.

### 10.10 .htaccess
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
Options -Indexes
<FilesMatch "\.(env|ini|log|sql|md)$">
  Require all denied
</FilesMatch>
```
`public/uploads/.htaccess`:
```apache
php_flag engine off
<FilesMatch "\.(php|phtml|php[0-9]|phar|cgi|pl)$">
  Require all denied
</FilesMatch>
```

---

## 11. SEO VE YAPISAL VERİ

### 11.1 Her sayfada
Tek `H1`, `H2` ile bölümlenmiş yapı, 60 karakterlik title, 155 karakterlik description, canonical, breadcrumb (görsel + şema), en az 3 iç link, sayfaya özel SSS, net eylem çağrısı.

### 11.2 Şema tipleri
| Sayfa | Şema |
|-------|------|
| Anasayfa, iletişim | `ProfessionalService` |
| Hizmet sayfaları | `Service` |
| İlçe sayfaları | `Service` + `areaServed` |
| SSS bölümü olan her sayfa | `FAQPage` |
| Referans detay | `CreativeWork` |
| Blog yazısı | `Article` |
| Tüm iç sayfalar | `BreadcrumbList` |

`ProfessionalService` içindeki isim, adres, telefon **Google İşletme Profili ile birebir aynı** yazılmalıdır. Uydurma yorum veya `AggregateRating` işaretlemesi yapılmaz.

### 11.3 Çok dil
`hreflang` seti her sayfada tüm dil karşılıklarını ve `x-default`'u içerir. Karşılığı olmayan dil için `hreflang` verilmez. Arapça sayfalarda `<html dir="rtl">` ve mantıksal CSS özellikleri (`margin-inline-start`) kullanılır.

**Uygulama notu — dil yayın anahtarı.** Kurulum yalnızca varsayılan dili açık
bırakır. Çevirisi girilmemiş bir dil açık olsaydı üst menüde görünür, ziyaretçi
tıklayınca Türkçe içeriğe düşerdi. Diller Ayarlar ekranındaki **Yayındaki
diller** anahtarından açılır; varsayılan dil kapatılamaz. Kapalı bir dil üst
menüde görünmez, `hreflang` setine girmez ve `/en/...` gibi önekli adresleri
404 döner.

### 11.4 Teknik
`sitemap.xml` dinamik üretilir, yalnızca yayınlanmış içerik girer, `lastmod` `updated_at`'ten gelir. Görseller WebP, `loading="lazy"`, `width`/`height` yazılı. Slug üretiminde Türkçe karakter dönüşümü: `ç→c, ğ→g, ı→i, İ→i, ö→o, ş→s, ü→u`.

---

## 12. FORM VE DÖNÜŞÜM TAKİBİ

Teklif formu alanları: ad, telefon, e-posta, ilgilenilen hizmet (seçim), mesaj, KVKK onayı, honeypot, zaman damgası, CSRF token.

Kayıt sırasında otomatik saklananlar: `source_url` (formun gönderildiği sayfa), `referrer`, `utm` parametreleri, dil, IP, tarayıcı.

Bu alanlar hangi ilçe veya hizmet sayfasının gerçekten iş getirdiğini gösterir. Panelde dönüşüm raporu bu veriden üretilir.

Gönderim sonrası: veritabanına kayıt, yöneticiye e-posta, kullanıcıya teşekkür sayfası (`/tesekkurler`, `noindex`). Teşekkür sayfası ayrı URL olmalıdır ki dönüşüm ölçülebilsin.

---

## 13. KURULUM VE DAĞITIM

### İlk kurulum
1. Depo klonlanır
2. `config/config.example.php` → `config/config.php`, veritabanı bilgileri girilir
3. `/install` açılır: bağlantı testi, `schema.sql` uygulanır, ilk admin oluşturulur, varsayılan diller ve anasayfa bölümleri eklenir
4. `storage/installed.lock` yazılır
5. `storage/` ve `public/uploads/` yazılabilir yapılır

### cPanel
Alan adı document root'u `public/` klasörüne yönlendirilir. PHP 8.1+ seçilir.
```
0 3 * * * php /home/kullanici/arcates-web-site/tools/backup.php
30 3 * * * php /home/kullanici/arcates-web-site/tools/rollup_visits.php
```

---

## 14. TEST PLANI

### 14.1 Çalıştırıcı
Composer yok. `tests/run.php` basit bir çalıştırıcıdır; `test()`, `assertTrue()`, `assertSame()` yardımcılarını sunar. Başarısızlıkta çıkış kodu 1 döner, böylece CI kırılır.
Çalıştırma: `php tests/run.php`

### 14.2 Birim testleri (U)
| ID | Kapsam | Beklenen |
|----|--------|----------|
| U-01 | `Security::slug('Çanakkale Yolu')` | `canakkale-yolu` |
| U-02 | `Security::slug('İzmir ŞŞ Ğ')` | `izmir-ss-g` |
| U-03 | `Security::e('<b>')` | `&lt;b&gt;` |
| U-04 | `Validator` `email` geçersiz değerle | başarısız |
| U-05 | `Validator` `required` boş dize | başarısız |
| U-06 | Slug çakışması | ikinci kayıt `-2` eki alır |
| U-07 | `Database::insert` + okuma | veri aynen döner |
| U-08 | `Media` uzantı beyaz listesi | `.php` reddedilir |
| U-09 | `Seo::score` 200 kelimeyle | kelime uyarısı döner |
| U-10 | `Seo::score` `location` türü 400 kelimeyle | güçlü uyarı döner |
| U-11 | İlçe benzerlik ölçümü | %70 üzeri örtüşme yakalanır |
| U-12 | `Router` `{slug}` eşleşmesi | doğru handler çağrılır |
| U-13 | `hreflang` üretimi eksik çeviriyle | eksik dil listeye girmez |
| U-14 | Sitemap üretimi | taslak içerik yer almaz |
| U-15 | Bot tespiti | `Googlebot` `device='bot'` işaretlenir |

### 14.3 Güvenlik testleri (S)
| ID | Senaryo | Beklenen |
|----|---------|----------|
| S-01 | Giriş alanına `' OR '1'='1` | başarısız, log kaydı |
| S-02 | Sayfa başlığına `<script>alert(1)</script>` | metin olarak görünür, çalışmaz |
| S-03 | CSRF token'sız POST | 419 |
| S-04 | Geçersiz CSRF token | 419 |
| S-05 | `test.php` yükleme | reddedilir |
| S-06 | `resim.php.jpg` yükleme | MIME kontrolünde reddedilir |
| S-07 | `uploads/` içindeki PHP dosyasına erişim | 403 |
| S-08 | 6 hatalı giriş | 15 dakika kilit |
| S-09 | Oturumsuz `/panel/sayfalar` | girişe yönlendirir |
| S-10 | Editör rolüyle `/panel/kullanicilar` | 403 |
| S-11 | `config/config.php` tarayıcıdan | 403 veya 404 |
| S-12 | `/.git/config` | 403 veya 404 |
| S-13 | Kurulum sonrası `/install` | kapalı |
| S-14 | Formu 1 saniyede gönderme | reddedilir |
| S-15 | Honeypot dolu gönderim | sessizce reddedilir |
| S-16 | Aynı IP'den saatte 6. gönderim | reddedilir |
| S-17 | Zararlı SVG yükleme | script ve `on*` temizlenir |
| S-18 | Yönlendirme döngüsü ekleme (`/a → /b`, `/b → /a`) | panel engeller |

### 14.4 İşlevsel testler (F)
| ID | Senaryo | Beklenen |
|----|---------|----------|
| F-01 | Sayfa oluştur ve yayınla | ön yüzde görünür |
| F-02 | Taslağa al | ön yüzde 404 |
| F-03 | Slug değiştir | eski adres 301 ile yeniye gider |
| F-04 | İngilizce çeviri ekle | `/en/slug` çalışır, hreflang doğru |
| F-05 | Arapça sayfa | `dir="rtl"`, düzen bozulmaz |
| F-06 | Görsel yükle | thumb, medium, large, webp üretilir |
| F-07 | Kullanımdaki görseli sil | uyarı verir |
| F-08 | Form gönder | kayıt oluşur, e-posta gider, `source_url` doğru |
| F-09 | Form durumunu "kazanıldı" yap | dönüşüm raporuna yansır |
| F-10 | Menü sıralaması değiştir | ön yüze yansır |
| F-11 | Olmayan adres | 404 sayfası, `not_found` kaydı artar |
| F-12 | 404 kaydını yönlendirmeye çevir | adres yeni hedefe gider |
| F-13 | `sitemap.xml` | yayınlanmışlar var, taslaklar yok |
| F-14 | Anasayfa bölümünü kapat | bölüm ön yüzde görünmez |
| F-15 | Kahraman başlığını değiştir | anasayfada anında yansır |
| F-16 | Bölge haritasına ilçe ekle | nokta doğru konumda çıkar ve sayfaya bağlanır |
| F-17 | Bakım modu | ziyaretçi bakım sayfası, admin site görür |
| F-18 | Türkçe karakterli uzun içerik | kayıt ve okuma bozulmaz |
| F-19 | Yedek al ve geri yükle | veri kaybı yok |
| F-20 | İlçe sayfası 400 kelimeyle yayınlanmak istenir | güçlü uyarı gösterilir |

### 14.5 Animasyon testleri (A)
| ID | Senaryo | Beklenen |
|----|---------|----------|
| A-01 | JavaScript kapalı | tüm içerik görünür, hiçbir bölüm gizli değil |
| A-02 | `prefers-reduced-motion: reduce` | animasyon yok, son durum görünür |
| A-03 | Anasayfa açılışı | 1.4 saniyede tamamlanır |
| A-04 | Bölge haritası scroll | çizgi ilerler, ilçeler sırayla yanar |
| A-05 | Geri kaydırma | görünmüş ögeler tekrar oynatılmaz |
| A-06 | Sekmeyi arka plana al ve dön | animasyon takılmaz, düzen bozulmaz |
| A-07 | Tarayıcı penceresini yeniden boyutlandır | harita çizgi uzunluğu yeniden hesaplanır |
| A-08 | 360px genişlik | şekiller taşmaz, yatay kaydırma oluşmaz |
| A-09 | `IntersectionObserver` desteklenmeyen tarayıcı | tüm ögeler görünür duruma alınır |
| A-10 | Sektör şeridi | kesintisiz döner, sıçrama yok |

### 14.6 Performans testleri (P)
| ID | Ölçüm | Hedef |
|----|-------|-------|
| P-01 | Anasayfa yükleme (masaüstü) | < 2 sn |
| P-02 | PageSpeed mobil / masaüstü | ≥ 70 / ≥ 90 |
| P-03 | CLS | < 0.1 |
| P-04 | Scroll sırasında kare hızı | 60 fps korunur |
| P-05 | Sayfa başına SQL sorgusu | < 25, N+1 yok |
| P-06 | Toplam ilk yük | < 800 KB |
| P-07 | LCP | < 2.5 sn |

### 14.7 Erişilebilirlik testleri (E)
| ID | Senaryo | Beklenen |
|----|---------|----------|
| E-01 | Klavye ile tam gezinme | tüm bağlantı ve butonlara erişilir |
| E-02 | Odak halkası | her odaklanabilir ögede görünür |
| E-03 | Görseller | anlamlı `alt`, dekoratifler `aria-hidden` |
| E-04 | Renk kontrastı | metin/zemin ≥ 4.5:1 |
| E-05 | Başlık hiyerarşisi | tek H1, seviye atlanmıyor |
| E-06 | Form etiketleri | her alan `label` ile bağlı |
| E-07 | SVG harita | `role="img"` ve açıklayıcı `aria-label` |

### 14.8 SEO testleri (O)
| ID | Senaryo | Beklenen |
|----|---------|----------|
| O-01 | Her sayfada tek H1 | sağlanır |
| O-02 | Title ve description | boş yok, uzunluk sınırında |
| O-03 | Canonical | her sayfada doğru ve mutlak URL |
| O-04 | Yapısal veri | Rich Results testinde hatasız |
| O-05 | `robots.txt` | sitemap satırı içerir, panel yolunu engeller |
| O-06 | Yinelenen içerik | ilçe sayfaları arası benzerlik %70 altında |
| O-07 | Kırık iç link | yok |
| O-08 | `http` ve `www` varyantları | tek hedefe 301 |

### 14.9 Kabul kapısı
Bir modül şu şartlar sağlanmadan `main` dalına giremez:
- İlgili U testleri geçiyor
- S testlerinin tamamı geçiyor
- Değişiklik ön yüzü etkiliyorsa A ve E testleri geçiyor
- `php -l` tüm dosyalarda temiz
- `CHANGELOG.md` güncellendi
- `DOCS.md`'de eksik varsa güncellendi

---

## 15. GİT AKIŞI VE CI

### Dallar
`main` (korumalı, her an yayına çıkabilir), `dev`, `feature/<modul>`, `fix/<konu>`

### Commit biçimi
```
feat(home): bolge haritasi scroll animasyonu eklendi
fix(seo): hreflang eksik dilde uretiliyordu
test(security): svg temizleme testleri eklendi
docs(db): home_sections tablosu belgelendi
```

### PR kontrol listesi
- [ ] Tek modüle dokunuyor
- [ ] Testler eklendi ve geçiyor
- [ ] Güvenlik listesi ilgili maddeleri kontrol edildi
- [ ] `DOCS.md` güncel
- [ ] Yeni bağımlılık eklenmedi
- [ ] JS kapalıyken sayfa çalışıyor

### .github/workflows/ci.yml
```yaml
name: CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: pdo_mysql, mbstring, gd, fileinfo
      - name: Sozdizimi kontrolu
        run: find app views config tests -name "*.php" -print0 | xargs -0 -n1 php -l
      - name: Testler
        run: php tests/run.php
```

---

## 16. YAPAY ZEKA ÇALIŞMA KURALLARI

`CLAUDE.md` içeriği:

1. Bu dosyayı ve `DOCS.md`'yi oku, sonra çalış.
2. Tek seferde tek modül, tek dal.
3. Dosyayı değiştirmeden önce oku. Var olan dosyanın üstüne kör yazma.
4. Composer, framework, npm paketi ekleme. Harici script ekleme.
5. Şema değişikliği `db/migrations/` altına yeni dosya olarak yazılır; `schema.sql` elle düzenlenmez.
6. Her SQL hazırlanmış ifade. Her çıktı `Security::e()`. Her POST formunda CSRF.
7. Animasyon eklerken bölüm 7'deki kuralları uygula: sadece `transform`/`opacity`, `html.js` koruması, `prefers-reduced-motion`.
8. Şablona sabit metin gömme. Her metin panelden gelmeli.
9. Modül bitince: testleri yaz, `CHANGELOG.md`'ye satır ekle, `DOCS.md`'yi güncelle.
10. Her turun sonunda söyle: hangi dosyalar değişti, ne kırılmış olabilir, elle hangi test numaraları çalıştırılmalı.
11. "Test ettim, çalışıyor" deme. Test dosyasını yaz; çalıştırma insana aittir.

**Dur ve sor:** şema değişikliği gerekiyorsa, çekirdek sınıf imzası değişecekse, bir güvenlik kuralı işi zorlaştırıyorsa, bir animasyon performans hedefini aşıyorsa.

---

## 17. FAZ PLANI VE TESLİM LİSTESİ

| Faz | İçerik | Bitiş şartı |
|-----|--------|-------------|
| 0 | Repo iskeleti, CLAUDE.md, DOCS.md, .gitignore, CI | CI yeşil |
| 1 | Database, Router, Security, Session, Auth, Logger, kurulum | S-01…S-13 geçiyor |
| 2 | Panel iskeleti, kullanıcılar, ayarlar, işlem günlüğü | giriş ve rol kontrolü çalışıyor |
| 3 | Diller, sayfalar, çeviriler, menü, şablon motoru | F-01…F-05 geçiyor |
| 4 | Medya, WebP, varyantlar, alt metin | F-06, F-07 geçiyor |
| 5 | Ön yüz şablonları ve animasyon motoru | A-01…A-10, P-01…P-07 geçiyor |
| 6 | Anasayfa bölüm yöneticisi, ilçe haritası | F-14…F-16 geçiyor |
| 7 | SEO modülü, sitemap, hreflang, içerik skoru | O-01…O-08 geçiyor |
| 8 | Yönlendirme ve 404 yönetimi | F-03, F-11, F-12 geçiyor |
| 9 | Form, dönüşüm takibi, KVKK | F-08, F-09, S-14…S-16 geçiyor |
| 10 | Referans, blog, SSS | tüm F testleri geçiyor |
| 11 | İstatistik, yedekleme, cron | F-19 geçiyor |
| 12 | İçerik girişi ve yayın | canlı |

### Yayın öncesi teslim listesi
- [ ] SSL ve `https` yönlendirmesi çalışıyor
- [ ] `www` tercihi tek yönde sabit
- [ ] `display_errors` kapalı
- [ ] `/install` erişilemez
- [ ] Panel şifresi güçlü
- [ ] Form test edildi, e-posta ulaşıyor, spam klasörü kontrol edildi
- [ ] `sitemap.xml` Search Console'a gönderildi
- [ ] Analytics bağlandı
- [ ] Yapısal veri Rich Results testinden hatasız geçti
- [ ] 404 sayfası düzgün
- [ ] Favicon ve OG görseli var
- [ ] Demo içerik temizlendi
- [ ] NAP bilgileri Google İşletme Profili ile birebir aynı
- [ ] Otomatik yedek cron'u kuruldu ve bir kez geri yüklendi
- [ ] Tüm test grupları (U, S, F, A, P, E, O) çalıştırıldı ve geçti
