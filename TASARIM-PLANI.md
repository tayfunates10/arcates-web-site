> **Tarihsel plan:** Aşağıdaki T1–T6 başlangıç durumu güncel devam noktası değildir. 9 Eylül 2026 devam kaydı için `REDESIGN-R3-ASSETS.md`, güncel davranış için `DOCS.md` esas alınır. R5/R6/R7 ve otomatik R8 main dalına girmiştir; R3 görsel üretimi kısmen açıktır.

# Arcates Web Site — CSS / JS Yeniden Tasarım Planı

Bu belge `DOCS.md`'yi **değiştirmez**, uygular. Çelişki halinde `DOCS.md` esastır;
planın gerektirdiği `DOCS.md` değişiklikleri bölüm 10'da tek tek listelenmiştir.

Sürüm hedefi: `1.1.0` (görsel sürüm). `1.0.0` yayına çıktıktan sonra başlar.

---

## 1. Bugünkü durum — ölçülmüş bulgular

Çalışan sitede (1440×900, gerçek Chromium) ölçüldü. Bunlar tahmin değil, sayı.

| Bulgu | Ölçüm | Sorun |
|---|---|---|
| Uzay ölçeği | **25+ farklı px değeri** (2,4,5,6,8,9,10,12,13,14,15,16,18,20,22,24,26,30,34,36,40,46,62,84,92) | Her yeni bileşen kendi sayısını uyduruyor |
| Köşe yarıçapı | **12 farklı değer** | Yüzeyler aynı aileden görünmüyor |
| Gölge | **7 farklı reçete** | Derinlik hiyerarşisi okunmuyor |
| Kırılma noktası | **8 farklı** (220–940 px) | Ara genişliklerde düzen tahmin edilemiyor |
| `!important` | **19 adet** | Özgüllük savaşı; her düzeltme bir sonrakini zorlaştırıyor |
| Blog listesi satır uzunluğu | **110 karakter** | Rahat okuma aralığı 45–75; göz satır sonunu kaybediyor |
| Bölüm ritmi (anasayfa) | **8 bölümün 6'sı aynı 84px** | Sayfa metronom gibi; hiyerarşi yok |
| İçerik sayfalarında görsel | **0** | İlçe/hizmet/blog sayfaları düz metin duvarı |
| İletişim ilk ekranında form | **yok** | Ziyaretçi dönüşüm ögesini görmüyor |
| İlçe sayfası | 500+ kelime, görsel çapa yok | En çok arama trafiği alan sayfa en tasarımsız sayfa |

**Doğru teşhis:** Tasarım kötü değil, **yarım.** Kahraman bölümü ve renk paleti
iyi; iç sayfalar hiç tasarlanmamış ve altta bir sistem yok. Bu yüzden plan
"daha güzel yapalım" değil, **"sistemi bitir, iç sayfaları tasarla."**

### Bozuk olmayan, dokunulmayacak şeyler

Bunları churn etmek zarar verir:

- `public/assets/js/site.js` — `DOCS.md` 7'deki yedi değişmez kurala harfiyen
  uyuyor, ES5, bağımlılıksız, `IntersectionObserver` + `unobserve` + `rAF`
  düzeni doğru. **Yeniden yazılmaz, üzerine eklenir.**
- `DOCS.md` 6'daki 14 renk belirteci. Hiçbiri silinmez.
- `DOCS.md` 7.2 süre tablosu ve 7.3 açılış sırası. Şartname bunları
  "değiştirilmeden korunur" diye yazıyor.
- `asset()` yardımcısı — `filemtime` karmasıyla önbellek kırıyor; yeni CSS
  yayına çıktığında ziyaretçi eskisini görmez.
- Şablonlarda sabit renk kodu yok (denetlendi, temiz). Bu disiplin korunur.

---

## 2. Değişmez kısıtlar

Plan bunların içinde kalır. Hiçbiri pazarlık konusu değil.

