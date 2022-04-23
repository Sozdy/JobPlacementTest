<?php

namespace Src\App\Middlewares;

use Src\App\Controllers\AuthController;

class AuthMiddleware extends AbstractMiddleware
{
    public function handle($request = null)
    {
        $auth = new AuthController();
        $isAuth = $auth->check();

        if ($isAuth) {
            return parent::handle($request);

        } else {
            header("Location: /login");
            die();
        }

    }
}