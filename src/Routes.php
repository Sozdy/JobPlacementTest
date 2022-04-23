<?php

namespace Src;

use Src\App\Controllers\AuthController;
use Src\App\Controllers\TaskController;
use Src\App\Middlewares\AuthMiddleware;
use Src\App\Router;


Router::route('GET', '/', function () {
    $controller = new TaskController();
    $controller->index();
});

Router::route('GET', '/create', function () {
    $controller = new TaskController();
    $controller->create();
});

Router::route('POST', '/', function () {
    $controller = new TaskController();
    $controller->store();
});

Router::route('GET', '/(\d+)/edit', function ($taskId) {
    $controller = new TaskController();
    $controller->edit($taskId);
}, [new AuthMiddleware()]);

Router::route('POST', '/(\d+)/patch', function ($taskId) {
    $controller = new TaskController();
    $controller->update($taskId);
}, [new AuthMiddleware()]);

Router::route('GET', '/login', function () {
    $controller = new AuthController();
    $controller->index();
});

Router::route('POST', '/login', function () {
    $controller = new AuthController();
    $controller->login();
});

Router::route('GET', '/logout', function () {
    $controller = new AuthController();
    $controller->logout();
});


Router::execute($_SERVER['REQUEST_METHOD'], parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));