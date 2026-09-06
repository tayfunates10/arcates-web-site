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

Kurulum bitince `storage/installed.lock` yazilir ve `/install` kapanir.

## Testler

```
php tests/run.php
```

Basarisizlikta cikis kodu 1 doner.

## Zamanlanmis gorevler

```
0 3 * * * php /home/kullanici/arcates-web-site/tools/backup.php
30 3 * * * php /home/kullanici/arcates-web-site/tools/rollup_visits.php
```

## Lisans

Tum haklari saklidir — Arcates Yazilim.
