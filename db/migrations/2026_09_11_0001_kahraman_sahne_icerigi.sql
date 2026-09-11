-- Kahraman sahnesindeki yuzen katmanlarin metni.
--
-- `Seeder::homeSections()` yalnizca EKSIK satiri ekler; kurulmus bir sitede
-- `hero` cevirisi zaten var, bu yuzden yeni `scene` alani tohumla ulasmiyor.
-- Asagidaki birlestirme o farki kapatir. DOCS.md 8.6
--
-- Kosul: yalnizca `scene` alani hic yoksa yazilir. Panelden girilmis ya da
-- bilerek bosaltilmis bir sahne oldugu gibi kalir.
--
-- Icerik kurali: sahne dekoratiftir ve isletmenin arkasinda durabilecegi
-- ifadeleri tasir. Uydurma proje sayaci ya da memnuniyet orani konmaz.

UPDATE home_section_translations
   SET content = JSON_MERGE_PATCH(content, '{"scene":{"card":{"title":"Fikirden yayına","text":"Konuşuyoruz, kuruyoruz, büyütüyoruz."},"chips":[{"value":"8 ilçe","label":"Yerinde görüşme"},{"value":"Aynı hafta","label":"Fiyat ve takvim"}],"rail":[{"icon":"layout","label":"Kurumsal web tasarım"},{"icon":"cart","label":"E-ticaret sitesi"},{"icon":"calendar","label":"Rezervasyon sistemi"},{"icon":"search","label":"SEO hizmeti"},{"icon":"globe","label":"Çoklu dil web sitesi"}]}}')
 WHERE section_key = 'hero'
   AND lang = 'tr'
   AND JSON_EXTRACT(content, '$.scene') IS NULL;
