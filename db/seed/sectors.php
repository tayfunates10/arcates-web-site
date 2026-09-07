<?php
/**
 * Sektor sayfalari.  DOCS.md 4.4
 *
 * Ilce sayfalarindan farkli olarak sektor sayfalari bolge degil is kolu
 * hedefler; bu yuzden metinleri sektorun kendi sorunlarindan yazilir.
 */

declare(strict_types=1);

return [
    [
        'slug' => 'otel-pansiyon-web-sitesi', 'sort' => 20,
        'title' => 'Otel ve Pansiyon Web Sitesi',
        'meta_title' => 'Otel ve pansiyon web sitesi — komisyonsuz rezervasyon',
        'meta_description' => 'Konaklama işletmeleri için doğrudan rezervasyon alan, çoklu dilde yayın yapan web siteleri.',
        'excerpt' => 'Komisyon ödemeden kendi misafirinizi kazanın.',
        'content' => <<<'HTML'
<h2>Platform bağımlılığından çıkmak</h2>
<p>Konaklama işletmelerinin büyük bölümü doluluğunun tamamını rezervasyon
platformlarından sağlıyor. Komisyon oranı yüzde on beş ile yirmi beş arasında
değiştiğinde, otuz odalı bir tesiste sezonluk kayıp ciddi bir rakama ulaşıyor.
Üstelik misafirin adı, e-postası ve tercihi de sizde kalmıyor.</p>

<h2>Sitede olması gerekenler</h2>
<ul>
  <li>Tarih ve kişi seçildikten hemen sonra görünen fiyat ve müsaitlik</li>
  <li>Kapora ya da tam ödeme seçeneği</li>
  <li>Onay ve hatırlatma e-postaları</li>
  <li>Odanın gerçek fotoğrafları; genel manzara kareleri değil</li>
  <li>Almanca ve İngilizce gerçek içerik, otomatik çeviri değil</li>
</ul>

<h2>Dönüşümü kesen üç nokta</h2>
<p>Doğrudan rezervasyon denemeleri genellikle üç yerde kesilir: fiyat görünmeden
bilgi istenmesi, müsaitliğin belirsiz bırakılması ve mobilde uzun form. Bu ucunu
düzeltmek, tasarımı değiştirmekten çok daha fazla iş getirir.</p>

<h2>Fotoğraf seçimi</h2>
<p>Misafir önce fotoğrafa bakar. En sık yapılan hata, yalnızca genel manzara ve
havuz kareleri koymaktır. Misafirin görmek istediği şey kalacağı odadır: yatak,
banyo, dolap ve pencereden görünen manzara. Bunlar yoksa misafir olumsuz
varsayar.</p>

<h2>Fiyat göstermek</h2>
<p>"Fiyat için arayın" yazan bir sayfa, karşılaştırma yapan misafiri kaybeder.
En azından sezon bazlı başlangıç fiyatının görünmesi, gereksiz telefon
trafiğini de azaltır.</p>

<h2>Sezon dışı anlatı</h2>
<p>Nisan-mayıs ve eylül-ekim aylarında gelen kitle farklıdır: sakinlik arayan,
uzun kalan, çalışırken tatil yapan bir grup. Bu dönem için ayrı bir sayfa
yazmak, boş geçen haftaların bir bölümünü doldurabiliyor.</p>

<p><a href="/rezervasyon-sistemi">Rezervasyon sistemi</a> ve
<a href="/akcay-web-tasarim">Akçay</a> sayfalarımıza bakın.</p>

<h2>İptal politikası</h2>
<p>İptal koşulunun net yazılması, doğrudan rezervasyonun önündeki en büyük
tereddüdü kaldırır. Belirsiz bırakılan koşul, misafiri komisyonlu ama güvendiği
kanala iter.</p>

<h2>Misafir listesi</h2>
<p>Sezon sonunda elinizde kalan misafir listesi, gelecek yılın en ucuz pazarlama
aracı. Erken rezervasyon duyurusu bu listeye gönderildiğinde hiçbir komisyon
ödenmez.</p>

<h2>Yorumlarla çalışmak</h2>
<p>Ayrılan misafire tek bir bağlantıyla yorum bırakma yolu sunmak, sezon
sonundaki puanı korumanın en ucuz yoludur. Uydurma yorum yayınlamıyoruz ve
yapısal veride puan işaretlemesi yapmıyoruz; yakalandığında tüm sayfaları riske
atar.</p>

<p>Ayrıntılar için <a href="/rezervasyon-sistemi">rezervasyon sistemi
sayfamıza</a> bakabilirsiniz.</p>
HTML,
    ],
    [
        'slug' => 'zeytinyagi-e-ticaret-sitesi', 'sort' => 21,
        'title' => 'Zeytinyağı E-Ticaret Sitesi',
        'meta_title' => 'Zeytinyağı e-ticaret sitesi — üretici ve kooperatifler',
        'meta_description' => 'Zeytinyağı üreticileri ve kooperatifler için hasat takvimine göre çalışan e-ticaret altyapısı.',
        'excerpt' => 'Aracıyı aradan çıkarın; kendi markanızla satın.',
        'content' => <<<'HTML'
<h2>Tenekeyle satmak ile etiketle satmak</h2>
<p>Yağını tenekeyle veren üretici ile kendi etiketiyle satan üretici arasındaki
fiyat farkı kat kat. Aradaki mesafeyi kapatan şey büyük bir yatırım değil; doğru
kurulmuş bir mağaza ve düzenli bir müşteri iletişimi.</p>

<h2>Hasat takvimine göre satış</h2>
<p>Zeytinyağı satışı yıl boyunca aynı değildir. Kasım-aralık döneminde yeni hasat
talebi patlar, ilkbaharda düşer, yaz aylarında hediye siparişi artar. Mağazanın
bu ritmi tanıması gerekir: on sipariş dönemi, stok bittiğinde bekleme listesi,
yeni hasat duyurusu için e-posta listesi.</p>

<h2>Ürün sayfası bir katalog değildir</h2>
<p>Ağacın yaşı, hasadın nasıl yapıldığı, sıkım tesisine ulaşma süresi ve asitlik
değeri; bunlar meraklı müşteri için fiyat farkını meşrulaştıran bilgilerdir ve
aynı zamanda uzun kuyruklu aramalarla birebir örtüşür.</p>

<h2>Yurt dışı satış</h2>
<h2>Kargo ve ambalaj</h2>
<p>Cam şişeyle gönderim, doğru koli ve dolgu kullanılmadığında kırılmayla
sonuçlanır ve ilk siparişin maliyeti iki katına çıkar. Teneke ve pet
seçeneklerinin fiyat ve ağırlık farkı mağazada açıkça gösterilmelidir.</p>

<h2>Analiz değerleri</h2>
<p>Asitlik, peroksit ve hasat yılı gibi değerler meraklı müşteri için fiyatı
meşrulaştırır. Bu bilgileri ürün sayfasında yayınlamak, aynı zamanda rakiplerden
ayrışma noktasıdır.</p>

<h2>Kooperatif yapısı</h2>
<p>Kooperatiflerde üye üreticinin görünürlüğü önemlidir. Her üretici için kısa
bir tanıtım sayfası, ortak markanın hikayesini güçledirir ve arama tarafında
ek sayfa kazandırır.</p>

<p><a href="/burhaniye-web-tasarim">Burhaniye</a> ve
<a href="/e-ticaret-sitesi">e-ticaret</a> sayfalarına bakabilirsiniz.</p>

<h2>Etiket ve mevzuat</h2>
<p>Gıda etiketinde bulunması gereken bilgiler mevzuatla belirlidir: üretici,
parti numarası, net miktar, saklama koşulu ve tüketim tarihi. Bu bilgilerin
ürün sayfasında da yer alması hem yasal hem de güven açısından faydalı.</p>

<h2>Hediye paketi</h2>
<p>Yeni yıl ve bayram dönemlerinde hediye paketi talebi belirgin şekilde artar.
Mağazada ayrı bir hediye kategorisi açmak, bu dönemde satış hacmini
yükseltiyor.</p>

<h2>Numune ve tadım</h2>
<p>Küçük boy numune satışı, ilk kez alacak müşterinin tereddüdünü kaldırmanın en
etkili yolu. Mağazada ayrı bir numune kategorisi açmak, tam boy satışa geçiş
oranını yükseltiyor.</p>

<p>İngilizce ve Almanca sayfalar çeviriden ibaret olamaz. Gümrük, teslim süresi
ve saklama koşulları o dilde ayrıca anlatılmalıdır. Kurulumu
<a href="/coklu-dil-web-sitesi">çoklu dil sayfamızda</a> anlatıyoruz.</p>
HTML,
    ],
    [
        'slug' => 'restoran-kafe-qr-menu', 'sort' => 22,
        'title' => 'Restoran ve Kafe QR Menü',
        'meta_title' => 'Restoran ve kafe QR menü — güncel, hızlı, çoklu dil',
        'meta_description' => 'Panelden beş dakikada güncellenen QR menü ve restoran web sitesi. Çoklu dil desteğiyle.',
        'excerpt' => 'Menüyü beş dakikada güncelleyin; masada açılan sayfa saniyede yüklensin.',
        'content' => <<<'HTML'
<h2>QR menünün asıl işi</h2>
<p>QR menü, kağıt menünün fotoğrafını koymak değildir. Masada telefonu açan
misafir yakınlaştırmadan okuyabilmeli, bölümler arasında hızlıca gezebilmeli ve
sayfa bir saniyede açılmalıdır. Fotoğraflı PDF menüler tam tersini yapar.</p>

<h2>Güncellik en büyük avantaj</h2>
<p>Fiyat değiştiğinde ya da bir ürün bittiğinde menüyü beş dakikada
güncelleyebilmek, hem müşteri memnuniyeti hem de personelin işini kolaylaştırır.
Panelden yapılan değişiklik masadaki telefona anında yansır.</p>

<h2>Çoklu dil</h2>
<p>Bölgede yabancı misafir ağırlayan işletmeler için İngilizce ve Almanca menü
zorunlu. Aynı menü tek panelden yönetilir; dil seçimi misafirin telefonunda
yapılır.</p>

<h2>Sitenin geri kalanı</h2>
<h2>Menü düzeni</h2>
<p>Bölümlerin sırası satışı doğrudan etkiler. En çok satan ve en yüksek kârlı
ürünlerin üst bölümlerde olması, mobilde aşağı kaydırma zahmetini azaltır.
Fotoğraf her üründe değil, öne çıkarılmak istenen ürünlerde kullanılmalıdır.</p>

<h2>Alerjen ve içerik bilgisi</h2>
<p>Alerjen bilgisi hem yasal hem de güven açısından önemli. Panelde her ürüne
etiket eklenebilmesi, kağıt menüye sığmayan bu bilgiyi taşımanın en kolay
yolu.</p>

<h2>Sosyal medyayla bağlantı</h2>
<p>Gelen trafiğin büyük bölümü sosyal medyadan geliyorsa, menünün tek dokunuşla
açılması ve rezervasyon ya da paket sipariş butonunun görünür olması gerekir.</p>

<p><a href="/ayvalik-web-tasarim">Ayvalık</a> ve
<a href="/web-tasarim">web tasarım</a> sayfalarımıza göz atın.</p>

<h2>Basılı QR kart</h2>
<p>Masadaki QR kartın okunaklı, dayanıklı ve tek dokunuşla çalışan bir bağlantı
taşıması gerekir. Uzun ve karışık adresler yerine kısa bir adres kullanmak
okuma hatasını azaltır.</p>

<h2>Sezon menüsü</h2>
<p>Bölgede menü mevsime göre değişiyorsa, eski menüyü silmek yerine sezon dışı
yapmak faydalı; gelecek sezon tek tıklamayla geri açılır.</p>

<h2>Panele erişim</h2>
<p>Menüyü güncelleyecek kişiye ayrı bir editör hesabı açıyoruz. Bu hesap yalnızca
menü bölümüne erişir; ayarlara ya da diğer içeriğe dokunamaz. Böylece güncelleme
yetkisi dağıtılırken risk artmıyor.</p>

<p>QR menü tek başına yeterli değil. Akşam yemek yeri arayan bir aile önce
haritaya bakar; çalışma saatlerinin doğru, fotoğrafların gerçek ve telefonun tek
dokunuşla aranabilir olması karar verdirir.</p>
HTML,
    ],
    [
        'slug' => 'emlak-web-sitesi', 'sort' => 23,
        'title' => 'Emlak Web Sitesi',
        'meta_title' => 'Emlak web sitesi — bölge rehberiyle öne geçin',
        'meta_description' => 'Emlak ofisleri için portal bağımlılığını azaltan, bölge rehberiyle güven kuran web siteleri.',
        'excerpt' => 'İlan listelemek yerine bölgeyi anlatan siteler daha çok iş getirir.',
        'content' => <<<'HTML'
<h2>Portal kalır, ama yetmez</h2>
<p>İlanların büyük bölümü portallarda yayınlanmaya devam edecek. Ancak alıcı bir
portalda ilanı gördükten sonra ofisin adını aratır. O anda karşısına çıkan sayfa,
ofisin bölgeyi ne kadar tanıdığını göstermelidir.</p>

<h2>Bölge rehberi neden ise yarar</h2>
<p>Alıcının ilanlarda bulamadığı bilgiler bellidir: kış aylarında kaç daire dolu
kalıyor, market ve eczane yürüyüş mesafesinde mi, aidat neyi kapsıyor, en yakın
sağlık kuruluşu ne kadar uzakta. Bu soruların cevabını veren ofis, ilan
listeleyen on ofisin önüne geçer.</p>

<h2>Yabancı alıcıya satış</h2>
<p>Yabancı alıcının soruları farklıdır: tapu süreci, oturma izni, vergi ve
aidat ödemesi. Bu başlıkların o dilde ayrıca anlatılması gerekir; ilan metnini
çevirmek yetmez.</p>

<h2>Ölçüm</h2>
<h2>İlan yönetimi</h2>
<p>İlanların portalla senkron tutulması zaman alır. Bu yüzden önerimiz genellikle
şudur: portal ana kanal olarak kalsın, sitede yalnızca öne çıkan ilanlar ve
bölge rehberi yayınlansın. Böylece güncelleme yükü düşük kalır.</p>

<h2>Güven unsurları</h2>
<p>Yetki belgesi, ekip tanıtımı, kapanan işlerden örnekler ve gerçek ofis
fotoğrafları; alıcının ilk temasta baktığı şeyler bunlardır.</p>

<h2>Uzun kuyruklu aramalar</h2>
<p>"Denize sıfır daire" gibi genel aramalar yerine "kış aylarında oturulabilir
site" gibi ayrıntılı aramalar, rekabetin düşük olduğu ve niyetin net olduğu
yerlerdir.</p>

<p><a href="/altinoluk-web-tasarim">Altınoluk</a> ve
<a href="/seo-hizmeti">SEO hizmeti</a> sayfalarımız ilgili olabilir.</p>

<h2>Fotoğraf ve video</h2>
<p>Yerinde çekilmiş kısa bir gezinti videosu, on fotoğraftan daha fazla iş
yapıyor. Uzun videolar yerine iki dakikayı geçmeyen, ışıklı saatte çekilmiş
kayıtlar tercih edilmeli.</p>

<h2>Talep formu</h2>
<p>Alıcının bütçesini, oda sayısını ve bölge tercihini soran kısa bir form,
ofisin elinde sürekli güncel bir talep listesi oluşturur.</p>

<h2>Portal ile iş bölümü</h2>
<p>Portal yeni alıcı getirir, site güven kurar. Bu iş bölümünü kabul ettiğinizde
sitede yüzlerce ilan tutmaya çalışmaktan kurtulur, enerjinizi bölge içeriğine
ayırabilirsiniz.</p>

<p>Hangi bölge sayfasının gerçekten arama getirdiği ölçüldüğünde, içerik
yatırımının nereye yapılacağı netleşir. Panelde her form kaydı kaynak sayfasıyla
birlikte tutulur.</p>
HTML,
    ],
    [
        'slug' => 'nakliyat-web-sitesi', 'sort' => 24,
        'title' => 'Nakliyat Web Sitesi',
        'meta_title' => 'Nakliyat web sitesi — teklif formu ve bölge sayfaları',
        'meta_description' => 'Nakliyat firmaları için hızlı teklif formu, bölge sayfaları ve mobil öncelikli site.',
        'excerpt' => 'Aramanın telefona dönmesi için kurulmuş siteler.',
        'content' => <<<'HTML'
<h2>Bu işte karar hızlı verilir</h2>
<p>Nakliyat arayan kişi genellikle taşınma tarihini belirlemiş ve fiyat
karşılaştırması yapıyordur. İlk üç firmadan hangisi hızlı dönerse iş genellikle
onda kalır. Bu yüzden sitenin tek işi vardır: aramayı telefona ya da teklif
formuna çevirmek.</p>

<h2>Teklif formunun doğru kurgusu</h2>
<ul>
  <li>Nereden nereye, hangi tarihte, kaç oda</li>
  <li>Asansör gerekiyor mu, kat bilgisi</li>
  <li>Telefon; e-posta ikinci planda</li>
  <li>Altı alandan uzun form terk oranını artırır</li>
</ul>

<h2>Bölge sayfaları</h2>
<p>Şehirler arası çalışan bir firmanın her güzergah için ayrı sayfası olmalıdır.
Ancak bu sayfaların aynı metnin şehir adı değiştirilmiş hali olmaması gerekir;
bu yöntem yakalandığında tüm sayfaları değersizleştirir.</p>

<h2>Güven unsurları</h2>
<h2>Mobil öncelik</h2>
<p>Bu sektörde ziyaretçilerin neredeyse tamamı telefondan geliyor. Telefon
numarasının ilk ekranda ve tek dokunuşla aranabilir olması, formdan daha fazla
iş getiriyor.</p>

<h2>Sık sorulan sorular</h2>
<p>Eşya sigortası, ambalaj dahil mi, asansörlü taşıma ücreti, hafta sonu
çalışma; bu soruların cevabını sayfada yazmak hem telefon trafiğini azaltıyor
hem de aramada karşılık buluyor.</p>

<h2>İş fotoğrafları</h2>
<p>Gerçek iş fotoğrafları, stok görsellerden çok daha ikna edici. Araç filosu ve
ambalaj malzemesi fotoğrafları teklif aşamasında fiyattan sonra en çok bakılan
yer.</p>

<p><a href="/web-tasarim">Web tasarım</a> ve
<a href="/edremit-web-tasarim">Edremit</a> sayfalarımıza bakabilirsiniz.</p>

<h2>Fiyat aralığı vermek</h2>
<p>Kesin fiyat vermek zor olsa da güzergah ve oda sayısına göre bir aralık
yayınlamak, hem gereksiz aramayı azaltıyor hem de fiyat gizleyen rakiplerin
önüne geçiyor.</p>

<h2>Depolama hizmeti</h2>
<p>Taşınma tarihleri uyuşmayan müşteriler için kısa süreli depolama seçeneği
ayrı bir gelir kalemi; sitede ayrı bir sayfayla anlatılması bu talebi
görünür kılıyor.</p>

<h2>Sezon yoğunluğu</h2>
<p>Taşınma trafiği haziran-eylül arasında zirve yapar. Bu döneme hazırlık için
sayfaların ve teklif formunun nisan ayında hazır olması gerekir; sezon içinde
yapılan değişiklikler geç kalır.</p>

<p>Sigorta kapsamı, ambalaj malzemesi, araç filosu ve gerçek iş fotoğrafları;
bu dört başlık teklif aşamasında fiyattan sonra en çok bakılan yerlerdir.</p>
HTML,
    ],
    [
        'slug' => 'tabela-matbaa-web-sitesi', 'sort' => 25,
        'title' => 'Tabela ve Matbaa Web Sitesi',
        'meta_title' => 'Tabela ve matbaa web sitesi — iş portföyü ve teklif',
        'meta_description' => 'Tabela ve matbaa işletmeleri için portföy odaklı, teklif formu çalışan web siteleri.',
        'excerpt' => 'Yapılan işin fotoğrafı, en iyi satış metnidir.',
        'content' => <<<'HTML'
<h2>Bu işte portföy konuşur</h2>
<p>Tabela ve matbaa işinde müşteri, yapılan işin fotoğrafına bakarak karar verir.
Uzun tanıtım metinleri yerine iyi çekilmiş on iş fotoğrafı çok daha fazla teklif
getirir.</p>

<h2>Kategori bazlı düzen</h2>
<p>İşler kategoriye ayrılmalı: ışıklı tabela, kutu harf, araç giydirme, dijital
baskı, kartvizit ve katalog. Müşteri kendi işini aradığında doğrudan o sayfaya
düşmelidir; hepsini tek sayfada göstermek arama tarafında kayıp demektir.</p>

<h2>Teklif için gereken bilgi</h2>
<p>Bu işte teklif ölçü ve malzemeye bağlıdır. Formun ölçü, adet ve malzeme
sorması, gereksiz telefon trafiğini azaltır. Müşterinin görsel yükleyebilmesi
süreci belirgin şekilde kısaltır.</p>

<h2>Yerel arama</h2>
<h2>Malzeme ve süre bilgisi</h2>
<p>Müşterinin en çok sorduğu iki şey malzeme seçenekleri ve teslim süresi.
Bunları kategori sayfalarında açıkça yazmak, teklif öncesi gereksiz yazışmayı
ortadan kaldırıyor.</p>

<h2>Dosya hazırlama rehberi</h2>
<p>Baskıya hazır dosya formatı, çözünürlük ve taşma payı gibi konularda kısa bir
rehber sayfası, hem müşteriyi rahatlatıyor hem de yanlış dosyadan kaynaklanan
yeniden baskı maliyetini düşürüyor.</p>

<h2>Kurumsal müşteri</h2>
<p>Düzenli çalışılan kurumsal müşteriler için geçmiş işlerin kayıtlı olduğu bir
yapı, tekrar siparişi kolaylaştırıyor. Aynı tabelanın ikinci şubesi için
yeniden ölçü almaya gerek kalmıyor.</p>

<p><a href="/web-tasarim">Web tasarım</a> ve
<a href="/balikesir-web-tasarim">Balıkesir</a> sayfalarımız ilgili olabilir.</p>

<h2>Montaj ve izin</h2>
<p>Işıklı tabelada belediye izni ve montaj koşulları müşterinin bilmediği ama
süreci uzatan başlıklar. Bu adımları anlatan bir sayfa, teklif aşamasında
yasanan şaşırmayı ortadan kaldırıyor.</p>

<h2>Aciliyet</h2>
<p>Acil iş talebi bu sektörde yaygın. Aynı gün teslim yapılan ürün gruplarının
sitede belirtilmesi, o talebin doğrudan size gelmesini sağlıyor.</p>

<h2>Numune ve renk</h2>
<p>Ekranda görünen renk ile basılan renk her zaman aynı değildir. Bunu sayfada
açıkça yazmak ve gerektiğinde numune baskı önerisi sunmak, teslim sonrası
itirazların büyük bölümünü ortadan kaldırıyor.</p>

<p>Tabela işi büyük ölçüde yereldir. Hizmet verilen ilçelerin her biri için ayrı
ve gerçekten farklı sayfalar, bölgedeki aramaların karşılığını verir.
Yaklaşımımızı <a href="/seo-hizmeti">SEO hizmeti sayfasında</a> anlatıyoruz.</p>
HTML,
    ],
];
