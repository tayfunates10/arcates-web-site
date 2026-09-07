<?php
/**
 * Bilgilendirme notu.
 *
 * Metin panelden gelir (Ayarlar > "Örnek site notu"); ayar bosaltilinca not
 * hic basilmaz. Gercek musteri isleri yayina girdiginde bu sekilde kapatilir.
 * DOCS.md 9.4, 16 (kural 8: sablona sabit metin gomulmez)
 *
 * @var string $text
 */

declare(strict_types=1);

use Arcates\Core\Security;

$text = trim((string) ($text ?? ''));
if ($text === '') {
    return;
}
?>
<p class="notice"><?= Security::e($text) ?></p>
