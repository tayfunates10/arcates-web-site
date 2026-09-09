# R3 — Özgün görseller ve devam kaydı

9 Eylül 2026.

- G-01 `main`e `5240c23d27f2102bdbc66c755b363556950182bb` ile alındı.
- G-02 `main`e PR #13 / `971ce94e0055c22f652813d581476b9c243f84f2` ile alındı.
- G-03 `main`e PR #14 / `ba350110e16fc2da8131e6f35bf75526af8ddad0` ile alındı.
- G-04/G-05 `main`e PR #15 / `ff926db04a162cdc797d9e4be3507ed0a9a1ede2` ile alındı.
- G-06/G-07 `main`e PR #16 / `81bf25622bc8b973e1159fee9e2c2f4cd94a844f` ile alındı.
- G-08/G-10 `main`e PR #17 / `09805e665fcd63dc5b795ab2691e453a422108cc` ile alındı.
- G-09 `main`e `82431fd8d5b4f4d153c35c1c49e476c28dd7a341` ile alındı.

## Nerede kalındı?

R0/R1/R4, R5 ana sayfa, R6 iç sayfalar, R7 panel/sistem kodları ve R8 otomatik
responsive/klavye/RTL/reduced-motion kabul denetimleri `main`dedir. Bu dal R3
özgün görsel ailesinin son açık parçası G-09 sosyal/OG görsel sistemi ile küçük
boyut favicon/logo kalite kontrolünü tamamlar. Canlı yayın kabulü tamamlanmış
sayılmaz.

## G-01 — hero

Özgün ana hero görseli 4:3 WebP varyantlarıyla kullanılır. Metin ve CTA panel
içeriğidir; görsel dekoratiftir ve sahte müşteri/metrik içermez.

## G-02 — altı hizmet görseli

Koyu lacivert, beyaz yüzey ve kobalt vurgu ailesindeki altı 4:3 hizmet SVG'si
ana sayfa hizmet kartlarında kullanılır. Başlık/açıklama panelden gelmeye devam eder.

## G-03 — altı sektör görseli

Gerçek müşteri/proje ekranı kanıtı bulunmadığı için sahte referans üretilmeden,
sektörler nesne metaforlarıyla anlatılır. Altı 4:3 SVG ilgili sektör sayfası hero
alanında slug eşlemesiyle kullanılır.

## G-04 — süreç

“Nasıl çalışıyoruz” alanındaki üç adım, dekoratif keşif/kurulum/büyüme vektörleri
ve bağlı akış hattıyla aynı görsel aileye taşındı. İçerik metni panelden gelir.

## G-05 — hizmet bölgesi

Dinamik bölge SVG'si koyu sahne, grid, rota derinliği ve kobalt rota diliyle
yenilendi. İlçe adı, konumu ve URL'leri panel/veritabanı verisidir; `site.js`
scroll çizim motoru korunur.

## G-06 — gerçek proje medya sunum sistemi

Repo ve seed envanteri incelendi. `db/seed/projects.php` içindeki sekiz kayıt açıkça
örnek kurgudur; `public/uploads` içinde sürüm kontrollü gerçek müşteri/proje medyası
yoktur. Bu nedenle sahte ekran görüntüsü üretilmedi. Gerçek `cover` veya `gallery`
medyası panelden yüklendiğinde ana sayfa vitrini, `/referanslar` ve proje detayında
otomatik Arcates sunum çerçevesi kullanılır.

## G-07 — blog kapak sistemi

Panelden gerçek `cover` yüklenmişse gerçek medya önceliklidir. Kapak yoksa yazının
kategorisinden türetilen metinsiz, iddiasız, soyut 16:10 Arcates editoryal kapağı
gösterilir. Başlık, tarih, sayı, müşteri veya performans iddiası görsel içine
gömülmez.

## G-08 — final CTA bağlantı yayı

Final çağrı alanı panel/ayar veri akışına dokunulmadan bağlantı yayı sahnesiyle
tamamlandı. Başlık/metin mevcut CTA içeriği veya `cta_title` / `cta_text` ayarından,
telefon ise tek NAP kaynağı olan `nap_phone` ayarından gelmeye devam eder.

## G-10 — boş, hata ve başarı durumları

Ortak `state-mark` ailesi başarı, boş/bekleme ve hata durumlarını aynı görsel dilde
toplar. `/tesekkurler`, proje/blog/SSS boş durumları ve 403/404/419/500/503
şablonları semantik metin, HTTP kodu, aksiyon ve robots davranışlarını korur.

## G-09 — sosyal/OG ve küçük boyut marka sistemi

Mevcut onaylı marka rasterları yeniden çizilmedi. Kaynak sürekliliği şu şekilde
korunur:

