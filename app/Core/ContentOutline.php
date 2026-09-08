<?php
/**
 * Zengin metin basliklarindan guvenli icindekiler listesi ve capa kimlikleri uretir.
 *
 * Icerik once Security::sanitizeHtml() ile temizlenir; ardindan yalnizca h2/h3
 * etiketlerine uygulama tarafindan uretilmis guvenli `id` nitelikleri eklenir.
 * Kullanici girdisinden gelen nitelikler yeniden kullanilmaz.
 */

declare(strict_types=1);

namespace Arcates\Core;

final class ContentOutline
{
    /**
     * @return array{html:string,items:array<int,array{id:string,label:string,level:int}>}
     */
    public static function prepare(string $html): array
    {
        $clean = Security::sanitizeHtml($html);
        if ($clean === '') {
            return ['html' => '', 'items' => []];
        }

        $items = [];
        $used  = [];

        $clean = preg_replace_callback(
            '#<h([23])>(.*?)</h\1>#isu',
            static function (array $match) use (&$items, &$used): string {
                $level = (int) $match[1];
                $label = trim(Security::toPlainText((string) $match[2]));
                if ($label === '') {
                    return $match[0];
                }

                $base = Security::slug($label);
                if ($base === '') {
                    $base = 'bolum';
                }

                $id = $base;
                $suffix = 2;
                while (isset($used[$id])) {
                    $id = $base . '-' . $suffix;
                    $suffix++;
                }
                $used[$id] = true;

                $items[] = [
                    'id'    => $id,
                    'label' => $label,
                    'level' => $level,
                ];

                return '<h' . $level . ' id="' . Security::attr($id) . '">' . $match[2] . '</h' . $level . '>';
            },
            $clean
        ) ?? $clean;

        return ['html' => $clean, 'items' => $items];
    }
}
