# Değişiklik Günlüğü

Bu dosya [Keep a Changelog](https://keepachangelog.com/tr/1.0.0/) biçimini izler.

## [Yayınlanmamış]

### Değiştirildi (12 Eylül 2026 — B4 parity bütçeleri referans ölçülerine yükseltildi)
- Site sahibinin kararıyla `reference-parity-check`teki beş bütçe, referansın kendi ölçülerine göre yükseltildi. Gerekçeleri (maket koordinatı → 1440 karşılığı) kontrol dosyasına yazıldı:
  - içerik rayı 1250–1290 → 1200–1250 (referans oranı %85.1, 1440'ta 1225px)
  - metrik hücresi 56–72 → 70–88 (referans ~81px)
  - hizmet kartı 130–165 → 165–215 (referans ~190px)
  - proje kartı 130–175 → 160–200 (referans ~179px)
  - sayfa yüksekliği < 2050 → < 2150 (bağımsız ölçüm değil, yukarıdakilerin toplamı)
- Metrik hücresi referans ölçüsüne çıktı: başlık 20 → 26px, ikon 35 → 37px, hücre 66 → 81px.
- Hizmet kartları referans ölçüsüne çıktı: ikon 27 → 34px, ikon altı boşluk 10 → 24px, başlık ve açıklama büyüdü; kart 156 → ~196px.
- Anasayfa içerik rayı 1280 → 1225px; logo ve tüm bölümler referanstaki kenar boşluğuna (1440'ta 107px) oturdu.
- Metrik etiketi 13px'te bırakıldı: referansın etiketleri kısa, bizimkiler uzun; 15px'te ikinci satıra kaçıp hücreyi 81 → 102px yapıyordu.
- Ölçülen her değer `@media (min-width: 941px)` altında; mobil korundu.

### Değiştirildi (12 Eylül 2026 — B3 bölüm başlıkları ve metrik bandı)
- Sağ tarafında bağlantı olan bölümlerde ("Hizmetlerimiz", "Öne Çıkan Projeler") başlık ve alt yazı referanstaki gibi tek satırda, taban hizasında. Bağlantısız bölümler alt alta kalıyor — referans da öyle.
- Bölüm başlığı 25.2 → 28.8px, alt yazı 11 → 15.8px.
- Metrik hücresinin başlığı 15 → 20px, ikonu 31 → 35px; ikon `svg`'si ayrıca sabitlenmişti, o da açıldı.
- Hizmet kartlarına dokunulmadı: referans kart 190px, parity bütçesi 130–165px. Yalnızca ikonu büyütmek bile CI'da bütçeye 6px bırakıyordu ve kart yüksekliği bu makinede yerel olarak doğrulanamıyor.
- Ölçülen her değer `@media (min-width: 941px)` altında; mobil değerler korundu.

### Değiştirildi (12 Eylül 2026 — B2 kahraman referans uyumu)
- Başlık satır yüksekliği 1.01'den 1.16'ya çıkarıldı; satırlar neredeyse birbirine değiyordu.
- Paragraf 13px'ten 15.8px'e, genişliği referanstaki 489px'e getirildi.
- Birincil çağrı butonu gradyan yerine düz `#0E77FE` (üst menüdeki butonla aynı mavi); yükseklik 42'den 48px'e. İkincil butonun çerçevesi `#184982`.
- Buton okları ve onay işaretleri metin glifi yerine satır içi SVG oldu; ikincil buton referanstaki gibi daire içinde ok taşıyor.
- Onay işaretlerindeki çember kaldırıldı — kapalı bir onay kutusu gibi okunuyordu; referansta düz mavi tik var.
- Ölçülen her değer `@media (min-width: 941px)` altında: referansın telefon maketinde kahraman metni görünmüyor, mobilde ölçülecek referans yok.

### Değiştirildi (11 Eylül 2026 — B1 üst menü referans uyumu)
- Üst menü referans görselden piksel olarak ölçülen değerlere getirildi: bant 66px, marka 212×50, öğe aralığı 42px, öğe yazısı 13px/500, çağrı butonu 114×36 düz `#0E78FE` ve ok. Değerler `vw` cinsinden yazıldı; referansın kendi genişliğinde (≈1863px) birebir, dar ekranda orantılı küçülür.
- Menüye "Ana Sayfa" öğesi eklendi. Referans düzende açık sayfa işareti bu öğenin üzerinde duruyor; öğe olmadan işaret anasayfada hiç görünmüyordu.
- Açık sayfa işareti ölçülen renklere alındı: yazı `#5AC8F5`, alt çizgi `#3988BC`. Açılır menüde alt çizgi yerine sol şerit.
- Menü 1200px üstünde ekran ortasına hizalandı; akış içinde ortalandığında marka genişliği kadar sağa kayıyordu.
- Anasayfada bant alt çizgisi kaldırıldı, zemine referanstaki ince diyagonal ışık huzmeleri eklendi (ölçülen şiddet, dekoratif).
- Mobil bantta marka, çağrı butonu ve açma düğmesi referans oranlarına getirildi; açma düğmesinin kutusu kaldırıldı. Bant yüksekliği 66px'lik mevcut bütçede bırakıldı: telefon maketinden güvenle okunamıyor.

### Düzeltildi (11 Eylül 2026 — B1 üst menü)
- Açma düğmesinin çubukları açık tema rengiyle (`--fg`, `#062244`) koyu bant üzerine çiziliyordu; ölçülen kontrast 1.2:1 idi, yani mobil menü düğmesi pratikte görünmüyordu. Çubuklar beyaza alındı.
- `reference-header.css` sayfaya özel stillerden önce yükleniyordu; `.is-reference-home` önekli kurallar (0,2,0) menü katmanını geri eziyordu. Menü stili son katmana taşındı.
- İç sayfalarda panel düzeni yalnızca `.is-reference-home` altında tanımlıydı; menü akıştan çıkınca yan blok sola düşüp menünün üzerine biniyordu. Düzen her sayfa için kuruldu.
- `F-10` testi menü satırlarının tümünü silip geri koymuyordu; sonraki testler boş menüyle çalışıyordu. Test kendi izini temizliyor.

### Düzeltildi (11 Eylül 2026 referans ikonları)
- Ana sayfa bilgi kartlarındaki fonta bağlı semboller yerel SVG ikonlarla değiştirildi; hizmet/süreç/avantaj ikonları aynı çizgi ve sabit mavi ışık ailesine alındı.
- Süreç ikonları eklendi; masaüstü numaraları ikon altına taşındı. Kolonlar CMS'deki gerçek adım sayısını izler.
- 641–940px aralığında ters kalan proje metin/görsel sırası düzeltildi; mobil hizmet ikonunun alt boşluğu sıfırlandı.
- 320/390/768/1024/1440px için JS kapalı ve reduced-motion ikon/gölge, proje sırası ve taşma tarayıcı kontrolleri eklendi.


### Düzeltildi (10 Eylül 2026 canlı arayüz denetimi)
- HEAD istekleri GET rotasına eşlenir; geçerli sayfaların izleme araçlarına yanlış 404 dönmesi önlenir.
- İç sayfalarda eksik kalan ortak Teklif Al eylemi, footer sütunları ve yasal bağlantılar paneldeki header/footer içeriğinden yüklenir; açık null/boş tercihler korunur.
- Eksik formda gönder butonu erişilebilir kalır; ilk hatalı alana odak ve tarayıcının açıklaması gösterilir. Kilit yalnız gönderim sürerken uygulanır, geri dönüşte açılır.
- Koyu CTA bandındaki WhatsApp eylemine okunabilir beyaz metin ve belirgin sınır verildi.
- Blog kategori seçimi aria-current ile bildiriliyor.
- Örnek site notu ayarı hiç bulunmayan eski kurulumlarda varsayılan açıklama gösterilir; bilerek boş bırakılmış ayar korunur.
- Ortak kabuk için PHP regresyonu; form, kategori ve koyu CTA için Chromium kontrolleri eklendi.


### Eklendi (R3 G-01 özgün hero görseli)
- Web ve mobil yüzeyleri Arcates mavisi bağlantı formuyla anlatan özgün sahne ve PNG ana kaynak eklendi.
- 640/1280 WebP varyantları, responsive kaynak seçimi ve sabit 4:3 oranı eklendi; ana kaynak sayfada yüklenmez.
- Eski CSS/DOM çizim sahnesi ve artık kullanılmayan 3D kontrol stilleri kaldırıldı.
- Tarayıcı kontrolü dosyanın gerçekten yüklenip çözüldüğünü doğrular; reduced-motion ve JS kapalı kontroller yeni sahneyi izler.
- R3 varlık durumu kaydedildi; tarihsel tasarım planının yanıltıcı T1 başlangıç yönlendirmesine güncel durum notu eklendi.


### Eklendi (tam arayüz yeniden tasarımı — R0/R1/R4)
- `REDESIGN-R0-INVENTORY.md` ile route, şablon, CSS/JS, statik varlık ve canlı medya sınırları kaydedildi.
- Ziyaretçi sitesi için ortak semantik renk/yüzey rolleri ve 14px kontrol / 22px büyük yüzey sistemi eklendi.
- Koyu header/footer, gerçek açık logo işareti, erişilebilir mobil navigasyon ve ortak buton/form foundation görünümü eklendi.
- Teklif formuna sunucu hata özeti, `aria-invalid`, `aria-describedby`, alan hata kimlikleri, telefon `inputmode` ve yerelleştirilmiş gönderiliyor durumu eklendi.
- Panel için koyu navigasyon + açık çalışma zemini ve 940px altında Escape/backdrop/focus-return destekli off-canvas navigasyon eklendi.
- Foundation sözleşmesini koruyan `F-RD-01…F-RD-10` regresyon testleri eklendi.

### Değiştirildi (R5 tam ana sayfa yeniden tasarımı)
- Ana sayfa sabit akışı `hero → sektör grubu → hizmetler → süreç → örnek siteler → hizmet bölgeleri → SSS → CTA` olarak yeniden kuruldu; header/footer kabukta kalıyor.
- Hero'daki eski daire/kare/üçgen/şekil parallax kümesi kaldırıldı; yerine sahte metrik veya müşteri verisi taşımayan masaüstü web ekranı + mobil ekran + Arcates bağlantı yayı sahnesi geldi.
- Sonsuz kayan sektör şeridi kaldırıldı. Yayındaki gerçek `sector` sayfaları statik ve gerçek URL'li kartlara dönüştürüldü; panel etiketi yalnız bağlantısız fallback olarak kaldı.
- Hizmetler tek mavi görsel aileye, süreç 1–2–3 bağlı yüzeye, örnek siteler bir büyük + destekleyici kart vitrinine geçirildi.
- Bölge SVG'si görünür biçimde temsili bağlantı grafiği olarak tanımlandı; gerçek ilçe iç linkleri ve HTML eşdeğeri korundu.
- SSS iki kolonlu sakin düzene, son CTA koyu dönüşüm yüzeyine geçirildi. WhatsApp hedefi sabit numara yerine yalnız NAP telefonundan üretiliyor.
- `home-redesign.css` yalnız anasayfada yüklenen responsive/RTL/reduced-motion katmanı olarak eklendi.
- `site.js` içinden R5'te artık kullanılmayan `HERO_SETTLE`, hero shape motoru, parallax ve moving-strip davranışları kaldırıldı; reveal/header/coast/TOC/sticky CTA/form davranışları korundu.
- Hero hareketi 600–700ms girişler ve en fazla 240ms gecikmeyle 1.05 saniye içinde yerleşecek şekilde sadeleştirildi; R5 anasayfada sonsuz animasyon kalmadı.
- `HomeSection` ve yeni seed sırası R5 ziyaretçi sırasıyla eşitlendi; eski kurulum sort değerleri PHP tarafında kanonik sıraya normalize ediliyor.
- TR/EN/DE/AR arayüzlerine proje görüntüleme ve temsili bölge grafiği açıklamaları eklendi.
- `A-*`, `F-P5-*`, `F-R5-*` testleri ve gerçek Chromium `animation-check.mjs` yeni DOM/hareket sözleşmesine geçirildi.

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
  karşılığı.
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
- `tools/preflight.php`: yayın öncesi teslim listesinin makine tarafından
  denetlenebilir maddelerini kontrol eder.
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
  `db/seed/` altındaki başlangıç içeriği ve test adları.
- Slug'lar ASCII kaldı; `/edremit-web-tasarim`, `/gizlilik-politikasi` gibi
  adresler değişmedi.
- `lang/de.php` Almanca metinleri `ae/oe/ue/ss` yerine `ä/ö/ü/ß` kullanıyor.
- `README.md`, `CHANGELOG.md` ve `CLAUDE.md` aynı yazıma çevrildi; kod
  blokları ve komutlar dokunulmadan bırakıldı.
- Yeni testler (F-P13-a…e): arayüz dizelerinin aksanlı olması, Almanca
  umlautlar, tohum başlıklarının anahtar kelimelerle örtüşmesi, slug'ların
  ASCII kalması ve SQL anahtar kelimeleriyle regex bayraklarının bozulmaması.

### Düzeltildi (Türkçe yazım turu)
- `tools/preflight.php` sütun hizalaması bayt sayısına göre yapılıyordu;
  çok baytlı harflerde tablo kayıyordu, `mb_strlen` ile düzeltildi.
- Kaynaktaki yazım hataları giderildi.

### Değiştirildi (örnek site sunumu)
- `project` kayıtları ön yüzde "Örnek Siteler" olarak sunuluyor. Gerçek müşteri
  işleri yayına girene kadar teslim edilmiş iş izlenimi vermemesi için hem
  etiketler hem tohum içeriği yeniden yazıldı.
- Adres `/referanslar` ve `/referanslar/{slug}` kaldı.
- Yeni ayar **Örnek site notu** (`projects_notice`) liste, detay, anasayfa ve
  ilçe bloklarında gösteriliyor; panelden boşaltılınca kayboluyor.
- `views/front/partials/notice.php` ve `.notice` stili eklendi.
- Yeni testler F-P14-a…d eklendi.

### Düzeltildi
- Türkçe yazım turunda diş hekimliği bağlamındaki üç yer düzeltildi:
  "Örnek Diş Polikliniği", "Edremit diş kliniği", "diş hekimi".

### Eklendi (blog içeriği ve marka görselleri)
- `db/seed/posts.php`: altı başlangıç blog yazısı; her biri 300+ kelime,
  kategori, özet, meta ve iç linklerle.
- `tools/seed_content.php` blog yazılarını da yazar; var olan kaydın üzerine yazmaz.
- Marka görselleri: `logo-mark.png`, `logo-wordmark.png`, `favicon-32.png`,
  `apple-touch-icon.png` ve `og-default.png`.
- Yeni testler F-P15-a…e eklendi.

### Değiştirildi (dil yayın anahtarı)
- Kurulum yalnızca varsayılan dili açık bırakıyor. EN/DE/AR çeviri girildikten
  sonra Ayarlar ekranındaki **Yayındaki diller** anahtarından açılıyor.
- Varsayılan dil kapatılamaz; kapalı dil üst menü/hreflang içinde görünmez.
- Yeni testler F-P16-a…d eklendi.

### Eklendi (işletme bilgileri)
- Gerçek NAP varsayılanları: Tuzcumurat Mah. 27016 Sk. No: 5, Edremit / Balıkesir;
  +90 545 946 50 73; info@arcatesyazilim.com.
- `config/config.example.php` alan adı ve e-posta `arcatesyazilim.com` olarak güncellendi.
- Posta kodu işletme tarafından girilecek şekilde boş bırakıldı.

### Düzeltildi (kurulmuş siteler için ayar göçü)
- Varsayılan değer değişikliklerinin kurulu siteye ulaşması için
  `db/migrations/2026_09_08_0001_ayar_ve_dil_uyumlastirma.sql` eklendi.
- Göç yalnız eski varsayılan/boş değere dokunur; elle girilmiş veriyi korur.
- Eksik `projects_notice` eklenir ve çevirisi olmayan dil yayından düşer.
- Yeni testler F-P17-a…e eklendi.

### Eklendi (kahraman sahnesi)
- Referans görseldeki laptop üstü yüzen katmanlar geldi: sol üstte kart, laptop
  tabanında iki rozet, sağda beş maddelik yetkinlik rayı. Tamamı
  `ref-hero__visual` içinde, yani `aria-hidden` — ekran okuyucuya bilgi
  eklemiyor, içeriğin tekrarı değil.
- `public/assets/css/reference-hero-scene.css` eklendi ve anasayfa stil
  listesine son katman olarak girdi. Giriş animasyonu yalnızca `transform` ve
  `opacity` kullanıyor, gizli başlangıç `html.js` altında, `prefers-reduced-motion`
  altında kapanıyor (DOCS.md 7.1).
- Sahne metni panelden geliyor (`hero.scene`): kart başlığı/metni, iki rozet,
  beş ray maddesi. Boşaltılan parça hiç basılmıyor.
- Tohumdaki değerler işletmenin arkasında durabileceği ifadeler: "8 ilçe /
  Yerinde görüşme", "Aynı hafta / Fiyat ve takvim" ve yayındaki beş hizmet.
  Referans kurgudaki "248+ tamamlanan proje", "%98 müşteri memnuniyeti" ve
  "7/24 destek" gibi doğrulanamayan iddialar **bilerek alınmadı**; F-HS-b
  bunu test olarak koruyor.
- Yeni göç `db/migrations/2026_09_11_0001_kahraman_sahne_icerigi.sql`
  sahneyi kurulmuş sitelere taşıyor. `JSON_MERGE_PATCH` ile yalnızca `scene`
  alanı hiç yoksa yazıyor; elle girilmiş sahne korunuyor, iki kez
  çalıştırıldığında sonucu değiştirmiyor.

### Düzeltildi (panel kaydında sessiz veri kaybı)
- `Admin\HomeController::readContent()` bölümün içeriğini beyaz listeden
  yeniden kuruyordu; formda alanı olmayan her veri, ilgili bölüm her
  kaydedildiğinde siliniyordu. Kahraman sahnesi de ilk başlık düzenlemesinde
  kaybolacaktı.
- `update()` artık saklanan içeriği `readContent()`'e veriyor; yeni
  `readScene()` gönderilen sahneyi temizleyip sınırlıyor, form sahne
  göndermediğinde saklanan değeri olduğu gibi bırakıyor. Beyaz liste
  disiplini korundu.
- Yeni testler F-HS-a…f: tohum içeriği, uydurma iddia denetimi, sayfada
  basılması, panelden boşaltılınca kaybolması, göçün yalnızca eksik sahneye
  dokunması ve panel kaydının sahneyi silmemesi.

### Düzeltildi (çalışma saatlerinde İngilizce gün kodu)
- Çalışma saatleri alt bilgide, iletişim kartında ve ilçe sayfasının hızlı
  olgu şeridinde `Mo-Fr 09:00–18:00` olarak görünüyordu. `opening_hours`
  ayarı schema.org biçimini saklıyor ve bu kod doğrudan ekrana basılıyordu;
  Türkçe sayfada İngilizce gün kısaltması çıkıyordu.
- Yeni `opening_days()` yardımcısı kodu sayfanın dilindeki gün adına
  çeviriyor: `Mo-Fr` → `Pzt–Cum`, `Mo,We,Fr` → `Pzt, Çar, Cum`. Aralık ve
  virgüllü liste korunuyor.
- **Yapısal veri değişmedi.** `Seo::professionalService()` ham kodu
  kullanmaya devam ediyor; `dayOfWeek` alanında hâlâ `Mo-Fr` var. Çeviri
  yalnızca görünen metinde.
- Tanınmayan belirteç olduğu gibi geçiyor: işletme panele "Hafta içi"
  yazarsa o metin bozulmadan görünüyor.
- Gün adları dört dile de eklendi (`day_mo` … `day_su`).
- Ayarlar ekranındaki biçim ipucu, ziyaretçinin çevrilmiş etiketi gördüğünü
  açıklıyor.
- Yeni testler F-OD-a…e: dile göre çeviri, serbest metnin korunması,
  yapısal verinin ham kalması, ön yüzde gün kodunun görünmemesi ve
  şablonlarda çevrilmeden basan yer kalmaması.

### Değiştirildi (B1 — üst menü)
- Menü masaüstünde satırın ortasına alındı; marka solda, dil seçici ve
  "Teklif Al" sağda kalıyor. Referans üst menü düzeni bu.
- Açık sayfanın menü öğesi artık işaretli: masaüstünde alt çizgi, dar ekrandaki
  açılır menüde sol şerit. Ekran okuyucu için `aria-current="page"` veriliyor.
- Aktif öğe canonical adrese göre belirleniyor (`Front\Controller::markCurrent()`).
  Alt sayfadayken üst öğe açık kalıyor — `/referanslar/akcay-ornek` açıkken
  "Örnek siteler" yanıyor. Anasayfa öğesi ('/') bu kuralın dışında, aksi halde
  her sayfada yanardı.
- Menü öğeleri değişmedi; hepsi panelden geliyor.
- Yeni katman `public/assets/css/reference-header.css`, tüm sayfalara yükleniyor.
- Yeni testler F-HD-a…d.

### Düzeltildi (koyu menüde okunmayan aktif öğe)
- Aktif menü öğesine önce `var(--fg)` verilmişti; o açık tema metin rengi
  (`#062244`) ve üst menü koyu bir yüzey (`#081426`) — kontrast 1.1:1, yani
  metin görünmüyordu. Ters yüzey rolü `--fg-on-invert` ile 18.45:1'e çıktı.
- `F-HD-b` açık tema metin renginin bu dosyada kullanılmasını engelliyor.
