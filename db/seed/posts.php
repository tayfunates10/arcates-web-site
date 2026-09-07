<?php
/**
 * Baslangic blog yazilari.  DOCS.md 4.1, 9.4
 *
 * Bunlar isletmenin yayina hazir taslaklaridir: konular `/blog` sayfasinda
 * sayilan basliklarla eslesir. Metinler panelden duzenlenebilir; kapak gorseli
 * bilerek bos birakildi, isletme kendi fotograflarini Medya ekranindan
 * yukleyip yaziya baglar.
 *
 * Uydurma istatistik, olculmemis sonuc ya da musteri hikayesi ICERMEZ;
 * hepsi genel gecer uygulama bilgisidir.
 */

declare(strict_types=1);

return [
    [
        'category' => 'Yerel SEO',
        'slug' => 'yerel-aramada-gorunurluk-isletme-profili',
        'title' => 'Yerel aramada görünürlük: önce İşletme Profili',
        'excerpt' => 'Site kurmadan önce düzeltilmesi gereken en ucuz iş, Google İşletme Profili.',
        'meta_title' => 'Yerel aramada görünürlük — Google İşletme Profili düzeni',
        'meta_description' => 'Edremit Körfezi\'ndeki işletmeler için Google İşletme Profili düzeni: NAP tutarlılığı, kategori seçimi, fotoğraf ve yorum yönetimi.',
        'published_at' => '2026-07-14 10:00:00',
        'content' => <<<'HTML'
<p>Yeni bir site kurmayı düşünen işletmelerle görüşmeye başladığımızda ilk
baktığımız yer site değil, Google İşletme Profili oluyor. Sebebi basit: harita
sonuçları çoğu yerel aramada organik sonuçların üstünde çıkıyor ve profilin
düzeltilmesi hiçbir şeye mal olmuyor.</p>

<h2>NAP tutarlılığı</h2>
<p>NAP, işletmenin adı (Name), adresi (Address) ve telefonu (Phone) demek. Bu
üç bilgi profilinizde, sitenizde ve kayıtlı olduğunuz rehberlerde birbirinin
aynı olmalı. "Cad." ile "Caddesi", başında sıfır olan ve olmayan telefon,
işletme adının sonundaki "Ltd. Şti." — bunların hepsi farklı yazımlar sayılır.
Küçük görünen bu tutarsızlıklar, arama motorunun aynı işletmeden bahsedildiğine
emin olmasını zorlaştırıyor.</p>

<p>En kolay yöntem: bir yazımı seçin, bir yere not edin, her yerde harfi harfine
onu kullanın. Sitenizdeki iletişim bilgilerini de aynı kaynaktan besleyin.</p>

<h2>Kategori seçimi</h2>
<p>Birincil kategori, hangi aramalarda çıkacağınızı doğrudan etkiliyor. Burada
sık yapılan hata, geniş bir kategori seçip her şeye görünmeye çalışmak.
"Restoran" yerine "Balık restoranı", "Konaklama" yerine "Pansiyon" genellikle
daha isabetli sonuç veriyor. İkincil kategoriler ekleyebilirsiniz, ama birincil
kategori işin ana tanımı olmalı.</p>

<h2>Fotoğraf ve çalışma saatleri</h2>
<p>Profilde en çok bakılan alanlar bunlar. Fotoğrafları telefonla, gündüz
ışığında, gerçek mekânda çekin; stok görsel kullanmayın. Dış cephe, giriş,
içerisi ve ürün fotoğrafı bulunsun — insanlar gitmeden önce kapıyı tanımak
istiyor.</p>

<p>Çalışma saatlerini sezona göre güncelleyin. Körfez'de birçok işletmede yaz ve
kış saatleri farklı; profil kışın hâlâ yaz saatlerini gösteriyorsa kapalı
kapıya gelen müşteri olumsuz yorum bırakıyor. Bayram ve tatil günleri için özel
saat girebiliyorsunuz, bu alan çoğu işletmede boş kalıyor.</p>

<h2>Yorumlara cevap vermek</h2>
<p>Olumlu yorumlara kısa bir teşekkür yeterli. Olumsuz yorumda savunmaya
geçmeyin; ne olduğunu sorun, çözüm önerin, tartışmayı özele taşıyın. Cevap
yazılmış bir olumsuz yorum, cevapsız bir olumsuz yorumdan çok daha az zarar
veriyor — çünkü sonraki okuyucu işletmenin ilgilendiğini görüyor.</p>

<h2>Peki site ne işe yarıyor</h2>
<p>Profil sizi listeye sokar, site kararı verdirir. Harita sonucunda üç işletme
yan yana duruyorsa, ziyaretçi genellikle sitesi olanın sayfasını açıp fiyat,
kapasite, hizmet ayrıntısı arıyor. Sitesi olmayan işletme bu aşamada
karşılaştırma dışı kalıyor.</p>

<p>İkisini birbirine bağlamak da önemli: profildeki site adresi doğru sayfaya
gitmeli, sitenizde de aynı NAP bilgileri ve harita bağlantısı bulunmalı. Bu
konuda ne yaptığımızı <a href="/seo-hizmeti">SEO hizmeti sayfamızda</a>
anlatıyoruz; ilçe bazında yaklaşımımız için
<a href="/edremit-web-tasarim">Edremit sayfamıza</a> bakabilirsiniz.</p>
HTML,
    ],

    [
        'category' => 'Performans',
        'slug' => 'kucuk-isletme-sitesinde-hiz-ve-mobil',
        'title' => 'Küçük işletme sitesinde hız ve mobil kullanım',
        'excerpt' => 'Ziyaretçilerin çoğu telefondan geliyor; site orada yavaşsa geri kalan her şey boşa gidiyor.',
        'meta_title' => 'Site hızı ve mobil kullanım — küçük işletmeler için',
        'meta_description' => 'Görsel boyutu, yazı tipi, gereksiz eklenti ve dokunma alanı: küçük işletme sitesinde hızı ve mobil kullanımı belirleyen ayarlar.',
        'published_at' => '2026-07-28 10:00:00',
        'content' => <<<'HTML'
<p>Yerel aramaların büyük bölümü telefondan yapılıyor ve genellikle acele bir
ihtiyaçla: "şu an açık mı", "fiyatı ne", "nasıl giderim". Bu üç sorunun cevabı
ekrana gelmeden site kapanıyorsa, tasarımın ne kadar güzel olduğunun bir önemi
kalmıyor.</p>

<h2>Görseller en büyük yük</h2>
<p>Yavaş sitelerin çoğunda sebep aynı: fotoğraf makinesinden ya da telefondan
çıktığı boyutuyla yüklenmiş görseller. Beş megabaytlık bir fotoğrafı tarayıcı
küçültüp gösterebilir, ama önce tamamını indirmek zorunda. Mobil veriyle bu
saniyeler demek.</p>

<p>Doğru yol, görseli yükleme anında birkaç boyuta çevirmek ve tarayıcıya
ekranın genişliğine uygun olanı vermek. Modern formatlar (WebP gibi) aynı
görüntüyü belirgin biçimde daha küçük dosyada tutuyor. Bu işi elle yapmak
zorunda kalmamalısınız; yönetim paneli yüklerken hallederse, içeriği giren
kişinin teknik bilgisi olmasına gerek kalmıyor.</p>

<h2>Yazı tipleri</h2>
<p>Her ek yazı tipi ailesi ve her ek kalınlık, indirilecek yeni bir dosya
demek. İki aile ve toplam üç-dört kalınlık çoğu site için fazlasıyla yeterli.
Yazı tipi yüklenene kadar metnin görünmez kalması da sık rastlanan bir sorun;
tarayıcıya "önce sistem yazı tipiyle göster, sonra değiştir" demek okumayı
hemen başlatıyor.</p>

<h2>Gereksiz eklenti</h2>
<p>Sohbet balonu, iki farklı analiz kodu, sosyal medya beslemesi, açılır
kampanya penceresi... Her biri ayrı ayrı masum görünüyor ama birlikte sitenin
en yavaş parçası hâline geliyor. Bir eklentinin yılda kaç kez işe yaradığını
soramıyorsanız, muhtemelen kaldırılabilir.</p>

<h2>Dokunma alanları</h2>
<p>Telefonda bir bağlantıya basmak, farede tıklamaktan daha kaba bir hareket.
Küçük ve birbirine yapışık bağlantılar yanlış basmaya yol açıyor. Telefon
numarası, yol tarifi ve teklif düğmesi gibi önemli hedefler parmakla rahat
basılacak büyüklükte olmalı ve başparmağın ulaştığı bölgede durmalı.</p>

<p>Bir de yatay kaydırma var: içeriğin ekrandan taşıp sayfanın sağa sola
oynaması, kullanıcıyı en çok rahatsız eden şeylerden biri. Dar ekranda test
etmeden yayına çıkmayın.</p>

<h2>Nasıl ölçersiniz</h2>
<p>Kendi telefonunuzla, wifi'den değil mobil veriyle, sitenizi açın ve saymaya
başlayın. Üç saniyeden uzun sürüyorsa, ziyaretçilerin bir bölümünü daha ilk
adımda kaybediyorsunuz demektir. Tarayıcıların geliştirici araçlarında bulunan
performans ölçümleri de ücretsiz ve yeterince yol gösterici.</p>

<p>Hızı baştan doğru kurmak, sonradan düzeltmekten her zaman ucuz. Bu konuda
nasıl çalıştığımızı <a href="/web-tasarim">web tasarım sayfamızda</a>,
yayın sonrası bakımı ise
<a href="/web-sitesi-bakim">bakım sayfamızda</a> anlatıyoruz.</p>
HTML,
    ],

    [
        'category' => 'Dönüşüm',
        'slug' => 'rezervasyon-ve-teklif-formunda-donusum',
        'title' => 'Rezervasyon ve teklif formunda dönüşüm',
        'excerpt' => 'Formu dolduranların sayısı çoğu zaman alan sayısıyla ters orantılı.',
        'meta_title' => 'Rezervasyon ve teklif formlarında dönüşüm',
        'meta_description' => 'Alan sayısı, zorunlu alanlar, hata mesajları, teşekkür sayfası ve KVKK onayı: form dolduran sayısını belirleyen ayrıntılar.',
        'published_at' => '2026-08-06 10:00:00',
        'content' => <<<'HTML'
<p>Site ziyaret ediliyor ama form dolmuyorsa sorun genellikle trafiğin azlığı
değil, formun kendisi oluyor. Formlar, ziyaretçiden emek isteyen tek yer;
istenen emek arttıkça vazgeçen sayısı da artıyor.</p>

<h2>Alan sayısı</h2>
<p>Her ek alan bir vazgeçme sebebi. İlk temas için gereken bilgi genellikle
üçtür: kim, nasıl ulaşırım, ne istiyor. Geri kalan her şeyi görüşmede
sorabilirsiniz. "Şirket ünvanı", "vergi dairesi", "nereden duydunuz" gibi
alanlar ilk formda değil, iş bağlandıktan sonra sorulmalı.</p>

<h2>Zorunlu alanları ayırın</h2>
<p>Zorunlu ve isteğe bağlı alanların hangisi olduğu bakar bakmaz anlaşılmalı.
Ziyaretçi göndere bastıktan sonra "telefon zorunludur" uyarısıyla karşılaşmak
yerine, formu doldururken bilmeli. İsteğe bağlı alanları açıkça işaretlemek,
zorunluları yıldızla işaretlemekten çoğu zaman daha anlaşılır oluyor.</p>

<h2>Hata mesajları</h2>
<p>"Bir hata oluştu" hiçbir işe yaramıyor. Hangi alanda ne sorun var, nasıl
düzeltilir — mesaj bunu söylemeli ve hatalı alanın yanında görünmeli. Form
gönderilirken doldurulmuş değerlerin kaybolması da kullanıcıyı en çok kaçıran
davranışlardan biri; hata durumunda girilen bilgiler ekranda kalmalı.</p>

<h2>Teşekkür sayfası ayrı bir adres olmalı</h2>
<p>Form gönderildikten sonra aynı sayfada beliren küçük bir yeşil kutu yerine,
ayrı bir teşekkür sayfasına yönlendirmek iki işe yarıyor. Birincisi ziyaretçi
işlemin gerçekten tamamlandığını anlıyor. İkincisi kaç kişinin formu
tamamladığını ölçebiliyorsunuz — çünkü o sayfa yalnızca formu gönderenler
tarafından görülüyor.</p>

<p>Teşekkür sayfasında ne zaman döneceğinizi de yazın. "En geç bir iş günü
içinde arıyoruz" cümlesi, bekleyen insanı rahatlatıyor ve gereksiz ikinci
gönderimleri azaltıyor.</p>

<h2>KVKK onayı</h2>
<p>Kişisel veri toplayan her formda açık rıza gerekiyor. Onay kutusu önceden
işaretli gelmemeli ve yanında aydınlatma metnine bağlantı bulunmalı. Bu hem
yasal bir zorunluluk hem de güven veren bir ayrıntı: verisinin ne olacağını
bilen insan formu daha rahat dolduruyor.</p>

<h2>Telefonu da bırakın</h2>
<p>Herkes form doldurmak istemiyor. Telefon numarasının her sayfada, telefondan
tek dokunuşla aranabilir olması gerekiyor. Form ile telefon birbirinin
alternatifi; biri diğerinin yerine geçmiyor.</p>

<h2>Formun durduğu yer</h2>
<p>Tek bir iletişim sayfasına konulan form, ziyaretçiyi ilgilendiği içerikten
koparıyor. Oda sayfasındaki bir misafir o sayfada rezervasyon isteyebilmeli,
hizmet sayfasını okuyan bir işletmeci o sayfadan teklif isteyebilmeli. Formun
hangi sayfadan geldiğini kaydetmek de faydalı: birkaç ay sonra hangi sayfanın
gerçekten iş getirdiğini yalnızca bu kayıt gösteriyor.</p>

<p>Son bir ayrıntı: formu doldurmaya başlayan biri yanlışlıkla sayfadan
çıkarsa girdikleri kayboluyor. Uzun formlarda bu, tamamlanmayan gönderimlerin
sessiz sebebi oluyor — bir sebep daha alanları azaltmak için.</p>

<p>Rezervasyon akışını nasıl kurduğumuzu
<a href="/rezervasyon-sistemi">rezervasyon sistemi sayfamızda</a>
anlatıyoruz. Kendi formunuzu konuşmak isterseniz
<a href="/iletisim">bize yazın</a>.</p>
HTML,
    ],

    [
        'category' => 'Çoklu dil',
        'slug' => 'coklu-dilde-yayin-yabanci-misafire-ulasmak',
        'title' => 'Çoklu dilde yayın: yabancı misafire ulaşmak',
        'excerpt' => 'Çeviri eklentisi ile gerçek çoklu dil yayını arasındaki fark, arama sonuçlarında ortaya çıkıyor.',
        'meta_title' => 'Çoklu dilde yayın — yabancı misafire ulaşmak',
        'meta_description' => 'Hangi diller, çeviri mi yerelleştirme mi, hreflang kurulumu ve hangi sayfaların çevrileceği üzerine uygulanabilir bir rehber.',
        'published_at' => '2026-08-18 10:00:00',
        'content' => <<<'HTML'
<p>Körfez'de yabancı misafir ağırlayan işletmelerin çoğu tarayıcının otomatik
çeviri özelliğine güveniyor. Bu, sayfayı okunur yapıyor ama arama sonuçlarında
hiçbir işe yaramıyor: Almanca arayan bir misafir, yalnızca Türkçe yayınlanmış
sayfanızı bulamıyor.</p>

<h2>Hangi diller</h2>
<p>Üç dil eklemek üç kat içerik demek. Önce şunu sorun: geçen sezon gelen
yabancı misafirler hangi ülkelerden geldi? Rezervasyon kayıtlarınız,
misafir defteriniz ya da sadece hafızanız bu soruyu cevaplar. Bölgede
genellikle Almanca ve İngilizce ilk sıraya çıkıyor; başka bir dil için
somut bir sebebiniz yoksa beklemekte fayda var.</p>

<h2>Çeviri mi, yerelleştirme mi</h2>
<p>Cümleyi kelimesi kelimesine çevirmek yetmiyor. Alman bir misafir için
"kapora" kavramı, İngiliz bir misafir için "yarım pansiyon" tanımı aynı şeyi
ifade etmeyebiliyor. Mesafeleri kilometre yanında dakika olarak da yazmak,
para birimini ve tarih biçimini okuyucunun alışkanlığına göre göstermek —
bunlar çeviriden çok yerelleştirme.</p>

<p>Makine çevirisini taslak olarak kullanabilirsiniz, ama yayına çıkmadan önce
o dili bilen birinin gözden geçirmesi gerekiyor. Yanlış çevrilmiş bir iptal
koşulu, sonradan çözülmesi zor bir anlaşmazlığa dönüşebiliyor.</p>

<h2>hreflang etiketi</h2>
<p>Aynı sayfanın farklı dillerdeki karşılıklarını arama motoruna bildiren
etiket bu. Doğru kurulduğunda Almanca arayan kişiye Almanca sayfa, Türkçe
arayana Türkçe sayfa gösteriliyor. Kurulmadığında iki sayfa birbirinin kopyası
gibi değerlendirilebiliyor.</p>

<p>Sık yapılan hata, yalnızca ana sayfaya etiket koyup iç sayfaları unutmak.
Etiketler karşılıklı olmalı: Türkçe sayfa Almanca sayfayı gösteriyorsa,
Almanca sayfa da Türkçe olanı göstermeli.</p>

<h2>Hangi sayfalar çevrilmeli</h2>
<p>Hepsi değil. Yabancı misafirin karar vermek için ihtiyaç duyduğu sayfalar
yeterli: ana sayfa, oda veya ürün sayfaları, fiyat ve koşullar, ulaşım,
iletişim. Yerel arama için yazılmış ilçe sayfalarını çevirmenin genellikle bir
karşılığı olmuyor — çünkü o aramalar Türkçe yapılıyor.</p>

<h2>Yönetimi kolay olmalı</h2>
<p>Çoklu dil yayının en çok aksadığı yer bakım aşaması. Türkçe fiyatı
güncelleyip Almanca sayfayı unutmak, güvenilirliği doğrudan zedeliyor. Panelde
diller yan yana sekmelerde duruyorsa ve hangi çevirinin eksik olduğu
görünüyorsa, bu hata büyük ölçüde önleniyor.</p>

<h2>Dil seçici nerede durmalı</h2>
<p>Dil seçicinin üst menüde, ilk bakışta görünen bir yerde olması gerekiyor.
Bayrak simgesi kullanmak yaygın ama yanıltıcı: bir dil ile bir ülke aynı şey
değil. Dilin kendi adını kendi yazımıyla yazmak — "Deutsch", "English" —
hem daha açık hem daha kısa.</p>

<p>Ziyaretçi dil değiştirdiğinde onu ana sayfaya atmayın; bulunduğu sayfanın
karşılığına götürün. Karşılığı yoksa, bunu söyleyip en yakın sayfayı önerin.
Sessizce ana sayfaya dönmek, okuduğu şeyi kaybeden ziyaretçiyi siteden
çıkarıyor.</p>

<p>Yaklaşımımızı <a href="/coklu-dil-web-sitesi">çoklu dil web sitesi
sayfamızda</a> anlatıyoruz; konaklama işletmelerine özel bakışımız için
<a href="/otel-pansiyon-web-sitesi">otel ve pansiyon sayfamıza</a>
bakabilirsiniz.</p>
HTML,
    ],

    [
        'category' => 'Bakım',
        'slug' => 'yedekleme-guvenlik-ve-bakim-aliskanliklari',
        'title' => 'Yedekleme, güvenlik ve bakım alışkanlıkları',
        'excerpt' => 'Yedeğin var olması yetmiyor; geri yüklenebildiğini bir kez denemek gerekiyor.',
        'meta_title' => 'Yedekleme, güvenlik ve bakım alışkanlıkları',
        'meta_description' => 'Yedek nerede durmalı, geri yükleme provası, güncelleme düzeni, şifre ve SSL: site sahibinin bilmesi gereken bakım alışkanlıkları.',
        'published_at' => '2026-08-27 10:00:00',
        'content' => <<<'HTML'
<p>Bakım, işler yolunda giderken kimsenin aklına gelmeyen konu. Bir sorun
çıktığında ise geriye dönüp yapılabilecek pek bir şey kalmıyor. Aşağıdakiler,
teknik bilgi gerektirmeyen ama sahibinin takip etmesi gereken alışkanlıklar.</p>

<h2>Yedek nerede duruyor</h2>
<p>Yedeğin sitenin durduğu sunucuda tutulması, hiç yedek almamaktan yalnızca
biraz iyi. Sunucuyla ilgili bir sorun çıktığında yedek de aynı sorundan
etkileniyor. En az bir kopyanın başka bir yerde — başka bir sağlayıcıda ya da
şirket bilgisayarında — durması gerekiyor.</p>

<p>İkinci soru: yedek ne sıklıkta alınıyor? İçeriğini haftada bir güncelleyen
bir işletme için günlük yedek fazla; sipariş alan bir mağaza için günlük yedek
bile az olabilir. Ölçü, "en fazla ne kadarlık veriyi kaybetmeyi göze
alabilirim" sorusunun cevabı.</p>

<h2>Geri yükleme provası</h2>
<p>Bu, en çok atlanan adım. Yedek dosyaları duruyor olabilir ama bozuk
olabilir, eksik olabilir, geri yüklemeyi kimse denememiş olabilir. Yılda bir
kez, sakin bir günde, yedekten geri dönmeyi deneyin. Denenmemiş yedek, yedek
sayılmıyor.</p>

<h2>Güncellemeler</h2>
<p>Güvenlik açıklarının çoğu, çoktan yaması çıkmış ama uygulanmamış
yazılımlardan geliyor. Sitenizde ne kadar çok üçüncü taraf bileşen varsa
takip edilecek liste o kadar uzuyor. Bu, sade kurulmuş bir sitenin uzun
vadedeki en somut avantajı: güncellenecek şey azsa, unutulacak şey de az.</p>

<h2>Şifreler ve erişim</h2>
<p>Panel şifresi, e-posta şifresiyle aynı olmamalı. Siteden ayrılan bir
çalışanın hesabı kapatılmalı. Herkese yönetici yetkisi vermek yerine, içerik
girecek kişiye yalnızca içerik yetkisi vermek yeterli — böylece yanlışlıkla
silinen ayar riski de ortadan kalkıyor.</p>

<p>Alan adı ve barındırma hesaplarının kimin adına kayıtlı olduğunu bilin.
Bu bilgiler çoğu zaman bir ajansta ya da eski bir çalışanda kalıyor ve
yıllar sonra siteyi taşımak isteyince sorun çıkarıyor.</p>

<h2>SSL sertifikası</h2>
<p>Adres çubuğunda kilit görünmüyorsa tarayıcılar ziyaretçiyi uyarıyor ve
form dolduran kişi geri dönüyor. Sertifikaların çoğu otomatik yenileniyor,
ama yenilenmediğinde kimse haber vermiyor. Yılda bir kez süresini kontrol
etmek yeterli.</p>

<h2>Kim sorumlu</h2>
<p>Bakımın en kritik parçası teknik değil: bu işlerin kime ait olduğunun
yazılı olması. "Yedekleri kim kontrol ediyor", "güncellemeyi kim yapıyor",
"sertifika bitince kime haber gidiyor" sorularının cevabı belliyse, geri
kalanı düzenli bir işe dönüşüyor.</p>

<p>Bu işleri sizin adınıza nasıl yürüttüğümüzü
<a href="/web-sitesi-bakim">bakım sayfamızda</a>, aylık maliyetini ise
<a href="/fiyatlar">fiyatlar sayfasında</a> anlatıyoruz.</p>
HTML,
    ],

    [
        'category' => 'İçerik',
        'slug' => 'sezonluk-isletme-icin-icerik-takvimi',
        'title' => 'Sezonluk işletme için içerik takvimi',
        'excerpt' => 'Körfez\'de arama trafiği sezona göre değişiyor; içerik de o ritme göre yazılmalı.',
        'meta_title' => 'Sezonluk işletme için içerik takvimi',
        'meta_description' => 'Sezon öncesi, sezon içi ve sezon sonrası: Edremit Körfezi\'nde çalışan işletmeler için basit bir içerik takvimi.',
        'published_at' => '2026-09-03 10:00:00',
        'content' => <<<'HTML'
<p>Körfez'de birçok işletmenin yılı ikiye ayrılıyor: yoğun sezon ve geri
kalanı. Site içeriği çoğu zaman bu ritmi görmezden geliyor — sezon başladıktan
sonra sayfa hazırlanıyor, sezon bitince site öylece bırakılıyor. Oysa arama
trafiği sezondan önce hareketleniyor.</p>

<h2>İnsanlar ne zaman arıyor</h2>
<p>Tatil planı, tatilin kendisinden haftalar önce yapılıyor. Temmuz için
konaklama arayan bir aile, aramayı genellikle nisan-mayıs döneminde yapıyor.
Aynı şekilde zeytin hasadıyla ilgili aramalar hasat başlamadan önce
yoğunlaşıyor. Sayfanın arama sonuçlarında yer bulması da zaman aldığı için,
içeriğin sezondan en az iki ay önce yayında olması gerekiyor.</p>

<h2>Sezon öncesi: hazırlık</h2>
<p>Bu dönemde yazılacak içerik karar verdiren içerik: fiyat aralıkları, oda ya
da ürün ayrıntıları, ulaşım tarifi, iptal koşulları, sık sorulan sorular.
Ziyaretçi karşılaştırma yapıyor; eksik bilgi doğrudan eleme sebebi oluyor.</p>

<p>Çalışma saatlerini ve müsaitlik takvimini de bu dönemde güncelleyin. Sezon
başladıktan sonra bunlara vakit ayırmak zorlaşıyor.</p>

<h2>Sezon içi: küçük ve sık</h2>
<p>Yoğun dönemde uzun yazı yazacak vakit olmuyor. Bu dönem için doğru iş,
küçük güncellemeler: bu haftanın menüsü, kalan müsait tarihler, yeni gelen
ürün, kısa bir fotoğraf. Telefondan girilip beş dakikada yapılabilecek
güncellemeler, sezon boyunca sitenin canlı görünmesini sağlıyor.</p>

<p>Bir de sezon içinde gelen sorulara dikkat edin. Misafirin telefonda tekrar
tekrar sorduğu her şey, aslında sitede eksik olan bir bilgi. Bunları not alın;
sezon sonunda yazacağınız içeriğin listesi kendiliğinden oluşuyor.</p>

<h2>Sezon sonu: değerlendirme</h2>
<p>Sakinleyen dönem, siteyi düzeltmek için en uygun zaman. Hangi sayfalar
ziyaret edildi, hangi sayfadan form geldi, hangi soru en çok soruldu — bunlara
bakıp gelecek sezonun içeriğini planlayabilirsiniz.</p>

<p>Kapanış dönemi olan işletmeler için önemli bir ayrıntı: siteyi kapatmayın.
Kışın da arama yapan insan var ve "kapalı" bilgisini bulamayan kişi rakibe
gidiyor. Çalışma saatlerine sezon bilgisini yazmak, kapanış tarihini ve
gelecek sezonun açılışını belirtmek yeterli.</p>

<h2>Yılda dört saat</h2>
<p>Bu takvimin işi büyütmesine gerek yok. Sezon öncesi bir günlük hazırlık,
sezon içinde haftada beş dakika, sezon sonunda bir değerlendirme — çoğu
işletme için yeterli. Önemli olan düzenli olması.</p>

<p>Bölgeye göre yaklaşımımızı <a href="/akcay-web-tasarim">Akçay</a> ve
<a href="/altinoluk-web-tasarim">Altınoluk</a> sayfalarında, konaklama
işletmelerine özel bakışımızı
<a href="/otel-pansiyon-web-sitesi">otel ve pansiyon sayfasında</a>
anlatıyoruz.</p>
HTML,
    ],
];
