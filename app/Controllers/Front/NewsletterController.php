<?php
/**
 * Bulten aboneligi — kayit, onay ve cikis.  DOCS.md 4.1, 12
 *
 * Cift onay: adres girildiginde kayit `pending` olur ve adrese bir onay
 * baglantisi gider; ziyaretci tiklayana kadar liste "etkin" saymaz. Boylece
 * baskasinin adresini yazan biri o kisiyi listeye sokamaz ve elimizde
 * onayin kaniti olur.
 *
 * Onay ve cikis GET ile calisir. Kimlik dogrulamasi oturuma degil,
 * baglantidaki 64 haneli gizli anahtara dayanir: anahtari bilmeyen hicbir
 * kaydi degistiremez, bilen zaten kendi kaydini degistirebilir. Bu yuzden
 * burada CSRF belirtecinin koruyacagi bir sey yoktur. E-posta icindeki
 * baglantiya CSRF belirteci de konamaz, cunku e-posta oturumsuzdur.
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\Config;
use Arcates\Core\Lang;
use Arcates\Core\Logger;
use Arcates\Core\Mailer;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Security;
use Arcates\Core\Seo;
use Arcates\Core\Session;
use Arcates\Core\Settings;
use Arcates\Core\Validator;
use Arcates\Models\Newsletter;

final class NewsletterController extends Controller
{
    private const HONEYPOT = 'website';

    /** Alt bilgideki formun gonderimi. */
    public function subscribe(Request $request): Response
    {
        $return = $this->safeReturn($request);

        // 1. CSRF. CLAUDE.md 6
        if (!Security::csrfCheck((string) $request->post('_token'))) {
            Logger::activity('csrf.fail', 'newsletter', null, $request->path());
            return Response::html(\Arcates\Core\View::render('errors/419'), 419);
        }

        // 2. Honeypot — dolu ise bot basarili sansin, kayit olusmasin.
        if (trim((string) $request->post(self::HONEYPOT, '')) !== '') {
            Logger::info('Bülten honeypot yakalandı', ['ip' => $request->ip()]);
            return $this->ok($return);
        }

        // 3. IP basina saatlik sinir; iletisim formuyla ayni ayar.
        $limit = (int) Config::get('security.form_max_hourly', 5);
        if ($limit > 0 && Newsletter::recentByIp($request->ip()) >= $limit) {
            Logger::info('Bülten saatlik sınırı aşıldı', ['ip' => $request->ip()]);
            return $this->reject($return, __('newsletter_rate_limited'));
        }

        // 4. Dogrulama. Acik riza kutusu zorunlu: KVKK'da riza acik ve
        //    ozgur iradeyle verilmis olmali, onceden isaretli olamaz.
        $validator = new Validator($request->allPost(), [
            'email' => 'E-posta',
        ]);
        $validator->required('email')->email('email')->max('email', 190)
            ->accepted('kvkk', __('newsletter_consent_required'));

        if ($validator->fails()) {
            Session::flash('error', $validator->firstErrors()['email'] ?? __('newsletter_consent_required'));
            return Response::redirect($return . '#bulten');
        }

        $email  = (string) $request->post('email', '');
        $sonuc  = Newsletter::subscribe($email, Lang::current(), $request->ip(), $return);

        if (!$sonuc['zaten_etkin']) {
            $this->sendConfirmation($email, $sonuc['token']);
        }

        // Ziyaretciye her durumda ayni ileti gosterilir: aksi halde form,
        // bir adresin listede olup olmadigini disariya soyleyen bir arac
        // haline gelir.
        return $this->ok($return);
    }

    /** E-postadaki onay baglantisi. */
    public function confirm(Request $request, array $params): Response
    {
        $ok = Newsletter::confirm((string) ($params['token'] ?? ''));

        return $this->page(
            $ok ? __('newsletter_confirmed_title') : __('newsletter_invalid_title'),
            $ok ? __('newsletter_confirmed_text') : __('newsletter_invalid_text')
        );
    }

    /** Her iletideki cikis baglantisi. */
    public function unsubscribe(Request $request, array $params): Response
    {
        $ok = Newsletter::unsubscribe((string) ($params['token'] ?? ''));

        return $this->page(
            $ok ? __('newsletter_unsubscribed_title') : __('newsletter_invalid_title'),
            $ok ? __('newsletter_unsubscribed_text') : __('newsletter_invalid_text')
        );
    }

    // --- Yardimcilar --------------------------------------------------------

    private function sendConfirmation(string $email, string $token): void
    {
        $site = (string) Settings::get('site_name', (string) Config::get('app.name', 'Arcates'));
        $baglanti = url('/bulten/onay/' . $token);
        $cikis    = url('/bulten/cikis/' . $token);

        $govde = __('newsletter_mail_body');
        $govde = str_replace(
            ['{site}', '{onay}', '{cikis}'],
            [$site, $baglanti, $cikis],
            $govde
        );

        Mailer::send($email, str_replace('{site}', $site, __('newsletter_mail_subject')), $govde);
    }

    /** Onay ve cikis sonrasi gosterilen sade sayfa. */
    private function page(string $title, string $text): Response
    {
        return $this->render('front/newsletter', [
            'baslik' => $title,
            'metin'  => $text,
            'head'   => [
                'title'       => Seo::title($title, ''),
                'description' => '',
                'canonical'   => '',
                // Anahtar tasiyan adresler dizine girmemeli.
                'robots'      => 'noindex,nofollow',
            ],
        ]);
    }

    private function ok(string $return): Response
    {
        Session::flash('success', __('newsletter_pending_text'));
        return Response::redirect($return . '#bulten');
    }

    private function reject(string $return, string $message): Response
    {
        Session::flash('error', $message);
        return Response::redirect($return . '#bulten');
    }

    /**
     * Formun gonderildigi sayfaya geri doner. Deger disaridan geldigi icin
     * yalnizca kendi sitemizdeki bir yola izin verilir; aksi halde form
     * acik yonlendirme araci olurdu.
     */
    private function safeReturn(Request $request): string
    {
        $aday = (string) $request->post('_return', '/');

        if ($aday === '' || $aday[0] !== '/' || str_starts_with($aday, '//')) {
            return url('/');
        }

        return url($aday);
    }
}
