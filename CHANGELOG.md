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

### Duzeltildi
- `Router` yer tutucu deseni: `preg_quote` suslu parantezleri kacirdigi icin
  `{slug}` eslesmiyordu.
- `Security::toPlainText` etiket sinirina bosluk koymuyordu; `</h2><p>`
  gecisindeki iki kelime birlesip kelime sayimi eksik cikiyordu.
