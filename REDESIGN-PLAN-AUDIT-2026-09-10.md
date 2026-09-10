# Tam arayüz planı karşılaştırması — 10 Eylül 2026

Kaynak: kullanıcının Arcates-Tam-Arayuz-Yeniden-Tasarim-Plani(2).md belgesi;
GitHub main 3a405ad1f1c409b289f92b12835881fe32b79a1d ve canlı bulut Chromium.

## Sonuç

Tamamı kabul edilmiş değildir. Kodda R0/R1/R4/R5/R6/R7 ve G-01–G-10 ailesi
vardır; kaynak varlığı tek başına planın tüm durumlarının canlı kabulü değildir.
Main CI run 34482342639 başarılıdır. Bu başarı bu yeni düzeltmelerin sonucu değildir.

| Plan alanı | Kanıt / durum |
|---|---|
| Ortak header/footer | Canlı ana sayfa ve incelenen iç sayfalarda teklif, 3 footer menüsü ve yasal bağlantılar var. Önceki düzeltmeler yansımış. |
| Ana sayfa sırası | Hero, sektörler, hizmetler, süreç, örnekler, bölgeler, SSS, CTA sırası canlı DOM'da mevcut. Hero yeni sahneyi kullanıyor. |
| G-02 hizmetler | Ana sayfada mevcut, altı hizmet detayında yoktu. Bu dalda mevcut görseller hizmet detaylarına eklendi. |
| G-03 sektörler | Temsilci otel/pansiyon sayfasında doğru SVG çözümleniyor. Kaynakta 6 eşleme var. |
| G-06 proje medyası | Şablonlar kapak/galeri destekliyor. İncelenen canlı proje detayında görsel yok; gerçek ekran görüntüsü teslimi tamamlanmış sayılamaz. |
| G-07 blog | Kaynakta gerçek kapak önceliği ve kategori fallback ailesi var. |
| G-08/G-09/G-10 | CTA, sosyal kart/ikon ve durum ailesi kaynakta var; özel medya/yetki/hata durumlarının tamamı bu canlı turda tetiklenmedi. |
| Hizmet/ilçe/sektör/genel sayfalar | İncelenen rotalarda tek H1, ortak footer ve masaüstünde yatay taşma yok. |
| Form | Boş gönderimde ilk hatalı alana odak ve gönderimi durdurma canlıda çalışıyor. Kalıcı aria-describedby açıklaması yoktu; bu dalda tamamlandı. Gerçek talep/e-posta gönderilmedi. |
| Örnek çalışma açıklaması | Liste içeriği örnek olduğunu açıklıyor; ancak ortak notice ana sayfa/liste/ilçe/proje detayında görünmüyor. Proje detayındaki Müşteri etiketi tek başına örnek niteliğini yeterince açıklamıyor. Panel ayarı doğrulanmalı. |
| Çalışma saatleri | Canlı footer'da görünmüyor. Doğrulanmış saatler olmadan doldurulmadı. |
| Panel R7 | Kaynak ve CI denetimi mevcut. Canlı /panel adresi /panel/giris ekranına yönlendi; oturum içi ekranlar kontrol edilmedi. |
| Mobil/RTL/zoom/reduced-motion | Main CI matrisi var. Bu oturumdaki canlı inceleme 1348px tarayıcıyla yapıldı; canlı mobil/RTL/zoom kabulü iddia edilmez. Yeni hizmet matrisi CI'da 320/390/768/1440px. |
| Performans | Bu turda Lighthouse, LCP/CLS/INP veya önbelleksiz 800 KB aktarım bütçesi ölçülmedi. |

## Bu turda canlı açılan rotalar

`/`, `/iletisim`, altı hizmet URL'si, `/edremit-web-tasarim`,
`/otel-pansiyon-web-sitesi`, `/referanslar`,
`/referanslar/balikesir-uretim-teknik-site`, `/blog`, `/hakkimizda`, `/fiyatlar`,
`/sss`, `/kvkk`, `/gizlilik-politikasi`, `/panel` → `/panel/giris`.

Bu liste 43 URL'nin veya her buton/durumun yeniden kabul edildiği anlamına gelmez.
Ana sayfa görsel olarak, iç sayfalar DOM/yerleşim ölçümleriyle de incelendi.

## Kalan kabul

1. Bu dalın PHP/MySQL ve Chromium CI sonuçlarını doğrula; yayından sonra hizmet
   görselleri ve form hata açıklamalarını canlıda tekrar kontrol et.
2. Canlı panelde projects_notice değerini ve gerçek çalışma saatlerini doğrula.
3. Panel liste/editör/medya/ayar ekranlarını, mobil menüyü ve hata durumlarını
   oturum açılmış canlı içerikle kontrol et. Yedek geri yükleme gibi işlemler
   üretimde sırf arayüz denemesi için yapılmamalı.
4. Tüm sayfa ailelerinin 390/1440px görsel kabulü ve plandaki ek genişlikler,
   klavye/zoom/RTL/JS kapalı durumları; üretim performans ölçümleri açık.

Menü/CTA metinleri planın öneri metninden farklıdır; mevcut panel içeriği topluca
ezilmedi. Özgün logo veya gerçek proje kanıtı uydurulmadı.
