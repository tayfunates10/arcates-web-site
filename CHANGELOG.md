# Changelog

Bu proje Semantic Versioning yaklaşımını izler.

## 1.0.0-draft

### Added
- Faz 0 repo iskeleti başlatıldı.
- CI, test koşucusu, güvenli klasör yapısı ve proje çalışma kuralları eklendi.
- Faz 1 çekirdeği: Database, Router, Request, Response, Security, Validator, Session, Auth, Logger ve Access katmanları eklendi.
- Güvenli `/install` akışı, ilk yönetici/dil/anasayfa bölüm kurulumu ve `storage/installed.lock` koruması eklendi.
- Sıfırdan kurulum için `db/schema.sql` eklendi; sonraki şema değişiklikleri migration dosyalarıyla yapılacak.
- Giriş deneme limiti, CSRF, prepared statement, çıktı kaçırma, güvenli oturum ve medya yükleme/SVG temizleme kontrolleri eklendi.
- U-01, U-02, U-03, U-04, U-05, U-07, U-12 ve S-01…S-13 güvenlik kabul kapsamını doğrulayan testler eklendi.
- Faz 2 yönetim paneli: giriş/çıkış, pano, admin/editör rol koruması, kullanıcı oluşturma/listeleme, site ayarları ve işlem günlüğü eklendi.
- Panel için bağımlılıksız erişilebilir şablonlar, responsive `admin.css` ve panel çıktı/rol testleri eklendi.