1. **Yapı adımı yok.** Sass, PostCSS, Tailwind, npm paketi yok (`CLAUDE.md` 4).
   Ne yazılırsa tarayıcıya o gider.
2. **Tek dosya.** `DOCS.md` 3 klasör yapısını `css/site.css` ve `css/admin.css`
   olarak sabitliyor. CSS dosyalara bölünemez → mimari **dosya içinde**
   `@layer` ile kurulur.
3. **CSP satır içi stili engelliyor.** `style-src 'self' https://fonts.googleapis.com`
   — `'unsafe-inline'` yok. Yani `style="..."` niteliği **çalışmaz.** Dinamik
   her değer (grafik yüksekliği, kademe gecikmesi, ilçe konumu) `data-*`
   niteliğiyle taşınır, JS ya da sınıf uygular. Bugünkü `data-depth` /
   `data-at` deseni budur ve sürdürülür.
4. **Tek harici kaynak Google Fonts.** Üçüncü yazı tipi eklenmez (`DOCS.md` 6).
5. **Şablona sabit metin gömülmez** (`CLAUDE.md` 8). Yeni bileşenlerin her
   metni panelden gelir.
6. **Animasyon yalnızca `transform` / `opacity`** (`DOCS.md` 7.1).
7. **Performans bütçesi:** P-06 toplam ilk yük < 800 KB, P-07 LCP < 2.5 sn,
   P-02 PageSpeed mobil ≥ 70 / masaüstü ≥ 90.

---

## 3. Tasarım sistemi

`site.css` başında, `@layer tokens` içinde tanımlanır.

### 3.1 Uzay ölçeği — 25 değer yerine 10

```css
--sp-1:   4px;   --sp-2:   8px;   --sp-3:  12px;   --sp-4:  16px;
--sp-5:  24px;   --sp-6:  32px;   --sp-7:  48px;   --sp-8:  64px;
--sp-9:  96px;   --sp-10:128px;
```

Kural: **ölçek dışı px yazılmaz.** Bir değer ölçeğe uymuyorsa ya ölçek
yanlıştır ya tasarım. İkisi de tartışılır, kaçamak yazılmaz.

### 3.2 Bölüm ritmi — metronom yerine nefes

Bugün 6 bölüm de 84px. Üç ritim tanımlanır, bölümler bilinçli seçer:

```css
--sec-tight: var(--sp-7);   /* 48px — bağlı iki bölüm arası */
--sec:       var(--sp-8);   /* 64px — varsayılan */
--sec-loose: var(--sp-9);   /* 96px — konu değişimi */
```

940px üstünde her biri bir basamak büyür (`--sp-8 / --sp-9 / --sp-10`).

### 3.3 Tipografi ölçeği — akışkan, JS'siz

`clamp()` ile; ara genişlikte kırılma noktası zıplaması olmaz.

```css
--fs-xs:   clamp(12px, .70rem + .15vw, 13px);
--fs-sm:   clamp(14px, .82rem + .18vw, 15px);
--fs-base: clamp(16px, .94rem + .20vw, 17px);
--fs-lg:   clamp(18px, 1.02rem + .32vw, 20px);
--fs-xl:   clamp(21px, 1.12rem + .60vw, 24px);
--fs-2xl:  clamp(25px, 1.28rem + 1.10vw, 32px);
--fs-3xl:  clamp(30px, 1.45rem + 1.90vw, 42px);
--fs-4xl:  clamp(34px, 1.60rem + 3.30vw, 62px);
```

`h1: --fs-4xl` · `h2: --fs-3xl` · `h3: --fs-2xl` · `h4: --fs-xl`

**Türkçe notu:** Türkçe uzun kelimeli bir dil ("müşterilerinizin",
"değerlendirmelerinizi"). Alt sınır 34px, 360px ekranda taşmayacak şekilde
seçildi; A-08 testi bunu koruyor. Başlıklarda `hyphens` **kullanılmaz** —
Türkçe hecelemede tarayıcı sözlüğü güvenilir değil; bunun yerine
`text-wrap: balance` (destekleyen tarayıcıda) ve `overflow-wrap: break-word`.

