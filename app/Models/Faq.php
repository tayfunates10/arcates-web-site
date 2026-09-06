<?php
/**
 * Sik sorulan sorular.
 *
 * SSS kayitlari sayfalara ve anasayfaya atanabilir.  DOCS.md 8.2, 9.4, 11.2
 */

declare(strict_types=1);

namespace Arcates\Models;

use Arcates\Core\Database;

final class Faq extends Model
{
    protected static string $table = 'faqs';
    protected static string $translations = 'faq_translations';
    protected static string $foreignKey = 'faq_id';

    /** Anasayfaya atanmis SSS kayitlari. DOCS.md 5 (bolum 8) */
    public static function forHome(string $lang, int $limit = 6): array
    {
        return self::db()->all(
            'SELECT f.id, t.question, t.answer
               FROM faq_home fh
               JOIN faqs f ON f.id = fh.faq_id AND f.status = 1
               JOIN faq_translations t ON t.faq_id = f.id AND t.lang = :lang
              ORDER BY fh.sort, f.sort, f.id
              LIMIT ' . max(1, min(24, $limit)),
            [':lang' => $lang]
        );
    }

    /** Tum etkin SSS kayitlari (sss sayfasi icin). */
    public static function published(string $lang): array
    {
        return self::db()->all(
            'SELECT f.id, t.question, t.answer
               FROM faqs f
               JOIN faq_translations t ON t.faq_id = f.id AND t.lang = :lang
              WHERE f.status = 1
              ORDER BY f.sort, f.id',
            [':lang' => $lang]
        );
    }

    public static function listing(string $lang): array
    {
        return self::db()->all(
            'SELECT f.id, f.sort, f.status, t.question,
                    (SELECT COUNT(*) FROM faq_page fp WHERE fp.faq_id = f.id) AS page_count,
                    (SELECT COUNT(*) FROM faq_home fh WHERE fh.faq_id = f.id) AS on_home
               FROM faqs f
               LEFT JOIN faq_translations t ON t.faq_id = f.id AND t.lang = :lang
              ORDER BY f.sort, f.id',
            [':lang' => $lang]
        );
    }

    /** SSS kaydini cevirileri, sayfa baglantilari ve anasayfa atamasiyla kaydeder. */
    public static function save(array $faq, array $translations, array $pageIds, bool $onHome, ?int $id = null): int
    {
        return self::db()->transaction(static function (Database $db) use ($faq, $translations, $pageIds, $onHome, $id): int {
            if ($id !== null && $id > 0) {
                $db->update('faqs', $faq, ['id' => $id]);
                $faqId = $id;
            } else {
                $faqId = $db->insert('faqs', $faq);
            }

            foreach ($translations as $lang => $fields) {
                $question = trim((string) ($fields['question'] ?? ''));

                if ($question === '') {
                    $db->run(
                        'DELETE FROM faq_translations WHERE faq_id = :id AND lang = :lang',
                        [':id' => $faqId, ':lang' => $lang]
                    );
                    continue;
                }

                $db->upsert(
                    'faq_translations',
                    [
                        'faq_id'   => $faqId,
                        'lang'     => $lang,
                        'question' => mb_substr($question, 0, 300),
                        'answer'   => (string) ($fields['answer'] ?? ''),
                    ],
                    ['question', 'answer']
                );
            }

            $db->run('DELETE FROM faq_page WHERE faq_id = :id', [':id' => $faqId]);
            foreach (array_unique(array_filter(array_map('intval', $pageIds))) as $pageId) {
                $exists = $db->value('SELECT id FROM pages WHERE id = :id', [':id' => $pageId]);
                if ($exists !== null) {
                    $db->insert('faq_page', ['faq_id' => $faqId, 'page_id' => $pageId]);
                }
            }

            $db->run('DELETE FROM faq_home WHERE faq_id = :id', [':id' => $faqId]);
            if ($onHome) {
                $db->insert('faq_home', ['faq_id' => $faqId, 'sort' => (int) ($faq['sort'] ?? 0)]);
            }

            return $faqId;
        });
    }
}
