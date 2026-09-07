# CLAUDE.md — Arcates Web Site çalışma kuralları

Bu depoda çalışan her yapay zeka oturumu aşağıdaki kurallara uyar.
Kurallar `DOCS.md` bölüm 16'dan gelir; çelişki halinde `DOCS.md` esas alınır.

1. Bu dosyayı ve `DOCS.md`'yi oku, sonra çalış.
2. Tek seferde tek modül, tek dal.
3. Dosyayı değiştirmeden önce oku. Var olan dosyanın üstüne kör yazma.
4. Composer, framework, npm paketi ekleme. Harici script ekleme.
   Tek harici kaynak Google Fonts'tür.
5. Şema değişikliği `db/migrations/YYYY_MM_DD_NNNN_aciklama.sql` altına yeni
   dosya olarak yazılır; `db/schema.sql` elle düzenlenmez.
6. Her SQL hazırlanmış ifade. Her çıktı `Security::e()`. Her POST formunda CSRF.
7. Animasyon eklerken `DOCS.md` bölüm 7'deki kuralları uygula: sadece
   `transform`/`opacity`, `html.js` koruması, `prefers-reduced-motion`.
8. Şablona sabit metin gömme. Her metin panelden gelmeli.
9. Modül bitince: testleri yaz, `CHANGELOG.md`'ye satır ekle, `DOCS.md`'yi güncelle.
10. Her türün sonunda söyle: hangi dosyalar değişti, ne kırılmış olabilir, elle
    hangi test numaraları çalıştırılmalı.
11. "Test ettim, çalışıyor" deme. Test dosyasını yaz; çalıştırma insana aittir.

## Dur ve sor

- Şema değişikliği gerekiyorsa
- Çekirdek sınıf imzası değişecekse
- Bir güvenlik kuralı işi zorlaştırıyorsa
- Bir animasyon performans hedefini aşıyorsa

## Hızlı komutlar

```
php tests/run.php                 # tum testler
php tests/run.php unit            # yalnizca birim testleri
find app views config tests -name "*.php" -print0 | xargs -0 -n1 php -l
php tools/migrate.php             # bekleyen gocleri uygula
```

## Kabul kapısı (DOCS.md 14.9)

Bir modül şu şartlar sağlanmadan `main` dalına giremez:
- İlgili U testleri geçiyor
- S testlerinin tamamı geçiyor
- Değişiklik on yüzü etkiliyorsa A ve E testleri geçiyor
- `php -l` tüm dosyalarda temiz
- `CHANGELOG.md` güncellendi
- `DOCS.md`'de eksik varsa güncellendi