### 3.4 Okuma genişliği

```css
--measure:      68ch;   /* gövde metni */
--measure-lead: 46ch;   /* giriş cümlesi, kart metni */
```

Blog listesindeki 110 karakterlik satır bununla biter.

### 3.5 Köşe ve gölge — 12+7 yerine 4+3

```css
--r-sm: 8px;  --r: 14px;  --r-lg: 22px;  --r-full: 999px;

--sh-1: 0 1px 2px rgba(6,34,68,.06), 0 2px 8px rgba(6,34,68,.05);
--sh-2: 0 4px 12px rgba(6,34,68,.07), 0 12px 32px rgba(6,34,68,.08);
--sh-3: 0 8px 24px rgba(6,34,68,.09), 0 28px 64px rgba(6,34,68,.10);
```

Renkli aksan gölgeleri (sarı/mor/mercan şekiller) tek reçeteye iner:
bileşen `--sh-tint` değişkenini kendi rengiyle doldurur.

### 3.6 Kırılma noktaları — 8 yerine 4

```css
/* --bp-xs:  480px */   /* tek sütun -> içerik nefes alır */
/* --bp-sm:  720px */   /* DOCS 5.3: şekil %60, süzülme kapanır */
/* --bp-md:  940px */   /* DOCS 5.3: ızgara tek sütuna düşer */
/* --bp-lg: 1180px */   /* wrap tam genişlik */
```

720 ve 940 şartnameden geliyor, uydurma değil. Diğer 4 nokta (220, 420, 640,
660, 820) bu dörde toplanır.

### 3.7 Anlamsal renk katmanı

`DOCS.md` 6'daki 14 belirteç **hue adıyla** tanımlı (`--blue`, `--sun`).
Bu yüzden şablon "hangi mavi" diye seçmek zorunda kalıyor ve kontrast
hataları sızıyor. Üstüne **rol katmanı** eklenir — hiçbir belirteç silinmez,
hepsi rollerin kaynağı olur:

```css
--bg:           var(--white);
--bg-sunken:    var(--mist-2);
--bg-raised:    var(--white);
--bg-invert:    var(--navy);

--fg:           var(--navy);
--fg-muted:     var(--ink-soft);
--fg-on-invert: var(--white);

--brand:        var(--blue);
--brand-strong: var(--blue-bright);
--brand-fg:     var(--white);

--border:        var(--line);
--border-strong: rgba(11,79,168,.28);
--focus:         var(--blue-bright);
```

Bundan sonra bileşen `var(--fg-muted)` yazar, `var(--ink-soft)` yazmaz.
Kontrast bir kere rolde doğrulanır, her kullanımda tekrar denetlenmez.

---

## 4. CSS mimarisi — `@layer`

19 `!important`'ın kökü özgüllük savaşı. Çözüm katman sırası:

```css
@layer tokens, reset, base, layout, components, pages, motion, utilities;
```

Katman sırası özgüllükten **güçlüdür**: `utilities` katmanındaki tek sınıflı
bir kural, `components` katmanındaki üç seçicili kuralı `!important` olmadan
ezer. Yapı adımı gerektirmez, tüm güncel tarayıcılarda çalışır.

| Katman | İçerik |
|---|---|
| `tokens` | Bölüm 3'teki tüm değişkenler |
| `reset` | Kutu modeli, kenar boşluğu sıfırlama, `:focus-visible` |
| `base` | `body`, başlıklar, `p`, `a`, liste, tablo, form ögesi |
| `layout` | `.wrap`, `.section`, ızgara iskeletleri |
| `components` | Buton, kart, rozet, form, SSS, kırıntı, not, şerit |
| `pages` | Yalnızca bir sayfa türüne ait düzenler (ilçe, iletişim) |
| `motion` | `html.js` altındaki gizli başlangıçlar, `@keyframes`, geçişler |
| `utilities` | `.u-measure`, `.u-stack-*`, `.u-hide-sm` gibi tek işli sınıflar |

