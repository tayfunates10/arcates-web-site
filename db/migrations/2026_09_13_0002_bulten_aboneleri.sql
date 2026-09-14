-- Bulten abonelerini tutan tabloyu ekler.
--
-- Sema degisikligi. `db/schema.sql` sifirdan kurulum icindir ve
-- `InstallController` kurulumda once onu calistirip bekleyen gocleri
-- CALISTIRMADAN uygulanmis isaretler. Bu yuzden yeni tablo iki yere birden
-- yazilir: schema.sql sifirdan kurulumu, bu goc kurulmus siteleri karsilar.
-- `CREATE TABLE IF NOT EXISTS` oldugu icin iki yol da guvenli ve goc
-- yinelenebilir.  DOCS.md 8.6
--
-- Cift onay (double opt-in) tasarimi:
--   pending      -> adres girildi, onay e-postasi gonderildi
--   active       -> ziyaretci onay baglantisina tikladi
--   unsubscribed -> ziyaretci cikti; kayit SILINMEZ, cunku onayin geri
--                   alindiginin kaniti da saklanmali
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL,
  lang CHAR(2) NULL,
  status ENUM('pending','active','unsubscribed') NOT NULL DEFAULT 'pending',
  token CHAR(64) NOT NULL,              -- onay ve cikis baglantisinin gizli anahtari
  consent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  consent_ip VARBINARY(16) NULL,        -- onayin kaniti; KVKK/IYS icin saklanir
  consent_source VARCHAR(255) NULL,     -- kaydin yapildigi sayfa
  confirmed_at DATETIME NULL,
  unsubscribed_at DATETIME NULL,        -- kayit silinmez: onayin geri alindiginin kaniti
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_newsletter_email (email),
  UNIQUE KEY uq_newsletter_token (token),
  INDEX idx_newsletter_status (status, created_at),
  INDEX idx_newsletter_ip (consent_ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Alt bilgideki bulten sutununun panelden gelen metinleri. Kosulludur:
-- anahtar zaten varsa hicbir sey yazilmaz, yani panelden degistirilen ya da
-- bilerek bosaltilan metin ezilmez. Baslik bos oldugunda sutun hic cizilmez.
UPDATE home_section_translations
   SET content = JSON_SET(content, '$.newsletter_title', 'Bülten')
 WHERE section_key = 'footer'
   AND lang = 'tr'
   AND JSON_CONTAINS_PATH(content, 'one', '$.newsletter_title') = 0;

UPDATE home_section_translations
   SET content = JSON_SET(content, '$.newsletter_text', 'Yeni içeriklerden ve duyurulardan haberdar olun.')
 WHERE section_key = 'footer'
   AND lang = 'tr'
   AND JSON_CONTAINS_PATH(content, 'one', '$.newsletter_text') = 0;
