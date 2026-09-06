# CLAUDE.md — Arcates Web Site calisma kurallari

Bu depoda calisan her yapay zeka oturumu asagidaki kurallara uyar.
Kurallar `DOCS.md` bolum 16'dan gelir; celiski halinde `DOCS.md` esas alinir.

1. Bu dosyayi ve `DOCS.md`'yi oku, sonra calis.
2. Tek seferde tek modul, tek dal.
3. Dosyayi degistirmeden once oku. Var olan dosyanin ustune kor yazma.
4. Composer, framework, npm paketi ekleme. Harici script ekleme.
   Tek harici kaynak Google Fonts'tur.
5. Sema degisikligi `db/migrations/YYYY_MM_DD_NNNN_aciklama.sql` altina yeni
   dosya olarak yazilir; `db/schema.sql` elle duzenlenmez.
6. Her SQL hazirlanmis ifade. Her cikti `Security::e()`. Her POST formunda CSRF.
7. Animasyon eklerken `DOCS.md` bolum 7'deki kurallari uygula: sadece
   `transform`/`opacity`, `html.js` korumasi, `prefers-reduced-motion`.
8. Sablona sabit metin gomme. Her metin panelden gelmeli.
9. Modul bitince: testleri yaz, `CHANGELOG.md`'ye satir ekle, `DOCS.md`'yi guncelle.
10. Her turun sonunda soyle: hangi dosyalar degisti, ne kirilmis olabilir, elle
    hangi test numaralari calistirilmali.
11. "Test ettim, calisiyor" deme. Test dosyasini yaz; calistirma insana aittir.

## Dur ve sor

- Sema degisikligi gerekiyorsa
- Cekirdek sinif imzasi degisecekse
- Bir guvenlik kurali isi zorlastiriyorsa
- Bir animasyon performans hedefini asiyorsa

## Hizli komutlar

```
php tests/run.php                 # tum testler
php tests/run.php unit            # yalnizca birim testleri
find app views config tests -name "*.php" -print0 | xargs -0 -n1 php -l
php tools/migrate.php             # bekleyen gocleri uygula
```

## Kabul kapisi (DOCS.md 14.9)

Bir modul su sartlar saglanmadan `main` dalina giremez:
- Ilgili U testleri geciyor
- S testlerinin tamami geciyor
- Degisiklik on yuzu etkiliyorsa A ve E testleri geciyor
- `php -l` tum dosyalarda temiz
- `CHANGELOG.md` guncellendi
- `DOCS.md`'de eksik varsa guncellendi