**Hedef:** `!important` sayısı **19 → 0**. Bu ölçülebilir bir kabul şartıdır.

`motion` katmanı ayrı tutuluyor çünkü `DOCS.md` 7.1 kural 2 gereği tüm gizli
başlangıç durumları `html.js` altında olmalı; tek katmanda toplanınca bu kural
denetlenebilir hale gelir (tek `grep`).

---

## 5. JS planı — yeniden yazma yok, dört ekleme

`site.js` çalışıyor ve kurallara uyuyor. Mevcut modüller korunur:
`initReveal`, `initHead`, `initNav`, `initHero`, `initParallax`, `initCoast`,
`initStrip`.

Eklenecek dört modül, hepsi aynı desende (rAF + `IntersectionObserver` +
`unobserve` + `prefersReducedMotion()` kapısı):

| Modül | İş | Neden |
|---|---|---|
| `initToc()` | İlçe/hizmet sayfasında yan içindekiler; görünen başlığı işaretler | 500+ kelimelik sayfada yönelim; kalış süresi ve iç link değeri |
| `initStickyCta()` | İç sayfada teklif kartını kaydırma boyunca tutar (`position: sticky`, JS yalnızca gizle/göster) | Dönüşüm ögesi ekranı terk etmiyor |
| `initFormState()` | Alan doldukça yerel doğrulama, gönder düğmesi durumu, çift gönderim kilidi | Form hatası sunucuya gitmeden görünür |
| `initReveal()` genişletme | Kademe gecikmesi `data-reveal-step` ile; CSP yüzünden satır içi `style` yok, sınıf uygulanır | Yeni bileşenler aynı giriş dilini kullanır |

**Eklenmeyecekler:** kaydırma tetikli sayaç, imleç efekti, tam sayfa geçiş,
otomatik dönen slider. Hepsi `DOCS.md` 7.1 kural 7'yi ya da 60 fps hedefini
(7.5) riske atar.

Tahmini artış: `site.js` 333 → ~470 satır, ~11 KB → ~15 KB. P-06 bütçesi
(800 KB) rahat.

---

## 6. Sayfa türü yeniden tasarımları

Asıl iş burada. Anasayfa zaten tasarlı; **trafiği alan iç sayfalar tasarımsız.**

### 6.1 İlçe sayfası (`location`) — en yüksek öncelik

8 sayfa, her biri 500+ kelime, bugün düz metin duvarı, tek görsel yok.

```
┌───────────────────────────────────────────────────────┐
│ kırıntı yolu                                          │
├───────────────────────────────────────────────────────┤
│ ROZET: İLÇE ADI                                       │
│ H1 · giriş cümlesi (--measure-lead)                   │
│ ┌─ hızlı olgu şeridi ────────────────────────────┐    │
│ │ Yerinde görüşme · Ortalama süre · Başlangıç    │    │  ← YENİ
│ └────────────────────────────────────────────────┘    │
├──────────────────────────────┬────────────────────────┤
│ İÇERİK (--measure)           │ YAN SÜTUN (sticky)     │
│  H2 + paragraf               │  ┌──────────────────┐  │
│  ┌ vurgu kutusu ─────┐       │  │ İçindekiler      │  │  ← YENİ
│  │ "Şunu bilin:"     │       │  │ (kaydırma takip) │  │
│  └───────────────────┘       │  ├──────────────────┤  │
│  H2 + liste                  │  │ Teklif kartı     │  │  ← YENİ
│  ┌ ilçe örnek sitesi kartı ┐ │  │ tel · WhatsApp   │  │
│  └─────────────────────────┘ │  └──────────────────┘  │
├──────────────────────────────┴────────────────────────┤
│ İlçeye özel SSS (details/summary — mevcut)            │
│ Komşu ilçeler şeridi                                  │  ← YENİ
│ Çağrı bandı                                           │
└───────────────────────────────────────────────────────┘
```

