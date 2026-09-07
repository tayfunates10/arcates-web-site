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
        'title' => 'Hakkımızda',
        'meta_title' => 'Arcates Yazılım — Edremit Körfezi web ajansı',
        'meta_description' => 'Edremit Körfezi bölgesinde web tasarım, e-ticaret ve yazılım geliştiren küçük bir ekibiz. Yerinde görüşüyor, ölçülebilir iş yapıyoruz.',
        'excerpt' => 'Körfezde çalışan, işini yerinde öğrenen küçük bir yazılım ekibi.',
        'content' => <<<'HTML'
<h2>Nereden geliyoruz</h2>
<p>Arcates Yazılım, Edremit Körfezi bölgesindeki işletmelere web tasarım, e-ticaret
ve yazılım hizmeti veren küçük bir ekiptir. Büyük şehirlerdeki ajansların uzaktan
verdiği hizmetin bölgedeki işletmelere yetmediğini gördüğümüz için kuruldu.</p>

<p>Bir zeytinyağı üreticisinin hasat takvimini, bir pansiyonun sezon dışı doluluk
kaygısını ya da bir emlak ofisinin yaz aylarındaki talep patlamasını uzaktan
anlamak zor. Biz aynı bölgede yaşıyoruz; müşterilerimizin işini yerinde
görüyoruz.</p>

<h2>Nasıl çalışıyoruz</h2>
<p>Önce dinliyoruz. İşletmenin gerçekten hangi müşteriyi aradığı, hangi aramalarda
görünmesi gerektiği ve rakiplerinin ne yaptığı netleşmeden tasarıma
başlamıyoruz. Bu görüşme çoğu zaman işletmenin kendi mekanında oluyor.</p>

<p>Sonra kuruyoruz. Tasarım, içerik ve teknik kurulum birlikte ilerliyor; her sayfa
hız ve arama görünürlüğü için ölçülüyor. Yayından sonra da bırakmıyoruz: hangi
sayfanın gerçekten müşteri getirdiğini ölçüp içeriği buna göre düzenliyoruz.</p>

<h2>Neye inanıyoruz</h2>
<ul>
  <li><strong>Hız her şeyden önce gelir.</strong> Mobilde geç açılan bir site,
      ne kadar güzel olursa olsun müşteri kaybettirir.</li>
  <li><strong>Yönetilemeyen site olu sitedir.</strong> Her metin, her görsel ve
      her fiyat işletmenin kendisi tarafından değiştirilebilmeli.</li>
  <li><strong>Ölçülmeyen iş büyütülemez.</strong> Hangi sayfanın telefon
      getirdiğini bilmiyorsanız neyi iyileştireceğinizi de bilemezsiniz.</li>
</ul>

<h2>Ekip ve çalışma biçimi</h2>
<p>Küçük bir ekibiz ve bu bilinçli bir tercih. Aynı anda üç dört projeden
fazlasını almıyoruz; böylece her işin başında aynı kişiler duruyor ve müşteri
her seferinde baştan anlatmak zorunda kalmıyor.</p>

<p>Alt yüklenici kullanmıyoruz. Tasarım, yazılım ve içerik aynı ekip içinde
yürütülüyor. Bu, sorumluluğun bölünmemesi anlamına geliyor: bir sorun
çıktığında kimin çözeceği bellidir.</p>

<h2>Neyi yapmıyoruz</h2>
<p>Hazır şablon satmıyoruz, kiralık panel kurmuyoruz ve müşteriyi teknik olarak
kendimize bağımlı hale getirmiyoruz. Sitenin tüm dosyaları ve veritabanı
müşterinindir; isteyen her an alıp başka bir ekiple devam edebilir.</p>

<p>Kısa vadede sonuç vaat eden ama uzun vadede zarar veren yöntemlerden de uzak
duruyoruz: uydurma yorum, satın alınmış bağlantı ve aynı metnin bölge adı
değiştirilerek çoğaltılması bunların başında geliyor.</p>

<h2>Bölge dışından gelen işler</h2>
<p>Körfez dışından da iş alıyoruz ancak burada bir ayrımımız var: bölgeyi
tanımadığımız bir yerde "yerel SEO" vaadinde bulunmuyoruz. Teknik kurulum,
tasarım ve e-ticaret işleri her yerde aynı kalitede yapılabilir; bölge bilgisi
gerektiren işler ise ancak yerinde öğrenilerek yapılır.</p>

<p>Bölgedeki çalışmalarımızı <a href="/referanslar">referanslar sayfasında</a>
görebilir, aklınızdaki proje için <a href="/iletisim">bize yazabilirsiniz</a>.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'iletisim', 'sort' => 2,
        'title' => 'İletişim',
        'meta_title' => 'İletişim — Arcates Yazılım',
        'meta_description' => 'Projeniz için ücretsiz görüşme. Edremit Körfezi bölgesinde yerinde görüşme yapıyoruz. Formu doldurun, aynı hafta dönüş yapalım.',
        'excerpt' => 'Projenizi konuşalım. Formu doldurun, aynı hafta fiyat ve takvim gönderelim.',
        'content' => <<<'HTML'
<h2>Nasıl ilerliyoruz</h2>
<p>Aşağıdaki formu doldurduğunuzda önce kısa bir görüşme yapıyoruz. Bu görüşmede
işinizi, hedefinizi ve bütçenizi konuşuyoruz. Ardından aynı hafta içinde yazılı
fiyat ve takvim gönderiyoruz.</p>

<p>Edremit, Akçay, Altınoluk, Burhaniye, Havran, Gömeç, Ayvalık ve Balıkesir
merkezde yerinde görüşme yapabiliyoruz. Bölge dışındaki işler için görüşmeler
çevrimiçi yürütülüyor.</p>

<h2>Ne kadar sürede dönüş alıyorum</h2>
<p>İş günlerinde gelen mesajlara aynı gün, hafta sonu gelenlere pazartesi dönüş
yapıyoruz. Acil bir durumda telefonla ulaşmak her zaman daha hızlı.</p>

<h2>Görüşmeye hazırlık</h2>
<p>İlk görüşmeyi verimli geçirmek için şu üç sorunun cevabını düşünmüş olmanız
yeterli: Sitenin size getirmesini istediğiniz şey nedir; telefon mu, sipariş mi,
rezervasyon mu? Şu an müşterileriniz sizi nasıl buluyor? Rakipleriniz arasında
sitesini beğendiğiniz biri var mı?</p>

<p>Elinizde logo, fotoğraf ya da eski site bilgisi varsa görüşmeden önce
paylaşmanız süreci hızlandırır; ancak şart değil.</p>

<h2>Yerinde görüşme</h2>
<p>Edremit, Akçay, Altınoluk, Burhaniye, Havran, Gömeç, Ayvalık ve Balıkesir
merkezde işletmenizi ziyaret ediyoruz. Görüşme ücretsizdir ve genellikle bir
saati geçmez. Bölge dışındaki işler çevrimiçi yürütülüyor.</p>

