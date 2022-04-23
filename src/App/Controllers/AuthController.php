<?php

namespace Src\App\Controllers;

use Src\App\Models\User;
use Src\App\Views\TwigView;
use Src\Database\Database;

class AuthController
{
    private TwigView $twig;

    public function __construct()
    {
        $this->twig = new TwigView();
    }

    public function index(): void
    {
        $isAuth = $this->check();

        if ($isAuth) {
            header('Location: /');
            die();
        }

        print $this->twig->render('Pages/Login/Index.html');
    }

    public function login(): void
    {
        $data = [
            'login' => $_POST['login'],
            'password' => $_POST['password']
        ];

        $user = User::find($data['login']);

        if (!!$user && $user['password'] === $data['password']) {
            $hash = md5(self::generateCode(10));

            User::updateHash($user['id'], $hash);

            setcookie("id", $user['id'], time() + 60 * 60 * 24 * 30, "/");
            setcookie("hash", $hash, time() + 60 * 60 * 24 * 30, "/", null, null, true);

            header("Location: /");
            die();
        } else {
            print $this->twig->render('Pages/Login/Index.html', [
                'info' => 'Не правильный логин/пароль',
                'login' => $data['login'],
                'password' => $data['password'],
            ]);
        }

    }

    public function logout(): void
    {
        setcookie("id", "", time() - 3600 * 24 * 30 * 12, "/");
        setcookie("hash", "", time() - 3600 * 24 * 30 * 12, "/", null, null, true);
        header("Location: /");
    }

    public function check(): bool
    {
        if (!isset($_COOKIE['id']) and !isset($_COOKIE['hash'])) {
            return false;
        }

        $user = User::findByHash($_COOKIE['hash']);
        $isAuth = !!$user && (($user['hash'] === $_COOKIE['hash']) || ($user['id'] === $_COOKIE['id']));

        if ($isAuth) {
            return true;
        } else {
            setcookie("id", "", time() - 3600 * 24 * 30 * 12, "/");
            setcookie("hash", "", time() - 3600 * 24 * 30 * 12, "/", null, null, true);
            return false;
        }
    }

    private static function generateCode($length = 6): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0, $clen)];
        }
        return $code;
    }


}