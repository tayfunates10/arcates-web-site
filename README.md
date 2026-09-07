# Arcates Web Site

Arcates Yazilim kurumsal sitesi ve yonetim paneli.
Edremit ve Balikesir Korfez bolgesinde yerel aramalardan musteri adayi toplamak
uzere tasarlanmistir.

- **Surum:** `VERSION` dosyasina bakiniz
- **Tam sartname:** [`DOCS.md`](DOCS.md)
- **Calisma kurallari:** [`CLAUDE.md`](CLAUDE.md)

## Gereksinimler

| Bilesen | Gereksinim |
|---------|-----------|
| PHP | 8.1 minimum, 8.2 onerilen |
| Eklentiler | `pdo_mysql`, `mbstring`, `gd` veya `imagick`, `json`, `fileinfo` |
| MySQL | 5.7+ / MariaDB 10.4+ |
| Sunucu | Apache + `mod_rewrite` |

Composer, npm veya derleme adimi yoktur.

## Kurulum

1. Depoyu klonlayin.
2. `config/config.example.php` dosyasini `config/config.php` olarak kopyalayin
   ve veritabani bilgilerini girin.
3. Alan adi document root'unu `public/` klasorune yonlendirin.
4. Tarayicidan `/install` adresini acin; baglanti testi yapilir, `db/schema.sql`
   uygulanir, ilk yonetici olusturulur.
5. `storage/` ve `public/uploads/` klasorlerini yazilabilir yapin.
6. Baslangic icerigini yazin: `php tools/seed_content.php`
   (bolum 4'teki URL haritasinin tamami, ilce sayfalari, referans ve SSS
   ornekleri. Var olan kayitlarin uzerine yazmaz.)
7. Yayina cikmadan once teslim listesini denetleyin:
   `php tools/preflight.php`

Kurulum bitince `storage/installed.lock` yazilir ve `/install` kapanir.

## Testler

```
php tests/run.php                 # tum gruplar
php tests/run.php unit            # yalnizca birim testleri
```

Basarisizlikta cikis kodu 1 doner. Veritabani gerektiren testler icin
`ARC_TEST_DB_NAME` ve kardes ortam degiskenleri tanimlanir; tanimli degilse
o testler atlanir ve surec kirmizi olmaz.

Tarayicida calisan animasyon denetimleri istege baglidir:

```
php -S 127.0.0.1:8321 -t public &
node tools/browser/animation-check.mjs
```

## Araclar

| Arac | Isi |
|------|-----|
| `tools/migrate.php` | Bekleyen sema goclerini uygular |
| `tools/seed_content.php` | Baslangic icerigini yazar |
| `tools/preflight.php` | Yayin oncesi teslim listesini denetler |
| `tools/backup.php` | Veritabani yedegi alir |
| `tools/rollup_visits.php` | Eski ziyaretleri gunluk tabloya toplar |
| `tools/purge_submissions.php` | Saklama suresi dolan form kayitlarini siler |

## Zamanlanmis gorevler

```
0 3 * * * php /home/kullanici/arcates-web-site/tools/backup.php
15 3 * * * php /home/kullanici/arcates-web-site/tools/purge_submissions.php
30 3 * * * php /home/kullanici/arcates-web-site/tools/rollup_visits.php
```

## Lisans

Tum haklari saklidir — Arcates Yazilim.