<h2>Bilgileriniz</h2>
<p>Formda verdiğiniz bilgiler yalnızca size dönüş yapmak için kullanılır,
üçüncü kişilerle paylaşılmaz ve saklama süresi dolduğunda otomatik silinir.
Ayrıntılı bilgi <a href="/kvkk">aydınlatma metnimizde</a>.</p>

<h2>Telefon mu form mu</h2>
<p>Acil bir konu için telefon her zaman daha hızlı. Ancak proje anlatmak için
form daha verimli: yazarken ihtiyacınızı netleştirmiş oluyorsunuz ve biz de
görüşmeye hazırlıklı geliyoruz. Uzun uzun yazmaya gerek yok; birkaç cümle
yeterli.</p>

<h2>Görüşmede ne konuşuyoruz</h2>
<p>İşletmenizin şu anki durumu, müşteri profiliniz, rakipleriniz ve bütçe
aralığınız. Bu dört başlık netleştiğinde teklif hazırlamak birkaç gün sürüyor.
Teklif yazılı gelir ve içinde kapsam, süre ve ödeme planı ayrı ayrı belirtilir;
sonradan sürpriz kalem çıkmaz.</p>

<h2>Çalışmaya başlamadan önce</h2>
<p>Teklifi onayladıktan sonra bir başlangıç toplantısı yapıyoruz. Bu toplantıda
içerik sorumlusu, görsel kaynağı ve teslim tarihleri belirleniyor. Projenin
gecikmesinin en yaygın sebebi teknik değil, içerik bekleyişidir; bunu baştan
planlamak süreyi kısaltıyor.</p>

<p>Sık sorulan soruların cevabını <a href="/sss">SSS sayfasında</a>,
fiyat aralıklarını <a href="/fiyatlar">fiyatlar sayfasında</a> bulabilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'fiyatlar', 'sort' => 3,
        'title' => 'Fiyatlar',
        'meta_title' => 'Web sitesi fiyatları — Edremit ve Balıkesir',
        'meta_description' => 'Kurumsal site, e-ticaret ve rezervasyon sistemi fiyat aralıkları. Ne ödediğini bilerek karar verin; gizli kalem yok.',
        'excerpt' => 'Ne ödediğini bilerek karar verin. Gizli kalem yok, her şey yazılı.',
        'content' => <<<'HTML'
<h2>Neden aralık veriyoruz</h2>
<p>Web sitesi fiyatı, sayfa sayısı kadar işin kendisine de bağlıdır. Altı sayfalık
bir tanıtım sitesiyle beş yüz ürünlü bir mağaza aynı iş değildir. Bu yüzden
sabit tek bir rakam yerine aralık veriyor, görüşmeden sonra kesin fiyatı yazılı
gönderiyoruz.</p>

<h2>Fiyatı belirleyen başlıklar</h2>
<ul>
  <li><strong>Sayfa ve içerik hacmi.</strong> Metinleri siz mi veriyorsunuz, biz mi
      yazıyoruz?</li>
  <li><strong>Dil sayısı.</strong> Yalnızca Türkçe mi, yoksa İngilizce ve Almanca
      da olacak mı?</li>
  <li><strong>İşlevler.</strong> Rezervasyon, sepet, ödeme, stok takibi gibi
      başlıklar işin büyüklüğünü değiştirir.</li>
  <li><strong>Görsel üretimi.</strong> Mekanda fotoğraf çekimi gerekiyor mu?</li>
</ul>

<h2>Ödeme ve süre</h2>
<p>İşler genellikle iki taksitle yürütülür: başlangıçta ve yayında. Kurumsal bir
site ortalama üç haftada, e-ticaret ve rezervasyon projeleri altı ila sekiz
haftada yayına alınır.</p>

<h2>Yayından sonra</h2>
<p>Her proje bir aylık ücretsiz destekle teslim edilir. Sonrasında isterseniz
<a href="/web-sitesi-bakim">aylık bakım anlaşması</a> yapıyoruz; güncelleme,
yedekleme, güvenlik ve içerik desteği bu kapsamda.</p>

<h2>Neler fiyata dahil</h2>
<p>Her projede şu kalemler fiyata dahildir: tasarım, kurulum, panel eğitimi,
temel SEO kurulumu, Google İşletme Profili düzenlemesi ve bir aylık destek.
Alan adı ve barındırma ücreti ayrıdır; dilerseniz mevcut sağlayıcınızla devam
edebilirsiniz.</p>

<h2>Ne fiyatı artırır</h2>
<p>Metinlerin bizim tarafımızdan yazılması, mekanda fotoğraf çekimi, ikinci ve
üçüncü dil, özel hesaplama gerektiren formlar ve dış sistemlerle entegrasyon
fiyatı yükselten başlıklardır. Bunların her biri görüşmede ayrı kalem olarak
konuşulur.</p>

<h2>Bütçesi sınırlı işletmeler için</h2>
<p>Bütçe kısıtlıysa önerimiz şudur: önce doğru kurulmuş küçük bir site ve düzgün
bir harita kaydı. Bu ikisi çalışmaya başladıktan sonra gelen talebe göre
büyütmek her zaman mümkün. Baştan büyük yatırım yapıp içeriği güncelleyemeyen
işletme ikinci yılda daha kötü durumda oluyor.</p>

<p>Bakım koşullarını <a href="/web-sitesi-bakim">bakım sayfasında</a>,
sık sorulan soruları <a href="/sss">SSS sayfasında</a> bulabilirsiniz.</p>

<h2>Fiyat dışı maliyetler</h2>
<p>Alan adı yıllık ücreti, barındırma ve varsa sanal pos komisyonu proje
fiyatının dışındadır. Bu kalemler doğrudan sağlayıcıya ödenir; araya girip
üzerine ekleme yapmıyoruz. Mevcut sağlayıcınızla devam etmek isterseniz o da
mümkün.</p>

<p>Kendi projeniz için net rakam almak isterseniz
<a href="/iletisim">iletişim formunu</a> doldurmanız yeterli.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'referanslar', 'sort' => 4,
        'title' => 'Referanslar',
        'meta_title' => 'Referanslar — Arcates Yazılım',
        'meta_description' => 'Edremit Körfezi bölgesinde yayına aldığımız web siteleri, e-ticaret mağazaları ve rezervasyon sistemleri.',
        'excerpt' => 'Körfezde yayına aldığımız projelerden bir bölümü.',
        'content' => <<<'HTML'
