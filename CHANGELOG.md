# Değişiklik Günlüğü

Bu dosya [Keep a Changelog](https://keepachangelog.com/tr/1.0.0/) biçimini izler.

## [Yayınlanmamış]

### Eklendi
- Faz 0: Depo iskeleti, `CLAUDE.md`, `DOCS.md`, `.gitignore`, CI iş akışı.
- Faz 1: Çekirdek sınıflar — `Config`, `Database`, `Router`, `Request`,
  `Response`, `Session`, `Auth`, `Security`, `Validator`, `View`, `Lang`,
  `Settings`, `Mailer`, `Logger`, `Migrator`, `Seeder`, `App`.
- Faz 1: `db/schema.sql` (29 tablo), `tools/migrate.php`, `/install` sihirbazı.
- Faz 1: Hata görünümleri (403, 404, 419, 500, 503), `robots.txt` çıktısı.
- Faz 1: `lang/tr.php`, `en.php`, `de.php`, `ar.php` arayüz dizeleri.
- Faz 1: `site.css` bölüm 1 — tasarım belirteçleri ve sistem sayfası stilleri.
- Faz 1 testleri: U-01…U-05, U-07, U-12, U-15 ve S-01…S-13, S-17.
- Faz 2: Panel iskeleti — `Admin\Controller` temel sınıfı (oturum, rol ve
  CSRF kapıları), giriş/çıkış, pano, kullanıcı yönetimi, ayarlar,
  işlem günlüğü ekranı.
- Faz 2: `admin.css` ve `admin.js`; panelde satır içi script yok.
- Faz 2 testleri: S-09, S-10 ve panel davranis testleri.
- Faz 3: `Model` tabanı (çeviri deseni, çakışmayan slug), `Page`, `MenuItem`,
  `Language`, `Redirect` modelleri.
- Faz 3: `Seo` sınıfı — meta üretimi, hreflang seti, yapısal veri ve içerik
  skoru (kelime eşikleri, H1, meta, alt metin, iç link, ilçe benzerliği).
- Faz 3: Panel sayfa yöneticisi (tür filtresi, dil sekmeleri, tam SEO paneli,
  içerik skoru, Google sonuç önizlemesi) ve menü düzenleyici.
- Faz 3: On yüz düzeni, `head`/`header`/`footer` parçaları, `page`, `service`,
  `location`, `sector` şablonları, kırıntı yolu, SSS ve çağrı bandı.
- Faz 3: Slug değişiminde otomatik 301; `NotFoundController` yönlendirme
  çözümü ve 404 kaydı.
- Faz 3: Bakım modu `App::handle` içinde devrede.
- Faz 3 testleri: U-06, U-09…U-11, U-13, F-01…F-05, F-10.
- Faz 4: `Media` sınıfı — MIME doğrulamalı güvenli yükleme, çift uzantı
  reddi, tahmin edilemez dosya adı, `uploads/YYYY/MM/` klasörlemesi,
  thumb/medium/large + WebP varyantlari, SVG temizliği, kullanım yeri
  çözümlemesi ve güvenli silme.
- Faz 4: Panel medya kitaplığı — çoklu yükleme, izgara görünüm, alt metni
  eksik uyarı rozeti, varyant tablosu, kullanımdaki dosya için silme onayı.
- Faz 4 testleri: U-08, S-05, S-06, F-06, F-07.
- Faz 5: `site.css` bölüm 2-15 — üst menü, kahraman, sektör şeridi, hizmet
  kartları, bölge haritası, süreç, referanslar, SSS, çağrı bandı, alt bilgi,
  animasyon katmanı, duyarlı davranis, azaltılmış hareket ve yazdırma.
- Faz 5: `site.js` animasyon motoru — IntersectionObserver ile görünürlük,
  rAF ile sınırlandırılmış passive scroll, ilerleme çubuğu, sabit üst menü,
  şekil sürüklenmesi, bölge haritası çizimi, kesintisiz sektör şeridi.
- Faz 5: Anasayfa şablonu ve bölüm parçaları (hero, strip, cards, coast,
  steps, works), `HomeSection`, `District`, `Project`, `Faq` modelleri,
  `HomeController`.
- Faz 5: Favicon, apple-touch-icon ve varsayılan OG görseli.
- Faz 5: `tools/browser/animation-check.mjs` — A testlerinin tarayıcıdaki
  karşılığı (istege bağlı, CI'da zorunlu değil).
- Faz 5 testleri: A-01…A-10, E-03, E-07, O-01, F-14, F-15, F-16.

### Düzeltildi (faz 5)
- `Router` özel alt desenli yer tutucular: `preg_quote` alt deseni de
  kaçırdığı için `{id:[0-9]+}` ve `{slug:[^/]+}` hiçbir zaman eşleşmiyordu;
  iç sayfalar ve kimlik alan panel ekranları açılmıyordu.
- `.skip-link` `top` geçişi ve üst menü `padding` geçişi bölüm 7.1 kural 1'i
  ihlal ediyordu; ikisi de kaldırıldı.
- 360 px genişlikte üst menü taşıyordu; panel düzeni yeniden kuruldu.
- Boş `db/migrations` klasörü git'te tutulmadığı için temiz klonda iskelet
  denetimi kalıyordu; açıklamalı `.gitkeep` eklendi.

### Eklendi (faz 6)
- Panel anasayfa yöneticisi: bölüm listesi, aç/kapat anahtarı, bölüm başına
  düzenleme ekranı, kahraman bölümünde canlı önizleme.
- Bölge haritası düzenleyicisi: ilçe noktaları sürükle-bırak ile
  konumlandırılır, `map_x`/`map_y` otomatik hesaplanır; klavye ile de
  taşınabilir, JavaScript kapalıyken sayı alanlarından girilebilir.
- Faz 6 testleri: F-14, F-15, F-16 panel tarafı.

### Eklendi (faz 7)
- `sitemap.xml` dinamik üretimi: yalnızca yayınlanmış ve dizine girmesine
  izin verilen içerik, `lastmod` alanı, `xhtml:link` ile dil karşılıkları.
- Panel SEO ekranı: `robots.txt` düzenleyici, sitemap durumu, varsayılan
  meta şablonu, Search Console alanı, tüm sayfaların meta durumu tablosu.
- `public/.htaccess` içinde `www` kanonikleştirmesi; tercih tek yönde sabit.
- Faz 7 testleri: U-14, F-13, O-01…O-08.

### Eklendi (faz 8)
- Panel yönlendirme ekranı: liste, elle ekleme, döngü kontrolü, silme.
- 404 listesi ve tek tıkla yönlendirmeye dönüştürme; çevrilen kayıt
  listeden düşer ve isabet sayacı islemeye başlar.
- Dil önekli adresler için öneksiz yönlendirme kaydı da çözülür.
- Faz 8 testleri: F-11, F-12, S-18 ve yönlendirme zinciri denetimleri.

### Eklendi (faz 9)
- Teklif formu: ad, telefon, e-posta, hizmet, mesaj, KVKK onayı, honeypot,
  zaman damgası ve CSRF token.
- Spam koruması: honeypot sessiz reddi, 3 saniye alt sınırı, IP başına
  saatlik gönderim sınırı, sunucu tarafı doğrulama.
- `source_url`, `referrer`, UTM, dil, IP ve tarayıcı otomatik saklanır.
- Teşekkür sayfası ayrı adreste ve `noindex`.
- Panel form ekranı: durum etiketleri, not alanı, kaynak sayfa, dönüşüm
  raporu, kaynak dağılımı, CSV dışa aktarma, saklama süresi temizliği.
- `tools/purge_submissions.php` günlük görevi.
- Faz 9 testleri: F-08, F-09, S-14, S-15, S-16, E-06.

### Eklendi (faz 10)
- Referanslar: liste, detay, galeri, ilçeye göre ilgili çalışmalar,
  `CreativeWork` şeması; panel ekranı ile müşteri adı, sektör, ilçe, canlı
  site linki, görseller ve yapılan işler.
- Blog: liste, kategori süzgeci, sayfalama, yazı sayfası, `Article` şeması;
  ileri tarihli yayın tarihi gelene kadar görünmez ve haritaya girmez.
  Slug değişiminde otomatik 301.
- SSS: `/sss` sayfası, sayfa ve anasayfa atamaları, `FAQPage` şeması.
- Faz 10 testleri: referans, blog ve SSS davranis testleri.

### Eklendi (faz 11)
- `Visits`: ziyaret kaydı, bot işaretleme, günlük seri, en çok girilen
  sayfalar, referans kaynakları, cihaz ve dil dağılımı, aylık CSV rapor,
  90 günden eski kayıtların `visits_daily`'ye toplanmasi.
- `Backup`: harici araç gerektirmeyen gzip'li SQL yedeği, son 10 yedek,
  indirme, geri yükleme (öncesinde otomatik güvenlik yedeği).
- Panel istatistik ve yedekleme ekranları.
- `tools/backup.php` ve `tools/rollup_visits.php` günlük görevleri.
- Faz 11 testleri: U-15 bot ayrımı, F-19 yedek/geri yükleme, toplama.

### Eklendi (faz 12)
- `db/seed/` altında başlangıç içeriği: bölüm 4'teki URL haritasının tamamı
  (8 ana sayfa, 6 hizmet, 8 ilçe, 6 sektör), 8 referans ve 23 SSS kaydı.
- İlçe sayfaları bölüm 4.7'ye uygun: her biri 500+ kelime özgün metin,
  kendi ilçesine ait referans, ilçeye özel SSS. Sayfalar arası en yüksek
  örtüşme 0.28 (eşik 0.70).
- `tools/seed_content.php`: içeriği yazar, var olan kayıtların üzerine
  yazmaz; `--dry` ve `--force` seçenekleri.
- `tools/preflight.php`: bölüm 17'deki yayın öncesi teslim listesinin
  makine tarafından denetlenebilir maddelerini kontrol eder.
- `tools/browser/animation-check.mjs` taşınabilir hale getirildi
  (`PLAYWRIGHT_PATH`, `CHROMIUM_PATH`).
- Faz 12 testleri: URL haritası bütünlüğü, ilçe kelime ve benzerlik
  kuralları, referans/SSS kapsaması, tohum içeriğinin güvenliği.

### Düzeltildi
- `Router` yer tutucu deseni: `preg_quote` süslü parantezleri kaçırdığı için
  `{slug}` eşleşmiyordu.
- `Security::toPlainText` etiket sınırına boşluk koymuyordu; `</h2><p>`
  geçişindeki iki kelime birleşip kelime sayımı eksik çıkıyordu.

### Değiştirildi (Türkçe yazım)
- Görünür metnin tamamı Türkçe aksanlı harflerle yazıldı: `lang/tr.php`,
  ön yüz ve panel şablonları, `app/` içindeki doğrulama ve akış mesajları,
  `db/seed/` altındaki başlangıç içeriği ve test adları. Böylece bölüm 4'teki
  anahtar kelime tablosu ("edremit web tasarım", "balıkesir web tasarım",
  "web sitesi fiyatları") sayfa başlıklarıyla birebir örtüşüyor.
- Slug'lar ASCII kaldı; `/edremit-web-tasarim`, `/gizlilik-politikasi` gibi
  adresler bölüm 4'teki hâliyle değişmedi.
- `lang/de.php` Almanca metinleri `ae/oe/ue/ss` yerine `ä/ö/ü/ß` kullanıyor.
- `README.md`, `CHANGELOG.md` ve `CLAUDE.md` de aynı yazıma çevrildi;
  kod blokları ve komutlar dokunulmadan bırakıldı.

- Yeni testler (F-P13-a…e): arayüz dizelerinin aksanlı olması, Almanca
  umlautlar, tohum başlıklarının anahtar kelimelerle örtüşmesi, slug'ların
  ASCII kalması ve SQL anahtar kelimeleriyle regex bayraklarının bozulmaması.

### Düzeltildi (Türkçe yazım turu)
- `tools/preflight.php` sütun hizalaması bayt sayısına göre yapılıyordu;
  çok baytlı harflerde tablo kayıyordu, `mb_strlen` ile düzeltildi.
- Kaynaktaki yazım hataları giderildi: `orne` → `örnek`, `taniten` → `tanıtan`,
  `suredan` → `süreden`, `baslikten` → `başlıktan`, `genisligde` → `genişlikte`.

### Değiştirildi (örnek site sunumu)
- `project` kayıtları ön yüzde "Örnek Siteler" olarak sunuluyor. Gerçek müşteri
  işleri yayına girene kadar teslim edilmiş iş izlenimi vermemesi için hem
  etiketler hem tohum içeriği yeniden yazıldı.
- Adres bölüm 4'teki gibi `/referanslar` ve `/referanslar/{slug}` kaldı; gerçek
  işler eklendiğinde yönlendirme gerekmiyor, yalnızca etiket geri çevriliyor.
- Yeni ayar **Örnek site notu** (`projects_notice`): liste, detay, anasayfa
  bloğu ve ilçe sayfasındaki blokta görünen açıklama. Panelden boşaltılınca
  not kendiliğinden kayboluyor — gerçek işler eklenince kapatma yolu bu.
- `views/front/partials/notice.php` ve `.notice` stili eklendi; metin
  şablona gömülmüyor, ayardan geliyor (CLAUDE.md kural 8).
- Yeni testler F-P14-a…d: notun görünmesi, ayar boşken kaybolması, etiket ve
  adresin doğru kalması, tohum kayıtlarının teslim edilmiş iş iddiası
  taşımaması.

### Düzeltildi
- Türkçe yazım turunda diş hekimliği bağlamındaki üç yer "dış" olmuştu:
  "Örnek Diş Polikliniği", "Edremit diş kliniği", "diş hekimi".

### Eklendi (blog içeriği ve marka görselleri)
- `db/seed/posts.php`: altı başlangıç blog yazısı — yerel SEO ve İşletme
  Profili, site hızı ve mobil kullanım, form dönüşümü, çoklu dilde yayın,
  yedekleme ve bakım, sezonluk içerik takvimi. Her biri 300+ kelime, kendi
  kategorisi, özeti, meta alanları ve iç linkleriyle.
- `tools/seed_content.php` blog yazılarını da yazıyor; var olan kaydın
  üzerine yazmıyor. Kapak görseli bilerek boş bırakıldı — işletme kendi
  fotoğrafını Medya ekranından yükleyip yazıya bağlar.
- Marka görselleri: `logo-mark.png` (üst menü işareti), `logo-wordmark.png`
  (alt bilgi ve panel, saydam zeminli), `favicon-32.png`,
  `apple-touch-icon.png` ve yazılı logoyla üretilmiş `og-default.png`.
  Yer tutucu `favicon.svg` kaldırıldı, tüm şablonlar PNG faviconu gösteriyor.
- Yeni testler F-P15-a…e: blog tohumunun kelime sayısı, meta alanları, iç
  link ve güvenli işaretleme denetimi; marka görsellerinin varlığı ve
  şablonlarda bağlanmış olması.

### Değiştirildi (dil yayın anahtarı)
- Kurulum artık yalnızca varsayılan dili açık bırakıyor. Önceden EN/DE/AR
  açıktı ama çevirileri yoktu; üst menüde görünüyor, tıklayan ziyaretçi Türkçe
  içeriğe düşüyordu. Kapalı dil üst menüde görünmüyor, `hreflang` setine
  girmiyor, önekli adresleri 404 dönüyor.
- Ayarlar ekranına **Yayındaki diller** anahtarı eklendi; varsayılan dil her
  zaman açık kalıyor ve kapatılamıyor. `Lang::allLanguages()` kapalılar dahil
  tüm dilleri veriyor.
- Yeni testler F-P16-a…d. Çok dilli davranışı sınayan mevcut testler (F-04,
  F-05, F-P8-c) ihtiyaç duydukları dili `arc_activate_langs()` ile açıyor.

### Eklendi (işletme bilgileri)
- Gerçek NAP bilgileri varsayılanlara yazıldı: adres (Tuzcumurat Mah. 27016 Sk.
  Uysal Apt. No: 5 Kat: 3 Daire: 8, Edremit / Balıkesir), telefon
  (+90 545 946 50 73) ve e-posta (info@arcatesyazilim.com). Telefon uluslararası
  biçimde saklanıyor; `tel:` bağlantısı ve yapısal veri bu biçimi bekliyor.
- `config/config.example.php` içindeki örnek alan adı ve e-posta adresleri
  `arcatesyazilim.com` olarak güncellendi.
- Posta kodu hâlâ boş; işletme girecek.
