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
     'q' => 'Bir web sitesi ne kadar surede yayina giriyor?',
     'a' => '<p>Kurumsal bir site ortalama uc haftada yayina alinir. E-ticaret ve '
          . 'rezervasyon projeleri icerik hazirligina bagli olarak alti ila sekiz hafta surer. '
          . 'Sureyi en cok uzatan sey metin ve fotograf bekleyisidir; bunlar hazirsa is hizlanir.</p>'],

    ['home' => true, 'pages' => ['fiyatlar', 'sss'], 'sort' => 2,
     'q' => 'Siteyi kendim guncelleyebilir miyim?',
     'a' => '<p>Evet. Anasayfa dahil her metin, fotograf ve baglanti panelden duzenlenir. '
          . 'Teslimde bir egitim yapiyoruz ve ilk ay boyunca ucretsiz destek veriyoruz. '
          . 'Amac bagimlilik kurmak degil; sitenizi kendinizin yonetebilmesi.</p>'],

    ['home' => true, 'pages' => ['sss', 'fiyatlar'], 'sort' => 3,
     'q' => 'Sitenin sahibi kim oluyor?',
     'a' => '<p>Sitenin tum dosyalari ve veritabani sizindir. Kapali bir sistem ya da '
          . 'kiralik panel kurmuyoruz. Calismayi birakmak isterseniz her seyi alip baska '
          . 'bir ekiple devam edebilirsiniz.</p>'],

    ['home' => true, 'pages' => ['sss'], 'sort' => 4,
     'q' => 'Google\'da ilk siraya cikaracak misiniz?',
     'a' => '<p>Hicbir ajans ilk sirayi garanti edemez; eden varsa dogru soylemiyordur. '
          . 'Yaptigimiz sey teknik altyapiyi duzeltmek, dogru sayfalari olusturmak ve '
          . 'olcumu kurmaktir. Yerel aramalarda ilk sayfaya cikmak cogu isletme icin '
          . 'ulasilabilir bir hedeftir ve genellikle uc ile alti ay surer.</p>'],

    ['home' => false, 'pages' => ['sss', 'kvkk'], 'sort' => 5,
     'q' => 'Form bilgilerim ne kadar sure saklaniyor?',
     'a' => '<p>Form kayitlari panelde belirlenen saklama suresi boyunca tutulur ve suresi '
          . 'dolan kayitlar gunluk gorevle otomatik silinir. Ayrintili bilgi '
          . '<a href="/kvkk">KVKK aydinlatma metnimizde</a>.</p>'],

    // --- Hizmet sayfalarina ozel --------------------------------------------
    ['home' => false, 'pages' => ['web-tasarim'], 'sort' => 10,
     'q' => 'Hazir sablon mu kullaniyorsunuz?',
     'a' => '<p>Hayir. Her site isletmeye ozel tasarlanir. Hazir sablonlar hem birbirinin '
          . 'ayni gorunur hem de kullanilmayan kod tasidiklari icin mobilde yavas acilir.</p>'],

    ['home' => false, 'pages' => ['e-ticaret-sitesi', 'zeytinyagi-e-ticaret-sitesi'], 'sort' => 11,
     'q' => 'Pazaryerinde satiyorum, kendi magazama gerek var mi?',
     'a' => '<p>Pazaryeri musteriyi size degil kendine baglar; komisyon oderken musteri '
          . 'listesi de sizde kalmaz. Ikisini birlikte yurutmek en saglikli yol: pazaryeri '
          . 'yeni musteri getirir, kendi magazaniz onu elinizde tutar.</p>'],

    ['home' => false, 'pages' => ['rezervasyon-sistemi', 'otel-pansiyon-web-sitesi'], 'sort' => 12,
     'q' => 'Rezervasyon platformlarini birakmam mi gerekiyor?',
     'a' => '<p>Hayir. Amac platformlari birakmak degil, oradan gelen misafiri bir sonraki '
          . 'sefere dogrudan size getirmek. Kendi kanaliniz guclendikce komisyonlu satisin '
          . 'payi kendiliginden duser.</p>'],

    ['home' => false, 'pages' => ['seo-hizmeti'], 'sort' => 13,
     'q' => 'Ayni metni ilce adi degistirerek cogaltabilir miyiz?',
     'a' => '<p>Hayir, bunu yapmiyoruz. Bu yontem kisa vadede sayfa sayisini artirsa da '
          . 'Google tarafindan doorway page olarak degerlendirilir ve yakalandiginda tum '
          . 'sayfalari birden degersizlestirir. Her bolge sayfasini o bolgenin kendi '
          . 'ekonomisinden yaziyoruz.</p>'],

    ['home' => false, 'pages' => ['coklu-dil-web-sitesi'], 'sort' => 14,
     'q' => 'Otomatik ceviri eklentisi yeterli olmaz mi?',
     'a' => '<p>Olmaz. Otomatik ceviri arama motorlari icin ayri bir sayfa uretmez; yani '
          . 'Almanca arama yapan biri sizi bulamaz. Gercek coklu dil, her dil icin ayri '
          . 'adres ve o dile gore yazilmis icerik demektir.</p>'],

    ['home' => false, 'pages' => ['web-sitesi-bakim'], 'sort' => 15,
     'q' => 'Bakim anlasmasi zorunlu mu?',
     'a' => '<p>Zorunlu degil. Her proje bir aylik ucretsiz destekle teslim edilir. '
          . 'Bakim anlasmasi aylik sabit ucretlidir, taahhut yoktur ve istediginiz ay '
          . 'birakabilirsiniz.</p>'],

    // --- Ilce sayfalarina ozel (bolum 9.6: her ilce sayfasinda SSS olmali) --
    ['home' => false, 'pages' => ['edremit-web-tasarim'], 'sort' => 20,
     'q' => 'Edremit disindaki ilcelere de hizmet veriyor musunuz?',
     'a' => '<p>Evet. Edremit merkezli calisiyoruz ama Havran, Burhaniye, Akcay, Altinoluk, '
          . 'Gomec, Ayvalik ve Balikesir merkezde de yerinde gorusme yapiyoruz. '
          . 'Cevre ilcelere teslimat ya da hizmet veren isletmeler icin her bolgeye ayri '
          . 'sayfa kuruyoruz.</p>'],

    ['home' => false, 'pages' => ['akcay-web-tasarim'], 'sort' => 21,
     'q' => 'Sezonluk pansiyonum icin siteye ne zaman baslamaliyim?',
     'a' => '<p>Kasim ile ocak arasi en dogru zaman. Sitenin subatta yayinda olmasi, arama '
          . 'motorlarinin sayfayi taniyip siralamaya alacagi sureyi kazandirir. Mayista '
          . 'baslanan bir is o sezonu yakalayamaz.</p>'],

    ['home' => false, 'pages' => ['altinoluk-web-tasarim'], 'sort' => 22,
     'q' => 'Site yonetimi icin duyuru sayfasi yapiyor musunuz?',
     'a' => '<p>Evet. Aidat duyurusu, havuz bakim takvimi, genel kurul cagrisi ve ariza '
          . 'bildirimi icin panelden yonetilen bir yapi kuruyoruz. Ariza formu bildirimi '
          . 'yapan daireyi, konuyu ve fotografi tek ekranda topluyor.</p>'],

    ['home' => false, 'pages' => ['burhaniye-web-tasarim'], 'sort' => 23,
     'q' => 'Zeytinyagimi kendi etiketimle satmak icin neye ihtiyacim var?',
     'a' => '<p>Urun adi ve etiket bilgisi, litre bazli fiyatlandirma, kirilabilir urun icin '
          . 'uygun koli, kargo anlasmasi ve iade sureci. Kurdugumuz magazalarda bu adimlarin '
          . 'hepsi bastan tanimli gelir; siz yalnizca urun, fiyat ve stok girersiniz.</p>'],

    ['home' => false, 'pages' => ['havran-web-tasarim'], 'sort' => 24,
     'q' => 'Kucuk bir esnafim, buyuk bir siteye ihtiyacim var mi?',
     'a' => '<p>Genellikle yok. Uc-bes sayfalik, telefonu ilk ekranda duran ve harita '
          . 'kaydiyla birebir ayni adresi gosteren bir yapi cogu esnaf icin yeterli. '
          . 'Once bunu kurup, gelen talebe gore buyutmek daha dogru.</p>'],

    ['home' => false, 'pages' => ['ayvalik-web-tasarim'], 'sort' => 25,
     'q' => 'Ayvalik\'ta rakiplerimden nasil ayrisirim?',
     'a' => '<p>Herkesin yazdigi "tarihi tas ev" ve "denize yurume mesafesi" cumleleri artik '
          . 'hicbir seyi ayirmiyor. Kahvaltida hangi yerel ureticiyi kullandiginiz, hangi '
          . 'odanin hangi saatte gunes aldigi gibi gercek ayrintilar ayristirir.</p>'],

    ['home' => false, 'pages' => ['gomec-web-tasarim'], 'sort' => 26,
     'q' => 'Kamp alani icin sitede neler olmali?',
     'a' => '<p>Elektrik ve su baglantisi, tuvalet ve dus durumu, gece guvenligi, evcil hayvan '
          . 'kabulu ve gunluk ucret. Bu sorularin cevabini acikca yazan bir isletme telefon '
          . 'trafiginin yarisindan kurtulur ve dogru misafiri ceker.</p>'],

    ['home' => false, 'pages' => ['balikesir-web-tasarim'], 'sort' => 27,
     'q' => 'Merkezde rekabet yuksek; reklam mi vermeliyim?',
     'a' => '<p>Reklam hizli sonuc verir ama musluk kapandiginda trafik de biter. Onerimiz '
          . 'dengeli kullanim: reklami kisa vadeli talep icin, icerigi uzun vadeli gorunurluk '
          . 'icin. Reklamdan donusum getiren kelimeler icin kalici icerik yazmak butceyi '
          . 'zamanla dusurur.</p>'],

    // --- Sektor sayfalarina ozel --------------------------------------------
    ['home' => false, 'pages' => ['restoran-kafe-qr-menu'], 'sort' => 30,
     'q' => 'QR menu icin PDF yeterli olmaz mi?',
     'a' => '<p>Olmaz. PDF menu telefonda yakinlastirma gerektirir, yavas acilir ve arama '
          . 'motorlari icerigini okuyamaz. Gercek bir QR menu, masada bir saniyede acilan ve '
          . 'panelden bes dakikada guncellenen bir sayfadir.</p>'],

    ['home' => false, 'pages' => ['emlak-web-sitesi'], 'sort' => 31,
     'q' => 'Portal ilanlarim varken siteye ne gerek var?',
     'a' => '<p>Alici portalda ilani gordukten sonra ofisin adini aratir. O anda karsisina '
          . 'cikan sayfa, ofisin bolgeyi ne kadar tanidigini gostermelidir. Bolge rehberi '
          . 'yazan bir ofis, yalnizca ilan listeleyen on ofisin onune gecer.</p>'],

    ['home' => false, 'pages' => ['nakliyat-web-sitesi'], 'sort' => 32,
     'q' => 'Teklif formunda kac alan olmali?',
     'a' => '<p>Alti alani gecmemeli: nereden nereye, tarih, oda sayisi, kat ve asansor '
          . 'bilgisi, telefon. Daha uzun formlar mobilde terk oranini gozle gorulur '
          . 'artiriyor.</p>'],

    ['home' => false, 'pages' => ['tabela-matbaa-web-sitesi'], 'sort' => 33,
     'q' => 'Fiyat listesi yayinlamali miyim?',
     'a' => '<p>Bu iste fiyat olcu ve malzemeye bagli oldugu icin liste yayinlamak zor. '
          . 'Bunun yerine formun olcu, adet ve malzeme sormasi, musterinin gorsel '
          . 'yukleyebilmesi sureci kisaltir ve gereksiz telefon trafigini azaltir.</p>'],
];
