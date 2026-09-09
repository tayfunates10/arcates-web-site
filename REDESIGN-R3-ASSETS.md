# R3 — Özgün görseller ve devam kaydı

9 Eylül 2026. G-01 `main`e `5240c23d27f2102bdbc66c755b363556950182bb` ile alındı.
G-02 `main`e PR #13 / `971ce94e0055c22f652813d581476b9c243f84f2` ile alındı.
G-03 dalı: `redesign/r3-sector-illustrations`.

## Nerede kalındı?

R0/R1/R4, R5 ana sayfa, R6 iç sayfalar, R7 panel/sistem kodları ve R8 otomatik
responsive/klavye/RTL/reduced-motion kabul denetimleri main'de. R3 özgün görsel
ailesinde **G-01 hero** ve **G-02 altı hizmet görseli** tamamlandı; bu dal
**G-03 altı sektör görselini** tamamlar. Bütün R3 veya canlı yayın kabulü
tamamlanmış sayılmaz.

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
derinliği ve 4:3 kompozisyon diliyle altı ayrı hizmet illüstrasyonu hazırlandı:

| Hizmet | Anahtar | Görsel fikri |
|---|---|---|
| Kurumsal web tasarım | `layout` | Masaüstü panel + mobil panel |
| E-ticaret sitesi | `cart` | Ürün yüzeyleri + alışveriş çantası/sepet |
| Rezervasyon sistemi | `calendar` | Rezervasyon paneli + takvim |
| SEO hizmeti | `search` | Sonuç yüzeyi + büyüteç |
| Çoklu dil web sitesi | `globe` | Küre + çoklu içerik kartları |
| Web sitesi bakım | `shield` | Sunucu katmanları + güvenlik kalkanı |

Görseller 480×360 intrinsic boyutlu SVG'dir. Hizmet bölümünde `loading="lazy"`
ve `decoding="async"` ile yüklenir. Dekoratif oldukları için alt metinleri boştur;
hizmet adı ve açıklaması HTML metni olarak panelden gelmeye devam eder.

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
tutulur. Her biri yaklaşık 1,3–1,4 KB; toplam bütçe 40 KB sınırının oldukça
altındadır. Harf, sayı, üçüncü taraf logo, müşteri adı, gerçek proje ekranı veya
uydurma performans metriği içermez.

Görseller `views/front/sector.php` içindeki sektör slug eşlemesiyle ilgili sektör
sayfasının hero alanına eklenir. Başlık ve açıklama veritabanı/panel içeriği olarak
kalır. Görseller dekoratif `alt=""` + `aria-hidden="true"` kullanır; hero içinde
oldukları için `loading="eager"`, `decoding="async"` ile yüklenir.

Ek stil: `public/assets/css/r3-sector-illustrations.css`. Bu CSS yalnız `sector`
sayfalarında `PageController` üzerinden yüklenir; diğer iç sayfa türlerine ek
transfer getirmez. 940px altında hero tek kolona iner.

## G-03 kabul

- PHP `F-R3-03`: altı SVG'nin varlığını, 480×360 / 4:3 sözleşmesini, tek dosya
  `< 8 KB`, toplam `< 40 KB`, harici kaynak/script/metin bulunmamasını doğrular.
- Şablon sözleşmesi dekoratif alt metin, `aria-hidden`, eager yükleme ve sektör
  sayfasına özel CSS yüklenmesini kontrol eder.
- Mevcut R8 PHP/MySQL ve Chromium regresyon kapıları PR üzerinde tekrar çalışır.

## Açık işlerin sırası

1. **G-04/G-05:** süreç ve bölge için mevcut vektörlerin marka tutarlılığı kontrolü.
2. **G-06/G-07:** gerçek ekran görüntüsü sunum çerçeveleri ve blog kapakları; canlı medya envanteri gerekiyor.
3. **G-08/G-10:** CTA yayı ve boş/hata/başarı işaretleri.
4. **G-09:** sosyal görsel yenilemesi ve favicon/logo küçük boyut kalite kontrolü.
5. Tüm görseller entegre edilince R8 tekrar; gerçek üretim LCP/CLS/INP/PageSpeed,
   800 KB ilk yük bütçesi, e-posta/cron/yedek ve canlı içerik kabulü.

## Canlı yayın sınırı

G-03'ün tamamlanması canlı dağıtım onayı değildir. Bu modülde FTP/üretim
dağıtımı yapılmaz; PR ve CI başarılı olduktan sonra G-04/G-05'e geçilir.
