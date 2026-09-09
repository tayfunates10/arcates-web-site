# R3 — Özgün görseller ve devam kaydı

9 Eylül 2026. G-01 `main`e `5240c23d27f2102bdbc66c755b363556950182bb` ile alındı.
G-02 `main`e PR #13 / `971ce94e0055c22f652813d581476b9c243f84f2` ile alındı.
G-03 `main`e PR #14 / `ba350110e16fc2da8131e6f35bf75526af8ddad0` ile alındı.
G-04/G-05 dalı: `redesign/r3-process-region-visuals`.

## Nerede kalındı?

R0/R1/R4, R5 ana sayfa, R6 iç sayfalar, R7 panel/sistem kodları ve R8 otomatik
responsive/klavye/RTL/reduced-motion kabul denetimleri main'de. R3 özgün görsel
ailesinde **G-01 hero, G-02 hizmet ve G-03 sektör** tamamlandı; bu dal **G-04 süreç**
ve **G-05 hizmet bölgesi** vektör dilini aynı aileye taşır. Bütün R3 veya canlı
yayın kabulü tamamlanmış sayılmaz.

## G-01 teslim

| Dosya | Boyut / rol |
|---|---|
| `design-assets/hero-master.png` | 1448×1086 özgün PNG ana kaynak; web kökü dışında |
| `public/assets/img/redesign/hero-640.webp` | 640×480, 14.606 bayt |
| `public/assets/img/redesign/hero-1280.webp` | 1280×960, 34.574 bayt |

Hero `views/front/partials/hero.php` içinde responsive `srcset` ile kullanılır.
Metin ve CTA panel içeriğidir; görsel dekoratiftir ve sahte müşteri/metrik içermez.

## G-02 teslim — altı hizmet görseli

Aynı koyu lacivert zemin, beyaz yüzey, kobalt mavi vurgu, yumuşak stüdyo
derinliği ve 4:3 kompozisyon diliyle altı ayrı hizmet illüstrasyonu hazırlandı.
Görseller 480×360 intrinsic boyutlu SVG'dir; hizmet adı ve açıklaması HTML metni
olarak panelden gelmeye devam eder.

Yerleşim: `views/front/partials/cards.php`.
Ek stil: `public/assets/css/r3-service-illustrations.css`.

## G-03 teslim — altı sektör görseli

Gerçek müşteri/proje ekranı kanıtı bulunmadığı için sahte referans veya ekran
görüntüsü üretilmedi. Sektörler yalnız nesne metaforlarıyla anlatıldı ve aynı
lacivert/kobalt/beyaz görsel aile korundu.

| Sektör | Slug | Görsel fikri |
|---|---|---|
| Otel ve pansiyon | `otel-pansiyon-web-sitesi` | Konaklama cephesi + giriş |
| Zeytinyağı üreticisi | `zeytinyagi-e-ticaret-sitesi` | Şişe + zeytin dalı/ürün yüzeyi |
| Restoran ve kafe | `restoran-kafe-qr-menu` | Tabak + servis + soyut dijital menü |
| Emlak ofisi | `emlak-web-sitesi` | Ev formu + kapı/pencere yüzeyleri |
| Nakliyat | `nakliyat-web-sitesi` | Koli yüklü taşıma aracı |
| Tabela ve matbaa | `tabela-matbaa-web-sitesi` | Tabela yüzeyi + baskı panelleri |

Altı SVG `public/assets/img/redesign/sector-*.svg` altında 480×360 / 4:3 olarak
tutulur. Görseller ilgili sektör sayfalarının hero alanına slug eşlemesiyle eklenir.

## G-04 teslim — süreç görsel dili

`views/front/partials/steps.php` içindeki üç adımlı süreç, panel metnine dokunmadan
özgün dekoratif vektör metaforlarıyla yenilendi:

1. Konuşma/keşif — kullanıcı + konuşma balonu.
2. Kurulum — tarayıcı/arayüz yüzeyi.
3. Büyüme — bağlantılı yükseliş rotası.

Vektörler `aria-hidden="true"` ve `focusable="false"` kullanır; başlık ve açıklama
panelden gelen metin olarak kalır. Masaüstünde üç kart tek bağlı hat üzerinde,
940 px altında tek sütunda gösterilir. Sahte metrik, müşteri adı veya ekran içeriği
yoktur.

## G-05 teslim — hizmet bölgesi bağlantı sahnesi

`views/front/partials/coast.php` içindeki dinamik SVG korunarak görsel dil yeniden
kuruldu. Koyu lacivert sahne, düşük kontrastlı grid, derinlik rotası ve kobalt rota
gradienti eklendi. İlçe noktaları, etiketleri ve URL'leri yine panel/veritabanı
verisinden üretilir; hiçbir ilçe adı şablona sabitlenmez.

Mevcut `data-coast`, `.coast__path`, `data-at` ve `site.js` scroll ilerleme motoru
aynen korunur. SVG ayrıca `role="img"` + dinamik `aria-label` taşır ve altında gerçek
HTML bağlantı listesi bulunduğu için klavye/ekran okuyucu erişimi kaybolmaz.

G-04 ve G-05 için ortak ek stil:
`public/assets/css/r3-process-region-visuals.css`.
Bu dosya yalnız ana sayfada `HomeController` üzerinden yüklenir.

## G-04/G-05 kabul

- `F-R3-04`: süreç vektörlerinin dekoratif olduğunu ve içerik metninin panelden
  gelmeye devam ettiğini doğrular.
- `F-R3-05`: bölge isim/URL kaynağını, erişilebilir SVG + HTML liste sözleşmesini
  ve mevcut scroll çizim kancalarının korunduğunu doğrular.
- `F-R3-045`: ek CSS'in `< 16 KB`, responsive, reduced-motion uyumlu ve
  `!important` içermeyen ana sayfaya özel bir katman olduğunu doğrular.
- Mevcut R8 PHP/MySQL ve gerçek Chromium regresyon kapıları PR üzerinde tekrar çalışır.

## Açık işlerin sırası

1. **G-06/G-07:** gerçek ekran görüntüsü sunum çerçeveleri ve blog kapakları; canlı medya envanteri gerekiyor.
2. **G-08/G-10:** CTA yayı ve boş/hata/başarı işaretleri.
3. **G-09:** sosyal görsel yenilemesi ve favicon/logo küçük boyut kalite kontrolü.
4. Tüm görseller entegre edilince R8 tekrar; gerçek üretim LCP/CLS/INP/PageSpeed,
   800 KB ilk yük bütçesi, e-posta/cron/yedek ve canlı içerik kabulü.

## Canlı yayın sınırı

G-04/G-05'in tamamlanması canlı dağıtım onayı değildir. Bu modülde FTP/üretim
dağıtımı yapılmaz; PR ve CI başarılı olduktan sonra G-06/G-07'ye geçilir.
