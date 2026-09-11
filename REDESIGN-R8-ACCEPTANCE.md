# Arcates yeniden tasarım — R8 nihai kabul kaydı

Kaynak temel: `main` / `233d71aa9a0a88738d7976cec0cf0577c3583ba4`

R8 yeni bir görsel faz değildir. R5 ana sayfa, R6 iç sayfalar ve R7 panel/sistem yüzeylerinin birlikte kırılmadan çalıştığını gerçek Chromium ve mevcut PHP/MySQL regresyon paketiyle doğrulayan kapanış kapısıdır.

## 1. Responsive kabul matrisi

Temsilci ziyaretçi rotaları gerçek Chromium’da şu CSS viewport genişliklerinde çalıştırılır:

- 320 px
- 360 px
- 390 px
- 768 px
- 1024 px
- 1366 px
- 1440 px
- 1920 px

Ana matris `/`, `/web-tasarim`, `/edremit-web-tasarim`, `/blog`, `/referanslar` ve `/iletisim` ailelerini kapsar. `/sss` ve `/tesekkurler` en dar 320 px ve en geniş 1920 px sınırında ayrıca doğrulanır.

Her rota için temel kapı: beklenen HTTP 200, tek H1, belge düzeyinde yatay taşma olmaması ve ana içerik/H1 alanının viewport içinde kalmasıdır. 390 px ve altındaki ana sayfa denetiminde mobil menü hedefi en az 44 × 44 px olmalıdır.

## 2. %200 etkili zoom / reflow yöntemi

Otomatik CI ortamında native tarayıcı zoom seviyesini kullanıcı arayüzündeki gibi güvenilir ve taşınabilir biçimde değiştirmek yerine, %200 büyütmenin reflow etkisi **1280 fiziksel / 640 CSS** düzen viewport’u ile modellenir. Playwright bağlamı `width: 640` ve `deviceScaleFactor: 2` kullanır.

Bu kontrol raporda **“%200 etkili reflow”** olarak adlandırılır; native tarayıcı zoom olduğu iddia edilmez. Ön yüzde `/`, `/web-tasarim`, `/iletisim`, `/blog`; panelde normal giriş akışı sonrasında `/panel/sayfalar` kontrol edilir. Belge yatay taşmamalı ve panel mobil navigasyonu erişilebilir kalmalıdır.

## 3. Klavye kabulü

Klavye kapısı aşağıdakileri doğrular:

- Ana sayfada ilk `Tab` odağı skip-link’e gelir.
- Odak halkası görünürdür.
- `Enter` ile `#icerik` hedefine gidilir.
- Mobil site menüsü odaklandıktan sonra `Enter` ile açılır.
- `Escape` ile kapanır ve odak menü düğmesine döner.

R7 panel kabulünde yönetim menüsü için aynı Escape / odak dönüşü davranışı ayrıca korunmaktadır.

## 4. RTL kabulü

Arapça üretim varsayılanı değiştirilmez. Mevcut R5–R7 browser paketleri tamamlandıktan sonra yalnız CI test veritabanında `ar` dili geçici olarak etkinleştirilir. R8 gerçek `/ar/` rotasını 320 px ve 1366 px genişliklerde açar.

Kabul koşulları:

- HTTP 200
- `<html lang="ar" dir="rtl">`
- hesaplanan yön `rtl`
- tek H1
- belge düzeyinde yatay taşma olmaması

Bu işlem üretim verisine dokunmaz.

## 5. reduced-motion kabulü

Playwright bağlamı `reducedMotion: 'reduce'` ile açılır. Ön yüzde reveal içeriklerinin görünür kaldığı ve temsilci animasyon/geçiş sürelerinin kapandığı; panel giriş/sistem yüzeyinde de temsilci kart, buton ve alan hareketlerinin kapalı kaldığı doğrulanır.

Mevcut R5 `A-02` denetimleri korunur; R8 bu davranışı fazlar arası kapanış kapısına taşır.

## 6. Performans kapısı

R8 yeni ve kırılgan ağ zamanlaması eşikleri eklemez. Mevcut gerçek Chromium tasarım paketindeki **A-11** sticky scroll akıcılık kapısı korunur ve son doğrulanan R7 akışında **60.0 fps** ölçülmüştür. R8 CI zinciri bu paketi değiştirmeden önce çalıştırdığı için performans regresyonu aynı kapıda başarısız olur.

## 7. CI sırası

Browser işi sırasıyla:

1. R5 animasyon denetimi
2. ortak tasarım denetimi
3. R6 iç sayfa denetimi
4. R7 panel/sistem denetimi
5. Arapça dilini yalnız CI veritabanında etkinleştirme
6. R8 nihai kabul denetimi

Her alt paket kendi çıkış kodunu üretir; herhangi biri başarısız olursa browser işi başarısızdır.

## 8. Kapsam sınırı

Bu kayıt **repo/CI düzeyindeki yeniden tasarım kabulünü** belgeler. Aşağıdakiler ayrıca gerçek canlı hosting ortamında doğrulanmalıdır ve bu CI tarafından kanıtlanmış sayılmaz:

- gerçek alan adı HTTPS ve www/non-www yönlendirmeleri
- canlı e-posta teslimi
- gerçek cron çalışmaları
- canlı yedek/geri yükleme
- Search Console ve Rich Results
- gerçek üretim Lighthouse/PageSpeed ölçümleri
- gerçek kullanıcı cihaz/analitik verisi

Dolayısıyla R8’in yeşil olması kod, tasarım sistemi, responsive davranış, erişilebilirlik etkileşimleri ve regresyonlar açısından yayın adayı kapısını kapatır; canlı hosting doğrulamasının yerine geçmez.

## 9. `main` kapanış kanıtı

PR #10 squash merge ile `main` dalına alındı. Nihai merge commit:

`00c21bc8b8125607062696ff1d9902a7b0ad0180`

Bu exact commit için tetiklenen **CI #247** sonucunda:

- test job: success
- browser job: success
- **238/238** PHP/unit/security/functional test geçti, 0 kaldı, 0 atlandı
- R5, ortak tasarım, R6 ve R7 Chromium paketleri geçti
- R8 320–1920 responsive matrisi geçti
- `/sss` ve `/tesekkurler` edge containment kontrolleri geçti
- %200 etkili reflow, klavye, RTL ve reduced-motion kontrolleri geçti
- A-11 sticky scroll akıcılığı 60.0 fps ölçüldü
- browser kapanış satırı: `TUM R8 NIHAI KABUL DENETIMLERI GECTI`

Bu nedenle R8 repo/CI düzeyinde tamamlanmıştır. Canlı host kabulü R9 kapsamında ayrıca yürütülür.
