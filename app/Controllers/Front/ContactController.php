<?php
/**
 * Teklif formu.
 *
 * Alanlar: ad, telefon, e-posta, ilgilenilen hizmet, mesaj, KVKK onayi,
 * honeypot, zaman damgasi, CSRF token.  DOCS.md 12
 *
 * Spam korumasi (DOCS.md 10.8):
 *   - Honeypot alani doluysa sessizce reddedilir (test S-15)
 *   - Form 3 saniyeden hizli gonderilirse reddedilir (test S-14)
 *   - Ayni IP'den saatte 5 gonderim siniri (test S-16)
 *   - Sunucu tarafi dogrulama
 *
 * Gonderim sonrasi: veritabanina kayit, yoneticiye e-posta, kullaniciya
 * tesekkur sayfasi (/tesekkurler, noindex). Tesekkur sayfasi ayri URL'dir
 * ki donusum olculebilsin.
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
use Arcates\Models\Page;
use Throwable;

final class ContactController extends Controller
{
    /** Honeypot alaninin adi; gercek bir alan gibi gorunur. DOCS.md 10.8 */
    public const HONEYPOT = 'website_url';

    /** Formu iceren sayfayi isler. */
    public function show(Request $request, array $params): Response
    {
        $lang = Lang::current();
        $slug = trim((string) ($params['slug'] ?? 'iletisim'), '/');
        $page = Page::published($slug, $lang);

        if ($page === null) {
            return $this->notFound($request);
        }

        $path = '/' . $page['slug'];

        return $this->render('front/contact', [
            'page'     => $page,
            'faqs'     => Page::faqs((int) $page['id'], $lang),
            'crumbs'   => [
                ['label' => __('home'), 'url' => url('/')],
                ['label' => (string) $page['title'], 'url' => url($path)],
            ],
            'services' => $this->serviceOptions($lang),
            'errors'   => Session::takeErrors(),
            'old'      => Session::takeOld(),
            'flash'    => Session::takeFlash(),
            'head'     => [
                'title'       => Seo::title((string) $page['title'], $page['meta_title'] ?? null),
                'description' => Seo::description($page['meta_description'] ?? null, $page['excerpt'] ?? null, (string) $page['content']),
                'canonical'   => Seo::canonical($path, $page['canonical'] ?? null, $lang),
                'robots'      => (string) ($page['robots'] ?? 'index,follow'),
                'hreflang'    => Seo::hreflang(Page::alternates((int) $page['id'])),
                'schemas'     => [Seo::professionalService()],
            ],
        ]);
    }

    /** Form gonderimi. */
    public function submit(Request $request, array $params): Response
    {
        $lang   = Lang::current();
        $return = url('/' . trim((string) $request->post('_return', 'iletisim'), '/'));

        // 1. CSRF — her POST formunda zorunlu. DOCS.md 10.5
        if (!Security::csrfCheck((string) $request->post('_token'))) {
            Logger::activity('csrf.fail', 'submission', null, $request->path());
            return Response::html(\Arcates\Core\View::render('errors/419'), 419);
        }

        // 2. Honeypot — dolu ise sessizce reddedilir. Test S-15
        if (trim((string) $request->post(self::HONEYPOT, '')) !== '') {
            Logger::info('Honeypot yakalandi', ['ip' => $request->ip()]);
            // Bot basarili sanmali; gercek bir kayit olusmaz.
            return Response::redirect(url('/tesekkurler'));
        }

        // 3. Zaman damgasi — 3 saniyeden hizli gonderim reddedilir. Test S-14
        $opened  = (int) $request->post('_opened', 0);
        $minimum = (int) Config::get('security.form_min_seconds', 3);

        if ($opened <= 0 || (time() - $opened) < $minimum) {
            Logger::info('Cok hizli form gonderimi', ['ip' => $request->ip(), 'sure' => time() - $opened]);
            return $this->reject($return, 'Form cok hizli gonderildi. Lutfen tekrar deneyin.');
        }

        // Cok eski form (24 saatten fazla acik kalmis) da kabul edilmez.
        if ((time() - $opened) > 86400) {
            return $this->reject($return, 'Form suresi doldu. Sayfayi yenileyip tekrar gonderin.');
        }

        // 4. IP basina saatlik sinir. Test S-16
        if ($this->overHourlyLimit($request->ip())) {
            Logger::info('Saatlik form siniri asildi', ['ip' => $request->ip()]);
            return $this->reject($return, 'Kisa surede cok fazla gonderim yapildi. Bir sure sonra tekrar deneyin.');
        }

        // 5. Sunucu tarafi dogrulama. DOCS.md 10.8
        $validator = new Validator($request->allPost(), [
            'name'    => 'Ad soyad',
            'phone'   => 'Telefon',
            'email'   => 'E-posta',
            'message' => 'Mesaj',
        ]);

        $validator->required('name')->max('name', 150)
            ->required('phone')->phone('phone')
            ->required('email')->email('email')
            ->max('service', 80)
            ->required('message')->min('message', 10)->max('message', 4000)
            ->accepted('kvkk', 'Aydinlatma metnini onaylamaniz gerekir.');

        if ($validator->fails()) {
            Session::flashErrors($validator->firstErrors(), [
                'name'    => $request->str('name'),
                'phone'   => $request->str('phone'),
                'email'   => $request->str('email'),
                'service' => $request->str('service'),
                'message' => $request->str('message'),
            ]);
            Session::flash('error', __('form_error_text'));
            return Response::redirect($return . '#teklif-formu');
        }

        // 6. Kayit
        $utm = $request->utm();

        try {
            $id = $this->db()->insert('submissions', [
                'form_key'     => 'contact',
                'name'         => $request->str('name'),
                'email'        => mb_strtolower($request->str('email')),
                'phone'        => $request->str('phone'),
                'service'      => $request->str('service') ?: null,
                'message'      => mb_substr($request->str('message'), 0, 4000),
                // Formun gonderildigi sayfa; hangi sayfanin is getirdigini
                // gosterir. DOCS.md 12
                'source_url'   => mb_substr((string) $request->post('_source', $return), 0, 255),
                'referrer'     => $request->referrer(),
                'utm'          => $utm ? Security::json($utm) : null,
                'lang'         => $lang,
                'kvkk_consent' => 1,
                'status'       => 'new',
                'ip'           => Security::packIp($request->ip()),
                'user_agent'   => $request->userAgent(),
            ]);
        } catch (Throwable $e) {
            Logger::exception($e);
            return $this->reject($return, 'Mesajiniz kaydedilemedi. Kisa sure sonra tekrar deneyin.');
        }

        // 7. Yoneticiye e-posta
        $this->notify($id, $request);

        Logger::activity('submission.create', 'submission', $id, $request->str('email'));

        // 8. Tesekkur sayfasi ayri URL — donusum olculebilsin. DOCS.md 12
        return Response::redirect(url('/tesekkurler'));
    }

    /** Tesekkur sayfasi; noindex. DOCS.md 12 */
    public function thanks(Request $request, array $params): Response
    {
        return $this->render('front/thanks', [
            'head' => [
                'title'       => Seo::title(__('form_success')),
                'description' => '',
                'canonical'   => url('/tesekkurler'),
                // Tesekkur sayfasi dizine girmez.
                'robots'      => 'noindex,nofollow',
                'hreflang'    => [],
                'schemas'     => [],
            ],
        ]);
    }

    // --- Yardimcilar --------------------------------------------------------

    /** Ayni IP'den saatte kac gonderim yapildi? DOCS.md 10.8, test S-16 */
    private function overHourlyLimit(string $ip): bool
    {
        $limit = (int) Config::get('security.form_max_hourly', 5);
        if ($limit <= 0) {
            return false;
        }

        try {
            $count = (int) $this->db()->value(
                'SELECT COUNT(*) FROM submissions WHERE ip = :ip AND created_at > :since',
                [':ip' => Security::packIp($ip), ':since' => date('Y-m-d H:i:s', time() - 3600)]
            );
        } catch (Throwable) {
            return false;
        }

        return $count >= $limit;
    }

    private function reject(string $return, string $message): Response
    {
        Session::flash('error', $message);
        return Response::redirect($return . '#teklif-formu');
    }

    /** Yoneticiye bildirim e-postasi. DOCS.md 12 */
    private function notify(int $id, Request $request): void
    {
        $to = (string) Config::get('mail.to', (string) Settings::get('nap_email', ''));
        if ($to === '') {
            return;
        }

        $lines = [
            'Yeni teklif formu geldi.',
            '',
            'Ad soyad : ' . $request->str('name'),
            'Telefon  : ' . $request->str('phone'),
            'E-posta  : ' . $request->str('email'),
            'Hizmet   : ' . ($request->str('service') ?: '—'),
            '',
            'Mesaj:',
            $request->str('message'),
            '',
            'Kaynak sayfa : ' . (string) $request->post('_source', ''),
            'Geldigi yer  : ' . ((string) $request->referrer() ?: '—'),
            'Dil          : ' . Lang::current(),
            'Kayit no     : #' . $id,
            '',
            'Panelden goruntuleyin: ' . path_url(admin_url('formlar/' . $id)),
        ];

        Mailer::send(
            $to,
            'Yeni teklif formu — ' . $request->str('name'),
            implode("\n", $lines),
            // Yanit dogrudan gonderene gitsin.
            ['Reply-To' => $request->str('email')]
        );
    }

    /** Formdaki hizmet secenekleri; yayindaki hizmet sayfalarindan gelir. */
    private function serviceOptions(string $lang): array
    {
        try {
            return $this->db()->column(
                'SELECT t.title
                   FROM pages p
                   JOIN page_translations t ON t.page_id = p.id AND t.lang = :lang
                  WHERE p.type = :type AND p.status = :status
                  ORDER BY p.sort, t.title',
                [':lang' => $lang, ':type' => 'service', ':status' => 'published']
            );
        } catch (Throwable) {
            return [];
        }
    }
}
