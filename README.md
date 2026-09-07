# Arcates Web Site

Arcates Yazılım kurumsal sitesi ve yönetim paneli.
Edremit ve Balıkesir Körfez bölgesinde yerel aramalardan müşteri adayı toplamak
üzere tasarlanmıştır.

- **Sürüm:** `VERSION` dosyasına bakınız
- **Tam şartname:** [`DOCS.md`](DOCS.md)
- **Çalışma kuralları:** [`CLAUDE.md`](CLAUDE.md)

## Gereksinimler

| Bileşen | Gereksinim |
|---------|-----------|
| PHP | 8.1 minimum, 8.2 önerilen |
| Eklentiler | `pdo_mysql`, `mbstring`, `gd` veya `imagick`, `json`, `fileinfo` |
| MySQL | 5.7+ / MariaDB 10.4+ |
| Sunucu | Apache + `mod_rewrite` |

Composer, npm veya derleme adımı yoktur.

## Kurulum

1. Depoyu klonlayın.
2. `config/config.example.php` dosyasını `config/config.php` olarak kopyalayın
   ve veritabanı bilgilerini girin.
3. Alan adı document root'unu `public/` klasörüne yönlendirin.
4. Tarayıcıdan `/install` adresini açın; bağlantı testi yapılır, `db/schema.sql`
   uygulanır, ilk yönetici olusturulur.
5. `storage/` ve `public/uploads/` klasörlerini yazılabilir yapın.
6. Başlangıç içeriğini yazın: `php tools/seed_content.php`
   (bölüm 4'teki URL haritasının tamamı, ilçe sayfaları, referans ve SSS
   örnekleri. Var olan kayıtların üzerine yazmaz.)
7. Yayına çıkmadan önce teslim listesini denetleyin:
   `php tools/preflight.php`

Kurulum bitince `storage/installed.lock` yazılır ve `/install` kapanır.

## Testler

```
php tests/run.php                 # tum gruplar
php tests/run.php unit            # yalnizca birim testleri
```

Başarısızlıkta çıkış kodu 1 döner. Veritabanı gerektiren testler için
`ARC_TEST_DB_NAME` ve kardeş ortam değişkenleri tanımlanır; tanımlı değilse
o testler atlanir ve süreç kırmızı olmaz.

Tarayıcıda çalışan animasyon denetimleri istege bağlıdır:

```
php -S 127.0.0.1:8321 -t public &
node tools/browser/animation-check.mjs
```

## Araclar

| Araç | İşi |
|------|-----|
| `tools/migrate.php` | Bekleyen şema göçlerini uygular |
| `tools/seed_content.php` | Başlangıç içeriğini yazar |
| `tools/preflight.php` | Yayın öncesi teslim listesini denetler |
| `tools/backup.php` | Veritabanı yedeği alır |
| `tools/rollup_visits.php` | Eski ziyaretleri günlük tabloya toplar |
| `tools/purge_submissions.php` | Saklama süresi dolan form kayıtlarını siler |

## Zamanlanmış görevler

```
0 3 * * * php /home/kullanici/arcates-web-site/tools/backup.php
15 3 * * * php /home/kullanici/arcates-web-site/tools/purge_submissions.php
30 3 * * * php /home/kullanici/arcates-web-site/tools/rollup_visits.php
```

## Lisans

Tüm hakları saklıdır — Arcates Yazılım.