<h2>Neye göre seçilmiş çalışmalar</h2>
<p>Aşağıdaki listede farklı sektörlerden ve farklı ölçeklerden örnekler var.
Amaç vitrin oluşturmak değil; benzer bir işin nasıl kurgulandığını göstermek.
Her çalışmanın sayfasında işletmenin ihtiyacı, yapılan işler ve varsa yayın
sonrası ölçülen sonuç yazıyor.</p>

<h2>Sektörünüzden örnek</h2>
<p>Kendi sektörünüzden bir çalışma görmek isterseniz
<a href="/iletisim">bize yazın</a>. Bazı müşterilerimiz çalışmalarının
yayınlanmasını istemediği için listede görünmeyen işler de var; bunları
görüşmede paylaşabiliyoruz.</p>

<h2>Çalışmanın sonrası</h2>
<p>Listedeki işlerin büyük bölümünde yayın sonrası da birlikte çalışmaya devam
ettik. Bir sitenin gerçek değeri, yayına alındığı gün değil altıncı ayında
belli olur: hangi sayfa telefon getiriyor, hangi içerik boşa yazılmış, nerede
düzeltme gerekiyor.</p>

<h2>Bölgeye göre</h2>
<h2>Referans vermek</h2>
<p>Çalıştığımız işletmelerin bir bölümü, kendilerine ulaşan yeni müşterilerle
konuşmayı kabul ediyor. Karar vermeden önce benzer bir işletmeyle görüşmek
isterseniz bunu ayarlayabiliyoruz; bizim anlattığımızdan çok daha değerli bir
bilgi kaynağı.</p>

<h2>Sayılar</h2>
<p>Bazı çalışmaların sayfasında yayın sonrası ölçülen değişim de yazıyor: gelen
form sayısı, arama görünürlüğü ya da doğrudan rezervasyon oranı. Bu rakamlar
işletmenin izniyle paylaşılıyor ve abartılmıyor; ölçülen ne ise o yazılıyor.</p>

<p>Çalışmaların büyük bölümü Körfez ilçelerinde. Bölgeye özel yaklaşımımızı
<a href="/edremit-web-tasarim">Edremit</a>,
<a href="/ayvalik-web-tasarim">Ayvalık</a> ve
<a href="/burhaniye-web-tasarim">Burhaniye</a> sayfalarında anlatıyoruz.
Sektörel yaklaşım için <a href="/otel-pansiyon-web-sitesi">konaklama</a> ve
<a href="/zeytinyagi-e-ticaret-sitesi">zeytinyağı</a> sayfalarına
bakabilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'blog', 'sort' => 5,
        'title' => 'Blog',
        'meta_title' => 'Blog — web tasarım ve yerel SEO rehberleri',
        'meta_description' => 'Küçük işletmeler için web sitesi, yerel SEO ve dijital pazarlama üzerine uygulanabilir yazılar.',
        'excerpt' => 'Küçük işletmeler için uygulanabilir rehberler.',
        'content' => <<<'HTML'
<h2>Burada ne yazıyoruz</h2>
<p>Görüşmelerde tekrar tekrar gelen soruları burada topluyoruz. Her yazı tek bir
soruyu, uygulanabilir adımlarla cevaplar. Amaç uzun teorik metinler değil;
okuyan işletmecinin aynı gün uygulayabileceği bir şey bırakmak.</p>

<h2>Sık yazdığımız konular</h2>
<ul>
  <li>Yerel aramada görünürlük ve Google İşletme Profili düzeni</li>
  <li>Küçük işletme için site hızı ve mobil kullanım</li>
  <li>Rezervasyon ve sipariş formlarında dönüşüm</li>
  <li>Çoklu dilde yayın ve yabancı müşteriye ulaşma</li>
  <li>Yedekleme, güvenlik ve bakım alışkanlıkları</li>
</ul>

<h2>Neden kısa yazıyoruz</h2>
<p>İşletmecinin okumaya ayırdığı süre sınırlı. Bu yüzden yazıları tek bir soruya
odaklı tutuyor, uygulanabilir adımlarla bitiriyoruz. Uzun rehberler yerine
birbirine bağlanan kısa yazılar hem daha çok okunuyor hem de aramada daha net
karşılık buluyor.</p>

<h2>Konu önerisi</h2>
<h2>Yazıları kim yazıyor</h2>
<p>Yazılar, işleri fiilen yapan ekip tarafından yazılıyor. Bu yüzden örnekler
gerçek projelerden geliyor ve genel geçer tavsiye yerine bölgede karşılaştığımız
somut durumlar anlatılıyor.</p>

<h2>Güncelleme</h2>
<p>Eskiyen yazıları silmek yerine güncelliyoruz. Bir konu değiştiğinde yazının
altına ne zaman ve neyin değiştiği ekleniyor; böylece okuyan kişi bilginin ne
kadar taze olduğunu görüyor.</p>

<p>Merak ettiğiniz bir konu varsa <a href="/iletisim">yazın</a>, sıradaki yazıyı
ona ayıralım. Hizmetlerimizi <a href="/web-tasarim">web tasarım</a> ve
<a href="/seo-hizmeti">SEO hizmeti</a> sayfalarından inceleyebilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'sss', 'sort' => 6,
        'title' => 'Sık Sorulan Sorular',
        'meta_title' => 'Sık sorulan sorular — Arcates Yazılım',
        'meta_description' => 'Web sitesi süreci, fiyatlandırma, bakım ve SEO hakkında en çok sorulan sorular ve cevapları.',
        'excerpt' => 'Aklınızdaki sorunun cevabı burada yoksa bize yazın.',
        'content' => <<<'HTML'
<h2>Burada ne var</h2>
<p>Aşağıdaki sorular ilk görüşmelerde en sık gelenler. Fiyat, süre, sahiplik,
bakım ve arama görünürlüğü başlıklarında net cevaplar bulacaksınız. Cevaplar
genel geçer değil; bölgede yaptığımız işlerden çıkan gerçek yanıtlar.</p>

<h2>Sayfaya özel sorular</h2>
<p>Her hizmet, ilçe ve sektör sayfasının altında o konuya özel sorular da var.
Sezonluk bir pansiyon işletiyorsanız
<a href="/akcay-web-tasarim">Akçay sayfasındaki</a> soru,
zeytinyağı üretiyorsanız
<a href="/burhaniye-web-tasarim">Burhaniye sayfasındaki</a> soru sizin için
daha anlamlı olabilir.</p>

<h2>Neden hepsini tek sayfada toplamıyoruz</h2>
<p>Arama yapan kişi genellikle tek bir soru sorar ve o sorunun cevabını taşıyan
sayfaya düşer. Bu yüzden soruları ilgili sayfalara dağıtıyor, burada yalnızca
herkesi ilgilendirenleri topluyoruz.</p>

<h2>Cevabını bulamadınız mı</h2>
<h2>Soruları nasıl seçiyoruz</h2>
<p>Buradaki sorular uydurulmuş değil; görüşmelerde ve form mesajlarında fiilen
gelen sorulardan seçiliyor. Bir soru üç dört kez tekrarlandığında listeye
ekleniyor ve cevabı güncel tutuluyor.</p>

