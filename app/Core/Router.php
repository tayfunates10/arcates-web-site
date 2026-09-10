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

        // Desen once yer tutuculardan bolunur: yalnizca DUZ metin parcalari
        // preg_quote'dan gecer, yer tutucunun kendi alt deseni ham birakilir.
        // (Butun deseni preg_quote'dan gecirmek `{id:[0-9]+}` gibi alt
        // desenleri de kacirir ve rota hicbir zaman eslesmez.)
        $keys  = [];
        $regex = '';
        $offset = 0;

        if (preg_match_all('#\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}#', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $start  = (int) $match[0][1];
                $regex .= preg_quote(substr($pattern, $offset, $start - $offset), '#');
                $keys[] = $match[1][0];
                $regex .= '(' . (($match[2][0] ?? '') !== '' ? $match[2][0] : '[^/]+') . ')';
                $offset = $start + strlen($match[0][0]);
            }
        }

        $regex .= preg_quote(substr($pattern, $offset), '#');

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
        // HEAD ayni GET kaynagini sorgular; POST isleyicilerini calistirmaz.
        if ($method === 'HEAD') $method = 'GET';
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
