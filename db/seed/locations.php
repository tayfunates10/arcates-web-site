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
 *   - o ilceye ait en az bir referans ornegi ile eslesir,
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
        'title'    => 'Edremit Web Tasarim',
        'meta_title' => 'Edremit web tasarim — yerel isletmeler icin | Arcates',
        'meta_description' => 'Edremit merkezli isletmeler icin web tasarim, e-ticaret ve yerel SEO. Yerinde gorusme, olculebilir sonuc.',
        'excerpt'  => 'Korfezin ticaret merkezinde, yerinde gorusen bir ekiple calisin.',
        'content'  => <<<'HTML'
<h2>Edremit ticaretinin kendine ozgu ritmi</h2>
<p>Edremit, Korfezin idari ve ticari merkezi. Cevre ilcelerden gelen alisveris
trafigi, hastane ve resmi kurumlarin yarattigi gunluk hareket, Kaz Daglari'na
cikan yolun buradan gecmesi; hepsi ilcenin is hayatini komsu ilcelerden ayirir.
Akcay ya da Altinoluk sezona bagli yasarken Edremit on iki ay ayni tempoda
calisir.</p>

<p>Bunun web tarafindaki karsiligi su: Edremitli bir isletmenin musterisi
cogunlukla yazlikci degil, bolgede yasayan biri. Arama aliskanliklari da farkli.
"Edremit oto servis", "Edremit dis klinigi", "Edremit mobilyaci" gibi aramalar
akşam saatlerinde ve hafta ici yogunlasir. Yazlik bolgelerde ise trafik cuma
gunu baslar, pazar aksami biter.</p>

<h2>Hangi isletmeler ne ariyor</h2>
<p>Ilcede en cok calistigimiz gruplar saglik kuruluslari, oto sektoru, mobilya ve
yapi malzemesi satan isletmeler, muhasebe ve hukuk burolari. Bunlarin ortak
sorunu ayni: harita sonuclarinda cikiyorlar ama sitesi olmadigi icin musteri
karsilastirma yaptigi anda rakibe geciyor.</p>

<p>Ikinci grup, cevre ilcelere de hizmet veren isletmeler. Bir yapi market
Edremit'te dukkani olsa da Havran ve Burhaniye'ye teslimat yapiyor. Bu durumda
tek bir "iletisim" sayfasi yetmiyor; hizmet verilen her bolge icin ayri ve
gercekten farkli sayfalar gerekiyor.</p>

<h2>Kaz Daglari trafigini kacirmayin</h2>
<p>Edremit uzerinden gecen dag turizmi, ilcedeki konaklama ve yeme icme
isletmeleri icin ciddi bir potansiyel. Ancak bu trafik cogunlukla telefonundan
arama yapan, yolda olan bir kitle. Sitenin mobilde iki saniyenin altinda
acilmasi, telefon numarasinin ilk ekranda gorunmesi ve yol tarifi baglantisinin
tek dokunusla calismasi burada dogrudan ciro demek.</p>

<h2>Bizim yaptigimiz is</h2>
<ul>
  <li>Isletmenin hangi aramalarda gorunmesi gerektiginin tespiti</li>
  <li>Mobil oncelikli, hizli acilan kurumsal site</li>
  <li>Google Isletme Profili ile birebir ayni iletisim bilgileri</li>
  <li>Cevre ilcelere hizmet veriliyorsa her bolge icin ayri sayfa</li>
  <li>Hangi sayfanin telefon getirdiginin olculmesi</li>
</ul>

<h2>Yerinde gorusme</h2>
<p>Edremit merkezdeki isletmelerle gorusmeyi kendi mekanlarinda yapiyoruz. Bir
mobilyacinin deposunu gormeden urun fotograflarinin nasil cekilecegine,
bir klinigin randevu akisini dinlemeden formun nasil kurulacagina karar
verilemez. Bu gorusme ucretsizdir ve bir saati gecmez.</p>

<p>Sonrasinda yazili fiyat ve takvim gonderiyoruz. Kurumsal bir site ucuncu
haftanin sonunda yayinda oluyor. Detaylar icin
<a href="/fiyatlar">fiyatlar sayfamiza</a>,
yaptigimiz islere <a href="/referanslar">referanslar sayfasindan</a>
bakabilirsiniz.</p>

<h2>Rakip analizinden cikan tablo</h2>
<p>Ilcedeki isletmelerin sitelerine bakildiginda tekrar eden uc sorun goze
carpiyor. Birincisi, sitelerin buyuk bolumu masaustu icin tasarlanmis ve
telefonda okunmuyor; oysa gelen trafigin dortte ucu mobil. Ikincisi, adres ve
telefon bilgisi harita kaydiyla ayni yazilmamis, bu da arama motorunun
isletmeyi dogrulamasini zorlastiriyor. Ucuncusu, sayfalarin cogunda hangi
hizmetin verildigi genel cumlelerle geciliyor; arayan kisi arayacagi hizmeti
sayfada goremeyince cikip gidiyor.</p>

<p>Bu uc sorunun ucu de teknik degil, karar sorunudur ve cozumu pahali degildir.
Bir isletmenin verdigi her hizmet icin ayri bir baslik ve birkac paragraf
yazmasi, ilcedeki rakiplerinin cogunun onune gecmesine yetiyor.</p>

<h2>Olcum olmadan iyilestirme olmaz</h2>
<p>Yayina aldigimiz her sitede hangi sayfanin telefon ya da form getirdigini
kaydediyoruz. Uc ay sonra tablo genellikle sasirtici oluyor: isletmenin en cok
onem verdigi anasayfa degil, kimsenin dikkate almadigi bir hizmet sayfasi is
getiriyor. Bu bilgi olmadan icerigin nereye yatirilacagina karar vermek tahminden
ibaret kalir.</p>

<p>Panelde bu veriyi isletmenin kendisi de goruyor. Kaynak sayfa, ziyaretcinin
siteye nereden geldigi ve hangi aramanin sonuca dondugu ayni ekranda duruyor.</p>

<h2>Panelden yonetim: kim ne degistirebilir</h2>
<p>Teslim ettigimiz her sitede metinlerin tamami panelden duzenlenebilir. Bu
sadece bir kolaylik degil, sitenin canli kalmasinin sartidir. Fiyatini
degistirmek icin bize yazmasi gereken bir isletme, ucuncu ayda fiyat
guncellemekten vazgecer ve site eskimeye baslar.</p>