- **İçindekiler + komşu ilçeler** iç link sayısını artırır → `Seo::countInternalLinks`
  eşiği ve F-P12-f testi güçlenir.
- **Benzerlik riski yok:** eklenen bileşenlerin metni her ilçede farklı
  (kendi başlıkları, kendi örnek sitesi, kendi komşuları). F-P12-c'nin %70
  eşiği yeniden ölçülür — kabul şartı bölüm 8'de.
- Görsel yok, **kutu ve tipografi ile çapa** kuruluyor. İşletme fotoğraf
  yüklerse Medya ekranından bağlanır; tasarım fotoğrafa bağımlı değil.

### 6.2 İletişim sayfası — dönüşüm sorunu

Bugün: başlık → 4 ekran metin → **sonra** form. Ziyaretçi formu görmüyor.

Yeni: **iki sütun.** Sol prose, sağda ilk ekranda görünen yapışkan kart:
form + telefon + WhatsApp + adres + çalışma saatleri. 940px altında kart
metnin **üstüne** çıkar, metnin altına değil.

Şablon değişikliği: `views/front/contact.php` — `form` parçası `prose`'un
sonrasından yan sütuna taşınır. Parçanın kendisi (`partials/form.php`)
değişmez, yalnızca yerleşimi değişir.

### 6.3 Blog listesi ve yazı

- Liste: giriş paragrafı `--measure`'a iner (110ch → 68ch).
- Kart: kategori rozeti + tarih + başlık + özet + "okuma süresi" (kelime
  sayısından hesaplanır, panelden metin gerektirmez).
- Yazı sayfası: `--measure` gövde, yan sütunda içindekiler + son yazılar,
  sonunda çağrı bandı. Kapak görseli **isteğe bağlı** — yoksa düzen bozulmaz
  (işletme kendi fotoğrafını sonra bağlayacak).

### 6.4 Örnek siteler listesi ve detayı

- Liste kart ızgarası: sektör rozeti + ilçe + başlık + tek cümle.
- Uyarı notu (`projects_notice`) listenin **üstünde**, göz kaçıramayacak yerde
  kalır — bugünkü davranış korunur, yalnızca stili sisteme uyar.
- Detay: "Çözdüğü ihtiyaç" / "Örnekteki bölümler" başlıkları kart yüzeyine
  alınır; okuma genişliği uygulanır.

### 6.5 Hizmet ve sektör sayfaları

İlçe düzeninin aynısı, yan sütunda "diğer hizmetler" listesi.

### 6.6 Anasayfa

Yeniden tasarlanmaz — **ritmi düzeltilir.** Bölümler bölüm 3.2'deki üç
ritmi kullanır, kart/gölge/köşe sisteme iner. Kahraman bölümü, açılış sırası
ve süreler (`DOCS.md` 7.2, 7.3) **aynen korunur.**

### 6.7 Panel (`admin.css`)

Bu sürümde **kapsam dışı.** 575 satır, çalışıyor, işletme dışı kullanıcı
görmüyor. Yalnızca ortak belirteçler paylaşılır. Ayrı bir faz olarak
sonra ele alınır.

---

## 7. Fazlar

Her faz ayrı dal, ayrı PR (`CLAUDE.md` 2: tek seferde tek modül, tek dal).
Her fazın sonunda testler + `CHANGELOG.md` + `DOCS.md` (`CLAUDE.md` 9).

