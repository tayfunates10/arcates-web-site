# ARCATES — R7 Yönetim Paneli ve Sistem Ekranları Yeniden Tasarım Kaydı

**Tarih:** 8 Eylül 2026  
**Branch:** `redesign/r7-admin`  
**Kaynak:** R6 sonrası doğrulanmış `main` commit `0af46d0923930d399b64ecfe6dd5caee503cb564`

## Amaç

R4 foundation ile oluşturulan panel kabuğunu tam bir yönetim çalışma alanına dönüştürmek; tüm mevcut route, rol, CSRF, veri ve işlem sözleşmelerini koruyarak dashboard, liste, editör, medya, SEO, ayar, yedekleme ve sistem ekranlarını tek Arcates tasarım dili altında birleştirmektir.

## Kapsam

### Yönetim paneli

- Giriş / hesap kabuğu
- Pano ve KPI kartları
- Kullanıcılar
- Ayarlar
- Sayfalar ve içerik editörleri
- Anasayfa bölüm yöneticisi ve ilçe koordinatları
- Menüler
- Örnek siteler / projeler
- Blog
- SSS
- Medya
- Form kayıtları
- Yönlendirmeler ve 404 kayıtları
- SEO / sitemap
- İstatistik
- Yedekleme / geri yükleme
- İşlem günlüğü

### Sistem ailesi

- `/install`
- 403
- 404
- 419
- 500
- 503

## Görsel katmanlar

Panel yükleme sırası:

1. `admin.css`
2. `admin-redesign.css`
3. `admin-r7.css`

Sistem sayfaları yükleme sırası:

1. `site.css`
2. `system-r7.css`

Eski CSS dosyaları geri dönüş tabanı olarak korunur. R7 yalnız son override katmanında yeni görsel hiyerarşiyi uygular.

## Panel tasarım sözleşmesi

- Koyu ve sakin yan navigasyon, belirgin aktif bölüm, 44px navigasyon hedefi.
- Daha geniş, yarı saydam üst çalışma çubuğu ve belirgin sayfa H1'i.
- Dashboard KPI kartlarında üst vurgu rayı, büyük sayısal değer ve düşük maliyetli dekor.
- Filtreler bağımsız araç çubuğu yüzeyi olarak ayrılır.
- Liste tablolarında daha belirgin başlık satırı, rahat satır yüksekliği ve kontrollü yatay kaydırma yüzeyi kullanılır.
- Formlarda 14px kontrol yarıçapı, geniş metin alanları, gruplanmış fieldset yüzeyleri ve hafif sticky kaydetme alanı kullanılır.
- Tehlikeli eylemler marka eylemlerinden ayrı kırmızı rol taşır; veri veya işlem anlamı değişmez.
- Sekmeler, SEO sorunları, SERP önizlemesi, detay alanları, menü satırları, medya kartları, bölge düzenleyicisi, hero önizlemesi, checkbox/switch ve geri yükleme satırları aynı yüzey ailesine geçirilir.
- 940px altında mevcut off-canvas navigasyon korunur; 640px altında KPI, filtre ve form eylemleri tek kolon davranışına geçer.
- `prefers-reduced-motion` altında R7 geçişleri kapatılır.

## Sistem tasarım sözleşmesi

- Koyu tam ekran Arcates zemini.
- 28px büyük beyaz sistem kartı ve üst marka vurgu çizgisi.
- Kurulumda üç adımlı durum yüzeyi.
- Hata ekranlarında büyük HTTP kodu / durum işareti.
- Form ve aksiyonlar panel kontrol ailesiyle uyumlu.
- Mobilde tek kolon, yatay taşmasız davranış.

## Korunan iş ve güvenlik kuralları

R7 aşağıdakileri değiştirmez:

- panel route adresleri ve controller eşlemeleri
- admin/editor rol yetkileri
- giriş doğrulaması ve login rate limit
- logout POST + CSRF zorunluluğu
- form POST aksiyonları ve CSRF alanları
- yayın/taslak/silme işlevleri
- medya güvenlik doğrulaması
- yönlendirme ve 404 davranışı
- SEO/sitemap üretim mantığı
- yedekleme ve geri yükleme kuralları
- `/install` kilit davranışı
- 403/404/419/500/503 HTTP ve robots semantiği

## Otomatik doğrulama

### PHP sözleşme testleri

- `F-R7-01` — panel R7 stil yükleme sırası
- `F-R7-02` — panel ekran ailelerinin CSS kapsamı
- `F-R7-03` — kurulum ve tüm hata şablonlarının sistem katmanı
- `F-R7-04` — sistem desktop/mobile/reduced-motion kapsamı
- `F-R7-05` — route ve güvenlik formu sözleşmelerinin korunması
- `F-R7-06` — gerçek Chromium R7 matrisinin CI içinde çalışması

R6 sonunda 223 test vardı. R7 ile beklenen toplam **229 testtir**; nihai sayı CI çıktısıyla doğrulanır.

### Gerçek Chromium R7 matrisi

CI yalnız `arcates_browser` test veritabanında geçici bir `admin` kullanıcısı üretir. Bu kullanıcı üretim verisine yazılmaz ve repository içinde gerçek kullanıcı/şifre olarak kullanılmaz.

Normal `/panel/giris` formundan oturum açıldıktan sonra temsilci ekranlar:

- `/panel`
- `/panel/sayfalar`
- `/panel/sayfalar/yeni`
- `/panel/medya`
- `/panel/seo`
- `/panel/ayarlar`
- `/panel/yedekleme`
- `/panel/yonlendirmeler`

Masaüstü kabulü:

- HTTP 200
- tek H1
- `admin-r7.css` gerçekten yüklenmiş
- ortak workspace shell
- yüzey yarıçapları ve navigasyon hedefleri
- yatay belge taşması yok
- dashboard KPI hiyerarşisi
- birincil ve tehlikeli eylemlerin görsel ayrımı

Mobil kabulü (390px):

- dashboard, sayfalar, medya, SEO ve ayarlar ekranlarında belge yatay taşması yok
- mobil menü düğmesi görünür
- off-canvas menü açılıp kapanır
- `aria-expanded` güncellenir
- Escape menüyü kapatır ve odağı menü düğmesine döndürür

Sistem kabulü:

- gerçek olmayan rota üzerinden 404 ekranı ve `system-r7.css`
- test sırasında `storage/installed.lock` geçici kaldırılarak `/install` ekranının render edilmesi
- kontrol sonunda kilidin geri oluşturulması

## Performans yaklaşımı

R6'da yaşanan sticky paint regresyonu nedeniyle R7'de büyük blur/backdrop zincirleri ve pahalı sticky gölgeler sınırlandırılır. Uzun tablolar ve formlar için dekor yerine sınır, düz yüzey ve küçük gölgeler tercih edilir.

## Sonraki faz

R8, tüm yeniden tasarımın nihai kabul turudur: 320–1920px matris, %200 zoom, klavye, RTL, reduced-motion, performans, erişilebilirlik ve dokümantasyon kapanışı.
