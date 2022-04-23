<?php

namespace Src\App\Views;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigView
{
    public $twig;
    /**
     * @param string $template Имя шаблона
     * @param array $params Передаваемые параметры
     */
    public function __construct()
    {

        $loader = new FilesystemLoader(realpath('../src/App/Views'));
        $this->twig = new Environment($loader);
    }

    public function render($template, $params = []): string
    {
        try {
            return $this->twig->render($template, $params);
        } catch (\Exception $e) {
            var_dump($e);

            return $this->twig->render('Pages/404.html');
        }
    }
}
