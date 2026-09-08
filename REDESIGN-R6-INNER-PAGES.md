# ARCATES — R6 İç Sayfa Yeniden Tasarım Kaydı

**Tarih:** 8 Eylül 2026  
**Branch:** `redesign/r6-inner-pages`  
**Kapsam:** Hizmet, ilçe, sektör, genel sayfa, blog, proje/referans, iletişim, SSS ve teşekkür sayfası aileleri.

## Amaç

R5 ana sayfa düzenine dokunmadan, bütün ziyaretçi iç sayfalarını aynı Arcates görsel sistemi altında birleştirmek; içerik/route/SEO/form veri sözleşmelerini korurken daha güçlü hiyerarşi, daha tutarlı yüzeyler ve daha iyi mobil kullanım sağlamaktır.

## Uygulanan tasarım sözleşmesi

- İç sayfalara ortak koyu breadcrumb + editoryal hero ailesi eklendi.
- Hero başlıkları yüksek kontrastlı, tek H1 düzenini koruyacak biçimde standardize edildi.
- Hizmet, ilçe, sektör ve blog detaylarında içerik/yan sütun düzeni yeni editoryal yüzey ailesine taşındı.
- İlçe hızlı bilgi alanları segmentli koyu hero yüzeyine uyarlandı.
- Blog ve proje liste kartları ortak 22px büyük yüzey sistemine geçirildi.
- Proje detayında meta bilgileri, kapak, hikâye ve galeri tek tasarım ailesinde birleştirildi.
- İletişim sayfasında kompakt koyu hero kullanıldı; teklif formunun DOM ve ilk viewport önceliği korundu.
- `/sss` sayfası ortak R6 hero + içerik yüzeyine geçirildi; SSS `details/summary` yapısı korundu.
- `/tesekkurler` dönüşüm sayfası ortak R6 hero + sonuç yüzeyine geçirildi; ayrı URL ve `noindex` iş kuralı korunuyor.
- Mobilde 360px dahil yatay taşma engellendi; 940px altında içerik/yan sütun sırası mevcut erişilebilirlik sözleşmesini koruyor.
- `prefers-reduced-motion` altında hover/transform geçişleri kapatılıyor.

## CSS katmanları

Yükleme sırası:

1. `site.css`
2. `redesign.css`
3. `inner-redesign.css`
4. `inner-performance.css`
5. varsa rota özel stil dosyaları

`inner-redesign.css` R6 görsel sözleşmesini taşır. `inner-performance.css`, uzun iç sayfalarda hero dekoru, editoryal gölgeler ve sticky teklif kartının paint maliyetini sınırlayan küçük performans koruma katmanıdır.

## Performans kararı

İlk R6 Chromium koşusunda mevcut A-11 sticky scroll kabul testi 49.4 fps, sonraki doğrulamada 48.5 fps ölçtü. Kabul eşiği düşürülmedi. Büyük masked/radial hero repaint işleri ve uzun editoryal yüzeylerin bulanık gölgeleri azaltıldı; sticky dönüşüm kartı pahalı gölge/gradient repaint işlerinden ayrıldı.

Son doğrulamada A-11 yeniden **60.0 fps** ölçüldü.

## Otomatik doğrulama

### PHP/MySQL

- `F-R6-01` — R6 stil yükleme sırası
- `F-R6-02` — ana iç sayfa CSS sözleşmesi
- `F-R6-03` — genel sayfa hero + içerik yüzeyi
- `F-R6-04` — iletişim formu önceliği
- `F-R6-05` — hizmet/ilçe/sektör/blog/proje/SSS/teşekkür şablon uyumu
- `F-R6-06` — gerçek Chromium R6 matrisinin CI içinde çalışması
- `F-R6-07` — SSS ve teşekkür sayfalarının R6 hero/yüzey sözleşmesi

Beklenen son test toplamı: **223 test, 0 atlanan**. Nihai sayı son PR CI ile doğrulanır.

### Chromium iç sayfa matrisi

Temsilci rotalar:

- `/web-tasarim`
- `/edremit-web-tasarim`
- `/otel-pansiyon-web-sitesi`
- `/blog`
- `/referanslar`
- `/hakkimizda`
- `/iletisim`
- `/sss`
- `/tesekkurler`
- `/referanslar/akcay-pansiyon-rezervasyon-sitesi`
- `/blog/yerel-aramada-gorunurluk-isletme-profili`

Denetimler:

- HTTP 200
- tek H1
- ortak hero yüzeyi ve yüksek kontrast
- masaüstü ve mobil yatay taşma yok
- editoryal içerik yüzeyleri
- proje/blog kartları
- proje detay yapısı
- iletişim formunun masaüstü ve 390px mobilde ilk viewport içinde kalması
- SSS ve teşekkür akışının ortak R6 hero sözleşmesini koruması
- 360px mobilde H1 viewport sınırları içinde kalması

Sonuç kabul ölçütü: **R5 browser + genel tasarım browser + R6 iç sayfa browser paketlerinin tamamının geçmesi**.

## Korunan iş kuralları

R6 yalnız arayüz ve kabul testi katmanını değiştirir. Aşağıdakiler değiştirilmedi:

- route sözleşmeleri
- veritabanı şeması
- yayın/taslak davranışları
- canonical, hreflang ve yapılandırılmış veri çekirdeği
- CSRF, honeypot, rate limit ve form saklama kuralları
- `/tesekkurler` için ayrı dönüşüm URL'si ve `noindex` kararı
- admin/editor yetki modeli
- NAP ve WhatsApp veri kaynakları
- R5 ana sayfa DOM ve hareket sözleşmesi

## Sonraki faz

R7, yönetim panelinin liste/editör/medya/SEO/ayar/yedekleme ekranlarının tam arayüz yeniden tasarımıdır. R6 ziyaretçi iç sayfa katmanı R7’den bağımsız tutulur.
