# Arcates Web Site — Yapay Zeka Çalışma Kuralları

1. Bu dosyayı ve `DOCS.md`'yi oku, sonra çalış.
2. Tek seferde tek modül, tek dal.
3. Dosyayı değiştirmeden önce oku. Var olan dosyanın üstüne kör yazma.
4. Composer, framework, npm paketi ekleme. Harici script ekleme.
5. Şema değişikliği `db/migrations/` altına yeni dosya olarak yazılır; `schema.sql` elle düzenlenmez.
6. Her SQL hazırlanmış ifade. Her çıktı `Security::e()`. Her POST formunda CSRF.
7. Animasyon eklerken DOCS bölüm 7 kuralları uygulanır: yalnızca `transform`/`opacity`, `html.js` koruması, `prefers-reduced-motion`.
8. Şablona sabit metin gömme. Her metin panelden gelmeli.
9. Modül bitince testleri yaz, `CHANGELOG.md`'ye satır ekle, `DOCS.md`'yi güncelle.
10. Her turun sonunda değişen dosyaları, olası kırılmaları ve elle çalıştırılacak test numaralarını belirt.
11. "Test ettim, çalışıyor" deme. Test dosyasını yaz; gerçek çalışma sonucu CI veya insan tarafından doğrulanır.

## Dur ve sor
- Şema değişikliği gerekiyorsa.
- Çekirdek sınıf imzası değişecekse.
- Bir güvenlik kuralı işi zorlaştırıyorsa.
- Bir animasyon performans hedefini aşıyorsa.