- `logo-mark.png`: 192×192 ana marka işareti,
- `favicon-32.png`: mevcut 32×32 küçük favicon,
- `apple-touch-icon.png`: mevcut 180×180 Apple Touch Icon,
- `logo-wordmark.png`: 560×187 onaylı wordmark,
- `og-default.png`: GD kullanılamadığı durumda güvenli 1200×630 statik fallback.

Yeni `public/assets/brand-icon.php`, yalnız 16 ve 48 px hedeflerini kabul eder.
16 px mevcut 32 px favicon kaynağından, 48 px ise 192 px ana marka işaretinden
`imagecopyresampled` ile üretilir. Böylece 16/32/48/180/192 zinciri farklı logo
geometrileri icat edilmeden tamamlanır.

Yeni `public/assets/social-card.php`, onaylı `logo-wordmark.png` kaynağını kullanarak
1200×630 PNG üretir. Kompozisyon koyu lacivert/kobalt R3 alanı, beyaz marka yüzeyi,
grid, bağlantı yayları ve nötr arayüz bloklarından oluşur. Sosyal karta dinamik
slogan, müşteri adı, sayı, puan veya performans metriği yazılmaz. Sayfaya özel gerçek
`og_image` varsa her zaman önceliklidir; bu görsel yalnız varsayılan fallback'tir.

`views/front/partials/head.php` artık:

- varsayılan OG görselini `/assets/social-card.php` üzerinden verir,
- `og:image:type`, 1200×630 boyutu, `og:image:alt` ve Twitter image metadata'sını
  tamamlar,
- 16/32/48/192 favicon hedeflerini ve 180×180 Apple ikonunu açıkça bildirir,
- Arcates koyu lacivertini `theme-color` olarak tanımlar.

## G-09 kabul

- `F-R3-09`: OG/Twitter metadata ve 16/32/48/180/192 ikon bağlantı sözleşmesini
  doğrular.
- `F-R3-09Q`: mevcut raster master boyutlarını, resample kaynaklarını, sosyal kart
  boyutunu ve harici/dinamik metin kullanılmamasını doğrular.
- `tools/browser/brand-check.mjs`: gerçek Chromium/HTTP ortamında sosyal kartın
  1200×630 PNG, faviconların gerçek 16/32/48/192, Apple ikonunun 180×180 olduğunu,
  MIME ve byte bütçelerini, header/footer'ın onaylı logo kaynaklarını ve geçersiz
  favicon boyutunun reddedildiğini doğrular.
- Mevcut R8 PHP/MySQL ve diğer Chromium regresyon kapıları çalışmaya devam eder.

## Açık işlerin sırası

1. Tam R8 regresyonu doğrulandı: `82431fd8d5b4f4d153c35c1c49e476c28dd7a341`, [CI 34387612830](https://github.com/tayfunates10/arcates-web-site/actions/runs/34387612830). PHP/MySQL ve Chromium işleri başarılı; atlanan PHP testini reddeden kapı geçti.
2. Gerçek üretimde LCP/CLS/INP/PageSpeed ve 800 KB ilk yük bütçesini ölç.
3. E-posta, cron, yedek, gerçek içerik ve canlı yayın kabulünü `PRODUCTION.md`
   üzerinden tamamla.

## Canlı yayın sınırı

G-09'un tamamlanması canlı dağıtım onayı değildir. FTP/üretim dağıtımı bu modülde
yapılmaz; PR ve CI başarılı olduktan sonra yalnız genel R8/üretim kabulüne geçilir.

## Güncel devam kaydı — 9 Eylül 2026

Güncel main üzerinde sekiz Chromium denetimi CI içinde çalıştırılmıştır:
animasyon, tasarım, iç sayfalar, medya, durumlar, marka, panel ve R8 kabul.
İki işin bütün test adımları başarıyla tamamlanmıştır. Aynı kodu yeniden
çalıştırmak yerine bu sürüme ait tamamlanmış CI kaydı doğrulandı.

Sıradaki iş üretim kabulüdür. `config/config.example.php` içindeki
`https://arcatesyazilim.com` örnek yapılandırmadır; gerçek yayın adresi veya
bu commit'in üretimde olduğunun kanıtı sayılmaz. Canlı adres/sürüm doğrulandıktan
sonra performans ve ilk yük bütçesi ölçülmeli; sunucuda preflight, gerçek e-posta,
cron ve yedekten geri yükleme kanıtları tamamlanmalıdır. CI mail yöntemi `log`
olduğu için gerçek e-posta teslimini doğrulamaz. Bu kontrolde dağıtım yapılmadı.

Önceki çalışma dalındaki alternatif raster hizmet görselleri mevcut SVG
entegrasyonunun üzerine uygulanmadı; R3 tamamlanmış iş olarak korunur.
