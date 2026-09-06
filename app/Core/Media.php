<?php

declare(strict_types=1);

namespace Arcates\Core;

use DOMDocument;
use finfo;

final class Media
{
    public const MAX_BYTES = 5 * 1024 * 1024;

    private const MIME_BY_EXTENSION = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'webp' => ['image/webp'],
        'gif' => ['image/gif'],
        'svg' => ['image/svg+xml', 'text/xml', 'application/xml'],
        'pdf' => ['application/pdf'],
    ];

    public static function isAllowedExtension(string $filename): bool
    {
        return isset(self::MIME_BY_EXTENSION[strtolower(pathinfo($filename, PATHINFO_EXTENSION))]);
    }

    public static function validateUpload(string $tmpPath, string $originalName, int $size): bool
    {
        if ($size <= 0 || $size > self::MAX_BYTES || !is_file($tmpPath)) {
            return false;
        }
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!isset(self::MIME_BY_EXTENSION[$extension])) {
            return false;
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmpPath);
        return is_string($mime) && in_array($mime, self::MIME_BY_EXTENSION[$extension], true);
    }

    public static function randomFilename(string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!isset(self::MIME_BY_EXTENSION[$extension])) {
            throw new \InvalidArgumentException('Unsupported file extension.');
        }
        return bin2hex(random_bytes(8)) . '.' . $extension;
    }

    public static function sanitizeSvg(string $svg): string
    {
        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument();
        if (!$document->loadXML($svg, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
            return '';
        }
        while ($document->getElementsByTagName('script')->length > 0) {
            $node = $document->getElementsByTagName('script')->item(0);
            $node?->parentNode?->removeChild($node);
        }
        foreach ($document->getElementsByTagName('*') as $element) {
            $remove = [];
            foreach ($element->attributes ?? [] as $attribute) {
                $name = strtolower($attribute->nodeName);
                if (str_starts_with($name, 'on') || $name === 'xlink:href') {
                    $remove[] = $attribute->nodeName;
                }
            }
            foreach ($remove as $attributeName) {
                $element->removeAttribute($attributeName);
            }
        }
        $clean = $document->saveXML($document->documentElement) ?: '';
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        return $clean;
    }
}
