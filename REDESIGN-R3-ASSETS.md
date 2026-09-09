# R3 — Özgün görseller ve devam kaydı

9 Eylül 2026.

- G-01 `main`e `5240c23d27f2102bdbc66c755b363556950182bb` ile alındı.
- G-02 `main`e PR #13 / `971ce94e0055c22f652813d581476b9c243f84f2` ile alındı.
- G-03 `main`e PR #14 / `ba350110e16fc2da8131e6f35bf75526af8ddad0` ile alındı.
- G-04/G-05 `main`e PR #15 / `ff926db04a162cdc797d9e4be3507ed0a9a1ede2` ile alındı.
- G-06/G-07 `main`e PR #16 / `81bf25622bc8b973e1159fee9e2c2f4cd94a844f` ile alındı.
- G-08/G-10 dalı: `redesign/r3-cta-status-states`.

## Nerede kalındı?

R0/R1/R4, R5 ana sayfa, R6 iç sayfalar, R7 panel/sistem kodları ve R8 otomatik
responsive/klavye/RTL/reduced-motion kabul denetimleri `main`dedir. R3 özgün
görsel ailesinde G-01–G-07 tamamlandı; bu dal G-08 final CTA ve G-10 boş/hata/
başarı durum görsellerini aynı Arcates ailesine taşır. Canlı yayın kabulü
tamamlanmış sayılmaz.

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
yoktur. Bu nedenle sahte ekran görüntüsü üretilmedi.

Gerçek `cover` veya `gallery` medyası panelden yüklendiğinde ana sayfa vitrini,
`/referanslar` kartları ve proje detayında otomatik Arcates sunum çerçevesi
kullanılır. Çerçeve yalnız gerçek medya mevcutsa oluşur.

## G-07 — blog kapak sistemi

Panelden gerçek `cover` yüklenmişse gerçek medya önceliklidir. Kapak yoksa yazının
kategorisinden türetilen metinsiz, iddiasız, soyut 16:10 Arcates editoryal kapağı
gösterilir. Başlık, tarih, sayı, müşteri veya performans iddiası görsel içine
gömülmez.

## G-08 — final CTA bağlantı yayı

`views/front/partials/cta.php` içindeki final çağrı alanı panel/ayar veri akışına
dokunulmadan bağlantı yayı sahnesiyle tamamlandı. Başlık/metin mevcut CTA içeriği
veya `cta_title` / `cta_text` ayarından, telefon ise tek NAP kaynağı olan
`nap_phone` ayarından gelmeye devam eder.

Yeni dekoratif SVG iki bağlantı yayı ve üç düğümden oluşur. Görsel `aria-hidden`
olduğu için erişilebilir isim üretmez; görselin içinde metin, sayı, logo, müşteri
veya performans iddiası bulunmaz. Mobilde kompozisyon geri çekilir ve içerik/CTA
önceliği korunur.

## G-10 — boş, hata ve başarı durumları

Aynı geometriyi kullanan ortak `state-mark` ailesi oluşturuldu:

- **Başarı:** `/tesekkurler` sayfasında yeşil başarı işareti; mevcut ayrı URL,
  dönüşüm akışı ve `noindex,nofollow` davranışı korunur.
- **Boş:** proje listesi, blog filtresi ve SSS boş durumunda ortak mavi nötr işaret;
  gerçek `__('no_results')` metni korunur. 503 bakım ekranı da kırmızı hata yerine
  nötr bekleme/boş işareti kullanır.
- **Hata:** 403, 404, 419 ve 500 standalone sayfaları aynı kırmızı hata işaretini
  kullanır. Mevcut HTTP kodu, güvenli mesaj, aksiyon ve robots davranışı değişmez.

Ortak stil: `public/assets/css/r3-cta-status.css`. Normal ön yüz sayfalarında
`views/front/partials/head.php` üzerinden, standalone HTTP hata şablonlarında ise
doğrudan yüklenir. Stil responsive ve reduced-motion uyumludur.

## G-08/G-10 kabul

- `F-R3-08`: CTA başlık/telefon veri kaynağını ve dekoratif bağlantı yayı
  sözleşmesini doğrular.
- `F-R3-10`: teşekkür, boş durumlar ve 403/404/419/500/503 durum işaretlerinin
  semantik metni ve `noindex` davranışını koruduğunu doğrular.
- `F-R3-0810`: ortak CSS'in `< 16 KB`, harici kaynaksız, `!important` içermeyen ve
  reduced-motion uyumlu olduğunu doğrular.
- `tools/browser/status-check.mjs`: gerçek Chromium'da masaüstü ve mobilde final
  CTA, teşekkür, kasıtlı boş blog filtresi ve gerçek 404 yanıtını; HTTP 404,
  `noindex` ve yatay taşmasızlık sözleşmeleriyle birlikte doğrular.
- Mevcut R8 PHP/MySQL, medya ve diğer Chromium regresyon kapıları çalışmaya devam eder.

## Açık işlerin sırası

1. **G-09:** sosyal/OG görsel yenilemesi ve favicon/logo küçük boyut kalite kontrolü.
2. Tüm görseller entegre edilince R8 tekrar; gerçek üretim LCP/CLS/INP/PageSpeed,
   800 KB ilk yük bütçesi, e-posta/cron/yedek ve canlı içerik kabulü.

## Canlı yayın sınırı

G-08/G-10'un tamamlanması canlı dağıtım onayı değildir. FTP/üretim dağıtımı bu
modülde yapılmaz; PR ve CI başarılı olduktan sonra G-09'a geçilir.
