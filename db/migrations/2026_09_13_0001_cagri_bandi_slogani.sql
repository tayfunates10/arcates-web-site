-- Cagri bandi slogani icin varsayilan degeri ekler.
--
-- Sema degisikligi degil, `home_section_translations.content` JSON'una yeni
-- anahtar. `Seeder::homeSections()` yalnizca ceviri satiri hic yokken calisir,
-- bu yuzden kurulmus bir siteye yeni anahtar eklemez; bu is goce aittir.
-- DOCS.md 8.6
--
-- Kosulludur: anahtar zaten varsa hicbir sey yazilmaz. Boylece goc ikinci kez
-- calissa bile yoneticinin panelden girdigi metni ezmez ve panelden bilerek
-- bosaltilan bir alan geri dolmaz (bosaltinca anahtar '' degeriyle var olmaya
-- devam eder).
--
-- El yazisi not (`footer.signature`) bu gocte yok: o cumle artik ekip
-- fotografinin icine gomulu geliyor, varsayilani bos. Panelden metin
-- girilirse CSS kaplamasi yeniden devreye girer.

-- Slogan. Satirlar ayri ayri cizildigi icin deger cok satirlidir.
UPDATE home_section_translations
   SET content = JSON_SET(content, '$.slogan', 'DAHA\nBÜYÜK\nMÜMKÜN')
 WHERE section_key = 'cta'
   AND lang = 'tr'
   AND JSON_CONTAINS_PATH(content, 'one', '$.slogan') = 0;
