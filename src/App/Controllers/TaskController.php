<?php

namespace Src\App\Controllers;

use Src\App\Models\Task;
use Src\App\Views\TwigView;

class TaskController
{
    private TwigView $twig;

    public function __construct()
    {
        $this->twig = new TwigView();
    }

    public function index(): string
    {
        $auth = new AuthController();
        $isAuth = $auth->check();

        $orderBy = $_GET['orderBy'] ?? 'id';
        $direction = $_GET['direction'] ?? 'DESC';
        $page = $_GET['page'] ?? 1;
        $message = $_GET['message'] ?? '';

        $pagination = Task::paginate($orderBy, $direction, $page);
        $params = [
            'isAuth' => $isAuth,
            'message' => $message,
            'tasks' => $pagination['data'],
            'orderBy' => $pagination['orderBy'],
            'direction' => $pagination['direction'],
            'currentPage' => $pagination['currentPage'],
            'pagesCount' => $pagination['pageCount']
        ];

        return print $this->twig->render('Pages/Tasks/Index.html', $params);
    }

    public function create(): string
    {
        $auth = new AuthController();
        $isAuth = $auth->check();

        return print $this->twig->render('Pages/Tasks/Create.html', ['isAuth' => $isAuth]);
    }

    public function store(): void
    {
        $data = [
            'user_name' => $_POST['user_name'],
            'email' => $_POST['email'],
            'description' => $_POST['description']
        ];

        if (Task::create($data)) {
            header('Location: ' . '/' . "?message=Задача создана");
            die();
        } else {
            header('Location: ' . '/' . "?message=Что то пошло не так");
        }
    }

    public function edit(int $taskId): string
    {
        $auth = new AuthController();
        $isAuth = $auth->check();

        $task = Task::find($taskId);

        return print $this->twig->render('Pages/Tasks/Edit.html', ['task' => $task, 'isAuth' => $isAuth]);
    }

    public function update(int $taskId): void
    {
        $data = [
            'taskId' => $taskId,
            'user_name' => $_POST['user_name'],
            'email' => $_POST['email'],
            'description' => $_POST['description'],
            'old_description' => $_POST['old_description'],
            'finished' => $_POST['finished']
        ];

        if (Task::update($data)) {
            header('Location: ' . '/' . "?message=Задача обновлена");
            die();
        }
    }
}