<p>Panelde iki rol vardir. Yonetici her seyi gorur; editor yalnizca icerik
bolumlerine erisir. Boylece ofisteki bir calisan blog yazisi eklerken ayarlara
ya da kullanici listesine dokunamaz. Her degisiklik kim tarafindan, ne zaman
yapildigiyla birlikte islem gunlugune yazilir.</p>

<p>Cevredeki diger ilcelerde de calisiyoruz:
<a href="/akcay-web-tasarim">Akcay</a>,
<a href="/havran-web-tasarim">Havran</a> ve
<a href="/burhaniye-web-tasarim">Burhaniye</a> sayfalarimiza goz atabilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'akcay' => [
        'district' => 'Akcay',
        'slug'     => 'akcay-web-tasarim',
        'title'    => 'Akcay Web Tasarim',
        'meta_title' => 'Akcay web tasarim — pansiyon ve restoran siteleri',
        'meta_description' => 'Akcay pansiyon, otel ve restoranlari icin rezervasyon odakli web tasarim. Sezon disi doluluk icin yerel SEO.',
        'excerpt'  => 'Sezonluk isletmeler icin rezervasyon ve doluluk odakli siteler.',
        'content'  => <<<'HTML'
<h2>Dokuz aylik hazirlik, uc aylik sezon</h2>
<p>Akcay'da is takvimi nettir: haziran basinda baslar, eylul ortasinda biter.
Geri kalan dokuz ay hazirlik ve bekleyistir. Bu ritim, internet sitesinden
beklentiyi de degistirir. Yil boyu ayni tempoda calisan bir isletmenin sitesiyle,
gelirinin tamamini uc ayda yapan bir pansiyonun sitesi ayni sekilde
kurgulanamaz.</p>

<p>Sezonluk isletmede sitenin isi, aralik ile mayis arasinda yapilir. Misafir
tatilini subatta planlar, martta karar verir, nisanda parayi yatirir. Haziranda
sitenizi acan kisi cogu zaman yer arayan degil, yol tarifi arayan kisidir.</p>

<h2>Rezervasyon sitelerinin disina cikmak</h2>
<p>Akcay'daki pansiyonlarin buyuk bolumu doluluğunun tamamini rezervasyon
platformlarindan sagliyor. Komisyon oraninin yuzde on bes ile yirmi bes arasinda
oldugu dusunulurse, otuz odali bir tesis icin sezonluk kayip ciddi bir rakama
ulasiyor.</p>

<p>Amac platformlari birakmak degil. Amac, oradan gelen misafiri bir sonraki
sene dogrudan size getirmek. Bunun icin uc sey gerekir: misafirin adini ve
e-postasini alan bir kanal, dogrudan rezervasyonda anlamli bir avantaj ve
kendi sitenizde calisan bir musaitlik takvimi.</p>

<h2>Sahil seridi ve yeme icme</h2>
<p>Akcay sahilindeki restoran ve kafeler icin durum farkli. Burada rezervasyondan
cok gunluk gorunurluk onemli. Aksam yemek yeri arayan bir aile telefonundan
harita uygulamasini acar, ilk uc sonuca bakar ve fotograflara goz atar. Menunun
guncel olmasi, fotograflarin gercek olmasi ve calisma saatlerinin dogru
yazilmasi bu asamada karar verdirir.</p>

<h2>Almanca ve Ingilizce icerik</h2>
<p>Bolgede yabanci misafir orani her yil artiyor. Otomatik ceviri eklentileri
arama motorlari icin ayri sayfa uretmedigi icin Almanca arama yapan biri sizi
bulamaz. Gercek coklu dil kurulumu, her dil icin ayri adres ve ayri metin
demektir; bunu <a href="/coklu-dil-web-sitesi">coklu dil sayfamizda</a>
anlatiyoruz.</p>

<h2>Ne zaman baslamali</h2>
<p>Sezonluk bir isletme icin dogru zaman kasim ile ocak arasidir. Sitenin subatta
yayinda olmasi, arama motorlarinin sayfayi taniyip siralamaya alacagi sureyi
kazandirir. Mayista baslanan bir is o sezonu yakalayamaz.</p>

<h2>Fotograf, metinden once konusur</h2>
<p>Konaklama aramasinda karar cogunlukla fotografla verilir. Sahilde cekilmis
genel bir manzara karesi degil, misafirin gercekten kalacagi odanin, banyonun ve
kahvalti masasinin fotografi ise yarar. Gorseli guzellestirmek yerine dogru
gostermek, sezon sonundaki yorum puanini korumanin en ucuz yolu.</p>

<p>Cektigimiz ya da isletmeden aldigimiz her fotograf siteye yuklenirken kucuk,
orta ve buyuk boyutlarda ve WebP bicimiyle yeniden uretiliyor. Boylece telefonla
bakan misafir on iki fotografi saniyeler icinde goruyor, sayfa agirligi
sismiyor.</p>

<h2>Sezon disi doluluk</h2>
<p>Akcay'da asil kazanc nisan-mayis ve eylul-ekim aylarinda gizli. Bu donemde
gelen kitle aile degil; yuruyus yapan, sakinlik arayan, calisirken tatil yapan
bir grup. Bu kitleye ulasmak icin sitenin yaz anlatisindan farkli bir dil
kurmasi gerekiyor: sicaklik ortalamalari, acik kalan isletmeler, calisma icin
internet hizi ve uzun konaklama fiyatlari.</p>

<p>Bu iceriklerin yaz sezonunda yazilmasi, sonbaharda arama yapan kisinin
karsisina cikmasi icin gereken sureyi kazandiriyor.</p>

<h2>Rezervasyon akisinda misafiri kaybettiren noktalar</h2>
<p>Dogrudan rezervasyon denemelerinin cogu uc noktada kesilir. Birincisi, fiyat
gorunmeden once bilgi istenmesi; misafir fiyati gormeden form doldurmaz.
Ikincisi, musaitligin belirsiz olmasi; "sorunuz" yaziyorsa misafir sormaz,
platforma doner. Ucuncusu, mobilde uzun form; alti alandan fazlasi terk
oranini gozle gorulur artirir.</p>

<p>Kurdugumuz akista once tarih ve kisi sayisi sorulur, hemen fiyat ve musaitlik
gosterilir, ancak ondan sonra iletisim bilgisi istenir. Bu sira degisikligi tek
basina donusum oranini belirgin sekilde yukseltiyor.</p>

<h2>Yorumlarla calismak</h2>
<p>Tesis puani sezon boyunca en degerli varlik. Sitede uydurma yorum
yayinlamiyoruz ve yapisal veride puan isaretlemesi yapmiyoruz; bu hem yanlis
hem de yakalandiginda tum sayfalari riske atiyor. Bunun yerine gercek
misafirlerden gelen yorumlari duzenli toplayacak bir akis kuruyoruz.</p>

<p>Konaklama isletmelerine ozel kurdugumuz altyapiyi
<a href="/rezervasyon-sistemi">rezervasyon sistemi sayfasinda</a>,
tesis sitelerine ozel calismamizi
<a href="/otel-pansiyon-web-sitesi">otel ve pansiyon sayfasinda</a>
bulabilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'altinoluk' => [
        'district' => 'Altinoluk',
        'slug'     => 'altinoluk-web-tasarim',
        'title'    => 'Altinoluk Web Tasarim',
        'meta_title' => 'Altinoluk web tasarim — emlak ve site yonetimi siteleri',
        'meta_description' => 'Altinoluk emlak ofisleri, site yonetimleri ve konaklama isletmeleri icin web tasarim ve yerel SEO.',
        'excerpt'  => 'Emlak, site yonetimi ve dag turizmi isletmeleri icin siteler.',
        'content'  => <<<'HTML'
<h2>Yazlik konut ekonomisi</h2>
<p>Altinoluk'u Korfezin diger ilcelerinden ayiran sey, ekonomisinin buyuk
bolumunun yazlik konut uzerine kurulu olmasi. Oksijen orani ve Kazdaglari
eteklerindeki konumu nedeniyle burasi yillardir emeklilik ve ikinci konut
tercihi. Bu da nufusun kis aylarinda dusuk, yaz aylarinda katlanarak arttigi
bir yapi olusturuyor.</p>

<p>Boyle bir yerde en yogun calisan iki sektor emlak ve site yonetimi hizmetleri.
Ikisinin de internet ihtiyaci birbirinden cok farkli.</p>

<h2>Emlak ofisleri icin</h2>
<p>Emlak ilanlarinin buyuk bolumu portallarda yayinlaniyor; bu degismeyecek.
Ancak alici bir portalda ilani gordukten sonra ofisin adini aratir. O anda
karsisina cikan sayfa, o ofisin bolgeyi ne kadar tanidigini gostermelidir.</p>

<p>Bizim onerdigimiz yapi su: portal ilanlarini birakip her mahalle icin bolge
rehberi yazmak. Denize uzaklik, site aidatlari, kis aylarinda acik kalan
isletmeler, saglik ocagi ve market mesafeleri. Alicinin gercekten merak ettigi
bunlardir ve bu bilgiyi veren ofis, ilan listeleyen ofisten one gecer.</p>

<h2>Site yonetimleri icin</h2>
<p>Yuzlerce daireli sitelerde yonetimin en buyuk yuku iletisim. Aidat
duyurusundan havuz bakim takvimine, genel kurul cagrisindan ariza bildirimine
kadar her sey telefonla yurutuluyor. Basit bir duyuru sayfasi ve form bu yuku
gozle gorulur sekilde azaltiyor.</p>

<h2>Dag turizmi ve yuruyus</h2>
<p>Kazdaglari'na yonelik doga yuruyusu, kamp ve butik konaklama isletmeleri son
yillarda arttı. Bu isletmelerin musterisi genellikle sehirden gelen, planli ve
internetten arastiran bir kitle. Rota anlatimi, mevsime gore tavsiye ve gercek
fotograf bu grupta dogrudan rezervasyona donusuyor.</p>

<h2>Bizim yaklasimimiz</h2>
<ul>
  <li>Sektore gore farkli site kurgusu; emlak ile konaklama ayni sablonla olmaz</li>
  <li>Mahalle ve bolge bazli icerik; kopyala yapistir degil, gercek bilgi</li>
  <li>Mobil hiz; alicinin cogu telefonundan bakiyor</li>
  <li>Yabanci alici hedefleniyorsa Ingilizce ve Almanca yayin</li>
</ul>

<h2>Alicinin gercekten sordugu sorular</h2>
<p>Yazlik alacak kisinin ilanlarda bulamadigi bilgiler bellidir: kis aylarinda
sitede kac daire dolu kaliyor, market ve eczane yuruyus mesafesinde mi, aidat
neyi kapsiyor, su kesintisi oluyor mu, en yakin saglik kurulusu ne kadar uzakta.
Bu sorularin cevabini veren bir ofis, ilan listeleyen on ofisin onune gecer.</p>

<p>Bu bilgiyi yazmak zaman ister ama bir kez yazilir ve yillarca calisir. Ustelik
bu tur ayrintili sayfalar, alicinin aramada kullandigi uzun cumlelerle birebir
ortustugu icin rekabetin en dusuk oldugu yerden trafik getirir.</p>

<h2>Yabanci aliciya satis</h2>
<p>Bolgede yabanci alici ilgisi artiyor. Ancak ilan metnini otomatik ceviriyle
Ingilizceye cevirmek yeterli degil; alicinin sordugu sorular farkli. Tapu
sureci, oturma izni, vergi ve site aidatinin nasil odendigi gibi basliklar
Turkiyeli alicinin zaten bildigi, yabanci alicinin ise hic bilmedigi
konular.</p>

<p>Bu yuzden coklu dil kurulumunu ceviri olarak degil, ayri icerik olarak
yapiyoruz. Her dil kendi adresinde yayinlanir ve o dile ait metin, o kitlenin
sorularina gore yazilir.</p>

<h2>Site yonetimi icin duyuru ve ariza akisi</h2>
<p>Buyuk sitelerde yonetici en cok zamani tekrarlayan sorulara harcar: aidat ne
zaman yatiyor, havuz ne zaman aciliyor, genel kurul ne zaman. Bu sorularin
cevabini tasiyan basit bir duyuru sayfasi telefon trafigini belirgin sekilde
azaltir.</p>

<p>Ariza bildirimi icin kurdugumuz form, bildirimi yapan daireyi, konuyu ve
fotografi tek ekranda topluyor ve yoneticiye e-posta olarak gonderiyor. Kayitlar
panelde durum etiketiyle takip ediliyor; hangi arizanin ne zaman kapandigi genel
kurulda tartisma konusu olmaktan cikiyor.</p>

<h2>Sezon disi gorunurluk</h2>
<p>Altinoluk'ta arama trafigi subat ile mayis arasinda zirve yapar; alici karari
bu aylarda verir. Sitenin bu donemde hazir olmasi, ocak ayindan once icerigin
yayinlanmis olmasini gerektirir. Nisanda yayina alinan bir emlak sitesi o yilin
sezonunu buyuk olcude kacirir.</p>

<p>Bolgedeki calismalarimizi <a href="/referanslar">referanslar sayfasinda</a>
gorebilir, emlak sektorune ozel yaklasimimizi
<a href="/emlak-web-sitesi">emlak web sitesi sayfasinda</a> okuyabilirsiniz.
Komsu ilce icin <a href="/akcay-web-tasarim">Akcay sayfamiza</a> da
bakabilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'burhaniye' => [
        'district' => 'Burhaniye',
        'slug'     => 'burhaniye-web-tasarim',
        'title'    => 'Burhaniye Web Tasarim',
        'meta_title' => 'Burhaniye web tasarim — uretici ve kooperatif siteleri',
        'meta_description' => 'Burhaniye zeytin ureticileri, kooperatifler ve Oren isletmeleri icin e-ticaret ve web tasarim.',
        'excerpt'  => 'Zeytin uretimi ve Oren turizmi; iki farkli is, iki farkli site.',
        'content'  => <<<'HTML'
<h2>Iki ayri ekonomi, tek ilce</h2>
<p>Burhaniye'de birbirinden bagimsiz iki ekonomi yan yana calisir. Biri ic
kesimdeki zeytin uretimi ve tarim; digeri Oren sahilindeki yazlik ve konaklama
hareketi. Ayni ilcede olmalarina ragmen musteri kitleleri, sezon takvimleri ve
internet ihtiyaclari ortusmez.</p>

<h2>Uretici ve kooperatifler</h2>
<p>Zeytin ve zeytinyagi ureticisinin en buyuk kaybi araciya kalan paydir. Yagini
tenekeyle veren bir uretici ile kendi etiketiyle satan uretici arasindaki fark
kat kat. Ancak kendi markasiyla satmak, kutu tasarimindan kargo anlasmasina,
etiket mevzuatindan musteri iletisimine uzanan yeni bir is demek.</p>

<p>Web tarafinda bizim isimiz sunlar: hasat donemine gore on siparis alan bir
magaza, litre ve kilogram varyantlariyla stok takibi, bolgeye gore kargo ucreti
ve tekrar siparisi kolaylastiran bir musteri hesabi yapisi. Kooperatifler icin
ayrica uye ureticilerin gorunurlugu ve ortak marka anlatisi.</p>

<p>Ayrintilari <a href="/zeytinyagi-e-ticaret-sitesi">zeytinyagi e-ticaret
sayfamizda</a> anlatiyoruz.</p>

<h2>Oren sahili</h2>
<p>Oren, Korfezin daha sakin sahil noktalarindan biri. Buradaki pansiyon ve
apart isletmelerinin musterisi, Akcay'in kalabaligindan kacan ve sessizlik
arayan bir kitle. Bu farki sitede anlatmak gerekiyor; "denize sifir" cumlesi
yeterli degil, "aksam sekizde sahil bosaliyor" bilgisi karar verdiriyor.</p>

<p>Oren isletmelerinin cogu tek kisiyle yonetiliyor. Bu yuzden kurdugumuz
sistemlerde yonetim panelinin sadeligi tasarimdan daha onemli: fiyat
degistirmek uc tiklama surmemeli.</p>

<h2>Uretici pazari ve yerel esnaf</h2>
<p>Ilcedeki uretici pazari ve carsi esnafi icin durum daha basit. Burada gereken
buyuk bir site degil; dogru bilgilerle kurulmus bir sayfa ve harita kaydi.
Calisma saati, guncel telefon, gercek fotograf ve birkac musteri sorusunun
cevabi cogu esnaf icin yeterli.</p>

<h2>Nasil basliyoruz</h2>
<p>Uretici isletmelerde gorusmeyi hasat disi donemde, isletmenin kendi tesisinde
yapiyoruz. Sikma tesisini gormeden urun anlatisinin nasil kurulacagina karar
vermek zor. Gorusme ucretsiz, bir saatlik.</p>

<h2>Etiketten kargoya: dogrudan satisin adimlari</h2>
<p>Kendi markasiyla satmaya baslayan bir ureticinin karsisina cikan basliklar
sirasiyla sunlar: urun adi ve etiket bilgisi, litre bazli fiyatlandirma,
kirilabilir urun icin uygun koli, kargo firmasiyla anlasma ve iade sureci. Bu
adimlarin hicbiri tek basina zor degil, ancak birlikte planlanmadiginda ilk
siparislerde sorun cikariyor.</p>

<p>Kurdugumuz magazalarda bu adimlarin hepsi bastan tanimli geliyor. Uretici
yalnizca urununu, fiyatini ve stogunu giriyor; kargo ucreti bolgeye gore
otomatik hesaplaniyor, siparis onayi musteriye e-posta ile gidiyor.</p>

<h2>Hasat takvimine gore satis</h2>
<p>Zeytinyagi satisi yil boyunca ayni degildir. Kasim-aralik doneminde yeni
hasat talebi patlar, ilkbaharda duser, yaz aylarinda hediye amacli siparis
artar. Magazanin bu ritmi tanimasi gerekir: on siparis donemi, stok bittiginde
bekleme listesi ve yeni hasat duyurusu icin e-posta listesi.</p>

<p>Bir ureticinin en degerli varligi, gecen yil urununu begenip bu yil yine
arayacak musteri listesidir. Pazaryerinde satan uretici bu listeye sahip
olamaz.</p>

<h2>Ambalaj ve anlati birlikte calisir</h2>
<p>Zeytinyagi satisinda urunun kendisi kadar anlatisi da satar. Agacin yasi,
hasadin elle mi makineyle mi yapildigi, sikim tesisine kac saatte ulasildigi ve
asitlik degeri; bunlar meraklı musteri icin fiyat farkini mesrulastiran
bilgilerdir. Ayni bilgiler arama motorlarinda da uzun kuyruklu aramalarla
ortusur.</p>

<p>Bu yuzden urun sayfalarini katalog gibi degil, uretim anlatisi gibi
kuruyoruz. Her urunun altinda hasat yili, bolgesi ve analiz degerleri duruyor.
Ayni sayfa hem musteriyi ikna ediyor hem arama sonuclarinda ayrisiyor.</p>

<h2>Oren'de sezon disi kullanim</h2>
<p>Oren'deki apart ve pansiyonlarin bir bolumu kis aylarinda uzun donem kiraya
veriliyor. Bu kitle tatilci degil; bolgede gecici olarak calisan ya da kisi
sakin gecirmek isteyen bir grup. Sitede bu secenegin ayri bir sayfayla
anlatilmasi, bos gecen aylarin bir bolumunu doldurabiliyor.</p>

<p><a href="/e-ticaret-sitesi">E-ticaret hizmetimize</a> ve
<a href="/fiyatlar">fiyat araliklarina</a> bakabilir,
komsu ilceler icin <a href="/havran-web-tasarim">Havran</a> ve
<a href="/gomec-web-tasarim">Gomec</a> sayfalarimizi inceleyebilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'havran' => [
        'district' => 'Havran',
        'slug'     => 'havran-web-tasarim',
        'title'    => 'Havran Web Tasarim',
        'meta_title' => 'Havran web tasarim — tarim ve esnaf siteleri',
        'meta_description' => 'Havran tarim isletmeleri, uretici ve carsi esnafi icin sade, hizli ve yonetimi kolay web siteleri.',
        'excerpt'  => 'Tarim ve esnaf agirlikli bir ilcede sade ve ise yarayan siteler.',
        'content'  => <<<'HTML'
<h2>Sahilden ic kesime</h2>
<p>Havran, Korfezin sahil ilcelerinin aksine ekonomisini turizmden degil tarimdan
alan bir ilce. Zeytin, seftali, ceviz ve buyukbas hayvancilik burada gelirin
omurgasini olusturuyor. Pazar gunu kurulan ilce pazari, cevre koylerden gelen
ureticinin dogrudan satis yaptigi ana kanal.</p>

<p>Boyle bir yapida internetten beklenti de farkli. Havranli bir isletmenin
ihtiyaci gorkemli bir tanitim sitesi degil; dogru bulunmak, telefonun calmasi ve
urunun sorulmasi.</p>

<h2>Kucuk isletme icin dogru olcek</h2>
<p>Ilcede en sik karsilastigimiz durum su: isletme sahibi yillar once bir siteyi
yaptirmis, sifresini kaybetmis, icerik guncellenmemis ve site artik telefon
numarasi bile yanlis gosteriyor. Bu durumda yeni ve buyuk bir site yapmak
degil, dogru bilgiyi tasiyan sade bir sayfa kurmak daha faydali.</p>

<p>Uc-bes sayfalik, telefonu ilk ekranda duran, harita kaydiyla birebir ayni
adresi gosteren ve isletmecinin kendi telefonundan guncelleyebildigi bir yapi
cogu esnaf icin fazlasiyla yeterli.</p>

<h2>Uretici icin dogrudan satis</h2>
<p>Havran seftalisi ve cevizi bolge disinda taninan urunler. Ancak uretici
cogunlukla komisyoncuya teslim ediyor ve son fiyati gormuyor. Sezonluk kucuk
partilerle dogrudan satis, bir e-ticaret altyapisi gerektirmeyecek kadar basit
kurulabilir: sezon acildiginda alinan on siparis, kapali gruplarla duyuru ve
kargo anlasmasi.</p>

<p>Isin buyudugu noktada gercek bir magazaya gecmek gerekir; bunu
<a href="/e-ticaret-sitesi">e-ticaret sayfamizda</a> anlatiyoruz.</p>

<h2>Tarim makineleri ve hizmet saglayicilar</h2>
<p>Ilcede traktor yedek parcasi, sulama sistemi, gubre ve tohum satan isletmeler
icin arama trafigi mevsimseldir ve cok nettir. Sulama sezonu yaklastiginda
"damla sulama" aramalari, hasat oncesinde ise makine kiralama aramalari
yukselir. Sitenin bu doneme hazir olmasi, urun sayfalarinin sezondan iki ay once
yazilmis olmasini gerektirir.</p>

<h2>Sadelik bir tercih degil, gereklilik</h2>
<p>Ilcedeki internet baglantisi ve kullanilan telefonlar dikkate alindiginda,
agir bir sitenin bedelini dogrudan isletme oduyor. Kurdugumuz sayfalar gorsel
agirligi dusuk, yazi tipi sayisi sinirli ve tek bir stil dosyasiyla calisir.
Amac guzel gorunmek degil, uc saniyeden once acilmak.</p>

<p>Yonetim tarafinda da ayni sadelik gecerli: fiyat degistirmek, calisma saati
guncellemek ya da yeni bir fotograf eklemek panelde tek ekranda yapilir.</p>

<h2>Nasil basliyoruz</h2>
<p>Gorusmeyi isletmenin kendi yerinde yapiyoruz. Bir tarim bayisinin deposunu
gormeden urun listesini nasil duzenleyecegimize karar veremeyiz. Gorusme
ucretsiz, bir saatlik ve sonrasinda yazili fiyat gonderiyoruz.</p>

<h2>Harita kaydi coguna yeter</h2>
<p>Ilcedeki kucuk isletmelerin buyuk bolumu icin gelen trafigin cogunlugu arama
motorunun kendisinden degil harita sonuclarindan geliyor. Bu yuzden ilk is
Google Isletme Profili kaydini duzgun kurmak: kategori dogru secilmis, calisma
saatleri guncel, telefon numarasi sitedekiyle birebir ayni ve icerideki
fotograflar gercek olmali.</p>

<p>Bu kaydin siteyle celismesi durumunda arama motoru hangi bilginin dogru
oldugunu anlayamiyor ve isletmeyi geriye atiyor. En sik gordugumuz hata,
sitede eski telefon numarasinin unutulmus olmasi. Kucuk gorunen bu ayrinti
siralamada olculebilir bir kayba yol aciyor.</p>

<h2>Mevsime gore icerik takvimi</h2>
<p>Tarim agirlikli bir ilcede icerik takvimi de tarim takvimine baglidir.
Budama donemi, gubreleme, ilaclama ve hasat; her biri oncesinde arama hacmi
yukselen basliklar. Bu iceriklerin sezondan iki ay once yayinlanmasi, arama
motorunun sayfayi taniyip siralamaya almasi icin gereken sureyi kazandirir.</p>

<p>Yil boyunca duzenli birkac yazi, ilcedeki cogu rakibin hic yapmadigi bir sey
oldugu icin uzun vadede belirgin fark yaratiyor. Bu yazilarin uzun olmasi da
gerekmiyor; dogru soruyu net cevaplamasi yeterli.</p>

<h2>Teslim sonrasi</h2>
<p>Site yayina alindiktan sonra bir ay boyunca ucretsiz destek veriyoruz. Bu
surede isletmeci panelde metin ve fotograf degistirmeyi ogreniyor. Amac
bagimlilik kurmak degil; isletmenin kendi sitesini kendi yonetebilmesi.</p>

<p>Komsu ilcelerdeki calismalarimiz icin
<a href="/edremit-web-tasarim">Edremit</a> ve
<a href="/burhaniye-web-tasarim">Burhaniye</a> sayfalarimiza,
fiyat araliklari icin <a href="/fiyatlar">fiyatlar sayfasina</a>
bakabilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'ayvalik' => [
        'district' => 'Ayvalik',
        'slug'     => 'ayvalik-web-tasarim',
        'title'    => 'Ayvalik Web Tasarim',
        'meta_title' => 'Ayvalik web tasarim — butik otel ve gastronomi siteleri',
        'meta_description' => 'Ayvalik ve Cunda butik otelleri, restoranlari ve zeytinyagi ureticileri icin coklu dil web tasarim.',
        'excerpt'  => 'Butik konaklama, gastronomi ve zeytinyagi; uc farkli kitle, uc farkli site.',
        'content'  => <<<'HTML'
<h2>Korfezin en cok ziyaret edilen ilcesi</h2>
<p>Ayvalik, bolgede turizm hacmi en yuksek ilce. Cunda adasindaki tas evler,
tarihi carsi dokusu, yat limani ve Yunan adalarina gunubirlik feribot seferleri
ilceyi yalnizca yazlikci degil, sehirli ve yabanci ziyaretci ceken bir noktaya
donusturuyor. Bu, isletmeler icin hem daha buyuk bir pazar hem cok daha sert bir
rekabet demek.</p>

<h2>Butik konaklamada ayrismak</h2>
<p>Ilcede yuzlerce butik otel ve pansiyon var. Hepsi ayni fotograflari cekiyor,
ayni cumleleri yaziyor: "tarihi tas ev", "denize yurume mesafesi", "sicak bir
atmosfer". Bu cumleler artik hicbir seyi ayirmiyor.</p>

<p>Ayrismak icin gereken, o isletmenin gercekten farkli olan seyini yazmak.
Kahvaltida hangi yerel uretici kullaniliyor, hangi odanin penceresi hangi
saatte gunes aliyor, gece sessizlik kacta basliyor. Bu ayrintilari yazan bir
site, ayni fiyattaki on rakibin onune gecer.</p>

<h2>Gastronomi ve carsi isletmeleri</h2>
<p>Ayvalik'ta yeme icme kararlari cogunlukla yurume sirasinda, telefondan
veriliyor. Bu yuzden restoran icin en kritik iki sey menunun guncel olmasi ve
haritada dogru gorunmek. Sezonda degisen menuyu panelden bes dakikada
guncelleyebilen bir isletme, kagit menuyu fotograflayip paylasandan onde
baslar.</p>

<p>Ilcedeki kahvalti kulturunun ve yerel lezzetlerin arama hacmi ciddi.
Bu aramalarin karsiligini veren icerik, sezon disinda bile duzenli trafik
getiriyor.</p>

<h2>Zeytinyagi ve sabun uretimi</h2>
<p>Ilcenin ikinci ekonomisi zeytinyagi ve zeytinyagi sabunu. Bu urunlerin
musterisi cogunlukla bolge disinda; dolayisiyla is dogrudan e-ticaret isi.
Hediyelik paketleme, yurt disi kargo ve coklu dil urun anlatisi burada
belirleyici oluyor.</p>

<p>Yurt disina satis yapan bir uretici icin Ingilizce ve Almanca sayfalar
ceviriden ibaret olamaz; gumruk, teslim suresi ve saklama kosullari gibi
basliklarin o dilde ayrica anlatilmasi gerekir. Yaklasimimizi
<a href="/coklu-dil-web-sitesi">coklu dil sayfamizda</a> anlatiyoruz.</p>

<h2>Yabanci ziyaretci ve dil</h2>
<p>Feribot seferleri ve turlar nedeniyle ilcede yabanci ziyaretci orani yuksek.
Menusu, oda anlatisi ve iletisim bilgisi Ingilizce olmayan bir isletme bu
kitlenin buyuk bolumunu goremeden kaybediyor. Ikinci dil, ilcede artik bir
ayricalik degil temel gereklilik.</p>

<h2>Rekabetin sert oldugu yerde ne yapariz</h2>
<ul>
  <li>Isletmenin gercekten farkli olan yanini bulup one cikarmak</li>
  <li>Genel turizm aramalari yerine niyeti net aramalari hedeflemek</li>
  <li>Gorsel agirligini dusurup mobilde hizli acilmayi garantilemek</li>
  <li>Coklu dilde gercek icerik yayinlamak</li>
  <li>Hangi sayfanin rezervasyon getirdigini olcmek</li>
</ul>

<h2>Sezon disi trafik nereden gelir</h2>
<p>Ayvalik'ta yaz aylarinin disinda da ciddi bir ziyaretci hareketi var. Sonbahar
ve ilkbaharda hafta sonu gelen sehirli kitle, kis aylarinda ise gastronomi
amacli kisa ziyaretler suruyor. Bu donemlerde arama yapan kisi tatil planlamiyor;
belirli bir seyi ariyor: acik olan bir kahvalti yeri, hafta sonu musait bir oda,
belirli bir urunun satildigi bir dukkan.</p>

<p>Bu yuzden sezon disi trafik icin genel tanitim metinleri degil, net sorulara
net cevap veren sayfalar gerekir. "Kis aylarinda acik miyiz" sorusunun cevabi
sitede yaziyorsa, o sayfa tum sezon disi boyunca calisir.</p>

<h2>Fotograf ve hiz dengesi</h2>
<p>Turizm isletmelerinde gorsel vazgecilmez, ancak agir fotograflar mobil hizi
oldurur. Yukledigimiz her gorsel uc farkli olcude ve WebP bicimiyle yeniden
uretiliyor; ziyaretcinin ekranina uygun olan gonderiliyor. Boylece galeri dolu
gorunurken sayfa agirligi kontrol altinda kaliyor.</p>

<p>Ayrica her gorselin alt metni giriliyor. Bu hem gorme engelli ziyaretciler
icin gerekli hem de gorsel aramalarindan gelen trafigin kaynagi.</p>

<h2>Cunda ve merkez ayrimi</h2>
<p>Cunda'daki bir isletme ile merkezdeki bir isletme ayni ilcede olsa da farkli
bir kitleye hitap eder. Cunda daha planli, daha uzun sure kalan ve daha yuksek
butceli bir ziyaretci ceker. Bu farkin site dilinde ve fiyat anlatiminda
gorunmesi gerekir; ikisini ayni sablonla anlatmak ikisine de zarar verir.</p>

<p>Konaklama isletmelerine ozel kurdugumuz sistemi
<a href="/rezervasyon-sistemi">rezervasyon sistemi sayfasinda</a>,
bolgedeki isleri <a href="/referanslar">referanslar sayfasinda</a>
gorebilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'gomec' => [
        'district' => 'Gomec',
        'slug'     => 'gomec-web-tasarim',
        'title'    => 'Gomec Web Tasarim',
        'meta_title' => 'Gomec web tasarim — kamp, apart ve kucuk isletme siteleri',
        'meta_description' => 'Gomec ve Karaagac bolgesindeki apart, kamp alani ve kucuk isletmeler icin sade ve hizli web siteleri.',
        'excerpt'  => 'Sakinlik arayan misafire hitap eden kucuk olcekli isletmeler icin.',
        'content'  => <<<'HTML'
<h2>Kucuk ilcenin avantaji</h2>
<p>Gomec, Korfezin en kucuk ilcelerinden biri ve tam da bu yuzden belirli bir
kitleyi ceker: kalabaliktan kacan, sakin bir sahil arayan, uzun konaklama yapan
misafir. Karaagac ve cevresindeki kamp alanlari, apart daireler ve kucuk
pansiyonlar bu talebi karsilar.</p>

<p>Kucuk olcek, internette dezavantaj degil avantajdir. Rekabetin dusuk oldugu
bir yerde dogru kurulmus bir sayfa, kisa surede arama sonuclarinda one gecebilir.
Buyuk ilcelerde aylar suren bir yukselis burada haftalarla olculur.</p>

<h2>Kamp ve karavan turizmi</h2>
<p>Son yillarda karavan ve kamp turizmi bolgede belirgin sekilde artti. Bu
kitlenin arama davranisi cok net: elektrik var mi, su baglantisi nasil, tuvalet
ve dus durumu, gece guvenligi, evcil hayvan kabul ediliyor mu, gunluk ucret ne
kadar. Bu sorularin cevabini sayfasinda acikca yazan bir isletme telefon
trafiginin yarisindan kurtulur ve dogru misafiri ceker.</p>

<p>Fotograf tarafinda da beklenti farkli: parlak tanitim kareleri degil, alanin
gercek halini gosteren fotograflar guven verir.</p>

<h2>Apart ve uzun donem konaklama</h2>
<p>Gomec'teki apartlarin onemli bir bolumu haftalik ya da aylik kiraliyor. Bu
model, gunluk konaklamadan farkli bir anlatim ister. Mutfagin donanimi, camasir
makinesi, internet hizi ve marketin uzakligi burada odanin manzarasindan daha
belirleyici.</p>

<p>Uzun donem misafirin ikinci kez gelme ihtimali yuksek oldugu icin, bu
isletmelerde musteri listesini tutmak ve sezon oncesinde hatirlatma yapmak
dogrudan doluluk getiriyor.</p>

<h2>Yerel uretim ve kucuk esnaf</h2>
<p>Ilcede sut urunleri, bal ve kucuk olcekli tarim uretimi var. Bu ureticiler
icin buyuk bir magaza kurmak gereksiz; onemli olan urunu taniten, uretim
sureclerini anlatan ve iletisim bilgisi net bir sayfa. Siparis telefonla ya da
basit bir formla alinabilir.</p>

<h2>Butcenin dogru kullanimi</h2>
<p>Kucuk isletmede butce sinirlidir ve yanlis yere harcanmamalidir. Bizim
onerimiz genellikle sudur: once dogru kurulmus kucuk bir site ve duzgun bir
harita kaydi. Bu ikisi calismaya basladiktan sonra, gelen talebe gore
buyutmek her zaman mumkun.</p>

<p>Bastan buyuk bir yatirim yapip icerigi guncelleyemeyen bir isletme, ikinci
yilda daha kotu durumda oluyor.</p>

<h2>Yorum ve tavsiye zinciri</h2>
<p>Kucuk isletmelerde yeni misafirin buyuk bolumu tavsiyeyle gelir. Bu zinciri
guclendirmenin en ucuz yolu, ayrilan misafire tek bir baglantiyla yorum
birakabilecegi bir yol sunmaktir. Bu baglanti sitede sabit dursa bile isini
gorur.</p>

<p>Uydurma yorum yayinlamiyoruz ve yapisal veride puan isaretlemesi yapmiyoruz.
Gercek yorumlar zaten yeterlidir; sahte olanlar yakalandiginda tum sayfalari
riske atar.</p>

<h2>Sezon disinda gorunur kalmak</h2>
<p>Kucuk isletmelerin cogu ekim ile nisan arasinda sitesine hic dokunmuyor.
Oysa gelecek sezonun rezervasyonu tam da bu aylarda araniyor. Kis aylarinda
yapilacak birkac guncelleme, yaz gelmeden sitenin arama sonuclarinda hazir
olmasini sagliyor.</p>

<p>En basit haliyle: fiyatlarin guncellenmesi, yeni fotograflarin eklenmesi ve
gecen sezonun sik sorulan sorularinin sayfaya yazilmasi. Uc islem, bir saat.</p>

<h2>Teknik yuk isletmede kalmasin</h2>
<p>Kucuk isletmede teknik isi takip edecek kimse yoktur. Bu yuzden teslim
ettigimiz sistemlerde gunluk yedek otomatik alinir, guncellemeler bizim
tarafimizda yurutulur ve isletmeciye yalnizca icerik kalir. Bakim anlasmasinin
kapsamini <a href="/web-sitesi-bakim">bakim sayfasinda</a> anlatiyoruz.</p>

<h2>Ilcenin kendi takvimi</h2>
<p>Gomec'te sezon Akcay ya da Ayvalik'tan biraz gec baslar ve biraz erken biter.
Haziranin ilk yarisi genellikle sakin gecer, asil yogunluk temmuz ortasindan
agustos sonuna kadar surer. Bu kisa pencerede doluluk kaybetmemek icin
rezervasyonun mayis basinda acilmis olmasi gerekir.</p>

<p>Ayni sekilde eylul ayi, uzun konaklama arayan ve kalabaligi bekleyen bir
kitle icin degerli. Bu iki donemi ayri ayri anlatan sayfalar, tek bir genel
tanitim sayfasindan cok daha fazla is getiriyor.</p>

<h2>Sahil ve koy arasindaki fark</h2>
<p>Ilcede sahil seridi ile ic kesimdeki koyler farkli misafir cekiyor. Sahilde
denize yakinlik belirleyiciyken koylerde sessizlik, bahce ve yerel uretim one
cikiyor. Ikisini ayni cumlelerle anlatan bir site her ikisini de yeterince
anlatamiyor.</p>

<p>Kucuk bir isletme icin bile iki ayri sayfa yazmak, dogru misafiri dogru
sayfaya getirdigi icin donusum oranini yukseltiyor.</p>

<p>Fiyat araliklarini <a href="/fiyatlar">fiyatlar sayfasinda</a>,
komsu ilce calismalarini <a href="/burhaniye-web-tasarim">Burhaniye</a> ve
<a href="/ayvalik-web-tasarim">Ayvalik</a> sayfalarinda bulabilirsiniz.</p>
HTML,
    ],

    // -----------------------------------------------------------------------
    'balikesir' => [
        'district' => 'Balikesir',
        'slug'     => 'balikesir-web-tasarim',
        'title'    => 'Balikesir Web Tasarim',
        'meta_title' => 'Balikesir web tasarim — kurumsal siteler ve e-ticaret',
        'meta_description' => 'Balikesir merkezdeki kurumsal firmalar, sanayi isletmeleri ve hizmet saglayicilar icin web tasarim ve SEO.',
        'excerpt'  => 'Il merkezinde rekabetin yuksek oldugu yerde ayrisan siteler.',
        'content'  => <<<'HTML'
<h2>Il merkezinde rekabet baska</h2>
<p>Balikesir merkez, Korfez ilcelerinden farkli bir oyun alani. Burada ayni
hizmeti veren onlarca firma var, cogunun sitesi mevcut ve bir bolumu duzenli
reklam veriyor. Ilcelerde ise yarayan "dogru kurulmus kucuk site" yaklasimi
merkezde tek basina yetmiyor.</p>

<p>Bu ortamda one gecmenin yolu daha buyuk bir site yapmak degil, daha dar bir
alanda en iyisi olmak. Genel "web tasarim" ya da "muhasebe" aramalarinda
yarismak yerine, firmanin gercekten guclu oldugu alt basliklarda derinlesmek.</p>

<h2>Sanayi ve uretim firmalari</h2>
<p>Organize sanayi bolgesindeki uretim firmalarinin musterisi son tuketici degil,
baska bir firma. Bu durumda site bir katalog degil, teknik guven belgesidir.
Karsi taraf urun kodunu, kapasiteyi, sertifikalari ve referans listesini arar.
Guzel bir anasayfadan cok, indirilebilir teknik dosyalar ve net bir urun
listesi ise yarar.</p>

<p>Ihracat yapan firmalarda Ingilizce icerik zorunludur ve ceviriyle
gecistirilemez. Karsi taraf teknik terimlerin dogru kullanildigini gormek
ister.</p>

<h2>Universite ve genc nufus</h2>
<p>Sehirdeki universite nufusu, ogrenciye yonelik isletmeler icin surekli
yenilenen bir pazar yaratiyor. Bu kitle neredeyse tamamen telefondan ve sosyal
medya uzerinden arama yapiyor. Onlar icin sitenin isi genellikle tek bir sey:
sosyal medyadan gelen kisiyi telefona ya da adrese hizlica ulastirmak.</p>

<p>Bu durumda uzun kurumsal metinler yerine tek ekranda toplanmis bilgi ve
buyuk dokunmatik butonlar daha iyi calisir.</p>

<h2>Hizmet saglayicilar ve serbest meslek</h2>
<p>Avukat, mali musavir, dis hekimi ve benzeri meslek gruplarinda karar guvene
dayanir ve arayan kisi genellikle karsilastirma yapar. Burada belirleyici olan
sey, mesleki icerigin varligidir: sik sorulan sorularin gercekten cevaplandigi,
sureci anlatan yazilar.</p>

<p>Bu tur icerik hem arama sonuclarinda uzun kuyruklu aramalari yakalar hem de
ilk temasta guven kurar. Meslek etigi geregi reklam kisitlari olan alanlarda
bilgilendirici icerik zaten en dogru yontemdir.</p>

<h2>Bolge geneline hizmet verenler</h2>
<p>Merkezdeki bircok firma tum ile hizmet veriyor. Bu durumda her ilce icin ayri
ve gercekten farkli sayfalar gerekir. Ayni metni ilce adi degistirerek
cogaltmak, kisa vadede sayfa sayisini artirsa da Google tarafindan
yakalandiginda tum sayfalari birden degersizlestirir.</p>

<p>Biz her bolge sayfasini o bolgenin kendi ekonomisinden yazariz; bu sayfanin
kendisi de buna ornektir. Yaklasimin ayrintisini
<a href="/seo-hizmeti">SEO hizmeti sayfamizda</a> anlatiyoruz.</p>

<h2>Reklamla organik trafigin dengesi</h2>
<p>Merkezde rekabet yuksek oldugu icin bircok firma dogrudan reklama yoneliyor.
Reklam hizli sonuc verir ama musluk kapandiginda trafik de biter. Organik
gorunurluk yavas kurulur, buna karsilik kalicidir.</p>

<p>Bizim onerdigimiz denge sudur: reklami kisa vadeli talep icin, icerigi uzun
vadeli gorunurluk icin kullanmak. Reklamdan gelen aramalarin hangi kelimelerde
donusum getirdigini olcup, ayni kelimeler icin kalici icerik yazmak reklam
butcesini zamanla dusurur.</p>

<h2>Kurumsal sitede olcum ve raporlama</h2>
<p>Merkezdeki firmalarda genellikle karar veren kisi ile siteyi kullanan kisi
farklidir. Bu yuzden raporlamanin anlasilir olmasi gerekir: kac kisi geldi
degil, hangi sayfa kac teklif talebi getirdi.</p>

<p>Panelde form kayitlari kaynak sayfasiyla birlikte tutuluyor. Uc ay sonra
hangi hizmet sayfasinin gercekten is getirdigi net bir tabloya donusuyor ve
icerik yatirimi buna gore yonlendiriliyor.</p>

<h2>Sitenin devri ve bagimsizlik</h2>
<p>Kurumsal musterilerimizin sik sordugu bir soru sudur: "Sizinle calismayi
birakirsak ne olur?" Cevap net: sitenin tum dosyalari ve veritabani
musterinindir. Kapali bir sistem, kiralik bir panel ya da disari cikilamayan
bir altyapi kurmuyoruz. Isteyen musteri her seyi alip baska bir ekiple devam
edebilir.</p>

<p>Korfez ilcelerindeki calismalarimiz icin
<a href="/edremit-web-tasarim">Edremit</a> ve
<a href="/ayvalik-web-tasarim">Ayvalik</a> sayfalarina bakabilirsiniz.</p>
HTML,
    ],

];