<h2>Cevapların sınırları</h2>
<p>Her işletme farklı olduğu için buradaki cevaplar genel çerçeve sunar. Kendi
durumunuza özel bir yanıt için kısa bir görüşme her zaman daha isabetli olur ve
bu görüşme ücretsizdir.</p>

<p>Aradığınız cevap burada yoksa <a href="/iletisim">iletişim formunu</a>
doldurmanız yeterli. Gelen soruları düzenli olarak bu sayfaya ekliyoruz;
sizin sorunuz da başkasının işine yarayabilir.
<a href="/fiyatlar">Fiyat aralıklarına</a> da bakabilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'kvkk', 'sort' => 90,
        'title' => 'KVKK Aydınlatma Metni',
        'meta_title' => 'KVKK Aydınlatma Metni — Arcates Yazılım',
        'meta_description' => '6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında aydınlatma metni.',
        'excerpt' => 'Kişisel verilerinizi neden topluyoruz, ne kadar saklıyoruz, kimlerle paylaşıyoruz.',
        'content' => <<<'HTML'
<h2>Veri sorumlusu</h2>
<p>Bu metin, 6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında,
sitemiz üzerinden toplanan kişisel verilerle ilgili olarak sizi
bilgilendirmek amacıyla hazırlanmıştır.</p>

<h2>Hangi verileri topluyoruz</h2>
<p>Teklif formunu doldurduğunuzda ad soyad, telefon, e-posta adresi, ilgilendiğiniz
hizmet ve mesajınız kaydedilir. Formu hangi sayfadan doldurduğunuz, siteye nereden
geldiğiniz, tercih ettiğiniz dil, IP adresiniz ve tarayıcı bilginiz de teknik kayıt
olarak saklanır.</p>

<h2>Neden topluyoruz</h2>
<ul>
  <li>Talebinize dönüş yapabilmek ve teklif hazırlayabilmek</li>
  <li>Hizmet kalitemizi ölçmek ve iyileştirmek</li>
  <li>Yasal yükümlülüklerimizi yerine getirmek</li>
  <li>Kötü niyetli ve otomatik gönderimleri engellemek</li>
</ul>

<h2>Ne kadar saklıyoruz</h2>
<p>Form kayıtları, ilişkinin sona ermesinden itibaren yasal saklama süresi boyunca
tutulur ve süresi dolan kayıtlar otomatik olarak silinir. Ziyaret kayıtları doksan
gün sonra kişiselleştirilemez toplu istatistiğe dönüştürülür.</p>

<h2>Kimlerle paylaşıyoruz</h2>
<p>Verileriniz reklam veya pazarlama amacıyla üçüncü kişilerle paylaşılmaz.
Yalnızca yasal olarak zorunlu hallerde ve yetkili kamu kurumlarıyla paylaşılır.</p>

<h2>Haklarınız</h2>
<h2>Verilerin güvenliği</h2>
<p>Toplanan veriler şifreli bağlantı üzerinden iletilir ve erişimi sınırlı bir
veritabanında saklanır. Panel erişimi rol bazlıdır; içerik editörleri form
kayıtlarını göremez. Başarısız giriş denemeleri kaydedilir ve tekrarlanan
denemelerde erişim geçici olarak kilitlenir.</p>

<h2>Otomatik silme</h2>
<p>Saklama süresi panelden belirlenir ve günlük çalışan bir görev süresi dolan
kayıtları siler. Bu işlem elle müdahale gerektirmez; böylece kayıtların
unutulup birikmesi engellenir.</p>

<h2>Çerezler</h2>
<p>Sitede reklam ya da izleme çerezi kullanılmaz. Ziyaret ölçümü için kimlik
taşımayan, geri çevrilemeyen bir karma kullanılır. Ayrıntılı bilgi
<a href="/gizlilik-politikasi">gizlilik politikası sayfamızda</a>.</p>

<h2>Başvuru ve yanıt süresi</h2>
<p>Kanun kapsamındaki taleplerinize en geç otuz gün içinde yanıt veriyoruz.
Başvurunuzu iletişim sayfasındaki adres üzerinden yazılı olarak iletmeniz
yeterli. Talebiniz ücretsiz sonuçlandırılır.</p>

<h2>Değişiklikler</h2>
<p>Bu metinde değişiklik yapıldığında güncel sürüm bu sayfada yayınlanır.
Önemli bir değişiklik olması halinde ayrıca bilgilendirme yapılır.</p>

<p>Kanunun 11. maddesi kapsamında; verilerinizin işlenip işlenmediğini öğrenme,
düzeltilmesini veya silinmesini isteme ve işleme faaliyetine itiraz etme
hakkınız vardır. Bu haklarınızı kullanmak için
<a href="/iletisim">iletişim sayfasındaki</a> adresten bize ulaşabilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'page', 'slug' => 'gizlilik-politikasi', 'sort' => 91,
        'title' => 'Gizlilik Politikası',
        'meta_title' => 'Gizlilik Politikası — Arcates Yazılım',
        'meta_description' => 'Sitemizde hangi verilerin toplandığını, çerez kullanımını ve güvenlik önlemlerini açıklayan gizlilik politikası.',
        'excerpt' => 'Sitede hangi verilerin toplandığı ve nasıl korunduğu.',
        'content' => <<<'HTML'
<h2>Çerez kullanımı</h2>
<p>Bu sitede reklam veya izleme çerezi kullanılmaz. Yalnızca oturum yönetimi için
gerekli teknik çerez bulunur; bu çerez panelde oturum açıldığında oluşur ve
tarayıcı kapatıldığında silinir.</p>

<h2>Ziyaret kayıtları</h2>
<p>Hangi sayfaların ne kadar ziyaret edildiğini ölçüyoruz. Bu ölçüm için ziyaretçi
kimliği değil, IP ve tarayıcı bilgisinden üretilen ve geri çevrilemeyen bir karma
kullanılır. Arama motoru robotları ayrı işaretlenir ve istatistiğe girmez.</p>

<h2>Üçüncü taraf hizmetler</h2>
<p>Sitede yalnızca Google Fonts üzerinden yazı tipi yüklenir. Bunun dışında üçüncü
taraf bir ölçüm veya reklam betiği çalıştırılmaz.</p>

<h2>Güvenlik</h2>
<p>Site tamamen HTTPS üzerinden yayın yapar. Form gönderimleri doğrulama
belirteciyle korunur, yüklenen dosyalar tür ve içerik açısından denetlenir,
veritabanı sorguları hazırlanmış ifadelerle çalışır.</p>

