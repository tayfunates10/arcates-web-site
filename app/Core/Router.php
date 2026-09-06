<?php
/**
 * Yonlendirici.
 *
 * Desenler `{slug}` ve `{id}` gibi yer tutucular icerebilir. Dil oneki
 * (`/en/...`) yonlendirmeden once ayiklanir.  DOCS.md 4.6, test U-12
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Router
{
    /** @var array<string, array<int, array{pattern:string, regex:string, keys:array, handler:mixed, name:?string}>> */
    private array $routes = ['GET' => [], 'POST' => []];

    private mixed $fallback = null;

    public function get(string $pattern, mixed $handler, ?string $name = null): self
    {
        return $this->add('GET', $pattern, $handler, $name);
    }

    public function post(string $pattern, mixed $handler, ?string $name = null): self
    {
        return $this->add('POST', $pattern, $handler, $name);
    }

    /** Hem GET hem POST icin kaydeder. */
    public function any(string $pattern, mixed $handler, ?string $name = null): self
    {
        $this->add('GET', $pattern, $handler, $name);
        return $this->add('POST', $pattern, $handler, $name);
    }

    /** Hicbir desen eslesmezse calisacak isleyici. */
    public function fallback(mixed $handler): self
    {
        $this->fallback = $handler;
        return $this;
    }

    private function add(string $method, string $pattern, mixed $handler, ?string $name): self
    {
        $pattern = '/' . trim($pattern, '/');
        if ($pattern !== '/') {
            $pattern = rtrim($pattern, '/');
        }

        // preg_quote suslu parantezleri de kacirir; yer tutucular taninabilsin
        // diye once kacislari geri alinir, sonra yer tutucu cevrimi yapilir.
        $quoted = str_replace(['\\{', '\\}'], ['{', '}'], preg_quote($pattern, '#'));

        $keys  = [];
        $regex = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}#',
            static function (array $m) use (&$keys): string {
                $keys[] = $m[1];
                $sub    = $m[2] ?? '[^/]+';
                return '(' . $sub . ')';
            },
            $quoted
        ) ?? $quoted;

        $this->routes[$method][] = [
            'pattern' => $pattern,
            'regex'   => '#^' . $regex . '$#u',
            'keys'    => $keys,
            'handler' => $handler,
            'name'    => $name,
        ];

        return $this;
    }

    /**
     * Yolu eslestirir.
     *
     * @return array{handler:mixed, params:array}|null
     */
    public function match(string $method, string $path): ?array
    {
        $method = strtoupper($method);
        $path   = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['regex'], $path, $m) === 1) {
                $params = [];
                foreach ($route['keys'] as $i => $key) {
                    $params[$key] = $m[$i + 1] ?? null;
                }
                return ['handler' => $route['handler'], 'params' => $params];
            }
        }

        if ($this->fallback !== null) {
            return ['handler' => $this->fallback, 'params' => []];
        }

        return null;
    }

    /** Kayitli desenleri dondurur (testler ve tanilama icin). */
    public function routes(): array
    {
        return $this->routes;
    }
}
