<?php

declare(strict_types=1);

namespace Arcates\Core;

use RuntimeException;

final class View
{
    public function __construct(private string $viewsPath) {}

    public function render(string $template, array $data = [], ?string $layout = null): string
    {
        $content = $this->capture($this->resolve($template), $data);
        return $layout === null
            ? $content
            : $this->capture($this->resolve($layout), $data + ['content' => $content]);
    }

    private function resolve(string $template): string
    {
        if (!preg_match('/^[A-Za-z0-9_\/-]+$/', $template)) {
            throw new RuntimeException('Invalid view name.');
        }
        $file = rtrim($this->viewsPath, '/') . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new RuntimeException('View not found: ' . $template);
        }
        return $file;
    }

    private function capture(string $file, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }
}