<h2>Veri saklama süreleri</h2>
<p>Form kayıtları panelde belirlenen süre boyunca tutulur; varsayılan süre iki
yıldır ve süresi dolan kayıtlar günlük görevle otomatik silinir. Ham ziyaret
kayıtları doksan gün sonra kişiselleştirilemez toplu istatistiğe dönüştürülür ve
ayrıntılı kayıt silinir.</p>

<h2>Erişim yetkileri</h2>
<p>Panelde iki rol vardır. Yönetici tüm bölümlere erişir; editör yalnızca içerik
bölümlerini görür ve form kayıtlarına ya da ayarlara dokunamaz. Her işlem, kim
tarafından ne zaman yapıldığıyla birlikte kaydedilir.</p>

<h2>Yedekler</h2>
<p>Veritabanı günlük olarak yedeklenir ve yedekler sunucuda web erişimine kapalı
bir klasörde tutulur. Son on yedek saklanır, eskiler otomatik silinir.</p>

<p>Kişisel verilerin işlenmesine ilişkin ayrıntılı bilgi
<a href="/kvkk">KVKK aydınlatma metnimizde</a>.</p>

<h2>Form gönderimlerinde toplananlar</h2>
<p>Teklif formunu doldurduğunuzda, formu hangi sayfadan gönderdiğiniz ve siteye
hangi kaynaktan geldiğiniz de kaydedilir. Bu bilgi kişisel bir profil
oluşturmak için değil, hangi sayfanın ise yaradığını ölçmek için kullanılır.</p>

<h2>Spam koruması</h2>
<p>Otomatik gönderimleri engellemek için formda görünmez bir alan ve zaman
kontrolü bulunur. Bu kontroller için ek bir kişisel veri toplanmaz.</p>

