-- Canlı preflight'ta örnek projeler varken boş kalan projects_notice değerini tamamlar.
-- Yalnızca anahtar yoksa veya değer boşsa varsayılan açıklamayı yazar;
-- kullanıcının mevcut özel metnine dokunmaz.

INSERT INTO settings (`key`, `value`, `autoload`)
SELECT
    'projects_notice',
    'Buradaki siteler, yapabildiklerimizi göstermek için hazırlanmış örnek kurgulardır; henüz yayına alınmış müşteri işleri değildir.',
    1
WHERE NOT EXISTS (
    SELECT 1 FROM settings WHERE `key` = 'projects_notice'
);

UPDATE settings
SET `value` = 'Buradaki siteler, yapabildiklerimizi göstermek için hazırlanmış örnek kurgulardır; henüz yayına alınmış müşteri işleri değildir.',
    `autoload` = 1
WHERE `key` = 'projects_notice'
  AND TRIM(COALESCE(`value`, '')) = '';
