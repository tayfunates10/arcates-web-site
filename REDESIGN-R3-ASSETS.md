# R3 — Özgün görseller ve devam kaydı

9 Eylül 2026. G-01 `main`e `5240c23d27f2102bdbc66c755b363556950182bb` ile alındı.
G-02 dalı: `redesign/r3-service-illustrations`.

## Nerede kalındı?

R0/R1/R4, R5 ana sayfa, R6 iç sayfalar, R7 panel/sistem kodları ve R8 otomatik
responsive/klavye/RTL/reduced-motion kabul denetimleri main'de. R3 özgün görsel
ailesinde **G-01 hero** tamamlandı; bu dal **G-02 altı hizmet görselini** tamamlar.
Bütün R3 veya canlı yayın kabulü tamamlanmış sayılmaz.

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

Görseller 480×360 intrinsic boyutlu, ölçeklenebilir SVG'dir. Altı dosyanın
toplamı yaklaşık 15 KB'dır; kart genişliğine göre tek kaynaktan kayıpsız ölçeklenir.
Hizmet bölümünde `loading="lazy"` ve `decoding="async"` ile yüklenir. Dekoratif
oldukları için alt metinleri boştur; hizmet adı ve açıklaması HTML metni olarak
panelden gelmeye devam eder.

Yerleşim: `views/front/partials/cards.php`.
Ek stil: `public/assets/css/r3-service-illustrations.css`.
Bu stil yalnız ana sayfada `HomeController` üzerinden yüklenir.

Görsellerde harf, sayı, üçüncü taraf logo, müşteri adı, gerçek proje ekranı veya
uydurma performans metriği bulunmaz. Hizmetlerin ayrımı yalnız nesne/sahne
metaforuyla yapılır.

## G-02 kabul

- PHP `F-R3-02`: altı SVG'nin varlığını, 480×360 / 4:3 sözleşmesini,
  tek dosya `< 8 KB` ve toplam `< 32 KB` bütçesini doğrular.
- Şablon testi hizmet görsellerinin `service-card__image` ve `loading="lazy"`
  sözleşmesini kontrol eder.
- Gerçek Chromium `design-check.mjs`, ana sayfayı açıp hizmet bölümüne kaydırır;
  altı görselin `complete/naturalWidth`, 4:3, boş dekoratif alt ve lazy-loading
  durumlarını doğrular.
- R8'in geri kalan testleri regresyon kapısı olarak çalışmaya devam eder.

## Açık işlerin sırası

1. **G-03:** altı sektör görseli; gerçek proje kanıtı gerektiren sahneler gerçek kaynakla hazırlanmalı.
2. **G-04/G-05:** süreç ve bölge için mevcut vektörlerin marka tutarlılığı kontrolü.
3. **G-06/G-07:** gerçek ekran görüntüsü sunum çerçeveleri ve blog kapakları; canlı medya envanteri gerekiyor.
4. **G-08/G-10:** CTA yayı ve boş/hata/başarı işaretleri.
5. **G-09:** sosyal görsel yenilemesi ve favicon/logo küçük boyut kalite kontrolü.
6. Tüm görseller entegre edilince R8 tekrar; gerçek üretim LCP/CLS/INP/PageSpeed,
   800 KB ilk yük bütçesi, e-posta/cron/yedek ve canlı içerik kabulü.

## Canlı yayın sınırı

G-02'nin tamamlanması canlı dağıtım onayı değildir. Bu modülde FTP/üretim
dağıtımı yapılmaz; PR ve CI başarılı olduktan sonra G-03'e geçilir.