<p>Kişisel verilerin işlenmesine ilişkin ayrıntılı bilgi için
<a href="/kvkk">KVKK aydınlatma metnimize</a> bakabilirsiniz.</p>
HTML,
    ],

    // --- 4.2 Hizmet sayfalari ----------------------------------------------

    [
        'type' => 'service', 'slug' => 'web-tasarim', 'sort' => 10,
        'title' => 'Web Tasarım',
        'meta_title' => 'Web tasarım — Edremit ve Balıkesir | Arcates',
        'meta_description' => 'Mobilde hızlı açılan, aramalarda görünür ve yönetimi kolay kurumsal web siteleri. Edremit Körfezi bölgesinde yerinde hizmet.',
        'excerpt' => 'Mobilde hızlı açılan, aramalarda görünür, yönetimi kolay kurumsal siteler.',
        'content' => <<<'HTML'
<h2>Kurumsal site neye yarar</h2>
<p>Bir işletmenin web sitesi katalog değildir; müşterinin size ulaşmasının en kısa
yoludur. Bu yüzden tasarıma değil, ziyaretçinin ne aradığına bakarak başlıyoruz.
Telefon numarası bulunamayan, mobilde geç açılan ya da aramalarda çıkmayan bir
site güzel olsa da ise yaramaz.</p>

<h2>Neler yapıyoruz</h2>
<ul>
  <li>İşletmeye özel tasarım; hazır şablon üzerine kurulmuyor</li>
  <li>Mobil öncelikli düzen ve hız optimizasyonu</li>
  <li>Panelden yönetilebilir her metin, görsel ve bağlantı</li>
  <li>Yerel arama için başlık, açıklama ve yapısal veri kurulumu</li>
  <li>Google İşletme Profili ile uyumlu iletişim bilgileri</li>
</ul>

<h2>Süre ve sonrası</h2>
<p>Kurumsal bir site ortalama üç haftada yayına alınır. Yayından sonra bir ay
ücretsiz destek verilir; sonrasında dilerseniz
<a href="/web-sitesi-bakim">bakım anlaşması</a> ile devam ediyoruz.</p>

<h2>Tasarımdan önce cevaplanan sorular</h2>
<p>İse renk ve yazı tipi seçerek başlamıyoruz. Önce şu soruları cevaplıyoruz:
Bu siteye giren kişi ne arıyor? Aradığını bulunca ne yapmasını istiyoruz?
Rakipler aynı aramada ne gösteriyor? Bu üç sorunun cevabı netleştiğinde sayfa
düzeni kendiliğinden ortaya çıkar.</p>

<p>Çoğu işletmede cevap şaşırtıcı şekilde basittir: ziyaretçi fiyat aralığını,
çalışma saatini ve konumu arıyordur. Bu bilgiler ilk ekranda yoksa tasarımın
geri kalanı bir ise yaramaz.</p>

<h2>Hız neden pazarlık konusu değil</h2>
<p>Mobilde üç saniyeden geç açılan bir sayfada ziyaretçilerin yaklaşık yarısı
sayfayı terk eder. Bu yüzden yüklenen her görsel üç ölçüde ve WebP biçimiyle
yeniden üretilir, tek bir stil ve tek bir betik dosyası kullanılır, harici
kaynak olarak yalnızca yazı tipi çağrılır.</p>

<h2>Erişilebilirlik</h2>
<p>Klavyeyle gezinebilme, görünür odak halkası, yeterli renk kontrastı ve
görsellerde alt metni; bunlar hem yasal bir gereklilik hem de arama
motorlarının değerlendirdiği başlıklar. Teslim ettiğimiz her sayfa bu
kontrollerden geçiyor.</p>

<p>Bölgeye özel çalışmalarımızı <a href="/edremit-web-tasarim">Edremit</a> ve
<a href="/ayvalik-web-tasarim">Ayvalık</a> sayfalarında görebilirsiniz.</p>

<h2>Teslim edilenler</h2>
<p>Her projede şu dosya ve erişimler müşteriye teslim edilir: sitenin tüm
kaynak dosyaları, veritabanı yedeği, panel yönetici hesabı ve alan adı
yönlendirme bilgileri. Teknik bir devir gerektiğinde bu paket başka bir ekibe
olduğu gibi verilebilir.</p>

<p>Bölgedeki çalışmalarımızı <a href="/referanslar">referanslar sayfasında</a>
görebilir, <a href="/fiyatlar">fiyat aralıklarını</a> inceleyebilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'service', 'slug' => 'e-ticaret-sitesi', 'sort' => 11,
        'title' => 'E-Ticaret Sitesi',
        'meta_title' => 'E-ticaret sitesi kurulumu — zeytinyağı ve yerel ürünler',
        'meta_description' => 'Zeytinyağı, zeytin ve yerel ürünler için satışa hazır e-ticaret altyapısı. Kargo, stok ve ödeme entegrasyonlarıyla.',
        'excerpt' => 'Zeytinyağı, zeytin ve yerel ürünler için satışa hazır mağaza altyapısı.',
        'content' => <<<'HTML'
<h2>Yerel ürünü internetten satmak</h2>
<p>Körfez bölgesinin en güçlü ürünü zeytin ve zeytinyağı. Üreticinin en büyük
sorunu ise aracıya kalan pay. Kendi mağazanız olduğunda ürününüzü doğrudan
satıyor, müşteri listenizi kendiniz kuruyorsunuz.</p>

<h2>Kurduğumuz mağazada neler var</h2>
<ul>
  <li>Ürün, varyant (litre, kilogram) ve stok yönetimi</li>
  <li>Kargo firması entegrasyonu ve bölgeye göre kargo ücreti</li>
  <li>Sanal pos veya havale ile ödeme</li>
  <li>Hasat dönemine göre on sipariş ve kampanya kurulumu</li>
  <li>Çoklu dil; yurt dışı müşteriye satış</li>
</ul>

<h2>Sık sorulan bir soru</h2>
<p>"Pazaryerinde satıyorum, siteye ne gerek var?" Pazaryeri müşteriyi size değil
kendine bağlar; komisyon öderken müşteri listesi de sizde kalmaz. Kendi
mağazanız uzun vadede daha ucuz ve daha değerlidir.</p>

<h2>İlk siparişe kadar olan yol</h2>
<p>Mağazayı açmak işin yarısı. İlk siparişin gelmesi için ürün fotoğrafları,
kargo anlaşması, iade süreci ve ödeme altyapısının hazır olması gerekir. Bu
adımların hiçbiri tek başına zor değildir ama birlikte planlanmadığında ilk
siparişte sorun çıkar.</p>

<p>Kurduğumuz mağazalarda bu adımların hepsi baştan tanımlı gelir. Siz yalnızca
ürün, fiyat ve stok girersiniz; kargo ücreti bölgeye göre hesaplanır, sipariş
onayı müşteriye otomatik gider.</p>

<h2>Tekrar eden müşteri</h2>
<p>Yerel üründe asıl kar, ikinci ve üçüncü sipariştedir. Bu yüzden mağazanın
müşteri hesabı, geçmiş siparişleri ve tekrar sipariş kolaylığı taşıması
gerekir. Geçen yıl ürününüzü beğenen müşteriye yeni hasat duyurusu gönderebilmek,
reklam bütçesinden çok daha değerlidir.</p>

<h2>Stok ve sezon</h2>
<p>Yerel ürünlerin çoğu mevsimlik. Stok bittiğinde ürünü gizlemek yerine
bekleme listesi açmak, hem talebi ölçmenizi hem de sonraki sezona hazır bir
müşteri listesiyle girmenizi sağlar.</p>

<p><a href="/zeytinyagi-e-ticaret-sitesi">Zeytinyağı üreticileri</a> ve
<a href="/coklu-dil-web-sitesi">yurt dışına satış</a> için ayrı sayfalarımız var.</p>

<h2>Yasal başlıklar</h2>
<p>Mesafeli satış sözleşmesi, iade ve teslimat koşulları ile gizlilik metni
mağazanın zorunlu parçaları. Bu sayfaların baştan doğru kurulması hem yasal
gereklilik hem de müşteride güven oluşturan bir ayrıntı.</p>

<h2>İlk üç ay</h2>
<p>Yeni bir mağazada ilk üç ay öğrenme dönemidir: hangi ürün ilgi görüyor, hangi
sayfa terk ediliyor, kargo ücreti kararı nasıl etkiliyor. Panelde bu veriler
takip edilir ve ürün düzeni buna göre sadeleştirilir.</p>

<p>Zeytinyağı üreticileri için hazırladığımız
<a href="/zeytinyagi-e-ticaret-sitesi">sektörel sayfamıza</a> da bakabilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'service', 'slug' => 'rezervasyon-sistemi', 'sort' => 12,
        'title' => 'Rezervasyon Sistemi',
        'meta_title' => 'Otel ve pansiyon rezervasyon sistemi — komisyonsuz',
        'meta_description' => 'Otel, pansiyon ve kamp alanları için doğrudan rezervasyon altyapısı. Komisyon ödemeden kendi müşterinizi kazanın.',
        'excerpt' => 'Otel, pansiyon ve kamp alanları için komisyonsuz doğrudan rezervasyon.',
        'content' => <<<'HTML'
<h2>Komisyonun gerçek maliyeti</h2>
<p>Rezervasyon sitelerinin aldığı komisyon çoğu işletmede yüzde on beş ile yirmi
beş arasında. Sezon boyunca bu rakam, kendi rezervasyon sisteminizin maliyetinin
kat kat üzerine çıkar. Üstelik müşteri bilgisi de sizde kalmaz.</p>

<h2>Sistemde neler var</h2>
<ul>
  <li>Oda ve dönem bazlı fiyatlandırma; sezon dışı indirimler</li>
  <li>Müsaitlik takvimi ve minimum konaklama kuralları</li>
  <li>Kapora veya tam ödeme seçeneği</li>
  <li>Onay ve hatırlatma e-postaları</li>
  <li>Çoklu dil; Almanca ve İngilizce misafirler için</li>
</ul>

<h2>Pazaryerinden vazgeçmek gerekmiyor</h2>
<p>Amaç pazaryerlerini bırakmak değil, oradan gelen misafiri bir sonraki sefere
doğrudan size getirmek. Kendi kanalınız güçlendikçe komisyonlu satışın payı
kendiliğinden düşer.</p>

<h2>Kurulum nasıl ilerliyor</h2>
<p>Önce oda tipleri, kapasiteler ve sezon dönemleri tanımlanır. Ardından fiyat
tablosu, minimum konaklama kuralları ve iptal koşulları girilir. Üç günlük bir
çalışmayla sistem devreye alınabilir; asıl zamanı alan şey fiyat politikasının
netleşmesidir.</p>

<h2>Müsaitlik yönetimi</h2>
<p>Platformlarla aynı odaları satarken en büyük risk çift rezervasyondur.
Sistemi kurarken işletmenin günlük akışına göre iki yol sunuyoruz: ya odaların
bir bölümü doğrudan satışa ayrılır, ya da müsaitlik tek bir yerden yönetilip
platformlara oradan yansıtılır.</p>

<h2>İptal ve kapora</h2>
<p>Doğrudan rezervasyonun en çok tereddüt yaratan tarafı ödeme. Kapora tutarını
ve iptal süresini net yazmak, misafirin platform yerine sizi seçmesini
kolaylaştırır. Belirsiz bırakılan koşul, misafiri komisyonlu ama güvendiği
kanala iter.</p>

<h2>Sezon sonrası</h2>
<p>Sezon bittiğinde elinizde misafir listesi kalır. Bu listeye gelecek yıl
erken rezervasyon duyurusu göndermek, doğrudan satışın en verimli tarafıdır ve
hiçbir komisyon içermez.</p>

<p><a href="/otel-pansiyon-web-sitesi">Konaklama sektörü sayfamıza</a> ve
<a href="/akcay-web-tasarim">Akçay</a> çalışmalarımıza bakabilirsiniz.</p>

<h2>Mevcut sisteme geçiş</h2>
<p>Halihazırda bir rezervasyon defteri ya da tablo kullanıyorsanız, mevcut
kayıtlar taşınabilir. Sezon ortasında geçiş önerilmez; en uygun zaman sezon
kapanışıdır.</p>

<h2>Personel eğitimi</h2>
<p>Sistemi kullanacak kişiyle bir saatlik bir oturum yapıyoruz. Rezervasyon
onaylama, fiyat değiştirme ve iptal işlemleri panelde birkaç tıklamayla
yapılıyor; ayrı bir teknik bilgi gerekmiyor.</p>

<h2>Yasal bilgiler</h2>
<p>On ödeme alınan sistemlerde mesafeli satış sözleşmesi ve iptal koşullarının
yayınlanması zorunludur; bu sayfaları kurulumla birlikte hazırlıyoruz.</p>

<p>Konaklama işletmeleri için hazırladığımız
<a href="/otel-pansiyon-web-sitesi">sektörel sayfaya</a> göz atabilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'service', 'slug' => 'seo-hizmeti', 'sort' => 13,
        'title' => 'SEO Hizmeti',
        'meta_title' => 'Yerel SEO hizmeti — Edremit, Balıkesir ve Körfez',
        'meta_description' => 'Yerel aramalarda üst sıralara çıkmak için teknik düzeltme, içerik çalışması ve Google İşletme Profili yönetimi.',
        'excerpt' => 'Yerel aramalarda üst sıralara çıkmak için teknik ve içerik çalışması.',
        'content' => <<<'HTML'
<h2>Yerel SEO neden farklı</h2>
<p>"Web tasarım" araması ile "edremit web tasarım" araması bambaşka iki iştir.
İkincisinde rekabet daha az, niyet daha nettir: arayan kişi zaten hizmet almak
üzeredir. Bölgedeki işletmeler için doğru hedef budur.</p>

<h2>Çalışmanın adımları</h2>
<ol>
  <li><strong>Teknik denetim.</strong> Hız, mobil uyum, başlık yapısı, kırık
      bağlantılar ve dizine alma sorunları.</li>
  <li><strong>Google İşletme Profili.</strong> İsim, adres ve telefon bilgisinin
      site ile birebir aynı olması; kategori ve görsel düzeni.</li>
  <li><strong>İçerik.</strong> Her hizmet ve her bölge için ayrı, gerçekten
      özgün sayfalar.</li>
  <li><strong>Ölçüm.</strong> Hangi sayfanın telefon ve form getirdiğinin
      takibi.</li>
</ol>

<h2>Yapmadığımız şeyler</h2>
<p>Aynı metni ilçe adı değiştirerek çoğaltmıyoruz; bu yöntem kısa vadede sayfa
sayısını artırsa da Google tarafından yakalandığında tüm sayfaları birden
değersizleştirir. Uydurma yorum ve puanlama işaretlemesi de yapmıyoruz.</p>

<h2>İlk üç ayda ne oluyor</h2>
<p>Teknik düzeltmelerin etkisi genellikle dört ile altı hafta içinde görülür.
İçerik çalışmasının karşılığı ise üç ile altı ay arasında alınır. Bu süreleri
baştan söylememizin sebebi basit: kısa vadede sonuç vaat eden yöntemlerin çoğu
riskli, bir bölümü de doğrudan zararlıdır.</p>

<h2>Ölçüm neye bakıyor</h2>
<p>Sıralama tek başına anlamlı bir ölçüt değil. Bizim baktığımız üç şey var:
kaç kişi geldi, hangi sayfadan geldi ve kaç tanesi telefon etti ya da form
doldurdu. Panelde her form kaydı kaynak sayfasıyla birlikte tutulduğu için bu
tablo üç ay sonra net bir şekilde okunabiliyor.</p>

<h2>İçerik kimin işi</h2>
<p>Metinleri biz yazabiliriz ama en iyi içerik işletmenin kendi bilgisinden
çıkar. Genellikle şu bölüşümü öneriyoruz: teknik yapıyı ve başlık düzenini biz
kuruyoruz, sektöre özgü bilgiyi işletme veriyor, yazıya dönüştürmeyi biz
yapıyoruz.</p>

<h2>Google İşletme Profili</h2>
<p>Yerel aramada en hızlı kazanç genellikle burada. Kategori seçimi, hizmet
listesi, gerçek fotoğraf, çalışma saati ve düzenli gönderi; bu beş başlığın
düzgün kurulması çoğu işletmede haritada görünürlüğü belirgin şekilde
artırıyor.</p>

<p>Yaklaşımın sonucunu <a href="/balikesir-web-tasarim">Balıkesir</a> ve
<a href="/havran-web-tasarim">Havran</a> sayfalarında görebilirsiniz.</p>

<h2>Raporlama</h2>
<p>Aylık raporlarda dört başlık var: gelen ziyaret, en çok giriş alan sayfalar,
form ve telefon sayısı, bir önceki aya göre değişim. Rapor iki sayfayı geçmez ve
teknik terim içermez.</p>

<p>Bölgeye özel sayfalarımızdan biri olan
<a href="/edremit-web-tasarim">Edremit web tasarım sayfamıza</a> bakarak
yaklaşımımızı görebilirsiniz.</p>
HTML,
    ],

    [
        'type' => 'service', 'slug' => 'coklu-dil-web-sitesi', 'sort' => 14,
        'title' => 'Çoklu Dil Web Sitesi',
        'meta_title' => 'Çoklu dil web sitesi — Türkçe, İngilizce, Almanca, Arapça',
        'meta_description' => 'Doğru hreflang kurulumuyla çoklu dil web sitesi. Yabancı misafir ve alıcı hedefleyen işletmeler için.',
        'excerpt' => 'Türkçe, İngilizce, Almanca ve Arapça yayın; doğru hreflang kurulumu.',
        'content' => <<<'HTML'
<h2>Kimin çoklu dile ihtiyacı var</h2>
<p>Körfezde yaz aylarında Alman ve İngiliz misafir ağırlayan konaklama
işletmeleri, yurt dışına ürün gönderen zeytinyağı üreticileri ve yabancı
alıcılara satış yapan emlak ofisleri için ikinci dil bir lüks değil, doğrudan
gelir kalemidir.</p>

<h2>Otomatik çeviri neden yetmez</h2>
<p>Tarayıcı çevirisi ya da otomatik çeviri eklentileri arama motorları için ayrı
bir sayfa üretmez; yanı Almanca arayan biri sizi bulamaz. Gerçek çoklu dil, her
dil için ayrı adres ve ayrı içerik demektir.</p>

<h2>Doğru kurulum neye benzer</h2>
<ul>
  <li>Her dil için ayrı adres: <code>/en/...</code>, <code>/de/...</code></li>
  <li>Diller arası <code>hreflang</code> bildirimi; karşılığı olmayan dil
      bildirilmez</li>
  <li>Arapça için sağdan sola düzen</li>
  <li>Panelden dil bazlı içerik yönetimi</li>
</ul>

<h2>Hangi dilleri seçmeli</h2>
<p>Her dil ek bir bakım yükü demek. Bu yüzden dil seçimini tahminle değil veriyle
yapıyoruz: mevcut ziyaretçilerin tarayıcı dili, gelen rezervasyon ya da
siparişlerin ülkesi ve hedeflenen pazar. Körfez bölgesinde en sık anlamlı olan
sıra İngilizce, Almanca ve Arapça.</p>

<h2>Çeviri değil, yerelleştirme</h2>
<p>Aynı metnin çevirisi çoğu zaman ise yaramaz çünkü her kitlenin sorusu farklı.
Alman misafir kahvaltı saatini ve sessizliği sorar; Arap misafir aile odasını ve
mutfak düzenini sorar. İyi bir çoklu dil kurulumu bu farkı metne yansıtır.</p>

<h2>Arapça ve sağdan sola düzen</h2>
<p>Arapça yayın yalnızca metni çevirmek değildir; sayfanın yönü değişir. Menü,
butonlar ve form alanları sağdan sola akmalıdır. Bunu mantıksal CSS
özellikleriyle yapıyoruz, böylece aynı tasarım iki yönde de bozulmadan
çalışıyor.</p>

<h2>Eksik çeviri sorun değildir</h2>
<p>Bir sayfanın her dilde karşılığı olmak zorunda değil. Önemli olan, olmayan
dil için arama motoruna yanlış bilgi vermemek. Karşılığı olmayan dil için
hreflang bildirimi yapılmaz; böylece yanlış dilde sayfa gösterilmesi
önlenir.</p>

<p><a href="/ayvalik-web-tasarim">Ayvalık</a> ve
<a href="/e-ticaret-sitesi">e-ticaret</a> sayfalarımızda örnekler var.</p>

<h2>Bakım yükü</h2>
<p>Her yeni dil, güncellenmesi gereken yeni bir içerik demek. Bu yüzden hangi
sayfaların çevrileceğine baştan karar veriyoruz; genellikle anasayfa, hizmet
sayfaları ve iletişim yeterli oluyor. Blog yazıları çoğu işletmede tek dilde
kalıyor ve bu bir sorun değil.</p>

<p>Kurulumu yapılmış bir örnek görmek isterseniz
<a href="/iletisim">bize yazın</a>.</p>
HTML,
    ],

    [
        'type' => 'service', 'slug' => 'web-sitesi-bakim', 'sort' => 15,
        'title' => 'Web Sitesi Bakım',
        'meta_title' => 'Web sitesi bakım hizmeti — aylık sabit ücret',
        'meta_description' => 'Güncelleme, yedekleme, güvenlik ve içerik desteği. Aylık sabit ücretle web sitesi bakım anlaşması.',
        'excerpt' => 'Güncelleme, yedekleme, güvenlik ve içerik desteği; aylık sabit ücret.',
        'content' => <<<'HTML'
<h2>Bakımsız site ne olur</h2>
<p>Bir web sitesi kurulup bırakıldığında genellikle iki yıl içinde ya güvenlik
açığından ele geçirilir ya da içeriği eskidiği için aramalarda geriler. İkisi de
sessizce olur; fark ettiğinizde iş işten geçmiş olur.</p>

<h2>Bakım kapsamında neler var</h2>
<ul>
  <li>Günlük otomatik yedek ve yedekten dönüş provası</li>
  <li>Güvenlik güncellemeleri ve günlük denetimi</li>
  <li>Ayda belirli süreye kadar içerik güncellemesi</li>
  <li>Hız ve kırık bağlantı kontrolü</li>
  <li>Aylık kısa rapor: ziyaret, form ve sıralamalar</li>
</ul>

<h2>Anlaşma nasıl işliyor</h2>
<p>Aylık sabit ücret, taahhüt yok. İstediğiniz ay bırakabilirsiniz; sitenin tüm
dosyaları ve veritabanı zaten sizindir.</p>

<h2>Yedeğin provası yapılmadan yedek sayılmaz</h2>
<p>Çoğu işletme yedek aldığını sanır ama yedeğin geri yüklenip yüklenemediğini
hiç denemez. Bakım kapsamında yılda en az bir kez geri yükleme provası
yapıyoruz. Çalışmayan bir yedek, hiç yedek olmamasından daha tehlikelidir çünkü
yanlış bir güven verir.</p>

<h2>Güncelleme ne demek</h2>
<p>Sitede kullanılan hiçbir hazır eklenti olmadığı için klasik anlamda
"eklenti güncellemesi" yok. Bizim yaptığımız şey PHP sürümü, sunucu
yapılandırması ve güvenlik başlıklarının takibi; ayrıca kırık bağlantı ve hız
kontrolü.</p>

<h2>Aylık rapor</h2>
<p>Her ayın başında kısa bir rapor gönderiyoruz: ziyaret sayısı, en çok girilen
sayfalar, gelen form sayısı ve varsa dikkat edilmesi gereken bir konu. Rapor iki
sayfayı geçmez; amaç veri yığını değil karar verdirmek.</p>

<h2>İçerik desteği</h2>
<p>Ayda belirli bir süreye kadar içerik güncellemesi kapsam içinde. Fiyat
değişikliği, yeni fotoğraf, kampanya duyurusu gibi işler için ayrıca ücret
alınmıyor.</p>

<p><a href="/fiyatlar">Fiyat aralıkları</a> ve
<a href="/sss">sık sorulan sorular</a> sayfalarına bakabilirsiniz.</p>

<h2>Acil durum</h2>
<p>Site erişilemez hale gelirse ilk müdahaleyi aynı gün içinde yapıyoruz. Yedekten
dönüş gerektiğinde son günlük yedekle geri dönülüyor; bu işlem genellikle bir
saatten kısa sürüyor.</p>

<h2>Anlaşma dışı işler</h2>
<p>Yeni sayfa tasarımı, yeni modül ya da kapsamlı içerik üretimi bakım
kapsamında değildir; bunlar ayrı teklif olarak konuşulur ve onayınız olmadan
başlamaz.</p>

<h2>Anlaşmanın sonlanması</h2>
<p>Bakımı bırakmanız durumunda son yedek ve tüm erişim bilgileri size teslim
edilir. Hiçbir şey elimizde tutulmaz.</p>

<p>Bakım kapsamını kendi siteniz için konuşmak isterseniz
<a href="/iletisim">iletişim formunu</a> doldurun.</p>
HTML,
    ],
];
