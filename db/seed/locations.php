<?php
/**
 * Ilce sayfalari.  DOCS.md 4.3, 4.7
 *
 * KRITIK KURAL (bolum 4.7): Ayni metnin ilce adi degistirilerek cogaltilmasi
 * Google tarafindan doorway page olarak degerlendirilir ve tum sayfalari
 * birden degersizlestirir. Bu yuzden asagidaki sayfalarin her biri:
 *
 *   - en az 500 kelime OZGUN metin icerir,
 *   - o ilcenin kendi ekonomisinden ve gunluk hayatindan yazilmistir,
 *   - farkli baslik yapisi ve farkli kelime dagarcigi kullanir,
 *   - o ilceye ait en az bir ornek site kaydiyla eslesir,
 *   - ilceye ozel SSS kayitlariyla baglanir.
 *
 * Panel, kaydedilen her ilce sayfasini digerleriyle karsilastirir ve %70
 * uzeri ortusmede guclu uyari verir (bolum 9.6).
 *
 * Ilce sayfalari yalnizca TR'de yayinlanir; diger dillerde hreflang
 * verilmez (bolum 4.6).
 */

declare(strict_types=1);

return [

    // -----------------------------------------------------------------------
    'edremit' => [
        'district' => 'Edremit',
        'slug'     => 'edremit-web-tasarim',
        'title'    => 'Edremit Web Tasarım',
        'meta_title' => 'Edremit web tasarım — yerel işletmeler için | Arcates',
        'meta_description' => 'Edremit merkezli işletmeler için web tasarım, e-ticaret ve yerel SEO. Yerinde görüşme, ölçülebilir sonuç.',
        'excerpt'  => 'Körfezin ticaret merkezinde, yerinde görüşen bir ekiple çalışın.',
        'content'  => <<<'HTML'
<h2>Edremit ticaretinin kendine özgü ritmi</h2>
<p>Edremit, Körfezin idari ve ticari merkezi. Çevre ilçelerden gelen alışveriş
trafiği, hastane ve resmi kurumların yarattığı günlük hareket, Kaz Dağları'na
çıkan yolun buradan geçmesi; hepsi ilçenin iş hayatını komşu ilçelerden ayırır.
Akçay ya da Altınoluk sezona bağlı yaşarken Edremit on iki ay aynı tempoda
çalışır.</p>

<p>Bunun web tarafındaki karşılığı şu: Edremitli bir işletmenin müşterisi
çoğunlukla yazlıkçı değil, bölgede yaşayan biri. Arama alışkanlıkları da farklı.
"Edremit oto servis", "Edremit diş kliniği", "Edremit mobilyacı" gibi aramalar
akşam saatlerinde ve hafta içi yoğunlaşır. Yazlık bölgelerde ise trafik cuma
günü başlar, pazar akşamı biter.</p>

<h2>Hangi işletmeler ne arıyor</h2>
<p>İlçede en çok çalıştığımız gruplar sağlık kuruluşları, oto sektörü, mobilya ve
yapı malzemesi satan işletmeler, muhasebe ve hukuk büroları. Bunların ortak
sorunu aynı: harita sonuçlarında çıkıyorlar ama sitesi olmadığı için müşteri
karşılaştırma yaptığı anda rakibe geçiyor.</p>

<p>İkinci grup, çevre ilçelere de hizmet veren işletmeler. Bir yapı market
Edremit'te dükkanı olsa da Havran ve Burhaniye'ye teslimat yapıyor. Bu durumda
tek bir "iletişim" sayfası yetmiyor; hizmet verilen her bölge için ayrı ve
gerçekten farklı sayfalar gerekiyor.</p>

<h2>Kaz Dağları trafiğini kaçırmayın</h2>
<p>Edremit üzerinden geçen dağ turizmi, ilçedeki konaklama ve yeme içme
işletmeleri için ciddi bir potansiyel. Ancak bu trafik çoğunlukla telefonundan
arama yapan, yolda olan bir kitle. Sitenin mobilde iki saniyenin altında
açılması, telefon numarasının ilk ekranda görünmesi ve yol tarifi bağlantısının
tek dokunuşla çalışması burada doğrudan ciro demek.</p>

<h2>Bizim yaptığımız iş</h2>
<ul>
  <li>İşletmenin hangi aramalarda görünmesi gerektiğinin tespiti</li>
  <li>Mobil öncelikli, hızlı açılan kurumsal site</li>
  <li>Google İşletme Profili ile birebir aynı iletişim bilgileri</li>
  <li>Çevre ilçelere hizmet veriliyorsa her bölge için ayrı sayfa</li>
  <li>Hangi sayfanın telefon getirdiğinin ölçülmesi</li>
</ul>

<h2>Yerinde görüşme</h2>
<p>Edremit merkezdeki işletmelerle görüşmeyi kendi mekanlarında yapıyoruz. Bir
mobilyacının deposunu görmeden ürün fotoğraflarının nasıl çekileceğine,
bir kliniğin randevu akışını dinlemeden formun nasıl kurulacağına karar
verilemez. Bu görüşme ücretsizdir ve bir saati geçmez.</p>

<p>Sonrasında yazılı fiyat ve takvim gönderiyoruz. Kurumsal bir site üçüncü
haftanın sonunda yayında oluyor. Detaylar için
<a href="/fiyatlar">fiyatlar sayfamıza</a>,
hazırladığımız <a href="/referanslar">örnek sitelere</a>
bakabilirsiniz.</p>

<h2>Rakip analizinden çıkan tablo</h2>
<p>İlçedeki işletmelerin sitelerine bakıldığında tekrar eden üç sorun göze
çarpıyor. Birincisi, sitelerin büyük bölümü masaüstü için tasarlanmış ve
telefonda okunmuyor; oysa gelen trafiğin dörtte ucu mobil. İkincisi, adres ve
telefon bilgisi harita kaydıyla aynı yazılmamış, bu da arama motorunun
işletmeyi doğrulamasını zorlaştırıyor. Üçüncüsü, sayfaların çoğunda hangi
hizmetin verildiği genel cümlelerle geçiliyor; arayan kişi arayacağı hizmeti
sayfada göremeyince çıkıp gidiyor.</p>

<p>Bu üç sorunun ucu de teknik değil, karar sorunudur ve çözümü pahalı değildir.
Bir işletmenin verdiği her hizmet için ayrı bir başlık ve birkaç paragraf
yazması, ilçedeki rakiplerinin çoğunun önüne geçmesine yetiyor.</p>

<h2>Ölçüm olmadan iyileştirme olmaz</h2>
<p>Yayına aldığımız her sitede hangi sayfanın telefon ya da form getirdiğini
kaydediyoruz. Üç ay sonra tablo genellikle şaşırtıcı oluyor: işletmenin en çok
önem verdiği anasayfa değil, kimsenin dikkate almadığı bir hizmet sayfası iş
getiriyor. Bu bilgi olmadan içeriğin nereye yatırılacağına karar vermek tahminden
ibaret kalır.</p>

<p>Panelde bu veriyi işletmenin kendisi de görüyor. Kaynak sayfa, ziyaretçinin
siteye nereden geldiği ve hangi aramanın sonuca döndüğü aynı ekranda duruyor.</p>

<h2>Panelden yönetim: kim ne değiştirebilir</h2>
<p>Teslim ettiğimiz her sitede metinlerin tamamı panelden düzenlenebilir. Bu
sadece bir kolaylık değil, sitenin canlı kalmasının şartıdır. Fiyatını
değiştirmek için bize yazması gereken bir işletme, üçüncü ayda fiyat
güncellemekten vazgeçer ve site eskimeye başlar.</p>

<p>Panelde iki rol vardır. Yönetici her şeyi görür; editör yalnızca içerik
bölümlerine erişir. Böylece ofisteki bir çalışan blog yazısı eklerken ayarlara
ya da kullanıcı listesine dokunamaz. Her değişiklik kim tarafından, ne zaman
yapıldığıyla birlikte işlem günlüğüne yazılır.</p>

<p>Çevredeki diğer ilçelerde de çalışıyoruz:
<a href="/akcay-web-tasarim">Akçay</a>,
<a href="/havran-web-tasarim">Havran</a> ve
<a href="/burhaniye-web-tasarim">Burhaniye</a> sayfalarımıza göz atabilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'akcay' => [
        'district' => 'Akçay',
        'slug'     => 'akcay-web-tasarim',
        'title'    => 'Akçay Web Tasarım',
        'meta_title' => 'Akçay web tasarım — pansiyon ve restoran siteleri',
        'meta_description' => 'Akçay pansiyon, otel ve restoranları için rezervasyon odaklı web tasarım. Sezon dışı doluluk için yerel SEO.',
        'excerpt'  => 'Sezonluk işletmeler için rezervasyon ve doluluk odaklı siteler.',
        'content'  => <<<'HTML'
<h2>Dokuz aylık hazırlık, üç aylık sezon</h2>
<p>Akçay'da iş takvimi nettir: haziran başında başlar, eylül ortasında biter.
Geri kalan dokuz ay hazırlık ve bekleyiştir. Bu ritim, internet sitesinden
beklentiyi de değiştirir. Yıl boyu aynı tempoda çalışan bir işletmenin sitesiyle,
gelirinin tamamını üç ayda yapan bir pansiyonun sitesi aynı şekilde
kurgulanamaz.</p>

<p>Sezonluk işletmede sitenin işi, aralık ile mayıs arasında yapılır. Misafir
tatilini şubatta planlar, martta karar verir, nisanda parayı yatırır. Haziranda
sitenizi açan kişi çoğu zaman yer arayan değil, yol tarifi arayan kişidir.</p>

<h2>Rezervasyon sitelerinin dışına çıkmak</h2>
<p>Akçay'daki pansiyonların büyük bölümü doluluğunun tamamını rezervasyon
platformlarından sağlıyor. Komisyon oranının yüzde on beş ile yirmi beş arasında
olduğu düşünülürse, otuz odalı bir tesis için sezonluk kayıp ciddi bir rakama
ulaşıyor.</p>

<p>Amaç platformları bırakmak değil. Amaç, oradan gelen misafiri bir sonraki
sene doğrudan size getirmek. Bunun için üç şey gerekir: misafirin adını ve
e-postasını alan bir kanal, doğrudan rezervasyonda anlamlı bir avantaj ve
kendi sitenizde çalışan bir müsaitlik takvimi.</p>

<h2>Sahil şeridi ve yeme içme</h2>
<p>Akçay sahilindeki restoran ve kafeler için durum farklı. Burada rezervasyondan
çok günlük görünürlük önemli. Akşam yemek yeri arayan bir aile telefonundan
harita uygulamasını açar, ilk üç sonuca bakar ve fotoğraflara göz atar. Menünün
güncel olması, fotoğrafların gerçek olması ve çalışma saatlerinin doğru
yazılması bu aşamada karar verdirir.</p>

<h2>Almanca ve İngilizce içerik</h2>
<p>Bölgede yabancı misafir oranı her yıl artıyor. Otomatik çeviri eklentileri
arama motorları için ayrı sayfa üretmediği için Almanca arama yapan biri sizi
bulamaz. Gerçek çoklu dil kurulumu, her dil için ayrı adres ve ayrı metin
demektir; bunu <a href="/coklu-dil-web-sitesi">çoklu dil sayfamızda</a>
anlatıyoruz.</p>

<h2>Ne zaman başlamalı</h2>
<p>Sezonluk bir işletme için doğru zaman kasım ile ocak arasıdır. Sitenin şubatta
yayında olması, arama motorlarının sayfayı tanıyıp sıralamaya alacağı süreyi
kazandırır. Mayısta başlanan bir iş o sezonu yakalayamaz.</p>

<h2>Fotoğraf, metinden önce konuşur</h2>
<p>Konaklama aramasında karar çoğunlukla fotoğrafla verilir. Sahilde çekilmiş
genel bir manzara karesi değil, misafirin gerçekten kalacağı odanın, banyonun ve
kahvaltı masasının fotoğrafı ise yarar. Görseli güzelleştirmek yerine doğru
göstermek, sezon sonundaki yorum puanını korumanın en ucuz yolu.</p>

<p>Çektiğimiz ya da işletmeden aldığımız her fotoğraf siteye yüklenirken küçük,
orta ve büyük boyutlarda ve WebP biçimiyle yeniden üretiliyor. Böylece telefonla
bakan misafir on iki fotoğrafı saniyeler içinde görüyor, sayfa ağırlığı
şişmiyor.</p>

<h2>Sezon dışı doluluk</h2>
<p>Akçay'da asıl kazanç nisan-mayıs ve eylül-ekim aylarında gizli. Bu dönemde
gelen kitle aile değil; yürüyüş yapan, sakinlik arayan, çalışırken tatil yapan
bir grup. Bu kitleye ulaşmak için sitenin yaz anlatısından farklı bir dil
kurması gerekiyor: sıcaklık ortalamaları, açık kalan işletmeler, çalışma için
internet hızı ve uzun konaklama fiyatları.</p>

<p>Bu içeriklerin yaz sezonunda yazılması, sonbaharda arama yapan kişinin
karşısına çıkması için gereken süreyi kazandırıyor.</p>

<h2>Rezervasyon akışında misafiri kaybettiren noktalar</h2>
<p>Doğrudan rezervasyon denemelerinin çoğu üç noktada kesilir. Birincisi, fiyat
görünmeden önce bilgi istenmesi; misafir fiyatı görmeden form doldurmaz.
İkincisi, müsaitliğin belirsiz olması; "sorunuz" yazıyorsa misafir sormaz,
platforma döner. Üçüncüsü, mobilde uzun form; altı alandan fazlası terk
oranını gözle görülür artırır.</p>

<p>Kurduğumuz akışta önce tarih ve kişi sayısı sorulur, hemen fiyat ve müsaitlik
gösterilir, ancak ondan sonra iletişim bilgisi istenir. Bu sıra değişikliği tek
başına dönüşüm oranını belirgin şekilde yükseltiyor.</p>

<h2>Yorumlarla çalışmak</h2>
<p>Tesis puanı sezon boyunca en değerli varlık. Sitede uydurma yorum
yayınlamıyoruz ve yapısal veride puan işaretlemesi yapmıyoruz; bu hem yanlış
hem de yakalandığında tüm sayfaları riske atıyor. Bunun yerine gerçek
misafirlerden gelen yorumları düzenli toplayacak bir akış kuruyoruz.</p>

<p>Konaklama işletmelerine özel kurduğumuz altyapıyı
<a href="/rezervasyon-sistemi">rezervasyon sistemi sayfasında</a>,
tesis sitelerine özel çalışmamızı
<a href="/otel-pansiyon-web-sitesi">otel ve pansiyon sayfasında</a>
bulabilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'altinoluk' => [
        'district' => 'Altınoluk',
        'slug'     => 'altinoluk-web-tasarim',
        'title'    => 'Altınoluk Web Tasarım',
        'meta_title' => 'Altınoluk web tasarım — emlak ve site yönetimi siteleri',
        'meta_description' => 'Altınoluk emlak ofisleri, site yönetimleri ve konaklama işletmeleri için web tasarım ve yerel SEO.',
        'excerpt'  => 'Emlak, site yönetimi ve dağ turizmi işletmeleri için siteler.',
        'content'  => <<<'HTML'
<h2>Yazlık konut ekonomisi</h2>
<p>Altınoluk'u Körfezin diğer ilçelerinden ayıran şey, ekonomisinin büyük
bölümünün yazlık konut üzerine kurulu olması. Oksijen oranı ve Kazdağları
eteklerindeki konumu nedeniyle burası yıllardır emeklilik ve ikinci konut
tercihi. Bu da nüfusun kış aylarında düşük, yaz aylarında katlanarak arttığı
bir yapı oluşturuyor.</p>

<p>Böyle bir yerde en yoğun çalışan iki sektör emlak ve site yönetimi hizmetleri.
İkisinin de internet ihtiyacı birbirinden çok farklı.</p>

<h2>Emlak ofisleri için</h2>
<p>Emlak ilanlarının büyük bölümü portallarda yayınlanıyor; bu değişmeyecek.
Ancak alıcı bir portalda ilanı gördükten sonra ofisin adını aratır. O anda
karşısına çıkan sayfa, o ofisin bölgeyi ne kadar tanıdığını göstermelidir.</p>

<p>Bizim önerdiğimiz yapı şu: portal ilanlarını bırakıp her mahalle için bölge
rehberi yazmak. Denize uzaklık, site aidatları, kış aylarında açık kalan
işletmeler, sağlık ocağı ve market mesafeleri. Alıcının gerçekten merak ettiği
bunlardır ve bu bilgiyi veren ofis, ilan listeleyen ofisten öne geçer.</p>

<h2>Site yönetimleri için</h2>
<p>Yüzlerce daireli sitelerde yönetimin en büyük yükü iletişim. Aidat
duyurusundan havuz bakım takvimine, genel kurul çağrısından arıza bildirimine
kadar her şey telefonla yürütülüyor. Basit bir duyuru sayfası ve form bu yükü
gözle görülür şekilde azaltıyor.</p>

<h2>Dağ turizmi ve yürüyüş</h2>
<p>Kazdağları'na yönelik doğa yürüyüşü, kamp ve butik konaklama işletmeleri son
yıllarda arttı. Bu işletmelerin müşterisi genellikle şehirden gelen, planlı ve
internetten araştıran bir kitle. Rota anlatımı, mevsime göre tavsiye ve gerçek
fotoğraf bu grupta doğrudan rezervasyona dönüşüyor.</p>

<h2>Bizim yaklaşımımız</h2>
<ul>
  <li>Sektöre göre farklı site kurgusu; emlak ile konaklama aynı şablonla olmaz</li>
  <li>Mahalle ve bölge bazlı içerik; kopyala yapıştır değil, gerçek bilgi</li>
  <li>Mobil hız; alıcının çoğu telefonundan bakıyor</li>
  <li>Yabancı alıcı hedefleniyorsa İngilizce ve Almanca yayın</li>
</ul>

<h2>Alıcının gerçekten sorduğu sorular</h2>
<p>Yazlık alacak kişinin ilanlarda bulamadığı bilgiler bellidir: kış aylarında
sitede kaç daire dolu kalıyor, market ve eczane yürüyüş mesafesinde mi, aidat
neyi kapsıyor, su kesintisi oluyor mu, en yakın sağlık kuruluşu ne kadar uzakta.
Bu soruların cevabını veren bir ofis, ilan listeleyen on ofisin önüne geçer.</p>

<p>Bu bilgiyi yazmak zaman ister ama bir kez yazılır ve yıllarca çalışır. Üstelik
bu tür ayrıntılı sayfalar, alıcının aramada kullandığı uzun cümlelerle birebir
örtüştüğü için rekabetin en düşük olduğu yerden trafik getirir.</p>

<h2>Yabancı alıcıya satış</h2>
<p>Bölgede yabancı alıcı ilgisi artıyor. Ancak ilan metnini otomatik çeviriyle
İngilizceye çevirmek yeterli değil; alıcının sorduğu sorular farklı. Tapu
süreci, oturma izni, vergi ve site aidatının nasıl ödendiği gibi başlıklar
Türkiyeli alıcının zaten bildiği, yabancı alıcının ise hiç bilmediği
konular.</p>

<p>Bu yüzden çoklu dil kurulumunu çeviri olarak değil, ayrı içerik olarak
yapıyoruz. Her dil kendi adresinde yayınlanır ve o dile ait metin, o kitlenin
sorularına göre yazılır.</p>

<h2>Site yönetimi için duyuru ve arıza akışı</h2>
<p>Büyük sitelerde yönetici en çok zamanı tekrarlayan sorulara harcar: aidat ne
zaman yatıyor, havuz ne zaman açılıyor, genel kurul ne zaman. Bu soruların
cevabını taşıyan basit bir duyuru sayfası telefon trafiğini belirgin şekilde
azaltır.</p>

<p>Arıza bildirimi için kurduğumuz form, bildirimi yapan daireyi, konuyu ve
fotoğrafı tek ekranda topluyor ve yöneticiye e-posta olarak gönderiyor. Kayıtlar
panelde durum etiketiyle takip ediliyor; hangi arızanın ne zaman kapandığı genel
kurulda tartışma konusu olmaktan çıkıyor.</p>

<h2>Sezon dışı görünürlük</h2>
<p>Altınoluk'ta arama trafiği şubat ile mayıs arasında zirve yapar; alıcı kararı
bu aylarda verir. Sitenin bu dönemde hazır olması, ocak ayından önce içeriğin
yayınlanmış olmasını gerektirir. Nisanda yayına alınan bir emlak sitesi o yılın
sezonunu büyük ölçüde kaçırır.</p>

<p>Hazırladığımız <a href="/referanslar">örnek siteleri</a>
görebilir, emlak sektörüne özel yaklaşımımızı
<a href="/emlak-web-sitesi">emlak web sitesi sayfasında</a> okuyabilirsiniz.
Komşu ilçe için <a href="/akcay-web-tasarim">Akçay sayfamıza</a> da
bakabilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'burhaniye' => [
        'district' => 'Burhaniye',
        'slug'     => 'burhaniye-web-tasarim',
        'title'    => 'Burhaniye Web Tasarım',
        'meta_title' => 'Burhaniye web tasarım — üretici ve kooperatif siteleri',
        'meta_description' => 'Burhaniye zeytin üreticileri, kooperatifler ve Ören işletmeleri için e-ticaret ve web tasarım.',
        'excerpt'  => 'Zeytin üretimi ve Ören turizmi; iki farklı iş, iki farklı site.',
        'content'  => <<<'HTML'
<h2>İki ayrı ekonomi, tek ilçe</h2>
<p>Burhaniye'de birbirinden bağımsız iki ekonomi yan yana çalışır. Biri iç
kesimdeki zeytin üretimi ve tarım; diğeri Ören sahilindeki yazlık ve konaklama
hareketi. Aynı ilçede olmalarına rağmen müşteri kitleleri, sezon takvimleri ve
internet ihtiyaçları örtüşmez.</p>

<h2>Üretici ve kooperatifler</h2>
<p>Zeytin ve zeytinyağı üreticisinin en büyük kaybı aracıya kalan paydır. Yağını
tenekeyle veren bir üretici ile kendi etiketiyle satan üretici arasındaki fark
kat kat. Ancak kendi markasıyla satmak, kutu tasarımından kargo anlaşmasına,
etiket mevzuatından müşteri iletişimine uzanan yeni bir iş demek.</p>

<p>Web tarafında bizim işimiz şunlar: hasat dönemine göre on sipariş alan bir
mağaza, litre ve kilogram varyantlarıyla stok takibi, bölgeye göre kargo ücreti
ve tekrar siparişi kolaylaştıran bir müşteri hesabı yapısı. Kooperatifler için
ayrıca üye üreticilerin görünürlüğü ve ortak marka anlatısı.</p>

<p>Ayrıntıları <a href="/zeytinyagi-e-ticaret-sitesi">zeytinyağı e-ticaret
sayfamızda</a> anlatıyoruz.</p>

<h2>Ören sahili</h2>
<p>Ören, Körfezin daha sakin sahil noktalarından biri. Buradaki pansiyon ve
apart işletmelerinin müşterisi, Akçay'in kalabalığından kaçan ve sessizlik
arayan bir kitle. Bu farkı sitede anlatmak gerekiyor; "denize sıfır" cümlesi
yeterli değil, "akşam sekizde sahil boşalıyor" bilgisi karar verdiriyor.</p>

<p>Ören işletmelerinin çoğu tek kişiyle yönetiliyor. Bu yüzden kurduğumuz
sistemlerde yönetim panelinin sadeliği tasarımdan daha önemli: fiyat
değiştirmek üç tıklama sürmemeli.</p>

<h2>Üretici pazarı ve yerel esnaf</h2>
<p>İlçedeki üretici pazarı ve çarşı esnafı için durum daha basit. Burada gereken
büyük bir site değil; doğru bilgilerle kurulmuş bir sayfa ve harita kaydı.
Çalışma saati, güncel telefon, gerçek fotoğraf ve birkaç müşteri sorusunun
cevabı çoğu esnaf için yeterli.</p>

<h2>Nasıl başlıyoruz</h2>
<p>Üretici işletmelerde görüşmeyi hasat dışı dönemde, işletmenin kendi tesisinde
yapıyoruz. Sıkma tesisini görmeden ürün anlatısının nasıl kurulacağına karar
vermek zor. Görüşme ücretsiz, bir saatlik.</p>

<h2>Etiketten kargoya: doğrudan satışın adımları</h2>
<p>Kendi markasıyla satmaya başlayan bir üreticinin karşısına çıkan başlıklar
sırasıyla şunlar: ürün adı ve etiket bilgisi, litre bazlı fiyatlandırma,
kırılabilir ürün için uygun koli, kargo firmasıyla anlaşma ve iade süreci. Bu
adımların hiçbiri tek başına zor değil, ancak birlikte planlanmadığında ilk
siparişlerde sorun çıkarıyor.</p>

<p>Kurduğumuz mağazalarda bu adımların hepsi baştan tanımlı geliyor. Üretici
yalnızca ürününü, fiyatını ve stoğunu giriyor; kargo ücreti bölgeye göre
otomatik hesaplanıyor, sipariş onayı müşteriye e-posta ile gidiyor.</p>

<h2>Hasat takvimine göre satış</h2>
<p>Zeytinyağı satışı yıl boyunca aynı değildir. Kasım-aralık döneminde yeni
hasat talebi patlar, ilkbaharda düşer, yaz aylarında hediye amaçlı sipariş
artar. Mağazanın bu ritmi tanıması gerekir: on sipariş dönemi, stok bittiğinde
bekleme listesi ve yeni hasat duyurusu için e-posta listesi.</p>

<p>Bir üreticinin en değerli varlığı, geçen yıl ürününü beğenip bu yıl yine
arayacak müşteri listesidir. Pazaryerinde satan üretici bu listeye sahip
olamaz.</p>

<h2>Ambalaj ve anlatı birlikte çalışır</h2>
<p>Zeytinyağı satışında ürünün kendisi kadar anlatısı da satar. Ağacın yaşı,
hasadın elle mi makineyle mi yapıldığı, sıkım tesisine kaç saatte ulaşıldığı ve
asitlik değeri; bunlar meraklı müşteri için fiyat farkını meşrulaştıran
bilgilerdir. Aynı bilgiler arama motorlarında da uzun kuyruklu aramalarla
örtüşür.</p>

<p>Bu yüzden ürün sayfalarını katalog gibi değil, üretim anlatısı gibi
kuruyoruz. Her ürünün altında hasat yılı, bölgesi ve analiz değerleri duruyor.
Aynı sayfa hem müşteriyi ikna ediyor hem arama sonuçlarında ayrışıyor.</p>

<h2>Ören'de sezon dışı kullanım</h2>
<p>Ören'deki apart ve pansiyonların bir bölümü kış aylarında uzun dönem kiraya
veriliyor. Bu kitle tatilci değil; bölgede geçici olarak çalışan ya da kişi
sakin geçirmek isteyen bir grup. Sitede bu seçeneğin ayrı bir sayfayla
anlatılması, boş geçen ayların bir bölümünü doldurabiliyor.</p>

<p><a href="/e-ticaret-sitesi">E-ticaret hizmetimize</a> ve
<a href="/fiyatlar">fiyat aralıklarına</a> bakabilir,
komşu ilçeler için <a href="/havran-web-tasarim">Havran</a> ve
<a href="/gomec-web-tasarim">Gömeç</a> sayfalarımızı inceleyebilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'havran' => [
        'district' => 'Havran',
        'slug'     => 'havran-web-tasarim',
        'title'    => 'Havran Web Tasarım',
        'meta_title' => 'Havran web tasarım — tarım ve esnaf siteleri',
        'meta_description' => 'Havran tarım işletmeleri, üretici ve çarşı esnafı için sade, hızlı ve yönetimi kolay web siteleri.',
        'excerpt'  => 'Tarım ve esnaf ağırlıklı bir ilçede sade ve ise yarayan siteler.',
        'content'  => <<<'HTML'
<h2>Sahilden iç kesime</h2>
<p>Havran, Körfezin sahil ilçelerinin aksine ekonomisini turizmden değil tarımdan
alan bir ilçe. Zeytin, şeftali, ceviz ve büyükbaş hayvancılık burada gelirin
omurgasını oluşturuyor. Pazar günü kurulan ilçe pazarı, çevre köylerden gelen
üreticinin doğrudan satış yaptığı ana kanal.</p>

<p>Böyle bir yapıda internetten beklenti de farklı. Havranlı bir işletmenin
ihtiyacı görkemli bir tanıtım sitesi değil; doğru bulunmak, telefonun çalması ve
ürünün sorulması.</p>

<h2>Küçük işletme için doğru ölçek</h2>
<p>İlçede en sık karşılaştığımız durum şu: işletme sahibi yıllar önce bir siteyi
yaptırmış, şifresini kaybetmiş, içerik güncellenmemiş ve site artık telefon
numarası bile yanlış gösteriyor. Bu durumda yeni ve büyük bir site yapmak
değil, doğru bilgiyi taşıyan sade bir sayfa kurmak daha faydalı.</p>

<p>Üç-beş sayfalık, telefonu ilk ekranda duran, harita kaydıyla birebir aynı
adresi gösteren ve işletmecinin kendi telefonundan güncelleyebildiği bir yapı
çoğu esnaf için fazlasıyla yeterli.</p>

<h2>Üretici için doğrudan satış</h2>
<p>Havran şeftalisi ve cevizi bölge dışında tanınan ürünler. Ancak üretici
çoğunlukla komisyoncuya teslim ediyor ve son fiyatı görmüyor. Sezonluk küçük
partilerle doğrudan satış, bir e-ticaret altyapısı gerektirmeyecek kadar basit
kurulabilir: sezon açıldığında alınan on sipariş, kapalı gruplarla duyuru ve
kargo anlaşması.</p>

<p>İşin büyüdüğü noktada gerçek bir mağazaya geçmek gerekir; bunu
<a href="/e-ticaret-sitesi">e-ticaret sayfamızda</a> anlatıyoruz.</p>

<h2>Tarım makineleri ve hizmet sağlayıcılar</h2>
<p>İlçede traktör yedek parçası, sulama sistemi, gübre ve tohum satan işletmeler
için arama trafiği mevsimseldir ve çok nettir. Sulama sezonu yaklaştığında
"damla sulama" aramaları, hasat öncesinde ise makine kiralama aramaları
yükselir. Sitenin bu döneme hazır olması, ürün sayfalarının sezondan iki ay önce
yazılmış olmasını gerektirir.</p>

<h2>Sadelik bir tercih değil, gereklilik</h2>
<p>İlçedeki internet bağlantısı ve kullanılan telefonlar dikkate alındığında,
ağır bir sitenin bedelini doğrudan işletme ödüyor. Kurduğumuz sayfalar görsel
ağırlığı düşük, yazı tipi sayısı sınırlı ve tek bir stil dosyasıyla çalışır.
Amaç güzel görünmek değil, üç saniyeden önce açılmak.</p>

<p>Yönetim tarafında da aynı sadelik geçerli: fiyat değiştirmek, çalışma saati
güncellemek ya da yeni bir fotoğraf eklemek panelde tek ekranda yapılır.</p>

<h2>Nasıl başlıyoruz</h2>
<p>Görüşmeyi işletmenin kendi yerinde yapıyoruz. Bir tarım bayisinin deposunu
görmeden ürün listesini nasıl düzenleyeceğimize karar veremeyiz. Görüşme
ücretsiz, bir saatlik ve sonrasında yazılı fiyat gönderiyoruz.</p>

<h2>Harita kaydı çoğuna yeter</h2>
<p>İlçedeki küçük işletmelerin büyük bölümü için gelen trafiğin çoğunluğu arama
motorunun kendisinden değil harita sonuçlarından geliyor. Bu yüzden ilk iş
Google İşletme Profili kaydını düzgün kurmak: kategori doğru seçilmiş, çalışma
saatleri güncel, telefon numarası sitedekiyle birebir aynı ve içerideki
fotoğraflar gerçek olmalı.</p>

<p>Bu kaydın siteyle çelişmesi durumunda arama motoru hangi bilginin doğru
olduğunu anlayamıyor ve işletmeyi geriye atıyor. En sık gördüğümüz hata,
sitede eski telefon numarasının unutulmuş olması. Küçük görünen bu ayrıntı
sıralamada ölçülebilir bir kayba yol açıyor.</p>

<h2>Mevsime göre içerik takvimi</h2>
<p>Tarım ağırlıklı bir ilçede içerik takvimi de tarım takvimine bağlıdır.
Budama dönemi, gübreleme, ilaçlama ve hasat; her biri öncesinde arama hacmi
yükselen başlıklar. Bu içeriklerin sezondan iki ay önce yayınlanması, arama
motorunun sayfayı tanıyıp sıralamaya alması için gereken süreyi kazandırır.</p>

<p>Yıl boyunca düzenli birkaç yazı, ilçedeki çoğu rakibin hiç yapmadığı bir şey
olduğu için uzun vadede belirgin fark yaratıyor. Bu yazıların uzun olması da
gerekmiyor; doğru soruyu net cevaplaması yeterli.</p>

<h2>Teslim sonrası</h2>
<p>Site yayına alındıktan sonra bir ay boyunca ücretsiz destek veriyoruz. Bu
sürede işletmeci panelde metin ve fotoğraf değiştirmeyi öğreniyor. Amaç
bağımlılık kurmak değil; işletmenin kendi sitesini kendi yönetebilmesi.</p>

<p>Komşu ilçelerdeki çalışmalarımız için
<a href="/edremit-web-tasarim">Edremit</a> ve
<a href="/burhaniye-web-tasarim">Burhaniye</a> sayfalarımıza,
fiyat aralıkları için <a href="/fiyatlar">fiyatlar sayfasına</a>
bakabilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'ayvalik' => [
        'district' => 'Ayvalık',
        'slug'     => 'ayvalik-web-tasarim',
        'title'    => 'Ayvalık Web Tasarım',
        'meta_title' => 'Ayvalık web tasarım — butik otel ve gastronomi siteleri',
        'meta_description' => 'Ayvalık ve Cunda butik otelleri, restoranları ve zeytinyağı üreticileri için çoklu dil web tasarım.',
        'excerpt'  => 'Butik konaklama, gastronomi ve zeytinyağı; üç farklı kitle, üç farklı site.',
        'content'  => <<<'HTML'
<h2>Körfezin en çok ziyaret edilen ilçesi</h2>
<p>Ayvalık, bölgede turizm hacmi en yüksek ilçe. Cunda adasındaki taş evler,
tarihi çarşı dokusu, yat limanı ve Yunan adalarına günübirlik feribot seferleri
ilçeyi yalnızca yazlıkçı değil, şehirli ve yabancı ziyaretçi çeken bir noktaya
dönüştürüyor. Bu, işletmeler için hem daha büyük bir pazar hem çok daha sert bir
rekabet demek.</p>

<h2>Butik konaklamada ayrışmak</h2>
<p>İlçede yüzlerce butik otel ve pansiyon var. Hepsi aynı fotoğrafları çekiyor,
aynı cümleleri yazıyor: "tarihi taş ev", "denize yürüme mesafesi", "sıcak bir
atmosfer". Bu cümleler artık hiçbir şeyi ayırmıyor.</p>

<p>Ayrışmak için gereken, o işletmenin gerçekten farklı olan şeyini yazmak.
Kahvaltıda hangi yerel üretici kullanılıyor, hangi odanın penceresi hangi
saatte güneş alıyor, gece sessizlik kaçta başlıyor. Bu ayrıntıları yazan bir
site, aynı fiyattaki on rakibin önüne geçer.</p>

<h2>Gastronomi ve çarşı işletmeleri</h2>
<p>Ayvalık'ta yeme içme kararları çoğunlukla yürüme sırasında, telefondan
veriliyor. Bu yüzden restoran için en kritik iki şey menünün güncel olması ve
haritada doğru görünmek. Sezonda değişen menüyü panelden beş dakikada
güncelleyebilen bir işletme, kağıt menüyü fotoğraflayıp paylaşandan önde
başlar.</p>

<p>İlçedeki kahvaltı kültürünün ve yerel lezzetlerin arama hacmi ciddi.
Bu aramaların karşılığını veren içerik, sezon dışında bile düzenli trafik
getiriyor.</p>

<h2>Zeytinyağı ve sabun üretimi</h2>
<p>İlçenin ikinci ekonomisi zeytinyağı ve zeytinyağı sabunu. Bu ürünlerin
müşterisi çoğunlukla bölge dışında; dolayısıyla iş doğrudan e-ticaret işi.
Hediyelik paketleme, yurt dışı kargo ve çoklu dil ürün anlatısı burada
belirleyici oluyor.</p>

<p>Yurt dışına satış yapan bir üretici için İngilizce ve Almanca sayfalar
çeviriden ibaret olamaz; gümrük, teslim süresi ve saklama koşulları gibi
başlıkların o dilde ayrıca anlatılması gerekir. Yaklaşımımızı
<a href="/coklu-dil-web-sitesi">çoklu dil sayfamızda</a> anlatıyoruz.</p>

<h2>Yabancı ziyaretçi ve dil</h2>
<p>Feribot seferleri ve turlar nedeniyle ilçede yabancı ziyaretçi oranı yüksek.
Menüsü, oda anlatısı ve iletişim bilgisi İngilizce olmayan bir işletme bu
kitlenin büyük bölümünü göremeden kaybediyor. İkinci dil, ilçede artık bir
ayrıcalık değil temel gereklilik.</p>

<h2>Rekabetin sert olduğu yerde ne yaparız</h2>
<ul>
  <li>İşletmenin gerçekten farklı olan yanını bulup öne çıkarmak</li>
  <li>Genel turizm aramaları yerine niyeti net aramaları hedeflemek</li>
  <li>Görsel ağırlığını düşürüp mobilde hızlı açılmayı garantilemek</li>
  <li>Çoklu dilde gerçek içerik yayınlamak</li>
  <li>Hangi sayfanın rezervasyon getirdiğini ölçmek</li>
</ul>

<h2>Sezon dışı trafik nereden gelir</h2>
<p>Ayvalık'ta yaz aylarının dışında da ciddi bir ziyaretçi hareketi var. Sonbahar
ve ilkbaharda hafta sonu gelen şehirli kitle, kış aylarında ise gastronomi
amaçlı kısa ziyaretler sürüyor. Bu dönemlerde arama yapan kişi tatil planlamıyor;
belirli bir şeyi arıyor: açık olan bir kahvaltı yeri, hafta sonu müsait bir oda,
belirli bir ürünün satıldığı bir dükkan.</p>

<p>Bu yüzden sezon dışı trafik için genel tanıtım metinleri değil, net sorulara
net cevap veren sayfalar gerekir. "Kış aylarında açık mıyız" sorusunun cevabı
sitede yazıyorsa, o sayfa tüm sezon dışı boyunca çalışır.</p>

<h2>Fotoğraf ve hız dengesi</h2>
<p>Turizm işletmelerinde görsel vazgeçilmez, ancak ağır fotoğraflar mobil hızı
öldürür. Yüklediğimiz her görsel üç farklı ölçüde ve WebP biçimiyle yeniden
üretiliyor; ziyaretçinin ekranına uygun olan gönderiliyor. Böylece galeri dolu
görünürken sayfa ağırlığı kontrol altında kalıyor.</p>

<p>Ayrıca her görselin alt metni giriliyor. Bu hem görme engelli ziyaretçiler
için gerekli hem de görsel aramalarından gelen trafiğin kaynağı.</p>

<h2>Cunda ve merkez ayrımı</h2>
<p>Cunda'daki bir işletme ile merkezdeki bir işletme aynı ilçede olsa da farklı
bir kitleye hitap eder. Cunda daha planlı, daha uzun süre kalan ve daha yüksek
bütçeli bir ziyaretçi çeker. Bu farkın site dilinde ve fiyat anlatımında
görünmesi gerekir; ikisini aynı şablonla anlatmak ikisine de zarar verir.</p>

<p>Konaklama işletmelerine özel kurduğumuz sistemi
<a href="/rezervasyon-sistemi">rezervasyon sistemi sayfasında</a>,
örnek kurguları <a href="/referanslar">örnek siteler sayfasında</a>
görebilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'gomec' => [
        'district' => 'Gömeç',
        'slug'     => 'gomec-web-tasarim',
        'title'    => 'Gömeç Web Tasarım',
        'meta_title' => 'Gömeç web tasarım — kamp, apart ve küçük işletme siteleri',
        'meta_description' => 'Gömeç ve Karaağaç bölgesindeki apart, kamp alanı ve küçük işletmeler için sade ve hızlı web siteleri.',
        'excerpt'  => 'Sakinlik arayan misafire hitap eden küçük ölçekli işletmeler için.',
        'content'  => <<<'HTML'
<h2>Küçük ilçenin avantajı</h2>
<p>Gömeç, Körfezin en küçük ilçelerinden biri ve tam da bu yüzden belirli bir
kitleyi çeker: kalabalıktan kaçan, sakin bir sahil arayan, uzun konaklama yapan
misafir. Karaağaç ve çevresindeki kamp alanları, apart daireler ve küçük
pansiyonlar bu talebi karşılar.</p>

<p>Küçük ölçek, internette dezavantaj değil avantajdır. Rekabetin düşük olduğu
bir yerde doğru kurulmuş bir sayfa, kısa sürede arama sonuçlarında öne geçebilir.
Büyük ilçelerde aylar süren bir yükseliş burada haftalarla ölçülür.</p>

<h2>Kamp ve karavan turizmi</h2>
<p>Son yıllarda karavan ve kamp turizmi bölgede belirgin şekilde arttı. Bu
kitlenin arama davranışı çok net: elektrik var mı, su bağlantısı nasıl, tuvalet
ve duş durumu, gece güvenliği, evcil hayvan kabul ediliyor mu, günlük ücret ne
kadar. Bu soruların cevabını sayfasında açıkça yazan bir işletme telefon
trafiğinin yarısından kurtulur ve doğru misafiri çeker.</p>

<p>Fotoğraf tarafında da beklenti farklı: parlak tanıtım kareleri değil, alanın
gerçek halini gösteren fotoğraflar güven verir.</p>

<h2>Apart ve uzun dönem konaklama</h2>
<p>Gömeç'teki apartların önemli bir bölümü haftalık ya da aylık kiralıyor. Bu
model, günlük konaklamadan farklı bir anlatım ister. Mutfağın donanımı, çamaşır
makinesi, internet hızı ve marketin uzaklığı burada odanın manzarasından daha
belirleyici.</p>

<p>Uzun dönem misafirin ikinci kez gelme ihtimali yüksek olduğu için, bu
işletmelerde müşteri listesini tutmak ve sezon öncesinde hatırlatma yapmak
doğrudan doluluk getiriyor.</p>

<h2>Yerel üretim ve küçük esnaf</h2>
<p>İlçede süt ürünleri, bal ve küçük ölçekli tarım üretimi var. Bu üreticiler
için büyük bir mağaza kurmak gereksiz; önemli olan ürünü tanıtan, üretim
süreçlerini anlatan ve iletişim bilgisi net bir sayfa. Sipariş telefonla ya da
basit bir formla alınabilir.</p>

<h2>Bütçenin doğru kullanımı</h2>
<p>Küçük işletmede bütçe sınırlıdır ve yanlış yere harcanmamalıdır. Bizim
önerimiz genellikle şudur: önce doğru kurulmuş küçük bir site ve düzgün bir
harita kaydı. Bu ikisi çalışmaya başladıktan sonra, gelen talebe göre
büyütmek her zaman mümkün.</p>

<p>Baştan büyük bir yatırım yapıp içeriği güncelleyemeyen bir işletme, ikinci
yılda daha kötü durumda oluyor.</p>

<h2>Yorum ve tavsiye zinciri</h2>
<p>Küçük işletmelerde yeni misafirin büyük bölümü tavsiyeyle gelir. Bu zinciri
güçlendirmenin en ucuz yolu, ayrılan misafire tek bir bağlantıyla yorum
bırakabileceği bir yol sunmaktır. Bu bağlantı sitede sabit dursa bile işini
görür.</p>

<p>Uydurma yorum yayınlamıyoruz ve yapısal veride puan işaretlemesi yapmıyoruz.
Gerçek yorumlar zaten yeterlidir; sahte olanlar yakalandığında tüm sayfaları
riske atar.</p>

<h2>Sezon dışında görünür kalmak</h2>
<p>Küçük işletmelerin çoğu ekim ile nisan arasında sitesine hiç dokunmuyor.
Oysa gelecek sezonun rezervasyonu tam da bu aylarda aranıyor. Kış aylarında
yapılacak birkaç güncelleme, yaz gelmeden sitenin arama sonuçlarında hazır
olmasını sağlıyor.</p>

<p>En basit haliyle: fiyatların güncellenmesi, yeni fotoğrafların eklenmesi ve
geçen sezonun sık sorulan sorularının sayfaya yazılması. Üç işlem, bir saat.</p>

<h2>Teknik yük işletmede kalmasın</h2>
<p>Küçük işletmede teknik işi takip edecek kimse yoktur. Bu yüzden teslim
ettiğimiz sistemlerde günlük yedek otomatik alınır, güncellemeler bizim
tarafımızda yürütülür ve işletmeciye yalnızca içerik kalır. Bakım anlaşmasının
kapsamını <a href="/web-sitesi-bakim">bakım sayfasında</a> anlatıyoruz.</p>

<h2>İlçenin kendi takvimi</h2>
<p>Gömeç'te sezon Akçay ya da Ayvalık'tan biraz geç başlar ve biraz erken biter.
Haziranın ilk yarısı genellikle sakin geçer, asıl yoğunluk temmuz ortasından
ağustos sonuna kadar sürer. Bu kısa pencerede doluluk kaybetmemek için
rezervasyonun mayıs başında açılmış olması gerekir.</p>

<p>Aynı şekilde eylül ayı, uzun konaklama arayan ve kalabalığı bekleyen bir
kitle için değerli. Bu iki dönemi ayrı ayrı anlatan sayfalar, tek bir genel
tanıtım sayfasından çok daha fazla iş getiriyor.</p>

<h2>Sahil ve köy arasındaki fark</h2>
<p>İlçede sahil şeridi ile iç kesimdeki köyler farklı misafir çekiyor. Sahilde
denize yakınlık belirleyiciyken köylerde sessizlik, bahçe ve yerel üretim öne
çıkıyor. İkisini aynı cümlelerle anlatan bir site her ikisini de yeterince
anlatamıyor.</p>

<p>Küçük bir işletme için bile iki ayrı sayfa yazmak, doğru misafiri doğru
sayfaya getirdiği için dönüşüm oranını yükseltiyor.</p>

<p>Fiyat aralıklarını <a href="/fiyatlar">fiyatlar sayfasında</a>,
komşu ilçe çalışmalarını <a href="/burhaniye-web-tasarim">Burhaniye</a> ve
<a href="/ayvalik-web-tasarim">Ayvalık</a> sayfalarında bulabilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'balikesir' => [
        'district' => 'Balıkesir',
        'slug'     => 'balikesir-web-tasarim',
        'title'    => 'Balıkesir Web Tasarım',
        'meta_title' => 'Balıkesir web tasarım — kurumsal siteler ve e-ticaret',
        'meta_description' => 'Balıkesir merkezdeki kurumsal firmalar, sanayi işletmeleri ve hizmet sağlayıcılar için web tasarım ve SEO.',
        'excerpt'  => 'İl merkezinde rekabetin yüksek olduğu yerde ayrışan siteler.',
        'content'  => <<<'HTML'
<h2>İl merkezinde rekabet başka</h2>
<p>Balıkesir merkez, Körfez ilçelerinden farklı bir oyun alanı. Burada aynı
hizmeti veren onlarca firma var, çoğunun sitesi mevcut ve bir bölümü düzenli
reklam veriyor. İlçelerde ise yarayan "doğru kurulmuş küçük site" yaklaşımı
merkezde tek başına yetmiyor.</p>

<p>Bu ortamda öne geçmenin yolu daha büyük bir site yapmak değil, daha dar bir
alanda en iyisi olmak. Genel "web tasarım" ya da "muhasebe" aramalarında
yarışmak yerine, firmanın gerçekten güçlü olduğu alt başlıklarda derinleşmek.</p>

<h2>Sanayi ve üretim firmaları</h2>
<p>Organize sanayi bölgesindeki üretim firmalarının müşterisi son tüketici değil,
başka bir firma. Bu durumda site bir katalog değil, teknik güven belgesidir.
Karşı taraf ürün kodunu, kapasiteyi, sertifikaları ve referans listesini arar.
Güzel bir anasayfadan çok, indirilebilir teknik dosyalar ve net bir ürün
listesi ise yarar.</p>

<p>İhracat yapan firmalarda İngilizce içerik zorunludur ve çeviriyle
geçiştirilemez. Karşı taraf teknik terimlerin doğru kullanıldığını görmek
ister.</p>

<h2>Üniversite ve genç nüfus</h2>
<p>Şehirdeki üniversite nüfusu, öğrenciye yönelik işletmeler için sürekli
yenilenen bir pazar yaratıyor. Bu kitle neredeyse tamamen telefondan ve sosyal
medya üzerinden arama yapıyor. Onlar için sitenin işi genellikle tek bir şey:
sosyal medyadan gelen kişiyi telefona ya da adrese hızlıca ulaştırmak.</p>

<p>Bu durumda uzun kurumsal metinler yerine tek ekranda toplanmış bilgi ve
büyük dokunmatik butonlar daha iyi çalışır.</p>

<h2>Hizmet sağlayıcılar ve serbest meslek</h2>
<p>Avukat, mali müşavir, diş hekimi ve benzeri meslek gruplarında karar güvene
dayanır ve arayan kişi genellikle karşılaştırma yapar. Burada belirleyici olan
şey, mesleki içeriğin varlığıdır: sık sorulan soruların gerçekten cevaplandığı,
süreci anlatan yazılar.</p>

<p>Bu tür içerik hem arama sonuçlarında uzun kuyruklu aramaları yakalar hem de
ilk temasta güven kurar. Meslek etiği gereği reklam kısıtları olan alanlarda
bilgilendirici içerik zaten en doğru yöntemdir.</p>

<h2>Bölge geneline hizmet verenler</h2>
<p>Merkezdeki birçok firma tüm ile hizmet veriyor. Bu durumda her ilçe için ayrı
ve gerçekten farklı sayfalar gerekir. Aynı metni ilçe adı değiştirerek
çoğaltmak, kısa vadede sayfa sayısını artırsa da Google tarafından
yakalandığında tüm sayfaları birden değersizleştirir.</p>

<p>Biz her bölge sayfasını o bölgenin kendi ekonomisinden yazarız; bu sayfanın
kendisi de buna örnektir. Yaklaşımın ayrıntısını
<a href="/seo-hizmeti">SEO hizmeti sayfamızda</a> anlatıyoruz.</p>

<h2>Reklamla organik trafiğin dengesi</h2>
<p>Merkezde rekabet yüksek olduğu için birçok firma doğrudan reklama yöneliyor.
Reklam hızlı sonuç verir ama musluk kapandığında trafik de biter. Organik
görünürlük yavaş kurulur, buna karşılık kalıcıdır.</p>

<p>Bizim önerdiğimiz denge şudur: reklamı kısa vadeli talep için, içeriği uzun
vadeli görünürlük için kullanmak. Reklamdan gelen aramaların hangi kelimelerde
dönüşüm getirdiğini ölçüp, aynı kelimeler için kalıcı içerik yazmak reklam
bütçesini zamanla düşürür.</p>

<h2>Kurumsal sitede ölçüm ve raporlama</h2>
<p>Merkezdeki firmalarda genellikle karar veren kişi ile siteyi kullanan kişi
farklıdır. Bu yüzden raporlamanın anlaşılır olması gerekir: kaç kişi geldi
değil, hangi sayfa kaç teklif talebi getirdi.</p>

<p>Panelde form kayıtları kaynak sayfasıyla birlikte tutuluyor. Üç ay sonra
hangi hizmet sayfasının gerçekten iş getirdiği net bir tabloya dönüşüyor ve
içerik yatırımı buna göre yönlendiriliyor.</p>

<h2>Sitenin devri ve bağımsızlık</h2>
<p>Kurumsal müşterilerimizin sık sorduğu bir soru şudur: "Sizinle çalışmayı
bırakırsak ne olur?" Cevap net: sitenin tüm dosyaları ve veritabanı
müşterinindir. Kapalı bir sistem, kiralık bir panel ya da dışarı çıkılamayan
bir altyapı kurmuyoruz. İsteyen müşteri her şeyi alıp başka bir ekiple devam
edebilir.</p>

<p>Körfez ilçelerindeki çalışmalarımız için
<a href="/edremit-web-tasarim">Edremit</a> ve
<a href="/ayvalik-web-tasarim">Ayvalık</a> sayfalarına bakabilirsiniz.</p>
HTML,
    ],

];
