<?php
/**
 * SSS yonetimi.
 *
 * Soru, cevap, hangi sayfalara atanacagi.  DOCS.md 9.4
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Lang;
use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Security;
use Arcates\Core\Validator;
use Arcates\Models\Faq;

final class FaqController extends Controller
{
    protected string $section = 'faqs';
    protected ?string $ability = 'faqs.edit';

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        return $this->view('faqs/index', [
            'title' => 'Sık sorulan sorular',
            'faqs'  => Faq::listing(Lang::defaultCode()),
        ]);
    }

    public function create(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        return $this->view('faqs/form', $this->formData(null));
    }

    public function edit(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $faq = Faq::find((int) ($params['id'] ?? 0));
        if ($faq === null) {
            return $this->back(admin_url('sss'), 'error', 'Kayıt bulunamadı.');
        }

        return $this->view('faqs/form', $this->formData($faq));
    }

    public function store(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id      = (int) ($params['id'] ?? 0);
        $url     = $id > 0 ? admin_url('sss/' . $id) : admin_url('sss/yeni');
        $default = Lang::defaultCode();
        $input   = $request->arr('t');

        $validator = new Validator(
            ['question' => trim((string) ($input[$default]['question'] ?? ''))],
            ['question' => 'Varsayılan dildeki soru']
        );
        $validator->required('question')->max('question', 300);

        if ($validator->fails()) {
            return $this->withErrors($url, $validator->firstErrors(), $request->allPost());
        }

        $translations = [];
        foreach (Lang::codes() as $code) {
            $fields = $input[$code] ?? [];
            $translations[$code] = [
                'question' => trim((string) ($fields['question'] ?? '')),
                'answer'   => Security::sanitizeHtml((string) ($fields['answer'] ?? '')),
            ];
        }

        $faqId = Faq::save(
            ['sort' => $request->int('sort'), 'status' => $request->bool('status') ? 1 : 0],
            $translations,
            array_map('intval', $request->arr('pages')),
            $request->bool('on_home'),
            $id > 0 ? $id : null
        );

        Logger::activity($id > 0 ? 'faq.update' : 'faq.create', 'faq', $faqId);

        return $this->back(admin_url('sss/' . $faqId), 'success', 'SSS kaydı kaydedildi.');
    }

    public function destroy(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id = (int) ($params['id'] ?? 0);
        $this->db()->delete('faqs', ['id' => $id]);
        Logger::activity('faq.delete', 'faq', $id);

        return $this->back(admin_url('sss'), 'success', 'Kayıt silindi.');
    }

    private function formData(?array $faq): array
    {
        $translations = [];
        $pages        = [];
        $onHome       = false;

        if ($faq !== null) {
            foreach ($this->db()->all(
                'SELECT * FROM faq_translations WHERE faq_id = :id',
                [':id' => (int) $faq['id']]
            ) as $row) {
                $translations[$row['lang']] = $row;
            }

            $pages  = array_map('intval', $this->db()->column(
                'SELECT page_id FROM faq_page WHERE faq_id = :id',
                [':id' => (int) $faq['id']]
            ));
            $onHome = $this->db()->value('SELECT faq_id FROM faq_home WHERE faq_id = :id', [':id' => (int) $faq['id']]) !== null;
        }

        return [
            'title'        => $faq === null ? 'Yeni SSS kaydı' : 'SSS kaydını düzenle',
            'faq'          => $faq,
            'translations' => $translations,
            'assigned'     => $pages,
            'onHome'       => $onHome,
            'langs'        => Lang::languages(),
            'pages'        => $this->db()->all(
                'SELECT p.id, p.type, t.title
                   FROM pages p
                   JOIN page_translations t ON t.page_id = p.id AND t.lang = :lang
                  ORDER BY p.type, t.title',
                [':lang' => Lang::defaultCode()]
            ),
        ];
    }
}
