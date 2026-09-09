# R3 — Özgün görseller ve devam kaydı

9 Eylül 2026. İncelenen main: `00c21bc8b8125607062696ff1d9902a7b0ad0180`.
Kaynak CI: https://github.com/tayfunates10/arcates-web-site/actions/runs/34319903851 — başarılı.

## Nerede kalındı?

R0/R1/R4, R5 ana sayfa, R6 iç sayfalar ve R7 panel/sistem kodları main'de. R8 responsive/klavye/RTL/reduced-motion otomasyonları main'de. Eski `TASARIM-PLANI.md` içindeki “sıradaki iş T1” yönlendirmesi tarihsel; tamamlanan işi yeniden başlatmamalı.

R3 özgün görsel ailesi atlanmıştı. Bu modül **G-01 hero sahnesini** üretip entegre eder. Bütün R3 veya bütün projenin tamamlandığı anlamına gelmez. R2 bağımsız tüm ekran prototipinin teslim kaydı ve canlı R0 envanteri de repo kanıtından doğrulanamamıştır.

## G-01 teslim

| Dosya | Boyut / rol |
|---|---|
| `design-assets/hero-master.png` | 1448×1086 özgün PNG ana kaynak; web kökü dışında |
| `public/assets/img/redesign/hero-640.webp` | 640×480, 14.606 bayt |
| `public/assets/img/redesign/hero-1280.webp` | 1280×960, 34.574 bayt |

Yerleşim: `views/front/partials/hero.php`; stil: `home-redesign.css`. Dekoratif tek grup, boş alt metin, aynı 4:3 oranı. Metin ve CTA'lar paneldeki HTML içeriği olarak kalır. İlk ekran görseli eager yüklenir; dosya preload edilmez. Responsive seçimde küçük ekran ve retina kaynakları `srcset/sizes` ile değerlendirilir. Koyu zeminli kompozisyon bilinçli seçildi; PNG şeffafmış gibi sunulmaz. Tasarım onayı kullanıcıdan henüz alınmış değildir.

Görsel; üç çeyrek açıdaki web paneli, mobil panel ve mavi bağlantı şeridini içerir. Görsel üretimi yerleşik imagegen ile yapıldı; WebP sürümleri aynı görselin boyut/format dönüşümüdür. Kaynakta müşteri, performans değeri, gerçek proje ekranı veya üçüncü taraf marka bulunmaz.

## Üretim promptu

Create a production-ready standalone hero illustration for ARCATES software/web design studio, landscape 4:3. Premium elegant sculptural 3D studio render: exactly three main elements, a matte white desktop browser panel at gentle three-quarter angle, smaller white mobile panel slightly in front to right, and a cobalt-blue curved connector ribbon passing behind and between them, hinting at an angular A shape without text or actual logo. Screen surfaces contain restrained abstract blue and pale-white modular layout blocks only, no letters, no numbers, no charts, no fake performance indicators. Unified subtle bevels, matte porcelain and small translucent blue glass accents, soft upper-left studio light and natural contact shadows. Palette deep midnight navy #081426 background perfectly uniform all the way to outer edges, cobalt #0B4FA8 and #1C7BF2, white. Center composition with generous 10 percent safe area on every edge, all objects fully in frame. Beautiful purposeful graphic for web development, not a screenshot of a finished website, no page titles, no buttons with labels, no watermark, no decorative spheres, no random floating shapes. Image will be used on the right half of an actual website hero; do not include the website itself.

## Açık işlerin sırası

1. G-02: altı hizmet illüstrasyonu; aynı kamera, beyaz/mavi malzeme ve ışık ailesi.
2. G-03: altı sektör görseli; gerçek proje kanıtı gerektiren sahneler gerçek kaynakla hazırlanmalı.
3. G-04/G-05: süreç ve bölge için mevcut vektörlerin marka tutarlılığı kontrolü.
4. G-06/G-07: gerçek ekran görüntüsü sunum çerçeveleri ve blog kapakları; canlı medya envanteri gerekiyor.
5. G-08/G-10: CTA yayı ve boş/hata/başarı işaretleri.
6. G-09: plandaki sosyal görsel yenilemesi ve favicon/logo küçük boyut kalite kontrolü.
7. Tüm görseller entegre edilince R8 tekrar; gerçek üretim LCP/CLS/INP/PageSpeed, 800 KB transfer bütçesi, e-posta/cron/yedek ve canlı içerik kabulü.

## Kabul ve sınırlar

PHP varlık testi WebP'nin çözülebildiğini, gerçek boyutunu, oranını ve 220 KB hero bütçesini kontrol eder. Tarayıcı animasyon testi görüntünün `complete` ve `naturalWidth` değerlerini, JS kapalı/reduced-motion görünürlüğünü izler. Eski üç DOM şeklinin sayısını korumak yerine yeni tek görselin yüklenmesi test edilir.

Yerelde PHP/MySQL çalışma ortamı bulunmadığından PHP ve gerçek uygulama tarayıcı sonuçları CI üzerinden değerlendirilmelidir. Kaynak main CI başarısı yeni dal için başarı sayılmaz. Canlı siteye dağıtım bu modülde yapılmaz.
