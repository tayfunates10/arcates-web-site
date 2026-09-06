<?php

declare(strict_types=1);

namespace Arcates\Core;

use RuntimeException;

final class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void { $this->add('GET', $path, $handler); }
    public function post(string $path, callable $handler): void { $this->add('POST', $path, $handler); }
    public function add(string $method, string $path, callable $handler): void { $this->routes[] = [strtoupper($method), $path, $handler]; }

    public function dispatch(string $method, string $path): mixed
    {
        $path = '/' . ltrim($path, '/');
        foreach ($this->routes as [$routeMethod, $routePath, $handler]) {
            if ($routeMethod !== strtoupper($method)) { continue; }
            if ($routePath === '/') {
                $pattern = '#^/$#';
            } else {
                $quoted = preg_quote(rtrim($routePath, '/'), '#');
                $pattern = preg_replace('/\\\{([A-Za-z_][A-Za-z0-9_]*)\\\}/', '(?P<$1>[^/]+)', $quoted) ?? '';
                $pattern = '#^' . $pattern . '/?$#u';
            }
            if (preg_match($pattern, $path, $matches) !== 1) { continue; }
            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) { $params[] = rawurldecode((string) $value); }
            }
            return $handler(...$params);
        }
        throw new RuntimeException('Route not found', 404);
    }
}