| # | Faz | Dosyalar | Görsel değişiklik | Kabul |
|---|---|---|---|---|
| **T1** | Belirteçler ve katmanlar | `site.css` (yalnız `tokens`+`reset`+`base`), `DOCS.md` 6 | **Kasıtlı olarak yok** — normalleştirme | `!important` 19→0, A/E testleri aynı, görsel fark ≤ eşik |
| **T2** | Düzen ve bileşenler | `site.css` (`layout`+`components`), `partials/*` sınıf adları | Kart/buton/form tek aileye iner | E-02 odak halkası, E-04 kontrast, A-08 360px |
| **T3** | İlçe + hizmet + sektör düzeni | `location.php`, `service.php`, `sector.php`, yeni `partials/toc.php`, `partials/aside-cta.php`, `site.js` `initToc`/`initStickyCta` | Yan sütun, içindekiler, teklif kartı | F-P12-b/c/d/f yeniden geçer, iç link artar |
| **T4** | İletişim + form | `contact.php`, `site.js` `initFormState` | Form ilk ekranda | F-09 form akışı, S-16 spam kapıları bozulmadı |
| **T5** | Blog + örnek siteler | `posts.php`, `post.php`, `projects.php`, `project.php` | Okuma genişliği, kart ızgarası | F-P14-*, F-P15-* geçer |
| **T6** | Anasayfa ritmi ve cila | `home.php`, `partials/*`, `site.css` `pages`+`motion` | Bölüm nefesi | **A-01…A-10 tamamı**, P-04 60 fps, P-07 LCP |

T1 kasıtlı olarak görsel değişiklik içermez. Sistem önce oturur, sonra
üstüne tasarım yapılır — tersi, her fazda geri dönmek demek.

---

## 8. Test etkisi

### Kırılması beklenen mevcut testler

| Test | Neden | Yapılacak |
|---|---|---|
| A-03 açılış 1.4 sn | Yeni bileşenler açılışa girerse | Yeni bileşenler kahraman açılışına **girmez**; ölçüm korunur |
| A-05 geri kaydırma | Yeni `data-reveal` ögeleri | `unobserve` deseni aynen kullanılır |
| A-08 360px taşma | Yeni yan sütun | 940 altında yan sütun tek sütuna düşer |
| E-01/E-02 klavye + odak | Yeni içindekiler ve yapışkan kart | Odaklanabilir; `:focus-visible` `reset` katmanında |
| E-04 kontrast | Yeni rol katmanı | Roller bir kere doğrulanır, test rolleri denetler |
| E-05 başlık hiyerarşisi | İçindekiler `h2` üretmemeli | İçindekiler `nav` + `ul`, başlık üretmez |
| F-P12-c benzerlik %70 | İlçe sayfalarına ortak bileşen ekleniyor | Ölçüm **yeniden alınır**; eşiği aşarsa bileşen metni ilçeye özelleşir |
| F-P12-f iç link ≥ 2 | İçindekiler + komşu ilçeler | Sayı artar, test güçlenir |

### Yazılacak yeni testler

| ID | Senaryo | Grup |
|---|---|---|
| `F-T1-a` | `site.css` ölçek dışı px içermiyor (belirteç dışında) | functional |
| `F-T1-b` | `site.css` içinde `!important` yok | functional |
| `F-T1-c` | `@layer` sırası tanımlı ve tüm kurallar bir katmanda | functional |
| `F-T2-a` | Şablonlarda `style="` niteliği yok (CSP kapısı) | security |
| `F-T3-a` | İlçe sayfası içindekiler üretiyor, her başlığa çapa var | functional |
| `F-T3-b` | Yan sütun 940px altında metnin üstüne çıkıyor | animasyon (tarayıcı) |
| `F-T4-a` | İletişim formu ilk ekranda (1440×900 ve 390×844) | animasyon (tarayıcı) |
| `A-11` | Yapışkan kart kaydırmada 60 fps koruyor | animasyon |
| `E-08` | İçindekiler klavyeyle gezilebilir, `aria-current` doğru | erişilebilirlik |

Ölçüm testleri (`F-T1-a`, `F-T1-b`) **kaynak metni denetler** — veritabanı
gerektirmez, CI'nin atlama yasağı kapısından sorunsuz geçer.

