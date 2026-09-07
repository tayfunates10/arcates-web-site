<?php
/**
 * Baslangic sayfa icerikleri — ana sayfalar, hizmetler ve sektorler.
 *
 * DOCS.md 4.1, 4.2, 4.4 — sayfa envanteri ve URL haritasi.
 *
 * Bu metinler baslangic noktasidir; hepsi panelden duzenlenir. Sablona
 * gomulmez, veritabanina yazilir.  DOCS.md 1 (ilke 4)
 *
 * Ilce sayfalari ayri dosyadadir (`locations.php`); bolum 4.7 geregi her biri
 * en az 500 kelime OZGUN metin icerir ve birbirinin kopyasi degildir.
 */

declare(strict_types=1);

return [

    // --- 4.1 Ana sayfalar ---------------------------------------------------

    [
        'type' => 'page', 'slug' => 'hakkimizda', 'sort' => 1,
        'title' => 'Hakkimizda',
        'meta_title' => 'Arcates Yazilim — Edremit Korfezi web ajansi',
        'meta_description' => 'Edremit Korfezi bolgesinde web tasarim, e-ticaret ve yazilim gelistiren kucuk bir ekibiz. Yerinde gorusuyor, olculebilir is yapiyoruz.',
        'excerpt' => 'Korfezde calisan, isini yerinde ogrenen kucuk bir yazilim ekibi.',
        'content' => <<<'HTML'
<h2>Nereden geliyoruz</h2>
<p>Arcates Yazilim, Edremit Korfezi bolgesindeki isletmelere web tasarim, e-ticaret
ve yazilim hizmeti veren kucuk bir ekiptir. Buyuk sehirlerdeki ajanslarin uzaktan
verdigi hizmetin bolgedeki isletmelere yetmedigini gordugumuz icin kuruldu.</p>

<p>Bir zeytinyagi ureticisinin hasat takvimini, bir pansiyonun sezon disi doluluk
kaygisini ya da bir emlak ofisinin yaz aylarindaki talep patlamasini uzaktan
anlamak zor. Biz ayni bolgede yasiyoruz; musterilerimizin isini yerinde
goruyoruz.</p>

<h2>Nasil calisiyoruz</h2>
<p>Once dinliyoruz. Isletmenin gercekten hangi musteriyi aradigi, hangi aramalarda
gorunmesi gerektigi ve rakiplerinin ne yaptigi netlesmeden tasarima
baslamiyoruz. Bu gorusme cogu zaman isletmenin kendi mekaninda oluyor.</p>

<p>Sonra kuruyoruz. Tasarim, icerik ve teknik kurulum birlikte ilerliyor; her sayfa
hiz ve arama gorunurlugu icin olculuyor. Yayindan sonra da birakmiyoruz: hangi
sayfanin gercekten musteri getirdigini olcup icerigi buna gore duzenliyoruz.</p>

<h2>Neye inaniyoruz</h2>
<ul>
  <li><strong>Hiz her seyden once gelir.</strong> Mobilde gec acilan bir site,
      ne kadar guzel olursa olsun musteri kaybettirir.</li>
  <li><strong>Yonetilemeyen site olu sitedir.</strong> Her metin, her gorsel ve
      her fiyat isletmenin kendisi tarafindan degistirilebilmeli.</li>
  <li><strong>Olculmeyen is buyutulemez.</strong> Hangi sayfanin telefon
      getirdigini bilmiyorsaniz neyi iyilestireceginizi de bilemezsiniz.</li>
</ul>

<h2>Ekip ve calisma bicimi</h2>
<p>Kucuk bir ekibiz ve bu bilincli bir tercih. Ayni anda uc dort projeden
fazlasini almiyoruz; boylece her isin basinda ayni kisiler duruyor ve musteri
her seferinde bastan anlatmak zorunda kalmiyor.</p>

<p>Alt yuklenici kullanmiyoruz. Tasarim, yazilim ve icerik ayni ekip icinde
yurutuluyor. Bu, sorumlulugun bolunmemesi anlamina geliyor: bir sorun
ciktiginda kimin cozecegi bellidir.</p>

<h2>Neyi yapmiyoruz</h2>
<p>Hazir sablon satmiyoruz, kiralik panel kurmuyoruz ve musteriyi teknik olarak
kendimize bagimli hale getirmiyoruz. Sitenin tum dosyalari ve veritabani
musterinindir; isteyen her an alip baska bir ekiple devam edebilir.</p>

<p>Kisa vadede sonuc vaat eden ama uzun vadede zarar veren yontemlerden de uzak
duruyoruz: uydurma yorum, satin alinmis baglanti ve ayni metnin bolge adi
degistirilerek cogaltilmasi bunlarin basinda geliyor.</p>

<h2>Bolge disindan gelen isler</h2>
<p>Korfez disindan da is aliyoruz ancak burada bir ayrimimiz var: bolgeyi
tanimadigimiz bir yerde "yerel SEO" vaadinde bulunmuyoruz. Teknik kurulum,
tasarim ve e-ticaret isleri her yerde ayni kalitede yapilabilir; bolge bilgisi
gerektiren isler ise ancak yerinde ogrenilerek yapilir.</p>

<p>Bolgedeki calismalarimizi <a href="/referanslar">referanslar sayfasinda</a>
gorebilir, aklinizdaki proje icin <a href="/iletisim">bize yazabilirsiniz</a>.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'iletisim', 'sort' => 2,
        'title' => 'Iletisim',
        'meta_title' => 'Iletisim — Arcates Yazilim',
        'meta_description' => 'Projeniz icin ucretsiz gorusme. Edremit Korfezi bolgesinde yerinde gorusme yapiyoruz. Formu doldurun, ayni hafta donus yapalim.',
        'excerpt' => 'Projenizi konusalim. Formu doldurun, ayni hafta fiyat ve takvim gonderelim.',
        'content' => <<<'HTML'
<h2>Nasil ilerliyoruz</h2>
<p>Asagidaki formu doldurdugunuzda once kisa bir gorusme yapiyoruz. Bu gorusmede
isinizi, hedefinizi ve butcenizi konusuyoruz. Ardindan ayni hafta icinde yazili
fiyat ve takvim gonderiyoruz.</p>

<p>Edremit, Akcay, Altinoluk, Burhaniye, Havran, Gomec, Ayvalik ve Balikesir
merkezde yerinde gorusme yapabiliyoruz. Bolge disindaki isler icin gorusmeler
cevrimici yurutuluyor.</p>

<h2>Ne kadar surede donus aliyorum</h2>
<p>Is gunlerinde gelen mesajlara ayni gun, hafta sonu gelenlere pazartesi donus
yapiyoruz. Acil bir durumda telefonla ulasmak her zaman daha hizli.</p>

<h2>Gorusmeye hazirlik</h2>
<p>Ilk gorusmeyi verimli gecirmek icin su uc sorunun cevabini dusunmus olmaniz
yeterli: Sitenin size getirmesini istediginiz sey nedir; telefon mu, siparis mi,
rezervasyon mu? Su an musterileriniz sizi nasil buluyor? Rakipleriniz arasinda
sitesini begendiginiz biri var mi?</p>

<p>Elinizde logo, fotograf ya da eski site bilgisi varsa gorusmeden once
paylasmaniz sureci hizlandirir; ancak sart degil.</p>

<h2>Yerinde gorusme</h2>
<p>Edremit, Akcay, Altinoluk, Burhaniye, Havran, Gomec, Ayvalik ve Balikesir
merkezde isletmenizi ziyaret ediyoruz. Gorusme ucretsizdir ve genellikle bir
saati gecmez. Bolge disindaki isler cevrimici yurutuluyor.</p>

<h2>Bilgileriniz</h2>
<p>Formda verdiginiz bilgiler yalnizca size donus yapmak icin kullanilir,
ucuncu kisilerle paylasilmaz ve saklama suresi dolduğunda otomatik silinir.
Ayrintili bilgi <a href="/kvkk">aydinlatma metnimizde</a>.</p>

<h2>Telefon mu form mu</h2>
<p>Acil bir konu icin telefon her zaman daha hizli. Ancak proje anlatmak icin
form daha verimli: yazarken ihtiyacinizi netlestirmis oluyorsunuz ve biz de
gorusmeye hazirlikli geliyoruz. Uzun uzun yazmaya gerek yok; birkac cumle
yeterli.</p>

<h2>Gorusmede ne konusuyoruz</h2>
<p>Isletmenizin su anki durumu, musteri profiliniz, rakipleriniz ve butce
araliginiz. Bu dort baslik netlestiginde teklif hazirlamak birkac gun suruyor.
Teklif yazili gelir ve icinde kapsam, sure ve odeme plani ayri ayri belirtilir;
sonradan surpriz kalem cikmaz.</p>

<h2>Calismaya baslamadan once</h2>
<p>Teklifi onayladiktan sonra bir baslangic toplantisi yapiyoruz. Bu toplantida
icerik sorumlusu, gorsel kaynagi ve teslim tarihleri belirleniyor. Projenin
gecikmesinin en yaygin sebebi teknik degil, icerik bekleyisidir; bunu bastan
planlamak sureyi kisaltiyor.</p>

<p>Sik sorulan sorularin cevabini <a href="/sss">SSS sayfasinda</a>,
fiyat araliklarini <a href="/fiyatlar">fiyatlar sayfasinda</a> bulabilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'fiyatlar', 'sort' => 3,
        'title' => 'Fiyatlar',
        'meta_title' => 'Web sitesi fiyatlari — Edremit ve Balikesir',
        'meta_description' => 'Kurumsal site, e-ticaret ve rezervasyon sistemi fiyat araliklari. Ne odedigini bilerek karar verin; gizli kalem yok.',
        'excerpt' => 'Ne odedigini bilerek karar verin. Gizli kalem yok, her sey yazili.',
        'content' => <<<'HTML'
<h2>Neden aralik veriyoruz</h2>
<p>Web sitesi fiyati, sayfa sayisi kadar isin kendisine de baglidir. Alti sayfalik
bir tanitim sitesiyle bes yuz urunlu bir magaza ayni is degildir. Bu yuzden
sabit tek bir rakam yerine aralik veriyor, gorusmeden sonra kesin fiyati yazili
gonderiyoruz.</p>

<h2>Fiyati belirleyen basliklar</h2>
<ul>
  <li><strong>Sayfa ve icerik hacmi.</strong> Metinleri siz mi veriyorsunuz, biz mi
      yaziyoruz?</li>
  <li><strong>Dil sayisi.</strong> Yalnizca Turkce mi, yoksa Ingilizce ve Almanca
      da olacak mi?</li>
  <li><strong>Islevler.</strong> Rezervasyon, sepet, odeme, stok takibi gibi
      basliklar isin buyuklugunu degistirir.</li>
  <li><strong>Gorsel uretimi.</strong> Mekanda fotograf cekimi gerekiyor mu?</li>
</ul>

<h2>Odeme ve sure</h2>
<p>Isler genellikle iki taksitle yurutulur: baslangicta ve yayinda. Kurumsal bir
site ortalama uc haftada, e-ticaret ve rezervasyon projeleri alti ila sekiz
haftada yayina alinir.</p>

<h2>Yayindan sonra</h2>
<p>Her proje bir aylik ucretsiz destekle teslim edilir. Sonrasinda isterseniz
<a href="/web-sitesi-bakim">aylik bakim anlasmasi</a> yapiyoruz; guncelleme,
yedekleme, guvenlik ve icerik destegi bu kapsamda.</p>

<h2>Neler fiyata dahil</h2>
<p>Her projede su kalemler fiyata dahildir: tasarim, kurulum, panel egitimi,
temel SEO kurulumu, Google Isletme Profili duzenlemesi ve bir aylik destek.
Alan adi ve barindirma ucreti ayridir; dilerseniz mevcut saglayicinizla devam
edebilirsiniz.</p>

<h2>Ne fiyati artirir</h2>
<p>Metinlerin bizim tarafimizdan yazilmasi, mekanda fotograf cekimi, ikinci ve
ucuncu dil, ozel hesaplama gerektiren formlar ve dis sistemlerle entegrasyon
fiyati yukselten basliklardir. Bunlarin her biri gorusmede ayri kalem olarak
konusulur.</p>

<h2>Butcesi sinirli isletmeler icin</h2>
<p>Butce kisitliysa onerimiz sudur: once dogru kurulmus kucuk bir site ve duzgun
bir harita kaydi. Bu ikisi calismaya basladiktan sonra gelen talebe gore
buyutmek her zaman mumkun. Bastan buyuk yatirim yapip icerigi guncelleyemeyen
isletme ikinci yilda daha kotu durumda oluyor.</p>

<p>Bakim kosullarini <a href="/web-sitesi-bakim">bakim sayfasinda</a>,
sik sorulan sorulari <a href="/sss">SSS sayfasinda</a> bulabilirsiniz.</p>

<h2>Fiyat disi maliyetler</h2>
<p>Alan adi yillik ucreti, barindirma ve varsa sanal pos komisyonu proje
fiyatinin disindadir. Bu kalemler dogrudan saglayiciya odenir; araya girip
uzerine ekleme yapmiyoruz. Mevcut saglayicinizla devam etmek isterseniz o da
mumkun.</p>

<p>Kendi projeniz icin net rakam almak isterseniz
<a href="/iletisim">iletisim formunu</a> doldurmaniz yeterli.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'referanslar', 'sort' => 4,
        'title' => 'Referanslar',
        'meta_title' => 'Referanslar — Arcates Yazilim',
        'meta_description' => 'Edremit Korfezi bolgesinde yayina aldigimiz web siteleri, e-ticaret magazalari ve rezervasyon sistemleri.',
        'excerpt' => 'Korfezde yayina aldigimiz projelerden bir bolumu.',
        'content' => <<<'HTML'
<h2>Neye gore secilmis calismalar</h2>
<p>Asagidaki listede farkli sektorlerden ve farkli olceklerden ornekler var.
Amac vitrin olusturmak degil; benzer bir isin nasil kurgulandigini gostermek.
Her calismanin sayfasinda isletmenin ihtiyaci, yapilan isler ve varsa yayin
sonrasi olculen sonuc yaziyor.</p>

<h2>Sektorunuzden ornek</h2>
<p>Kendi sektorunuzden bir calisma gormek isterseniz
<a href="/iletisim">bize yazin</a>. Bazi musterilerimiz calismalarinin
yayinlanmasini istemedigi icin listede gorunmeyen isler de var; bunlari
gorusmede paylasabiliyoruz.</p>

<h2>Calismanin sonrasi</h2>
<p>Listedeki islerin buyuk bolumunde yayin sonrasi da birlikte calismaya devam
ettik. Bir sitenin gercek degeri, yayina alindigi gun degil altinci ayinda
belli olur: hangi sayfa telefon getiriyor, hangi icerik bosa yazilmis, nerede
duzeltme gerekiyor.</p>

<h2>Bolgeye gore</h2>
<h2>Referans vermek</h2>
<p>Calistigimiz isletmelerin bir bolumu, kendilerine ulasan yeni musterilerle
konusmayi kabul ediyor. Karar vermeden once benzer bir isletmeyle gorusmek
isterseniz bunu ayarlayabiliyoruz; bizim anlattigimizdan cok daha degerli bir
bilgi kaynagi.</p>

<h2>Sayilar</h2>
<p>Bazi calismalarin sayfasinda yayin sonrasi olculen degisim de yaziyor: gelen
form sayisi, arama gorunurlugu ya da dogrudan rezervasyon orani. Bu rakamlar
isletmenin izniyle paylasiliyor ve abartilmiyor; olculen ne ise o yaziliyor.</p>

<p>Calismalarin buyuk bolumu Korfez ilcelerinde. Bolgeye ozel yaklasimimizi
<a href="/edremit-web-tasarim">Edremit</a>,
<a href="/ayvalik-web-tasarim">Ayvalik</a> ve
<a href="/burhaniye-web-tasarim">Burhaniye</a> sayfalarinda anlatiyoruz.
Sektorel yaklasim icin <a href="/otel-pansiyon-web-sitesi">konaklama</a> ve
<a href="/zeytinyagi-e-ticaret-sitesi">zeytinyagi</a> sayfalarina
bakabilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'blog', 'sort' => 5,
        'title' => 'Blog',
        'meta_title' => 'Blog — web tasarim ve yerel SEO rehberleri',
        'meta_description' => 'Kucuk isletmeler icin web sitesi, yerel SEO ve dijital pazarlama uzerine uygulanabilir yazilar.',
        'excerpt' => 'Kucuk isletmeler icin uygulanabilir rehberler.',
        'content' => <<<'HTML'
<h2>Burada ne yaziyoruz</h2>
<p>Gorusmelerde tekrar tekrar gelen sorulari burada topluyoruz. Her yazi tek bir
soruyu, uygulanabilir adimlarla cevaplar. Amac uzun teorik metinler degil;
okuyan isletmecinin ayni gun uygulayabilecegi bir sey birakmak.</p>

<h2>Sik yazdigimiz konular</h2>
<ul>
  <li>Yerel aramada gorunurluk ve Google Isletme Profili duzeni</li>
  <li>Kucuk isletme icin site hizi ve mobil kullanim</li>
  <li>Rezervasyon ve siparis formlarinda donusum</li>
  <li>Coklu dilde yayin ve yabanci musteriye ulasma</li>
  <li>Yedekleme, guvenlik ve bakim aliskanliklari</li>
</ul>

<h2>Neden kisa yaziyoruz</h2>
<p>Isletmecinin okumaya ayirdigi sure sinirli. Bu yuzden yazilari tek bir soruya
odakli tutuyor, uygulanabilir adimlarla bitiriyoruz. Uzun rehberler yerine
birbirine baglanan kisa yazilar hem daha cok okunuyor hem de aramada daha net
karsilik buluyor.</p>

<h2>Konu onerisi</h2>
<h2>Yazilari kim yaziyor</h2>
<p>Yazilar, isleri fiilen yapan ekip tarafindan yaziliyor. Bu yuzden ornekler
gercek projelerden geliyor ve genel gecer tavsiye yerine bolgede karsilastigimiz
somut durumlar anlatiliyor.</p>

<h2>Guncelleme</h2>
<p>Eskiyen yazilari silmek yerine guncelliyoruz. Bir konu degistiginde yazinin
altina ne zaman ve neyin degistigi ekleniyor; boylece okuyan kisi bilginin ne
kadar taze oldugunu goruyor.</p>

<p>Merak ettiginiz bir konu varsa <a href="/iletisim">yazin</a>, siradaki yaziyi
ona ayiralim. Hizmetlerimizi <a href="/web-tasarim">web tasarim</a> ve
<a href="/seo-hizmeti">SEO hizmeti</a> sayfalarindan inceleyebilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'sss', 'sort' => 6,
        'title' => 'Sik Sorulan Sorular',
        'meta_title' => 'Sik sorulan sorular — Arcates Yazilim',
        'meta_description' => 'Web sitesi sureci, fiyatlandirma, bakim ve SEO hakkinda en cok sorulan sorular ve cevaplari.',
        'excerpt' => 'Aklinizdaki sorunun cevabi burada yoksa bize yazin.',
        'content' => <<<'HTML'
<h2>Burada ne var</h2>
<p>Asagidaki sorular ilk gorusmelerde en sik gelenler. Fiyat, sure, sahiplik,
bakim ve arama gorunurlugu basliklarinda net cevaplar bulacaksiniz. Cevaplar
genel gecer degil; bolgede yaptigimiz islerden cikan gercek yanitlar.</p>

<h2>Sayfaya ozel sorular</h2>
<p>Her hizmet, ilce ve sektor sayfasinin altinda o konuya ozel sorular da var.
Sezonluk bir pansiyon isletiyorsaniz
<a href="/akcay-web-tasarim">Akcay sayfasindaki</a> soru,
zeytinyagi uretiyorsaniz
<a href="/burhaniye-web-tasarim">Burhaniye sayfasindaki</a> soru sizin icin
daha anlamli olabilir.</p>

<h2>Neden hepsini tek sayfada toplamiyoruz</h2>
<p>Arama yapan kisi genellikle tek bir soru sorar ve o sorunun cevabini tasiyan
sayfaya duser. Bu yuzden sorulari ilgili sayfalara dagitiyor, burada yalnizca
herkesi ilgilendirenleri topluyoruz.</p>

<h2>Cevabini bulamadiniz mi</h2>
<h2>Sorulari nasil seciyoruz</h2>
<p>Buradaki sorular uydurulmus degil; gorusmelerde ve form mesajlarinda fiilen
gelen sorulardan seciliyor. Bir soru uc dort kez tekrarlandiginda listeye
ekleniyor ve cevabi guncel tutuluyor.</p>

<h2>Cevaplarin sinirlari</h2>
<p>Her isletme farkli oldugu icin buradaki cevaplar genel cerceve sunar. Kendi
durumunuza ozel bir yanit icin kisa bir gorusme her zaman daha isabetli olur ve
bu gorusme ucretsizdir.</p>

<p>Aradiginiz cevap burada yoksa <a href="/iletisim">iletisim formunu</a>
doldurmaniz yeterli. Gelen sorulari duzenli olarak bu sayfaya ekliyoruz;
sizin sorunuz da baskasinin isine yarayabilir.
<a href="/fiyatlar">Fiyat araliklarina</a> da bakabilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'kvkk', 'sort' => 90,
        'title' => 'KVKK Aydinlatma Metni',
        'meta_title' => 'KVKK Aydinlatma Metni — Arcates Yazilim',
        'meta_description' => '6698 sayili Kisisel Verilerin Korunmasi Kanunu kapsaminda aydinlatma metni.',
        'excerpt' => 'Kisisel verilerinizi neden topluyoruz, ne kadar sakliyoruz, kimlerle paylasiyoruz.',
        'content' => <<<'HTML'
<h2>Veri sorumlusu</h2>
<p>Bu metin, 6698 sayili Kisisel Verilerin Korunmasi Kanunu kapsaminda,
sitemiz uzerinden toplanan kisisel verilerle ilgili olarak sizi
bilgilendirmek amaciyla hazirlanmistir.</p>

<h2>Hangi verileri topluyoruz</h2>
<p>Teklif formunu doldurdugunuzda ad soyad, telefon, e-posta adresi, ilgilendiginiz
hizmet ve mesajiniz kaydedilir. Formu hangi sayfadan doldurdugunuz, siteye nereden
geldiginiz, tercih ettiginiz dil, IP adresiniz ve tarayici bilginiz de teknik kayit
olarak saklanir.</p>

<h2>Neden topluyoruz</h2>
<ul>
  <li>Talebinize donus yapabilmek ve teklif hazirlayabilmek</li>
  <li>Hizmet kalitemizi olcmek ve iyilestirmek</li>
  <li>Yasal yukumluluklerimizi yerine getirmek</li>
  <li>Kotu niyetli ve otomatik gonderimleri engellemek</li>
</ul>

<h2>Ne kadar sakliyoruz</h2>
<p>Form kayitlari, iliskinin sona ermesinden itibaren yasal saklama suresi boyunca
tutulur ve suresi dolan kayitlar otomatik olarak silinir. Ziyaret kayitlari doksan
gun sonra kisisellestirilemez toplu istatistige donusturulur.</p>

<h2>Kimlerle paylasiyoruz</h2>
<p>Verileriniz reklam veya pazarlama amaciyla ucuncu kisilerle paylasilmaz.
Yalnizca yasal olarak zorunlu hallerde ve yetkili kamu kurumlariyla paylasilir.</p>

<h2>Haklariniz</h2>
<h2>Verilerin guvenligi</h2>
<p>Toplanan veriler sifreli baglanti uzerinden iletilir ve erisimi sinirli bir
veritabaninda saklanir. Panel erisimi rol bazlidir; icerik editorleri form
kayitlarini goremez. Basarisiz giris denemeleri kaydedilir ve tekrarlanan
denemelerde erisim gecici olarak kilitlenir.</p>

<h2>Otomatik silme</h2>
<p>Saklama suresi panelden belirlenir ve gunluk calisan bir gorev suresi dolan
kayitlari siler. Bu islem elle mudahale gerektirmez; boylece kayitlarin
unutulup birikmesi engellenir.</p>

<h2>Cerezler</h2>
<p>Sitede reklam ya da izleme cerezi kullanilmaz. Ziyaret olcumu icin kimlik
tasimayan, geri cevrilemeyen bir karma kullanilir. Ayrintili bilgi
<a href="/gizlilik-politikasi">gizlilik politikasi sayfamizda</a>.</p>

<h2>Basvuru ve yanit suresi</h2>
<p>Kanun kapsamindaki taleplerinize en gec otuz gun icinde yanit veriyoruz.
Basvurunuzu iletisim sayfasindaki adres uzerinden yazili olarak iletmeniz
yeterli. Talebiniz ucretsiz sonuclandirilir.</p>

<h2>Degisiklikler</h2>
<p>Bu metinde degisiklik yapildiginda guncel surum bu sayfada yayinlanir.
Onemli bir degisiklik olmasi halinde ayrica bilgilendirme yapilir.</p>

<p>Kanunun 11. maddesi kapsaminda; verilerinizin islenip islenmedigini ogrenme,
duzeltilmesini veya silinmesini isteme ve isleme faaliyetine itiraz etme
hakkiniz vardir. Bu haklarinizi kullanmak icin
<a href="/iletisim">iletisim sayfasindaki</a> adresten bize ulasabilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'gizlilik-politikasi', 'sort' => 91,
        'title' => 'Gizlilik Politikasi',
        'meta_title' => 'Gizlilik Politikasi — Arcates Yazilim',
        'meta_description' => 'Sitemizde hangi verilerin toplandigini, cerez kullanimini ve guvenlik onlemlerini aciklayan gizlilik politikasi.',
        'excerpt' => 'Sitede hangi verilerin toplandigi ve nasil korundugu.',
        'content' => <<<'HTML'
<h2>Cerez kullanimi</h2>
<p>Bu sitede reklam veya izleme cerezi kullanilmaz. Yalnizca oturum yonetimi icin
gerekli teknik cerez bulunur; bu cerez panelde oturum acildiginda olusur ve
tarayici kapatildiginda silinir.</p>

<h2>Ziyaret kayitlari</h2>
<p>Hangi sayfalarin ne kadar ziyaret edildigini olcuyoruz. Bu olcum icin ziyaretci
kimligi degil, IP ve tarayici bilgisinden uretilen ve geri cevrilemeyen bir karma
kullanilir. Arama motoru robotlari ayri isaretlenir ve istatistige girmez.</p>

<h2>Ucuncu taraf hizmetler</h2>
<p>Sitede yalnizca Google Fonts uzerinden yazi tipi yuklenir. Bunun disinda ucuncu
taraf bir olcum veya reklam betigi calistirilmaz.</p>

<h2>Guvenlik</h2>
<p>Site tamamen HTTPS uzerinden yayin yapar. Form gonderimleri dogrulama
belirteciyle korunur, yuklenen dosyalar tur ve icerik acisindan denetlenir,
veritabani sorgulari hazirlanmis ifadelerle calisir.</p>

<h2>Veri saklama sureleri</h2>
<p>Form kayitlari panelde belirlenen sure boyunca tutulur; varsayilan sure iki
yildir ve suresi dolan kayitlar gunluk gorevle otomatik silinir. Ham ziyaret
kayitlari doksan gun sonra kisisellestirilemez toplu istatistige donusturulur ve
ayrintili kayit silinir.</p>

<h2>Erisim yetkileri</h2>
<p>Panelde iki rol vardir. Yonetici tum bolumlere erisir; editor yalnizca icerik
bolumlerini gorur ve form kayitlarina ya da ayarlara dokunamaz. Her islem, kim
tarafindan ne zaman yapildigiyla birlikte kaydedilir.</p>

<h2>Yedekler</h2>
<p>Veritabani gunluk olarak yedeklenir ve yedekler sunucuda web erisimine kapali
bir klasorde tutulur. Son on yedek saklanir, eskiler otomatik silinir.</p>

<p>Kisisel verilerin islenmesine iliskin ayrintili bilgi
<a href="/kvkk">KVKK aydinlatma metnimizde</a>.</p>

<h2>Form gonderimlerinde toplananlar</h2>
<p>Teklif formunu doldurdugunuzda, formu hangi sayfadan gonderdiginiz ve siteye
hangi kaynaktan geldiginiz de kaydedilir. Bu bilgi kisisel bir profil
olusturmak icin degil, hangi sayfanin ise yaradigini olcmek icin kullanilir.</p>

<h2>Spam korumasi</h2>
<p>Otomatik gonderimleri engellemek icin formda gorunmez bir alan ve zaman
kontrolu bulunur. Bu kontroller icin ek bir kisisel veri toplanmaz.</p>

<p>Kisisel verilerin islenmesine iliskin ayrintili bilgi icin
<a href="/kvkk">KVKK aydinlatma metnimize</a> bakabilirsiniz.</p>
HTML,
    ],

    // --- 4.2 Hizmet sayfalari ----------------------------------------------

    [
        'type' => 'service', 'slug' => 'web-tasarim', 'sort' => 10,
        'title' => 'Web Tasarim',
        'meta_title' => 'Web tasarim — Edremit ve Balikesir | Arcates',
        'meta_description' => 'Mobilde hizli acilan, aramalarda gorunur ve yonetimi kolay kurumsal web siteleri. Edremit Korfezi bolgesinde yerinde hizmet.',
        'excerpt' => 'Mobilde hizli acilan, aramalarda gorunur, yonetimi kolay kurumsal siteler.',
        'content' => <<<'HTML'
<h2>Kurumsal site neye yarar</h2>
<p>Bir isletmenin web sitesi katalog degildir; musterinin size ulasmasinin en kisa
yoludur. Bu yuzden tasarima degil, ziyaretcinin ne aradigina bakarak basliyoruz.
Telefon numarasi bulunamayan, mobilde gec acilan ya da aramalarda cikmayan bir
site guzel olsa da ise yaramaz.</p>

<h2>Neler yapiyoruz</h2>
<ul>
  <li>Isletmeye ozel tasarim; hazir sablon uzerine kurulmuyor</li>
  <li>Mobil oncelikli duzen ve hiz optimizasyonu</li>
  <li>Panelden yonetilebilir her metin, gorsel ve baglanti</li>
  <li>Yerel arama icin baslik, aciklama ve yapisal veri kurulumu</li>
  <li>Google Isletme Profili ile uyumlu iletisim bilgileri</li>
</ul>

<h2>Sure ve sonrasi</h2>
<p>Kurumsal bir site ortalama uc haftada yayina alinir. Yayindan sonra bir ay
ucretsiz destek verilir; sonrasinda dilerseniz
<a href="/web-sitesi-bakim">bakim anlasmasi</a> ile devam ediyoruz.</p>

<h2>Tasarimdan once cevaplanan sorular</h2>
<p>Ise renk ve yazi tipi secerek baslamiyoruz. Once su sorulari cevapliyoruz:
Bu siteye giren kisi ne ariyor? Aradigini bulunca ne yapmasini istiyoruz?
Rakipler ayni aramada ne gosteriyor? Bu uc sorunun cevabi netlestiginde sayfa
duzeni kendiliginden ortaya cikar.</p>

<p>Cogu isletmede cevap sasirtici sekilde basittir: ziyaretci fiyat araligini,
calisma saatini ve konumu ariyordur. Bu bilgiler ilk ekranda yoksa tasarimin
geri kalani bir ise yaramaz.</p>

<h2>Hiz neden pazarlik konusu degil</h2>
<p>Mobilde uc saniyeden gec acilan bir sayfada ziyaretcilerin yaklasik yarisi
sayfayi terk eder. Bu yuzden yuklenen her gorsel uc olcude ve WebP bicimiyle
yeniden uretilir, tek bir stil ve tek bir betik dosyasi kullanilir, harici
kaynak olarak yalnizca yazi tipi cagrilir.</p>

<h2>Erisilebilirlik</h2>
<p>Klavyeyle gezinebilme, gorunur odak halkasi, yeterli renk kontrasti ve
gorsellerde alt metni; bunlar hem yasal bir gereklilik hem de arama
motorlarinin degerlendirdigi basliklar. Teslim ettigimiz her sayfa bu
kontrollerden geciyor.</p>

<p>Bolgeye ozel calismalarimizi <a href="/edremit-web-tasarim">Edremit</a> ve
<a href="/ayvalik-web-tasarim">Ayvalik</a> sayfalarinda gorebilirsiniz.</p>

<h2>Teslim edilenler</h2>
<p>Her projede su dosya ve erisimler musteriye teslim edilir: sitenin tum
kaynak dosyalari, veritabani yedegi, panel yonetici hesabi ve alan adi
yonlendirme bilgileri. Teknik bir devir gerektiginde bu paket baska bir ekibe
oldugu gibi verilebilir.</p>

<p>Bolgedeki calismalarimizi <a href="/referanslar">referanslar sayfasinda</a>
gorebilir, <a href="/fiyatlar">fiyat araliklarini</a> inceleyebilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'service', 'slug' => 'e-ticaret-sitesi', 'sort' => 11,
        'title' => 'E-Ticaret Sitesi',
        'meta_title' => 'E-ticaret sitesi kurulumu — zeytinyagi ve yerel urunler',
        'meta_description' => 'Zeytinyagi, zeytin ve yerel urunler icin satisa hazir e-ticaret altyapisi. Kargo, stok ve odeme entegrasyonlariyla.',
        'excerpt' => 'Zeytinyagi, zeytin ve yerel urunler icin satisa hazir magaza altyapisi.',
        'content' => <<<'HTML'
<h2>Yerel urunu internetten satmak</h2>
<p>Korfez bolgesinin en guclu urunu zeytin ve zeytinyagi. Ureticinin en buyuk
sorunu ise araciya kalan pay. Kendi magazaniz oldugunda urununuzu dogrudan
satiyor, musteri listenizi kendiniz kuruyorsunuz.</p>

<h2>Kurdugumuz magazada neler var</h2>
<ul>
  <li>Urun, varyant (litre, kilogram) ve stok yonetimi</li>
  <li>Kargo firmasi entegrasyonu ve bolgeye gore kargo ucreti</li>
  <li>Sanal pos veya havale ile odeme</li>
  <li>Hasat donemine gore on siparis ve kampanya kurulumu</li>
  <li>Coklu dil; yurt disi musteriye satis</li>
</ul>

<h2>Sik sorulan bir soru</h2>
<p>"Pazaryerinde satiyorum, siteye ne gerek var?" Pazaryeri musteriyi size degil
kendine baglar; komisyon oderken musteri listesi de sizde kalmaz. Kendi
magazaniz uzun vadede daha ucuz ve daha degerlidir.</p>

<h2>Ilk siparise kadar olan yol</h2>
<p>Magazayi acmak isin yarisi. Ilk siparisin gelmesi icin urun fotograflari,
kargo anlasmasi, iade sureci ve odeme altyapisinin hazir olmasi gerekir. Bu
adimlarin hicbiri tek basina zor degildir ama birlikte planlanmadiginda ilk
sipariste sorun cikar.</p>

<p>Kurdugumuz magazalarda bu adimlarin hepsi bastan tanimli gelir. Siz yalnizca
urun, fiyat ve stok girersiniz; kargo ucreti bolgeye gore hesaplanir, siparis
onayi musteriye otomatik gider.</p>

<h2>Tekrar eden musteri</h2>
<p>Yerel urunde asil kar, ikinci ve ucuncu siparistedir. Bu yuzden magazanin
musteri hesabi, gecmis siparisleri ve tekrar siparis kolaylıgı tasimasi
gerekir. Gecen yil urununuzu begenen musteriye yeni hasat duyurusu gonderebilmek,
reklam butcesinden cok daha degerlidir.</p>

<h2>Stok ve sezon</h2>
<p>Yerel urunlerin cogu mevsimlik. Stok bittiginde urunu gizlemek yerine
bekleme listesi acmak, hem talebi olcmenizi hem de sonraki sezona hazir bir
musteri listesiyle girmenizi saglar.</p>

<p><a href="/zeytinyagi-e-ticaret-sitesi">Zeytinyagi ureticileri</a> ve
<a href="/coklu-dil-web-sitesi">yurt disina satis</a> icin ayri sayfalarimiz var.</p>

<h2>Yasal basliklar</h2>
<p>Mesafeli satis sozlesmesi, iade ve teslimat kosullari ile gizlilik metni
magazanin zorunlu parcalari. Bu sayfalarin bastan dogru kurulmasi hem yasal
gereklilik hem de musteride guven olusturan bir ayrinti.</p>

<h2>Ilk uc ay</h2>
<p>Yeni bir magazada ilk uc ay ogrenme donemidir: hangi urun ilgi goruyor, hangi
sayfa terk ediliyor, kargo ucreti karari nasil etkiliyor. Panelde bu veriler
takip edilir ve urun duzeni buna gore sadelestirilir.</p>

<p>Zeytinyagi ureticileri icin hazirladigimiz
<a href="/zeytinyagi-e-ticaret-sitesi">sektorel sayfamiza</a> da bakabilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'service', 'slug' => 'rezervasyon-sistemi', 'sort' => 12,
        'title' => 'Rezervasyon Sistemi',
        'meta_title' => 'Otel ve pansiyon rezervasyon sistemi — komisyonsuz',
        'meta_description' => 'Otel, pansiyon ve kamp alanlari icin dogrudan rezervasyon altyapisi. Komisyon odemeden kendi musterinizi kazanin.',
        'excerpt' => 'Otel, pansiyon ve kamp alanlari icin komisyonsuz dogrudan rezervasyon.',
        'content' => <<<'HTML'
<h2>Komisyonun gercek maliyeti</h2>
<p>Rezervasyon sitelerinin aldigi komisyon cogu isletmede yuzde on bes ile yirmi
bes arasinda. Sezon boyunca bu rakam, kendi rezervasyon sisteminizin maliyetinin
kat kat uzerine cikar. Ustelik musteri bilgisi de sizde kalmaz.</p>

<h2>Sistemde neler var</h2>
<ul>
  <li>Oda ve donem bazli fiyatlandirma; sezon disi indirimler</li>
  <li>Musaitlik takvimi ve minimum konaklama kurallari</li>
  <li>Kapora veya tam odeme secenegi</li>
  <li>Onay ve hatirlatma e-postalari</li>
  <li>Coklu dil; Almanca ve Ingilizce misafirler icin</li>
</ul>

<h2>Pazaryerinden vazgecmek gerekmiyor</h2>
<p>Amac pazaryerlerini birakmak degil, oradan gelen misafiri bir sonraki sefere
dogrudan size getirmek. Kendi kanaliniz guclendikce komisyonlu satisin payi
kendiliginden duser.</p>

<h2>Kurulum nasil ilerliyor</h2>
<p>Once oda tipleri, kapasiteler ve sezon donemleri tanimlanir. Ardindan fiyat
tablosu, minimum konaklama kurallari ve iptal kosullari girilir. Uc gunluk bir
calismayla sistem devreye alinabilir; asil zamani alan sey fiyat politikasinin
netlesmesidir.</p>

<h2>Musaitlik yonetimi</h2>
<p>Platformlarla ayni odalari satarken en buyuk risk cift rezervasyondur.
Sistemi kurarken isletmenin gunluk akisina gore iki yol sunuyoruz: ya odalarin
bir bolumu dogrudan satisa ayrilir, ya da musaitlik tek bir yerden yonetilip
platformlara oradan yansitilir.</p>

<h2>Iptal ve kapora</h2>
<p>Dogrudan rezervasyonun en cok tereddut yaratan tarafi odeme. Kapora tutarini
ve iptal suresini net yazmak, misafirin platform yerine sizi secmesini
kolaylastirir. Belirsiz birakilan kosul, misafiri komisyonlu ama guvendigi
kanala iter.</p>

<h2>Sezon sonrasi</h2>
<p>Sezon bittiginde elinizde misafir listesi kalir. Bu listeye gelecek yil
erken rezervasyon duyurusu gondermek, dogrudan satisin en verimli tarafidir ve
hicbir komisyon icermez.</p>

<p><a href="/otel-pansiyon-web-sitesi">Konaklama sektoru sayfamiza</a> ve
<a href="/akcay-web-tasarim">Akcay</a> calismalarimiza bakabilirsiniz.</p>

<h2>Mevcut sisteme gecis</h2>
<p>Halihazirda bir rezervasyon defteri ya da tablo kullaniyorsaniz, mevcut
kayitlar tasinabilir. Sezon ortasinda gecis onerilmez; en uygun zaman sezon
kapanisidir.</p>

<h2>Personel egitimi</h2>
<p>Sistemi kullanacak kisiyle bir saatlik bir oturum yapiyoruz. Rezervasyon
onaylama, fiyat degistirme ve iptal islemleri panelde birkac tiklamayla
yapiliyor; ayri bir teknik bilgi gerekmiyor.</p>

<h2>Yasal bilgiler</h2>
<p>On odeme alinan sistemlerde mesafeli satis sozlesmesi ve iptal kosullarinin
yayinlanmasi zorunludur; bu sayfalari kurulumla birlikte hazirliyoruz.</p>

<p>Konaklama isletmeleri icin hazirladigimiz
<a href="/otel-pansiyon-web-sitesi">sektorel sayfaya</a> goz atabilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'service', 'slug' => 'seo-hizmeti', 'sort' => 13,
        'title' => 'SEO Hizmeti',
        'meta_title' => 'Yerel SEO hizmeti — Edremit, Balikesir ve Korfez',
        'meta_description' => 'Yerel aramalarda ust siralara cikmak icin teknik duzeltme, icerik calismasi ve Google Isletme Profili yonetimi.',
        'excerpt' => 'Yerel aramalarda ust siralara cikmak icin teknik ve icerik calismasi.',
        'content' => <<<'HTML'
<h2>Yerel SEO neden farkli</h2>
<p>"Web tasarim" araması ile "edremit web tasarim" araması bambaska iki istir.
Ikincisinde rekabet daha az, niyet daha nettir: arayan kisi zaten hizmet almak
uzeredir. Bolgedeki isletmeler icin dogru hedef budur.</p>

<h2>Calismanin adimlari</h2>
<ol>
  <li><strong>Teknik denetim.</strong> Hiz, mobil uyum, baslik yapisi, kirik
      baglantilar ve dizine alma sorunlari.</li>
  <li><strong>Google Isletme Profili.</strong> Isim, adres ve telefon bilgisinin
      site ile birebir ayni olmasi; kategori ve gorsel duzeni.</li>
  <li><strong>Icerik.</strong> Her hizmet ve her bolge icin ayri, gercekten
      ozgun sayfalar.</li>
  <li><strong>Olcum.</strong> Hangi sayfanin telefon ve form getirdiginin
      takibi.</li>
</ol>

<h2>Yapmadigimiz seyler</h2>
<p>Ayni metni ilce adi degistirerek cogaltmiyoruz; bu yontem kisa vadede sayfa
sayisini artirsa da Google tarafindan yakalandiginda tum sayfalari birden
degersizlestirir. Uydurma yorum ve puanlama isaretlemesi de yapmiyoruz.</p>

<h2>Ilk uc ayda ne oluyor</h2>
<p>Teknik duzeltmelerin etkisi genellikle dort ile alti hafta icinde gorulur.
Icerik calismasinin karsiligi ise uc ile alti ay arasinda alinir. Bu sureleri
bastan soylememizin sebebi basit: kisa vadede sonuc vaat eden yontemlerin cogu
riskli, bir bolumu de dogrudan zararlidir.</p>

<h2>Olcum neye bakiyor</h2>
<p>Siralama tek basina anlamli bir olcut degil. Bizim baktigimiz uc sey var:
kac kisi geldi, hangi sayfadan geldi ve kac tanesi telefon etti ya da form
doldurdu. Panelde her form kaydi kaynak sayfasiyla birlikte tutuldugu icin bu
tablo uc ay sonra net bir sekilde okunabiliyor.</p>

<h2>Icerik kimin isi</h2>
<p>Metinleri biz yazabiliriz ama en iyi icerik isletmenin kendi bilgisinden
cikar. Genellikle su bolusumu oneriyoruz: teknik yapiyi ve baslik duzenini biz
kuruyoruz, sektore ozgu bilgiyi isletme veriyor, yaziya donusturmeyi biz
yapiyoruz.</p>

<h2>Google Isletme Profili</h2>
<p>Yerel aramada en hizli kazanc genellikle burada. Kategori secimi, hizmet
listesi, gercek fotograf, calisma saati ve duzenli gonderi; bu bes basligin
duzgun kurulmasi cogu isletmede haritada gorunurlugu belirgin sekilde
artiriyor.</p>

<p>Yaklasimin sonucunu <a href="/balikesir-web-tasarim">Balikesir</a> ve
<a href="/havran-web-tasarim">Havran</a> sayfalarinda gorebilirsiniz.</p>

<h2>Raporlama</h2>
<p>Aylik raporlarda dort baslik var: gelen ziyaret, en cok giris alan sayfalar,
form ve telefon sayisi, bir onceki aya gore degisim. Rapor iki sayfayi gecmez ve
teknik terim icermez.</p>

<p>Bolgeye ozel sayfalarimizdan biri olan
<a href="/edremit-web-tasarim">Edremit web tasarim sayfamiza</a> bakarak
yaklasimimizi gorebilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'service', 'slug' => 'coklu-dil-web-sitesi', 'sort' => 14,
        'title' => 'Coklu Dil Web Sitesi',
        'meta_title' => 'Coklu dil web sitesi — Turkce, Ingilizce, Almanca, Arapca',
        'meta_description' => 'Dogru hreflang kurulumuyla coklu dil web sitesi. Yabanci misafir ve alici hedefleyen isletmeler icin.',
        'excerpt' => 'Turkce, Ingilizce, Almanca ve Arapca yayin; dogru hreflang kurulumu.',
        'content' => <<<'HTML'
<h2>Kimin coklu dile ihtiyaci var</h2>
<p>Korfezde yaz aylarinda Alman ve Ingiliz misafir agirlayan konaklama
isletmeleri, yurt disina urun gonderen zeytinyagi ureticileri ve yabanci
alicilara satis yapan emlak ofisleri icin ikinci dil bir luks degil, dogrudan
gelir kalemidir.</p>

<h2>Otomatik ceviri neden yetmez</h2>
<p>Tarayici cevirisi ya da otomatik ceviri eklentileri arama motorlari icin ayri
bir sayfa uretmez; yani Almanca arayan biri sizi bulamaz. Gercek coklu dil, her
dil icin ayri adres ve ayri icerik demektir.</p>

<h2>Dogru kurulum neye benzer</h2>
<ul>
  <li>Her dil icin ayri adres: <code>/en/...</code>, <code>/de/...</code></li>
  <li>Diller arasi <code>hreflang</code> bildirimi; karsiligi olmayan dil
      bildirilmez</li>
  <li>Arapca icin sagdan sola duzen</li>
  <li>Panelden dil bazli icerik yonetimi</li>
</ul>

<h2>Hangi dilleri secmeli</h2>
<p>Her dil ek bir bakim yuku demek. Bu yuzden dil secimini tahminle degil veriyle
yapiyoruz: mevcut ziyaretcilerin tarayici dili, gelen rezervasyon ya da
siparislerin ulkesi ve hedeflenen pazar. Korfez bolgesinde en sik anlamli olan
sira Ingilizce, Almanca ve Arapca.</p>

<h2>Ceviri degil, yerellestirme</h2>
<p>Ayni metnin cevirisi cogu zaman ise yaramaz cunku her kitlenin sorusu farkli.
Alman misafir kahvalti saatini ve sessizligi sorar; Arap misafir aile odasini ve
mutfak duzenini sorar. Iyi bir coklu dil kurulumu bu farki metne yansitir.</p>

<h2>Arapca ve sagdan sola duzen</h2>
<p>Arapca yayin yalnizca metni cevirmek degildir; sayfanin yonu degisir. Menu,
butonlar ve form alanlari sagdan sola akmalidir. Bunu mantiksal CSS
ozellikleriyle yapiyoruz, boylece ayni tasarim iki yonde de bozulmadan
calisiyor.</p>

<h2>Eksik ceviri sorun degildir</h2>
<p>Bir sayfanin her dilde karsiligi olmak zorunda degil. Onemli olan, olmayan
dil icin arama motoruna yanlis bilgi vermemek. Karsiligi olmayan dil icin
hreflang bildirimi yapilmaz; boylece yanlis dilde sayfa gosterilmesi
onlenir.</p>

<p><a href="/ayvalik-web-tasarim">Ayvalik</a> ve
<a href="/e-ticaret-sitesi">e-ticaret</a> sayfalarimizda ornekler var.</p>

<h2>Bakim yuku</h2>
<p>Her yeni dil, guncellenmesi gereken yeni bir icerik demek. Bu yuzden hangi
sayfalarin cevrileceğine bastan karar veriyoruz; genellikle anasayfa, hizmet
sayfalari ve iletisim yeterli oluyor. Blog yazilari cogu isletmede tek dilde
kaliyor ve bu bir sorun degil.</p>

<p>Kurulumu yapilmis bir orne gormek isterseniz
<a href="/iletisim">bize yazin</a>.</p>
HTML,
    ],

    [
        'type' => 'service', 'slug' => 'web-sitesi-bakim', 'sort' => 15,
        'title' => 'Web Sitesi Bakim',
        'meta_title' => 'Web sitesi bakim hizmeti — aylik sabit ucret',
        'meta_description' => 'Guncelleme, yedekleme, guvenlik ve icerik destegi. Aylik sabit ucretle web sitesi bakim anlasmasi.',
        'excerpt' => 'Guncelleme, yedekleme, guvenlik ve icerik destegi; aylik sabit ucret.',
        'content' => <<<'HTML'
<h2>Bakimsiz site ne olur</h2>
<p>Bir web sitesi kurulup birakildiginda genellikle iki yil icinde ya guvenlik
acigindan ele gecirilir ya da icerigi eskidigi icin aramalarda geriler. Ikisi de
sessizce olur; fark ettiginizde is isten gecmis olur.</p>

<h2>Bakim kapsaminda neler var</h2>
<ul>
  <li>Gunluk otomatik yedek ve yedekten donus provasi</li>
  <li>Guvenlik guncellemeleri ve gunluk denetimi</li>
  <li>Ayda belirli sureye kadar icerik guncellemesi</li>
  <li>Hiz ve kirik baglanti kontrolu</li>
  <li>Aylik kisa rapor: ziyaret, form ve siralamalar</li>
</ul>

<h2>Anlasma nasil isliyor</h2>
<p>Aylik sabit ucret, taahhut yok. Istediginiz ay birakabilirsiniz; sitenin tum
dosyalari ve veritabani zaten sizindir.</p>

<h2>Yedegin provasi yapilmadan yedek sayilmaz</h2>
<p>Cogu isletme yedek aldigini sanir ama yedegin geri yuklenip yuklenemedigini
hic denemez. Bakim kapsaminda yilda en az bir kez geri yukleme provasi
yapiyoruz. Calismayan bir yedek, hic yedek olmamasindan daha tehlikelidir cunku
yanlis bir guven verir.</p>

<h2>Guncelleme ne demek</h2>
<p>Sitede kullanilan hicbir hazir eklenti olmadigi icin klasik anlamda
"eklenti guncellemesi" yok. Bizim yaptigimiz sey PHP surumu, sunucu
yapilandirmasi ve guvenlik basliklarinin takibi; ayrica kirik baglanti ve hiz
kontrolu.</p>

<h2>Aylik rapor</h2>
<p>Her ayin basinda kisa bir rapor gonderiyoruz: ziyaret sayisi, en cok girilen
sayfalar, gelen form sayisi ve varsa dikkat edilmesi gereken bir konu. Rapor iki
sayfayi gecmez; amac veri yigini degil karar verdirmek.</p>

<h2>Icerik destegi</h2>
<p>Ayda belirli bir sureye kadar icerik guncellemesi kapsam icinde. Fiyat
degisikligi, yeni fotograf, kampanya duyurusu gibi isler icin ayrica ucret
alinmiyor.</p>

<p><a href="/fiyatlar">Fiyat araliklari</a> ve
<a href="/sss">sik sorulan sorular</a> sayfalarina bakabilirsiniz.</p>

<h2>Acil durum</h2>
<p>Site erisilemez hale gelirse ilk mudahaleyi ayni gun icinde yapiyoruz. Yedekten
donus gerektiginde son gunluk yedekle geri donuluyor; bu islem genellikle bir
saatten kisa suruyor.</p>

<h2>Anlasma disi isler</h2>
<p>Yeni sayfa tasarimi, yeni modul ya da kapsamli icerik uretimi bakim
kapsaminda degildir; bunlar ayri teklif olarak konusulur ve onayiniz olmadan
baslamaz.</p>

<h2>Anlasmanin sonlanmasi</h2>
<p>Bakimi birakmaniz durumunda son yedek ve tum erisim bilgileri size teslim
edilir. Hicbir sey elimizde tutulmaz.</p>

<p>Bakim kapsamini kendi siteniz icin konusmak isterseniz
<a href="/iletisim">iletisim formunu</a> doldurun.</p>
HTML,
    ],
];
