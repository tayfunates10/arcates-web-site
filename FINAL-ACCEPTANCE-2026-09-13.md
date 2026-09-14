# Arcates — 13 Eylül 2026 devam ve teslim kaydı

## Doğrulanan başlangıç

- Kaynak: `b75601e5d128f9e34d47210bef1aa4ac1dccdc67` / PR #50.
- B1–B9 referans uygulaması ve üç yeni WebP bu sürümde birleşmiştir.
- [CI çalışması](https://github.com/tayfunates10/arcates-web-site/actions/runs/34752613866): PHP sözdizimi, **333/333 test; 0 başarısız, 0 atlanan**, browser işi başarılı.
- [PR #50 Reference Parity](https://github.com/tayfunates10/arcates-web-site/actions/runs/34752165327): başarılı.

## Bu kapanışın düzeltmesi

B9 son CSS katmanı 940 px altında iki sütun tanımlıyor ve daha önceki 640 px tek sütun kuralını eziyordu. Kart `overflow:hidden` kullandığı için sayfada yatay taşma olmaması içerideki teklif eyleminin sağlam olduğunu kanıtlamıyordu. Telefon tek sütun kuralı son katmanda korundu; eylem konumu ve kart sınırları gerçek Chromium denetimine eklendi.

Reference Parity artık `main` push olayında da çalışır. Sloganlı/slogansız durumlar 320/390/640/641/768/940/941/1440 px genişliklerde denetlenir. Önceki 320/390/768/1024/1440 px ekran görüntüleri ve JS kapalı/reduced-motion kontrolleri korunur.

## cPanel güncellemesinde gerekli adım

Kullanıcının mevcut akışı: Update from Remote → Deploy HEAD Commit. `.cpanel.yml` dosyaları kopyalar; göçleri çalıştırmaz. PR #50 slogan varsayılanının kurulu veritabanına gelmesi için dağıtım sonrasında dağıtılan uygulama kökünde çalıştırılmalıdır:

```bash
php tools/migrate.php
php tools/preflight.php
```

Göç `2026_09_13_0001_cagri_bandi_slogani.sql` yalnız eksik Türkçe slogan anahtarını ekler; panelde mevcut ya da bilerek boşaltılmış değeri korur. Kurulu veritabanına `schema.sql` uygulanmaz.

## Tamamlandı sayılmayan dış kontroller

Bu oturumda canlı alan adına erişim sağlanamadı. Yeni sürümün cPanel yayını, gerçek hosting preflight sonucu, e-posta teslimi, cron ve yedek geri yükleme kanıtlanmadı. Önceki sürümlerin başarılı canlı kontrolleri yeni sürümün kanıtı değildir. `PRODUCTION.md` ve `RELEASE-R9-PRODUCTION.md` bu kapıları tanımlar; sürüm release-candidate kalır.

Gerçek sosyal hesaplar ve gerçek müşteri proje görselleri işletme verisi olarak panelden girilir; eksik verilerin yerine uydurma hesap veya müşteri eklenmez. Bülten aboneliği ayrı veri/abonelik modülüdür; bu kapanışta çalışmayan bir bülten formu eklenmez.

## Doğrulama kaydı

Bu dalın CI ve Reference Parity sonuçları PR üzerinde izlenir; birleştirme yalnız başarılı sonuçtan sonra yapılır. Yerel ortamda PHP/MySQL ve Chromium yürütücüsü bulunmadığı için tam test kanıtı GitHub Actions çıktısıdır.
