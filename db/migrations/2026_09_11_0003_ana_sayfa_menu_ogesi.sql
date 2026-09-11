-- Ust menuye "Ana Sayfa" ogesini ekler.
--
-- Referans duzende menunun ilk ogesi Ana Sayfa'dir ve acik sayfa isareti
-- (mavi alt cizgi) anasayfada onun uzerinde durur. Bu oge olmadan isaret
-- anasayfada hic gorunmuyordu.
--
-- `Seeder::menus()` yalnizca menu bos oldugunda calisir, bu yuzden kurulmus
-- bir siteye yeni oge eklemez; bu is goce aittir.
--
-- Iki ifade de kosulludur: oge ya da cevirisi varsa hicbir sey yazilmaz.
-- Yoneticinin panelden sildigi bir oge geri gelmez, cunku uygulanan goc
-- ikinci kez calismaz.  DOCS.md 8.6

-- 1. Oge. `sort = 0` mevcut ogelerin (1..6) onune yerlestirir.
--    Kendi tablosuna bakan alt sorgu turetilmis tabloya sarilir; MySQL
--    hedef tabloyu dogrudan okuyan alt sorguyu reddedebiliyor.
INSERT INTO menu_items (menu_key, parent_id, page_id, url, target, sort)
SELECT 'main', NULL, NULL, '/', '_self', 0
  FROM DUAL
 WHERE NOT EXISTS (
         SELECT 1
           FROM (SELECT url, menu_key FROM menu_items) AS mevcut
          WHERE mevcut.menu_key = 'main'
            AND mevcut.url = '/'
       );

-- 2. Turkce etiket. Birincil anahtar (menu_item_id, lang) oldugu icin
--    INSERT IGNORE ikinci calismada sessizce gecer.
INSERT IGNORE INTO menu_item_translations (menu_item_id, lang, label)
SELECT mi.id, 'tr', 'Ana Sayfa'
  FROM menu_items mi
 WHERE mi.menu_key = 'main'
   AND mi.url = '/';
