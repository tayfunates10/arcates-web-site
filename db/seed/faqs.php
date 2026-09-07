<?php
/**
 * SSS kayitlari.  DOCS.md 9.4, 9.6
 *
 * `pages` alani, kaydin atanacagi sayfa slug'larini tasir. Bolum 9.6 geregi
 * her ilce ve hizmet sayfasinda en az bir SSS bulunmalidir; asagidaki liste
 * her ilce icin O ILCEYE OZEL soru icerir.
 */

declare(strict_types=1);

return [
    // --- Genel (anasayfa ve ana sayfalar) ----------------------------------
    ['home' => true, 'pages' => ['fiyatlar', 'sss'], 'sort' => 1,
     'q' => 'Bir web sitesi ne kadar sürede yayına giriyor?',
     'a' => '<p>Kurumsal bir site ortalama üç haftada yayına alınır. E-ticaret ve '
          . 'rezervasyon projeleri içerik hazırlığına bağlı olarak altı ila sekiz hafta sürer. '
          . 'Süreyi en çok uzatan şey metin ve fotoğraf bekleyişidir; bunlar hazırsa iş hızlanır.</p>'],

    ['home' => true, 'pages' => ['fiyatlar', 'sss'], 'sort' => 2,
     'q' => 'Siteyi kendim güncelleyebilir miyim?',
     'a' => '<p>Evet. Anasayfa dahil her metin, fotoğraf ve bağlantı panelden düzenlenir. '
          . 'Teslimde bir eğitim yapıyoruz ve ilk ay boyunca ücretsiz destek veriyoruz. '
          . 'Amaç bağımlılık kurmak değil; sitenizi kendinizin yönetebilmesi.</p>'],

    ['home' => true, 'pages' => ['sss', 'fiyatlar'], 'sort' => 3,
     'q' => 'Sitenin sahibi kim oluyor?',
     'a' => '<p>Sitenin tüm dosyaları ve veritabanı sizindir. Kapalı bir sistem ya da '
          . 'kiralık panel kurmuyoruz. Çalışmayı bırakmak isterseniz her şeyi alıp başka '
          . 'bir ekiple devam edebilirsiniz.</p>'],

    ['home' => true, 'pages' => ['sss'], 'sort' => 4,
     'q' => 'Google\'da ilk sıraya çıkaracak mısınız?',
     'a' => '<p>Hiçbir ajans ilk sırayı garanti edemez; eden varsa doğru söylemiyordur. '
          . 'Yaptığımız şey teknik altyapıyı düzeltmek, doğru sayfaları oluşturmak ve '
          . 'ölçümü kurmaktır. Yerel aramalarda ilk sayfaya çıkmak çoğu işletme için '
          . 'ulaşılabilir bir hedeftir ve genellikle üç ile altı ay sürer.</p>'],

    ['home' => false, 'pages' => ['sss', 'kvkk'], 'sort' => 5,
     'q' => 'Form bilgilerim ne kadar süre saklanıyor?',
     'a' => '<p>Form kayıtları panelde belirlenen saklama süresi boyunca tutulur ve süresi '
          . 'dolan kayıtlar günlük görevle otomatik silinir. Ayrıntılı bilgi '
          . '<a href="/kvkk">KVKK aydınlatma metnimizde</a>.</p>'],

    // --- Hizmet sayfalarina ozel --------------------------------------------
    ['home' => false, 'pages' => ['web-tasarim'], 'sort' => 10,
     'q' => 'Hazır şablon mu kullanıyorsunuz?',
     'a' => '<p>Hayır. Her site işletmeye özel tasarlanır. Hazır şablonlar hem birbirinin '
          . 'aynı görünür hem de kullanılmayan kod taşıdıkları için mobilde yavaş açılır.</p>'],

    ['home' => false, 'pages' => ['e-ticaret-sitesi', 'zeytinyagi-e-ticaret-sitesi'], 'sort' => 11,
     'q' => 'Pazaryerinde satıyorum, kendi mağazama gerek var mı?',
     'a' => '<p>Pazaryeri müşteriyi size değil kendine bağlar; komisyon öderken müşteri '
          . 'listesi de sizde kalmaz. İkisini birlikte yürütmek en sağlıklı yol: pazaryeri '
          . 'yeni müşteri getirir, kendi mağazanız onu elinizde tutar.</p>'],

    ['home' => false, 'pages' => ['rezervasyon-sistemi', 'otel-pansiyon-web-sitesi'], 'sort' => 12,
     'q' => 'Rezervasyon platformlarını bırakmam mı gerekiyor?',
     'a' => '<p>Hayır. Amaç platformları bırakmak değil, oradan gelen misafiri bir sonraki '
          . 'sefere doğrudan size getirmek. Kendi kanalınız güçlendikçe komisyonlu satışın '
          . 'payı kendiliğinden düşer.</p>'],

    ['home' => false, 'pages' => ['seo-hizmeti'], 'sort' => 13,
     'q' => 'Aynı metni ilçe adı değiştirerek çoğaltabilir miyiz?',
     'a' => '<p>Hayır, bunu yapmıyoruz. Bu yöntem kısa vadede sayfa sayısını artırsa da '
          . 'Google tarafından doorway page olarak değerlendirilir ve yakalandığında tüm '
          . 'sayfaları birden değersizleştirir. Her bölge sayfasını o bölgenin kendi '
          . 'ekonomisinden yazıyoruz.</p>'],

    ['home' => false, 'pages' => ['coklu-dil-web-sitesi'], 'sort' => 14,
     'q' => 'Otomatik çeviri eklentisi yeterli olmaz mı?',
     'a' => '<p>Olmaz. Otomatik çeviri arama motorları için ayrı bir sayfa üretmez; yanı '
          . 'Almanca arama yapan biri sizi bulamaz. Gerçek çoklu dil, her dil için ayrı '
          . 'adres ve o dile göre yazılmış içerik demektir.</p>'],

    ['home' => false, 'pages' => ['web-sitesi-bakim'], 'sort' => 15,
     'q' => 'Bakım anlaşması zorunlu mu?',
     'a' => '<p>Zorunlu değil. Her proje bir aylık ücretsiz destekle teslim edilir. '
          . 'Bakım anlaşması aylık sabit ücretlidir, taahhüt yoktur ve istediğiniz ay '
          . 'bırakabilirsiniz.</p>'],

    // --- Ilce sayfalarina ozel (bolum 9.6: her ilce sayfasinda SSS olmali) --
    ['home' => false, 'pages' => ['edremit-web-tasarim'], 'sort' => 20,
     'q' => 'Edremit dışındaki ilçelere de hizmet veriyor musunuz?',
     'a' => '<p>Evet. Edremit merkezli çalışıyoruz ama Havran, Burhaniye, Akçay, Altınoluk, '
          . 'Gömeç, Ayvalık ve Balıkesir merkezde de yerinde görüşme yapıyoruz. '
          . 'Çevre ilçelere teslimat ya da hizmet veren işletmeler için her bölgeye ayrı '
          . 'sayfa kuruyoruz.</p>'],

    ['home' => false, 'pages' => ['akcay-web-tasarim'], 'sort' => 21,
     'q' => 'Sezonluk pansiyonum için siteye ne zaman başlamalıyım?',
     'a' => '<p>Kasım ile ocak arası en doğru zaman. Sitenin şubatta yayında olması, arama '
          . 'motorlarının sayfayı tanıyıp sıralamaya alacağı süreyi kazandırır. Mayısta '
          . 'başlanan bir iş o sezonu yakalayamaz.</p>'],

    ['home' => false, 'pages' => ['altinoluk-web-tasarim'], 'sort' => 22,
     'q' => 'Site yönetimi için duyuru sayfası yapıyor musunuz?',
     'a' => '<p>Evet. Aidat duyurusu, havuz bakım takvimi, genel kurul çağrısı ve arıza '
          . 'bildirimi için panelden yönetilen bir yapı kuruyoruz. Arıza formu bildirimi '
          . 'yapan daireyi, konuyu ve fotoğrafı tek ekranda topluyor.</p>'],

    ['home' => false, 'pages' => ['burhaniye-web-tasarim'], 'sort' => 23,
     'q' => 'Zeytinyağımı kendi etiketimle satmak için neye ihtiyacım var?',
     'a' => '<p>Ürün adı ve etiket bilgisi, litre bazlı fiyatlandırma, kırılabilir ürün için '
          . 'uygun koli, kargo anlaşması ve iade süreci. Kurduğumuz mağazalarda bu adımların '
          . 'hepsi baştan tanımlı gelir; siz yalnızca ürün, fiyat ve stok girersiniz.</p>'],

    ['home' => false, 'pages' => ['havran-web-tasarim'], 'sort' => 24,
     'q' => 'Küçük bir esnafım, büyük bir siteye ihtiyacım var mı?',
     'a' => '<p>Genellikle yok. Üç-beş sayfalık, telefonu ilk ekranda duran ve harita '
          . 'kaydıyla birebir aynı adresi gösteren bir yapı çoğu esnaf için yeterli. '
          . 'Önce bunu kurup, gelen talebe göre büyütmek daha doğru.</p>'],

    ['home' => false, 'pages' => ['ayvalik-web-tasarim'], 'sort' => 25,
     'q' => 'Ayvalık\'ta rakiplerimden nasıl ayrışırım?',
     'a' => '<p>Herkesin yazdığı "tarihi taş ev" ve "denize yürüme mesafesi" cümleleri artık '
          . 'hiçbir şeyi ayırmıyor. Kahvaltıda hangi yerel üreticiyi kullandığınız, hangi '
          . 'odanın hangi saatte güneş aldığı gibi gerçek ayrıntılar ayrıştırır.</p>'],

    ['home' => false, 'pages' => ['gomec-web-tasarim'], 'sort' => 26,
     'q' => 'Kamp alanı için sitede neler olmalı?',
     'a' => '<p>Elektrik ve su bağlantısı, tuvalet ve duş durumu, gece güvenliği, evcil hayvan '
          . 'kabulü ve günlük ücret. Bu soruların cevabını açıkça yazan bir işletme telefon '
          . 'trafiğinin yarısından kurtulur ve doğru misafiri çeker.</p>'],

    ['home' => false, 'pages' => ['balikesir-web-tasarim'], 'sort' => 27,
     'q' => 'Merkezde rekabet yüksek; reklam mı vermeliyim?',
     'a' => '<p>Reklam hızlı sonuç verir ama musluk kapandığında trafik de biter. Önerimiz '
          . 'dengeli kullanım: reklamı kısa vadeli talep için, içeriği uzun vadeli görünürlük '
          . 'için. Reklamdan dönüşüm getiren kelimeler için kalıcı içerik yazmak bütçeyi '
          . 'zamanla düşürür.</p>'],

    // --- Sektor sayfalarina ozel --------------------------------------------
    ['home' => false, 'pages' => ['restoran-kafe-qr-menu'], 'sort' => 30,
     'q' => 'QR menü için PDF yeterli olmaz mı?',
     'a' => '<p>Olmaz. PDF menü telefonda yakınlaştırma gerektirir, yavaş açılır ve arama '
          . 'motorları içeriğini okuyamaz. Gerçek bir QR menü, masada bir saniyede açılan ve '
          . 'panelden beş dakikada güncellenen bir sayfadır.</p>'],

    ['home' => false, 'pages' => ['emlak-web-sitesi'], 'sort' => 31,
     'q' => 'Portal ilanlarım varken siteye ne gerek var?',
     'a' => '<p>Alıcı portalda ilanı gördükten sonra ofisin adını aratır. O anda karşısına '
          . 'çıkan sayfa, ofisin bölgeyi ne kadar tanıdığını göstermelidir. Bölge rehberi '
          . 'yazan bir ofis, yalnızca ilan listeleyen on ofisin önüne geçer.</p>'],

    ['home' => false, 'pages' => ['nakliyat-web-sitesi'], 'sort' => 32,
     'q' => 'Teklif formunda kaç alan olmalı?',
     'a' => '<p>Altı alanı geçmemeli: nereden nereye, tarih, oda sayısı, kat ve asansör '
          . 'bilgisi, telefon. Daha uzun formlar mobilde terk oranını gözle görülür '
          . 'artırıyor.</p>'],

    ['home' => false, 'pages' => ['tabela-matbaa-web-sitesi'], 'sort' => 33,
     'q' => 'Fiyat listesi yayınlamalı mıyım?',
     'a' => '<p>Bu işte fiyat ölçü ve malzemeye bağlı olduğu için liste yayınlamak zor. '
          . 'Bunun yerine formun ölçü, adet ve malzeme sorması, müşterinin görsel '
          . 'yükleyebilmesi süreci kısaltır ve gereksiz telefon trafiğini azaltır.</p>'],
];
