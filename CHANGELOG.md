# Degisiklik Gunlugu

Bu dosya [Keep a Changelog](https://keepachangelog.com/tr/1.0.0/) bicimini izler.

## [Yayinlanmamis]

### Eklendi
- Faz 0: Depo iskeleti, `CLAUDE.md`, `DOCS.md`, `.gitignore`, CI is akisi.
- Faz 1: Cekirdek siniflar — `Config`, `Database`, `Router`, `Request`,
  `Response`, `Session`, `Auth`, `Security`, `Validator`, `View`, `Lang`,
  `Settings`, `Mailer`, `Logger`, `Migrator`, `Seeder`, `App`.
- Faz 1: `db/schema.sql` (29 tablo), `tools/migrate.php`, `/install` sihirbazi.
- Faz 1: Hata gorunumleri (403, 404, 419, 500, 503), `robots.txt` ciktisi.
- Faz 1: `lang/tr.php`, `en.php`, `de.php`, `ar.php` arayuz dizeleri.
- Faz 1: `site.css` bolum 1 — tasarim belirtecleri ve sistem sayfasi stilleri.
- Faz 1 testleri: U-01…U-05, U-07, U-12, U-15 ve S-01…S-13, S-17.
- Faz 2: Panel iskeleti — `Admin\Controller` temel sinifi (oturum, rol ve
  CSRF kapilari), giris/cikis, pano, kullanici yonetimi, ayarlar,
  islem gunlugu ekrani.
- Faz 2: `admin.css` ve `admin.js`; panelde satir ici script yok.
- Faz 2 testleri: S-09, S-10 ve panel davranis testleri.
- Faz 3: `Model` tabani (ceviri deseni, cakismayan slug), `Page`, `MenuItem`,
  `Language`, `Redirect` modelleri.
- Faz 3: `Seo` sinifi — meta uretimi, hreflang seti, yapisal veri ve icerik
  skoru (kelime esikleri, H1, meta, alt metin, ic link, ilce benzerligi).
- Faz 3: Panel sayfa yoneticisi (tur filtresi, dil sekmeleri, tam SEO paneli,
  icerik skoru, Google sonuc onizlemesi) ve menu duzenleyici.
- Faz 3: On yuz duzeni, `head`/`header`/`footer` parcalari, `page`, `service`,
  `location`, `sector` sablonlari, kirinti yolu, SSS ve cagri bandi.
- Faz 3: Slug degisiminde otomatik 301; `NotFoundController` yonlendirme
  cozumu ve 404 kaydi.
- Faz 3: Bakim modu `App::handle` icinde devrede.
- Faz 3 testleri: U-06, U-09…U-11, U-13, F-01…F-05, F-10.
- Faz 4: `Media` sinifi — MIME dogrulamali guvenli yukleme, cift uzanti
  reddi, tahmin edilemez dosya adi, `uploads/YYYY/MM/` klasorlemesi,
  thumb/medium/large + WebP varyantlari, SVG temizligi, kullanim yeri
  cozumlemesi ve guvenli silme.
- Faz 4: Panel medya kitapligi — coklu yukleme, izgara gorunum, alt metni
  eksik uyari rozeti, varyant tablosu, kullanimdaki dosya icin silme onayi.
- Faz 4 testleri: U-08, S-05, S-06, F-06, F-07.
- Faz 5: `site.css` bolum 2-15 — ust menu, kahraman, sektor seridi, hizmet
  kartlari, bolge haritasi, surec, referanslar, SSS, cagri bandi, alt bilgi,
  animasyon katmani, duyarli davranis, azaltilmis hareket ve yazdirma.
- Faz 5: `site.js` animasyon motoru — IntersectionObserver ile gorunurluk,
  rAF ile sinirlandirilmis passive scroll, ilerleme cubugu, sabit ust menu,
  sekil suruklenmesi, bolge haritasi cizimi, kesintisiz sektor seridi.
- Faz 5: Anasayfa sablonu ve bolum parcalari (hero, strip, cards, coast,
  steps, works), `HomeSection`, `District`, `Project`, `Faq` modelleri,
  `HomeController`.
- Faz 5: Favicon, apple-touch-icon ve varsayilan OG gorseli.
- Faz 5: `tools/browser/animation-check.mjs` — A testlerinin tarayicidaki
  karsiligi (istege bagli, CI'da zorunlu degil).
- Faz 5 testleri: A-01…A-10, E-03, E-07, O-01, F-14, F-15, F-16.

### Duzeltildi (faz 5)
- `Router` ozel alt desenli yer tutucular: `preg_quote` alt deseni de
  kacirdigi icin `{id:[0-9]+}` ve `{slug:[^/]+}` hicbir zaman eslesmiyordu;
  ic sayfalar ve kimlik alan panel ekranlari acilmiyordu.
- `.skip-link` `top` gecisi ve ust menu `padding` gecisi bolum 7.1 kural 1'i
  ihlal ediyordu; ikisi de kaldirildi.
- 360 px genisligde ust menu tasiyordu; panel duzeni yeniden kuruldu.
- Bos `db/migrations` klasoru git'te tutulmadigi icin temiz klonda iskelet
  denetimi kaliyordu; aciklamali `.gitkeep` eklendi.

### Eklendi (faz 6)
- Panel anasayfa yoneticisi: bolum listesi, ac/kapat anahtari, bolum basina
  duzenleme ekrani, kahraman bolumunde canli onizleme.
- Bolge haritasi duzenleyicisi: ilce noktalari surukle-birak ile
  konumlandirilir, `map_x`/`map_y` otomatik hesaplanir; klavye ile de
  tasinabilir, JavaScript kapaliyken sayi alanlarindan girilebilir.
- Faz 6 testleri: F-14, F-15, F-16 panel tarafi.

### Eklendi (faz 7)
- `sitemap.xml` dinamik uretimi: yalnizca yayinlanmis ve dizine girmesine
  izin verilen icerik, `lastmod` alani, `xhtml:link` ile dil karsiliklari.
- Panel SEO ekrani: `robots.txt` duzenleyici, sitemap durumu, varsayilan
  meta sablonu, Search Console alani, tum sayfalarin meta durumu tablosu.
- `public/.htaccess` icinde `www` kanoniklestirmesi; tercih tek yonde sabit.
- Faz 7 testleri: U-14, F-13, O-01…O-08.

### Eklendi (faz 8)
- Panel yonlendirme ekrani: liste, elle ekleme, dongu kontrolu, silme.
- 404 listesi ve tek tikla yonlendirmeye donusturme; cevrilen kayit
  listeden duser ve isabet sayaci islemeye baslar.
- Dil onekli adresler icin oneksiz yonlendirme kaydi da cozulur.
- Faz 8 testleri: F-11, F-12, S-18 ve yonlendirme zinciri denetimleri.

### Eklendi (faz 9)
- Teklif formu: ad, telefon, e-posta, hizmet, mesaj, KVKK onayi, honeypot,
  zaman damgasi ve CSRF token.
- Spam korumasi: honeypot sessiz reddi, 3 saniye alt siniri, IP basina
  saatlik gonderim siniri, sunucu tarafi dogrulama.
- `source_url`, `referrer`, UTM, dil, IP ve tarayici otomatik saklanir.
- Tesekkur sayfasi ayri adreste ve `noindex`.
- Panel form ekrani: durum etiketleri, not alani, kaynak sayfa, donusum
  raporu, kaynak dagilimi, CSV disa aktarma, saklama suresi temizligi.
- `tools/purge_submissions.php` gunluk gorevi.
- Faz 9 testleri: F-08, F-09, S-14, S-15, S-16, E-06.

### Duzeltildi
- `Router` yer tutucu deseni: `preg_quote` suslu parantezleri kacirdigi icin
  `{slug}` eslesmiyordu.
- `Security::toPlainText` etiket sinirina bosluk koymuyordu; `</h2><p>`
  gecisindeki iki kelime birlesip kelime sayimi eksik cikiyordu.
