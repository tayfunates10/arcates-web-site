# Arcates yeniden tasarım — R0 uygulama envanteri

Kaynak sürüm: `main` / `b96b112d4c0b49be1f682eace598b14bd28e01f5`

Bu kayıt, 8 Eylül 2026 tarihli **ARCATES — Tam Arayüz Yeniden Tasarım Planı** için uygulama başlangıç envanteridir. Amaç, yeni görünümü mevcut güvenlik, içerik, URL ve panel davranışlarını bozmadan modüler PR'larla uygulamaktır.

## 1. Doğrulanan teknik temel

- PHP/MySQL uygulaması; ön yüzde framework/derleme adımı yok.
- Ön yüz stili: `public/assets/css/site.css`.
- Panel stili: `public/assets/css/admin.css`.
- Ön yüz davranışı: `public/assets/js/site.js`.
- Panel davranışı: `public/assets/js/admin.js`.
- Tek düzey içerik slug yapısı korunuyor.
- Çoklu dil ve RTL altyapısı mevcut.
- CI PHP sözdizimi, MySQL testleri ve gerçek Chromium kabul denetimlerini çalıştırıyor.

## 2. Ön yüz route aileleri

| Aile | Route / kaynak | Tasarım fazı |
|---|---|---|
| Ana sayfa | `/` | R5 |
| Örnek siteler | `/referanslar`, `/referanslar/{slug}` | R6 |
| Blog | `/blog`, `/blog/{slug}` | R6 |
| SSS | `/sss` | R6 |
| İletişim | `/iletisim`, `/tesekkurler` | R6 |
| Genel/hizmet/ilçe/sektör | `/{slug}` + veritabanı türü | R6 |
| Kurulum | `/install` | R7 |
| Sistem | `robots.txt`, `sitemap.xml` | Teknik davranış korunur |
| Hata ailesi | 403/404/419/500/503 | R7 |

## 3. Panel route grupları

Panel yolu yapılandırılabilir. Doğrulanan ekran grupları:

1. Giriş / çıkış / hesabım
2. Pano
3. Kullanıcılar
4. Ayarlar
5. Sayfalar
6. Anasayfa bölüm yöneticisi + ilçe koordinatları
7. Menüler
8. Örnek siteler
9. Blog
10. SSS
11. Medya
12. Form kayıtları / CSV / temizleme
13. Yönlendirmeler ve 404 kayıtları
14. SEO / sitemap
15. İstatistik / CSV
16. Yedekleme / geri yükleme
17. İşlem günlüğü

Bu işlevlerin route ve yetki davranışı tasarım değişikliği nedeniyle değiştirilmez.

## 4. Ortak şablon grupları

### Ön yüz kabuğu

- `views/front/layout.php`
- `views/front/partials/head.php`
- `views/front/partials/header.php`
- `views/front/partials/footer.php`
- `views/front/partials/form.php`
- `views/front/partials/breadcrumbs.php`
- `views/front/partials/toc.php`
- `views/front/partials/aside-cta.php`
- `views/front/partials/notice.php`
- `views/front/partials/related-links.php`

### Ana sayfa

- `views/front/home.php`
- `views/front/partials/hero.php`
- `views/front/partials/strip.php`
- `views/front/partials/cards.php`
- `views/front/partials/steps.php`
- `views/front/partials/works.php`
- `views/front/partials/coast.php`
- `views/front/partials/faq.php`
- `views/front/partials/cta.php`

### İç sayfalar

- `page.php`
- `service.php`
- `location.php`
- `sector.php`
- `projects.php`
- `project.php`
- `posts.php`
- `post.php`
- `faqs.php`
- `contact.php`
- `thanks.php`

### Panel

`views/admin` altındaki giriş, dashboard, liste, form, medya, SEO, ayar, istatistik, yedekleme, form kaydı, kullanıcı ve işlem günlüğü ekranları R7 kapsamındadır. R4 foundation yalnızca ortak panel kabuğu ve kontrol ailesini değiştirir.

## 5. Statik görsel envanteri

Repo içinde doğrulanan altı PNG:

| Dosya | Repo boyutu | Plan |
|---|---:|---|
| `apple-touch-icon.png` | 14,262 B | R3 kalite/güvenli alan doğrulaması |
| `favicon-32.png` | 1,215 B | R3 küçük boyut okunurluğu |
| `logo-mark-light.png` | 27,044 B | koyu yüzey marka işareti |
| `logo-mark.png` | 16,333 B | açık yüzey marka işareti |
| `logo-wordmark.png` | 76,379 B | header/panel/footer boyut sistemi |
| `og-default.png` | 64,106 B | R3 / G-09 yeniden üretim |

`public/uploads` Git dışında olduğu için gerçek canlı medya envanteri bu kayıtla doğrulanamaz.

## 6. R4 foundation tasarım sözleşmesi

Bu PR'da uygulanacak ortak kurallar:

- Koyu yüzey: `#081426`
- Koyu yükseltilmiş yüzey: `#10233D`
- Marka mavisi: `#0B4FA8`
- Canlı mavi: `#1C7BF2`
- Açık mavi: `#7FB6FF`
- Açık yüzey: `#FFFFFF`
- Yumuşak açık yüzey: `#F7FAFF`
- Ana metin: `#062244`
- İkincil metin: `#4A6588`
- Koyu yüzey metni: `#F5F8FF`

Kontroller:

- Varsayılan buton min. 48 px ön yüz, min. 44 px panel dokunma alanı.
- Buton köşesi 14 px; tam kapsül yalnız etiket/rozet gibi küçük seçimlerde.
- Büyük yüzey 22 px; küçük kontrol 8 px.
- Görünür odak 3 px.
- Tehlikeli renk yalnız yıkıcı işlemlerde.
- Form etiketleri kalıcı; hata metni alanla programatik bağlı.
- 320 px taşma kontrolü; 940 px altında panel navigasyonu açılır yan yüzeye dönüşür.

## 7. R0'da repo üzerinden doğrulanamayanlar

Aşağıdakiler tasarım planında R0 görevidir ancak yalnız Git deposundan kanıtlanamaz:

- Canlı veritabanındaki gerçek içerik uzunlukları ve tüm yayın durumları.
- `public/uploads` altındaki canlı medya dosyalarının eksiksiz listesi.
- Gerçek canlı site ve panel ekran görüntüleri.
- Gerçek kullanıcı analitiği / kullanılabilirlik verisi.

Bu maddeler ilerleyen kabul turunda canlı ortama erişim olduğunda ayrıca kaydedilecek; repo içeriğinden varsayılmayacaktır.

## 8. PR sırası

1. **R0/R1/R4 foundation** — tokenlar, ortak kontroller, header/footer/form, panel kabuğu.
2. **R5 ana sayfa** — yeni sıra, hero, statik sektör bağlantıları, hizmet/süreç/proje/bölge/SSS/CTA.
3. **R3 görsel varlıklar** — G-01…G-10 ve varlık kayıtları; ana sayfa PR'ı ile gerektiğinde paralel fakat ayrı inceleme.
4. **R6 iç sayfalar** — tüm sayfa aileleri ve dönüşüm akışları.
5. **R7 panel/sistem** — tüm yönetim ekranları, hata ve kurulum ailesi.
6. **R8 kabul** — 320–1920 px matris, RTL, %200 zoom, klavye, reduced-motion, performans ve dokümantasyon.
