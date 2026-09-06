<?php
/**
 * Ayarlar.
 *
 * Site adi, NAP, sosyal hesaplar, calisma saatleri, varsayilan dil,
 * bakim modu, e-posta.  DOCS.md 9.11, 11.2
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Lang;
use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Security;
use Arcates\Core\Settings;
use Arcates\Core\Validator;

final class SettingController extends Controller
{
    protected string $section = 'settings';
    protected ?string $ability = 'settings.manage';

    /** Duzenlenebilir metin ayarlari ve etiketleri. */
    private const TEXT_FIELDS = [
        'site_name'          => ['Site adi', 120],
        'site_tagline'       => ['Kisa tanim', 200],
        'nap_name'           => ['Isletme adi (NAP)', 150],
        'nap_street'         => ['Adres satiri', 200],
        'nap_district'       => ['Ilce', 60],
        'nap_city'           => ['Sehir', 60],
        'nap_postcode'       => ['Posta kodu', 12],
        'nap_country'        => ['Ulke kodu', 2],
        'nap_phone'          => ['Telefon', 40],
        'nap_email'          => ['E-posta', 190],
        'nap_lat'            => ['Enlem', 20],
        'nap_lng'            => ['Boylam', 20],
        'meta_title_pattern' => ['Baslik sablonu', 120],
        'meta_description'   => ['Varsayilan aciklama', 320],
        'maintenance_text'   => ['Bakim mesaji', 320],
    ];

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        return $this->view('settings', [
            'title'     => 'Ayarlar',
            'fields'    => self::TEXT_FIELDS,
            'settings'  => Settings::all(),
            'hours'     => Settings::getArray('opening_hours'),
            'social'    => Settings::getArray('social_links'),
            'languages' => Lang::languages(),
        ]);
    }

    public function update(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $validator = new Validator($request->allPost(), array_map(
            static fn (array $f): string => $f[0],
            self::TEXT_FIELDS
        ));

        $validator->required('site_name')->max('site_name', 120)
            ->required('nap_name')->max('nap_name', 150);

        if ($request->str('nap_email') !== '') {
            $validator->email('nap_email');
        }

        foreach (self::TEXT_FIELDS as $key => [$label, $max]) {
            $validator->max($key, $max);
        }

        if ($validator->fails()) {
            return $this->withErrors(admin_url('ayarlar'), $validator->firstErrors(), $request->allPost());
        }

        $pairs = [];
        foreach (array_keys(self::TEXT_FIELDS) as $key) {
            $pairs[$key] = $request->str($key);
        }

        // Bakim modu: ziyaretci bakim sayfasini gorur, yonetici siteyi gorur.
        // DOCS.md 9.11, test F-17
        $pairs['maintenance_mode'] = $request->bool('maintenance_mode') ? '1' : '0';

        // Calisma saatleri; yapisal veride opening_hours olarak kullanilir.
        $hours = [];
        foreach ($request->arr('hours_days') as $index => $days) {
            $days   = trim((string) $days);
            $opens  = trim((string) ($request->arr('hours_opens')[$index] ?? ''));
            $closes = trim((string) ($request->arr('hours_closes')[$index] ?? ''));
            if ($days !== '' && $opens !== '' && $closes !== '') {
                $hours[] = ['days' => mb_substr($days, 0, 40), 'opens' => mb_substr($opens, 0, 5), 'closes' => mb_substr($closes, 0, 5)];
            }
        }
        $pairs['opening_hours'] = Security::json($hours);

        $social = [];
        foreach ($request->arr('social_label') as $index => $label) {
            $label = trim((string) $label);
            $url   = trim((string) ($request->arr('social_url')[$index] ?? ''));
            if ($label !== '' && $url !== '' && preg_match('#^https?://#i', $url) === 1) {
                $social[] = ['label' => mb_substr($label, 0, 60), 'url' => mb_substr($url, 0, 255)];
            }
        }
        $pairs['social_links'] = Security::json($social);

        $default = $request->str('default_lang');
        if (Lang::exists($default)) {
            $pairs['default_lang'] = $default;
            $this->db()->run('UPDATE languages SET is_default = 0');
            $this->db()->update('languages', ['is_default' => 1], ['code' => $default]);
        }

        Settings::setMany($pairs);
        Logger::activity('settings.update', 'settings', null, implode(', ', array_keys($pairs)));

        return $this->back(admin_url('ayarlar'), 'success', 'Ayarlar kaydedildi.');
    }
}
