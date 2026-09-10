# Arcates canlı arayüz kontrolü — 10 Eylül 2026

Kaynak: a42e768 tabanlı düzeltme dalı; ekli tam yeniden tasarım planı. Canlı gözlemler değişiklikler yayımlanmadan önce alınmıştır.

## Doğrulanan bulgular ve düzeltmeler

| Bulgu | Etki | Kaynak düzeltmesi |
|---|---|---|
| İç sayfalarda Teklif Al ve footer menüleri eksik | Dönüşüm/yasal gezinme kopuyor | Ortak controller panel içeriğini bütün sayfalara geçirir |
| Boş formun gönder butonu pasif | Ziyaretçi eksikleri açıklatan gönderim akışına ulaşamıyor | Açık buton, ilk hataya odak, yerel hata açıklaması; yalnız gönderim sırasında kilit |
| Koyu CTA üzerinde WhatsApp metni koyu mavi | İkincil eylem zor okunuyor | Beyaz metin, belirgin açık sınır, hover durumu |
| Blog aktif kategorisi yalnız sınıfla belirtiliyor | Ekran okuyucu seçimi bilmiyor | aria-current=page |
| Örnek proje detayında demo notu görünmüyor | Örneklerin gerçek müşteri işi sanılması mümkün | Anahtar yoksa varsayılan not; bilerek boşsa panelden düzeltilmesi gerekli |

## Canlı kontroller

- Sitemap içindeki 43 URL'nin GET yanıtı 200. Her birinde tek H1, dolu ve benzersiz title, meta açıklama ve kendi adresine canonical var.
- Ana sayfa, hizmet, bölge, sektör, blog liste/filtre, proje liste/detay, fiyatlar, hakkımızda, iletişim, SSS, KVKK ve gizlilik sayfaları tarayıcıda incelendi.
- Blog Bakım filtresi tek doğru yazıyı getiriyor. Hizmet içindekiler bağlantısı doğru başlık kimliğine gidiyor. SSS açıldığında cevap görünür oluyor.
- Sayfalarda kullanılan 14 görüntü kaynağı HTTP 200 döndürdü (toplam 133.251 bayt; toplam sayfa yükü ölçümü değildir).
- Teşekkür sayfası noindex,nofollow; 404 sayfası anlaşılır açıklama ve ana sayfa/iletişim dönüş bağlantıları taşıyor.
- Kontrol edilen masaüstü sayfalarda yatay taşma gözlenmedi. Ana sayfa hero ve altı hizmet görseli yükleniyor; footer logosu kaydırma sonrası yükleniyor.
- İç link hedefleri sitemap içinde; ek hedefler altı blog kategori filtresi. Telefon, e-posta ve WhatsApp aynı NAP bilgileriyle uyumlu.

## Yayın ve kabul sınırları

- cPanel oturumu kapanmış; giriş ekranı görülüyor. Kaynak düzeltmesi canlıya alınmış sayılmaz.
- Gerçek mesaj gönderimi/e-posta teslimi, giriş gerektiren canlı panel işlemleri ve backup geri yükleme denemesi yapılmadı. Üretimde sahte form mesajı oluşturulmadı.
- Canlı mobil cihaz ve Lighthouse/Core Web Vitals ölçümü tamamlanmadı; 320–1920, RTL, %200 yakınlaştırma ve admin mobil kontrolleri repo CI matrisinde ayrıca değerlendirilir. HTTP 200 sonucu performans veya görsel kusursuzluk kanıtı değildir.
- Çalışma saatleri canlı footer'da yok. İşletmenin doğruladığı saatler girilmelidir; tahmini saat yayınlanmadı.
- Örnek proje kapağı/galerisi olmayan kayıtlarda mevcut nötr fallback korunur. Gerçek müşteri işi veya uydurma sonuç görseli eklenmedi.
- HEAD /blog isteği 404 döndürürken GET 200 idi. Router HEAD isteklerini GET kaynağına eşleştirecek biçimde düzeltildi; POST rotalarını çalıştırmaz. Birim ve gerçek HTTP tarayıcı regresyonu eklendi.