### Her fazda çalıştırılacak

```
php tests/run.php
find app views config tests -name "*.php" -print0 | xargs -0 -n1 php -l
node tools/browser/animation-check.mjs        # T2'den sonra her fazda
php tools/preflight.php
```

---

## 9. Riskler ve karar noktaları

### Karar bekleyenler

1. **`DOCS.md` 6 genişletilsin mi?** Rol katmanı (3.7) belirteç **eklemek**
   demek. Hiçbir mevcut belirteç silinmiyor, ama şartname tablosu büyüyor.
   *Öneri: evet, ekle.* Bugünkü tablo hue adlı ve şablonu kontrast seçmeye
   zorluyor.
2. **İlçe sayfalarına yan sütun, benzerlik oranını yükseltir mi?** Ortak
   bileşen = ortak metin. Ölçüm T3'te alınacak; %70'e yaklaşırsa içindekiler
   metni sayfanın kendi başlıklarından üretildiği için zaten farklılaşır,
   komşu ilçeler listesi de her sayfada farklıdır. *Risk düşük ama ölçülmeden
   birleştirilmez.*
3. **Panel yeniden tasarımı bu sürümde mi?** *Öneri: hayır.* `admin.css` 575
   satır, çalışıyor, dış kullanıcı görmüyor. Ayrı sürüm.

### Riskler

| Risk | Etki | Önlem |
|---|---|---|
| `@layer` eski tarayıcı | Katmansız tarayıcıda tüm kurallar aynı ağırlıkta → düzen bozulur | Güncel tüm tarayıcılar destekliyor; T1'de gerçek Chromium ile doğrulanır. Hedef kitle (yerel işletme müşterisi) mobil ağırlıklı ve güncel |
| Yapışkan yan sütun + parallax | 60 fps düşer (P-04) | `position: sticky` CSS'tir, JS değil. `DOCS.md` 7.5 sırası: önce süzülme, sonra parallax kapanır |
| Akışkan tipografi + CLS | P-03 CLS < 0.1 bozulur | `clamp()` düzen kayması yaratmaz (yükleme anında son değer); yazı tipi `display=swap` zaten mevcut, metrik uyumlu yedek yığın tanımlanır |
| Faz sayısı | 6 PR, uzun süre yarım görünüm | T1 görsel değişiklik içermiyor; T2'den sonra her faz kendi başına yayınlanabilir durumda |

---

## 10. Gerekecek `DOCS.md` değişiklikleri

Planın şartnameye dokunduğu tek yerler. Hepsi **ekleme**, hiçbiri silme:

| Bölüm | Değişiklik | Faz |
|---|---|---|
| 6 | Uzay / tipografi / köşe / gölge ölçekleri ve rol katmanı tablosu eklenir. 14 renk belirteci **aynen kalır** | T1 |
| 6 | "Ölçek dışı px yazılmaz" kuralı ve `@layer` sırası eklenir | T1 |
| 5.3 | Mobil davranışa iç sayfa yan sütunu kuralı eklenir (940 altında üste çıkar) | T3 |
| 7.2 | **Değişmez.** Süre tablosu korunur | — |
| 7.3 | **Değişmez.** Açılış sırası korunur | — |
| 14.5 | `A-11` yapışkan kart kare hızı eklenir | T3 |
| 14.7 | `E-08` içindekiler klavye erişimi eklenir | T3 |
| 3 | **Değişmez.** Klasör yapısı tek `site.css` olarak kalır | — |

---

## 11. Başlangıç

Sıradaki iş **T1**. Dal adı: `claude/tasarim-t1-belirtecler`.

T1'in tek çıktısı: `site.css`'in ilk üç katmanı (`tokens`, `reset`, `base`)
yeni ölçeklerle yazılmış, `!important` sıfırlanmış, görsel çıktı bugünküyle
aynı. Ölçüm testleri `F-T1-a…c` yazılır. `DOCS.md` 6 güncellenir.
