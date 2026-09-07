# Arcates Web Site

Arcates Yazılım kurumsal sitesi ve yönetim paneli.
Edremit ve Balıkesir Körfez bölgesinde yerel aramalardan müşteri adayı toplamak
üzere tasarlanmıştır.

- **Sürüm:** `VERSION` dosyasına bakınız
- **Tam şartname:** [`DOCS.md`](DOCS.md)
- **Canlı yayın kontrolü:** [`PRODUCTION.md`](PRODUCTION.md)
- **Çalışma kuralları:** [`CLAUDE.md`](CLAUDE.md)

> Tek güncel teknik şartname `DOCS.md` dosyasıdır.

## Gereksinimler

| Bileşen | Gereksinim |
|---------|-----------|
| PHP | 8.1 minimum, 8.2 önerilen |
| Eklentiler | `pdo_mysql`, `mbstring`, `gd`, `json`, `fileinfo` |
| MySQL | 5.7+ / MariaDB 10.4+ |
| Sunucu | Apache + `mod_rewrite` |

Görsel varyantları ve WebP üretimi mevcut sürümde **GD** ile çalışır; yalnızca
Imagick kurulu bir sunucu yeterli değildir. Composer, npm veya uygulama derleme
adımı yoktur.

## Kurulum

1. Depoyu klonlayın.
2. `config/config.example.php` dosyasını `config/config.php` olarak kopyalayın
   ve veritabanı bilgilerini girin.
3. Alan adı document root'unu `public/` klasörüne yönlendirin.
4. Tarayıcıdan `/install` adresini açın; bağlantı testi yapılır, `db/schema.sql`
   uygulanır, ilk yönetici oluşturulur.
5. `storage/` ve `public/uploads/` klasörlerini yazılabilir yapın.
6. Başlangıç içeriğini yazın: `php tools/seed_content.php`.
7. Yayına çıkmadan önce `php tools/preflight.php` çalıştırın ve `PRODUCTION.md`
   içindeki elle doğrulanan maddeleri tamamlayın.

Kurulum bitince `storage/installed.lock` yazılır ve `/install` kapanır.

## Testler

```bash
php tests/run.php
php tests/run.php unit
```

Yerel ortamda veritabanı değişkenleri tanımlanmazsa DB gerektiren testler
atlanabilir. GitHub CI ise MySQL 8 servisi oluşturur ve **atlanan testi hata
kabul eder**; dolayısıyla yeşil CI gerçek DB testlerinin de çalıştığını gösterir.

Tarayıcıda çalışan animasyon ve erişilebilirlik denetimleri:

```bash
php -S 127.0.0.1:8321 -t public &
node tools/browser/animation-check.mjs
```

## Araçlar

| Araç | İşi |
|------|-----|
| `tools/migrate.php` | Bekleyen şema göçlerini uygular |
| `tools/seed_content.php` | Başlangıç içeriğini yazar |
| `tools/preflight.php` | Yayın öncesi teslim listesini denetler |
| `tools/backup.php` | Veritabanı yedeği alır |
| `tools/rollup_visits.php` | Eski ziyaretleri günlük tabloya toplar |
| `tools/purge_submissions.php` | Saklama süresi dolan form kayıtlarını siler |

## Zamanlanmış görevler

```cron
0 3 * * * php /home/kullanici/arcates-web-site/tools/backup.php
15 3 * * * php /home/kullanici/arcates-web-site/tools/purge_submissions.php
30 3 * * * php /home/kullanici/arcates-web-site/tools/rollup_visits.php
```

## Lisans

Tüm hakları saklıdır — Arcates Yazılım.
