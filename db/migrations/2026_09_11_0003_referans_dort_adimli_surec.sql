-- Referans kurgudaki dort adimli sureci, yalnizca eski varsayilan 3 adim
-- hic degistirilmeden duruyorsa uygular. Panelden ozellestirilmis icerige dokunmaz.
-- DOCS.md 8.6

UPDATE home_section_translations
   SET content = JSON_MERGE_PATCH(
       content,
       '{"items":[{"title":"Analiz","text":"İşinizi, müşterilerinizi ve hedeflerinizi dinliyoruz. Doğru kapsamı birlikte belirliyoruz."},{"title":"Tasarım","text":"Kullanıcı odaklı arayüzü ve içerik akışını tasarlıyoruz."},{"title":"Geliştirme","text":"Hızlı, güvenli ve yönetilebilir altyapıyı geliştiriyoruz."},{"title":"Yayına Alma","text":"Test ediyor, ölçüyor ve güvenle yayına alıyoruz."}]}'
   )
 WHERE section_key = 'steps'
   AND lang = 'tr'
   AND JSON_LENGTH(JSON_EXTRACT(content, '$.items')) = 3
   AND JSON_UNQUOTE(JSON_EXTRACT(content, '$.items[0].title')) = 'Konuşuyoruz'
   AND JSON_UNQUOTE(JSON_EXTRACT(content, '$.items[0].text')) = 'İşinizi, müşterilerinizi ve rakiplerinizi dinliyoruz. Hangi aramalarda görünmeniz gerektiğini birlikte belirliyoruz.'
   AND JSON_UNQUOTE(JSON_EXTRACT(content, '$.items[1].title')) = 'Kuruyoruz'
   AND JSON_UNQUOTE(JSON_EXTRACT(content, '$.items[1].text')) = 'Tasarım, içerik ve teknik kurulumu yapıyoruz. Her sayfayı hız ve arama görünürlüğü için ölçüyoruz.'
   AND JSON_UNQUOTE(JSON_EXTRACT(content, '$.items[2].title')) = 'Büyütüyoruz'
   AND JSON_UNQUOTE(JSON_EXTRACT(content, '$.items[2].text')) = 'Yayından sonra hangi sayfanın iş getirdiğini ölçüyor, içeriği ve reklamı buna göre düzenliyoruz.';
