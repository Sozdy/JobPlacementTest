<?php

namespace Src\App;

use Src\App\Middlewares\AbstractMiddleware;

class Router
{
    private static array $routes = [];

    public static function route(string $method, string $pattern, callable $callback, array $middlewares = []): void
    {
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';

        $firstHandle = self::setMiddlewareChain($middlewares);

        $route = [
            'path' => $pattern,
            'method' => $method,
            'callback' => $callback,
            'middleware' => $firstHandle
        ];

        self::$routes[] = $route;
    }


    /**
     * @return false|mixed|void
     */
    public static function execute(string $method, string $url)
    {
        foreach (self::$routes as $route) {
            $isRouteHasMatch = preg_match($route['path'], $url, $params) && strcasecmp($route['method'], $method) === 0;
            if ($isRouteHasMatch) {
                if (!is_null($route['middleware'])) {
                    $route['middleware']->handle();
                }

                array_shift($params);
                return call_user_func_array($route['callback'], array_values($params));
            }
        }

        return require(realpath('../src/App/Views/Pages/404.html'));
    }

    private static function setMiddlewareChain(array $middlewares): ?AbstractMiddleware
    {
        $firstHandle = null;

        for ($i = 0; $i < count($middlewares); $i++) {
            if ($i === 0)
                $firstHandle = $middlewares[$i];
            else
                $firstHandle->setNext($middlewares[$i]);
        }

        return $firstHandle;
    }
}