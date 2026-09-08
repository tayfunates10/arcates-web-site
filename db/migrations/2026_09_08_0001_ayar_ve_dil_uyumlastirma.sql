-- Var olan kurulumlari degisen varsayilanlarla uyumlastirir.
--
-- `Seeder::settings()` ve `Seeder::languages()` yalnizca EKSIK kaydi ekler,
-- var olan satira dokunmaz. `Settings::get()` de veritabani satirini okur,
-- `Settings::defaults()` degerine dusmez. Bu yuzden bir varsayilani
-- degistirmek kurulmus bir siteyi hic etkilemiyordu: alt bilgi, iletisim
-- sayfasi ve LocalBusiness yapisal verisi eski degeri gostermeye devam
-- ediyordu.
--
-- Elle girilmis degerler korunur: her guncelleme yalnizca eski varsayilanin
-- aynen durdugu ya da hic doldurulmamis satira dokunur.
-- DOCS.md 8.6, 9.4, 11.3

-- 1. Adres — apartman, kat ve daire bilgisi cikarildi.
--    Yalnizca uzun varsayilanin aynen durdugu satir guncellenir.
UPDATE settings
   SET `value` = 'Tuzcumurat Mah. 27016 Sk. No: 5'
 WHERE `key` = 'nap_street'
   AND (`value` = 'Tuzcumurat Mah. 27016 Sk. Uysal Apt. No: 5 Kat: 3 Daire: 8'
        OR `value` IS NULL
        OR `value` = '');

-- 2. Telefon — kurulum sihirbazindan once bos birakilmis satira yazilir.
UPDATE settings
   SET `value` = '+90 545 946 50 73'
 WHERE `key` = 'nap_phone'
   AND (`value` IS NULL OR `value` = '');

-- 3. E-posta — ilk fazin yer tutucusu duruyorsa gercek adres yazilir.
UPDATE settings
   SET `value` = 'info@arcatesyazilim.com'
 WHERE `key` = 'nap_email'
   AND (`value` = 'info@arcates.com' OR `value` IS NULL OR `value` = '');

-- 4. Ornek site notu — anahtar yoksa eklenir. Satir olmadiginda
--    `Settings::get('projects_notice', '')` bos deger dondurur ve uyari hic
--    basilmaz; boylece ornek kayitlar isaretsiz kalirdi. Var olan satira
--    dokunulmaz, isletmenin yazdigi metin korunur.
INSERT IGNORE INTO settings (`key`, `value`, autoload)
VALUES ('projects_notice', 'Buradaki siteler, yapabildiklerimizi göstermek için hazırlanmış örnek kurgulardır; henüz yayına alınmış müşteri işleri değildir.', 1);

-- 5. Tek bir cevirisi bile olmayan dil yayindan kalkar. Acik kalirsa ust
--    menude gorunur, ziyaretci tiklayinca Turkce icerige duser.
--    Varsayilan dil her zaman acik kalir; sayfa, yazi ya da ornek site
--    cevirisi girilmis dil de dokunulmadan birakilir.
UPDATE languages l
   SET l.is_active = 0
 WHERE l.is_default = 0
   AND l.is_active = 1
   AND NOT EXISTS (SELECT 1 FROM page_translations    t WHERE t.lang = l.code)
   AND NOT EXISTS (SELECT 1 FROM post_translations    t WHERE t.lang = l.code)
   AND NOT EXISTS (SELECT 1 FROM project_translations t WHERE t.lang = l.code);
