# R3 — Özgün görseller ve devam kaydı

9 Eylül 2026.

- G-01 `main`e `5240c23d27f2102bdbc66c755b363556950182bb` ile alındı.
- G-02 `main`e PR #13 / `971ce94e0055c22f652813d581476b9c243f84f2` ile alındı.
- G-03 `main`e PR #14 / `ba350110e16fc2da8131e6f35bf75526af8ddad0` ile alındı.
- G-04/G-05 `main`e PR #15 / `ff926db04a162cdc797d9e4be3507ed0a9a1ede2` ile alındı.
- G-06/G-07 dalı: `redesign/r3-project-blog-media`.

## Nerede kalındı?

R0/R1/R4, R5 ana sayfa, R6 iç sayfalar, R7 panel/sistem kodları ve R8 otomatik
responsive/klavye/RTL/reduced-motion kabul denetimleri `main`dedir. R3 özgün
görsel ailesinde G-01–G-05 tamamlandı; bu dal G-06 proje medya sunum sistemi ile
G-07 blog kapak sistemini tamamlar. Canlı yayın kabulü tamamlanmış sayılmaz.

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

Gerçek `cover` veya `gallery` medyası panelden yüklendiğinde aşağıdaki yüzeylerde
otomatik premium Arcates sunum çerçevesi kullanılır:

- ana sayfa örnek site vitrini,
- `/referanslar` kartları,
- proje detay kapak görseli,
- proje detay galeri görselleri.

Çerçeve yalnız medya verisi mevcutsa oluşturulur. Medyasız örnek kayıtlar gerçek
ekran görüntüsü varmış gibi gösterilmez. Tarayıcı üst çubuğunda URL, marka,
performans değeri veya başka sahte içerik bulunmaz; yalnız nötr üç noktalı sunum
kromu kullanılır.

Stil: `public/assets/css/r3-project-blog-media.css`.

## G-07 — blog kapak sistemi

`db/seed/posts.php` başlangıç yazılarının kapak alanını bilerek boş bırakır. Sistem
bu davranışı bozmaz:

1. Panelden gerçek bir `cover` yüklenmişse gerçek medya her zaman önceliklidir.
2. Kapak yoksa yazının kategorisinden türetilen soyut Arcates editoryal kapağı
   gösterilir.

Fallback kapaklar metinsizdir; başlık, tarih, kategori, sayı, istatistik, müşteri
ve marka iddiası görselin içine gömülmez. Yerel SEO, Performans, Dönüşüm, Çoklu dil,
Bakım ve İçerik kategorileri aynı lacivert/kobalt/beyaz aile içinde farklı soyut
geometri kullanır. Bilinmeyen kategoriler genel kompozisyona düşer.

Partial: `views/front/partials/editorial-cover.php`.
Stil: `public/assets/css/r3-project-blog-media.css`.

## G-06/G-07 kabul

- `F-R3-06`: proje sunum çerçevesinin yalnız gerçek `cover/gallery` verisine bağlı
  olduğunu ve projelerde sahte editoryal fallback üretilmediğini doğrular.
- `F-R3-07`: blogda gerçek kapağın önceliğini, kapaksız durumda metinsiz ve
  `aria-hidden` editoryal fallback kullanılmasını doğrular.
- `F-R3-067`: ortak CSS'in `< 18 KB`, harici kaynaksız ve reduced-motion uyumlu
  olduğunu; ilgili controller'larda yüklendiğini doğrular.
- `tools/browser/media-check.mjs`: gerçek Chromium'da seed proje kayıtlarında
  sahte `project-shot` oluşmadığını, altı seed blog yazısında fallback kapakların
  16:10 ve metinsiz olduğunu, masaüstü/mobil yatay taşma olmadığını doğrular.
- Mevcut R8 PHP/MySQL ve diğer Chromium regresyon kapıları çalışmaya devam eder.

## Açık işlerin sırası

1. **G-08/G-10:** CTA yayı ve boş/hata/başarı görsel durumları.
2. **G-09:** sosyal görsel yenilemesi ve favicon/logo küçük boyut kalite kontrolü.
3. Tüm görseller entegre edilince R8 tekrar; gerçek üretim LCP/CLS/INP/PageSpeed,
   800 KB ilk yük bütçesi, e-posta/cron/yedek ve canlı içerik kabulü.

## Canlı yayın sınırı

G-06/G-07'nin tamamlanması canlı dağıtım onayı değildir. FTP/üretim dağıtımı
bu modülde yapılmaz; PR ve CI başarılı olduktan sonra G-08/G-10'a geçilir.
