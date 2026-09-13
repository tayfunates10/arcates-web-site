-- Cagri bandi slogani ve ekip gorselindeki el yazisi not icin varsayilan
-- degerleri ekler.
--
-- Ikisi de sema degisikligi degil, `home_section_translations.content`
-- JSON'una yeni anahtar. `Seeder::homeSections()` yalnizca ceviri satiri hic
-- yokken calisir, bu yuzden kurulmus bir siteye yeni anahtar eklemez; bu is
-- goce aittir.  DOCS.md 8.6
--
-- Iki ifade de kosulludur: anahtar zaten varsa hicbir sey yazilmaz. Boylece
-- goc ikinci kez calissa bile yoneticinin panelden girdigi metni ezmez ve
-- panelden bilerek bosaltilan bir alan geri dolmaz (bosaltinca anahtar
-- '' degeriyle var olmaya devam eder).

-- 1. Slogan. Satirlar ayri ayri cizildigi icin deger cok satirlidir.
UPDATE home_section_translations
   SET content = JSON_SET(content, '$.slogan', 'DAHA\nBÜYÜK\nMÜMKÜN')
 WHERE section_key = 'cta'
   AND lang = 'tr'
   AND JSON_CONTAINS_PATH(content, 'one', '$.slogan') = 0;

-- 2. El yazisi not. Anasayfadaki ekip gorselinin uzerine biner.
UPDATE home_section_translations
   SET content = JSON_SET(content, '$.signature', 'Daha iyi bir yarın için birlikte üretiyoruz.')
 WHERE section_key = 'footer'
   AND lang = 'tr'
   AND JSON_CONTAINS_PATH(content, 'one', '$.signature') = 0;
