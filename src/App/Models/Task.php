<?php

namespace Src\App\Models;

use Src\Database\Database;
use \PDO;

class Task
{
    public int $id;
    public string $userName;
    public string $email;
    public string $description;
    public string $updated;
    public string $finished;

    private static ?PDO $pdo;

    /**
     * @param int $taskId
     * @return bool|array
     */
    static function find(int $taskId)
    {
        self::openDatabaseConnection();

        $sql = '
            SELECT * FROM tasks
            WHERE id = :id
        ';

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
        $stmt->execute();
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$task) {
            header('Location: ' . '/404');
            die();
        }

        self::closeDatabaseConnection();

        return $task;
    }

    static function paginate($orderBy, $direction, $page, $limit = 3): array
    {
        self::openDatabaseConnection();

        $orderBy = self::white_list($orderBy, ["id", "user_name", "email", "finished"]);
        $direction = self::white_list($direction, ["ASC", "DESC"]);
        $offset = ($page - 1) * $limit;

        $sql = "
            SELECT * FROM tasks
            ORDER BY $orderBy $direction
            LIMIT :limit
            OFFSET :page
         ";

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':page', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pageCountStmt = self::$pdo->prepare('SELECT COUNT(*) FROM tasks;');
        $pageCountStmt->execute();
        $pageCount = ceil($pageCountStmt->fetch(PDO::FETCH_NUM)[0] / $limit);

        self::closeDatabaseConnection();

        return [
            'pageCount' => $pageCount,
            'currentPage' => $page,
            'data' => $tasks,
            'direction' => $direction,
            'orderBy' => $orderBy
        ];
    }

    static function create($data): bool
    {
        self::openDatabaseConnection();

        $sql = '
            INSERT INTO tasks (email, user_name, description)
            VALUES (:email, :user_name, :description)
        ';

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':user_name', $data['user_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':description', $data['description']);

        self::closeDatabaseConnection();

        return $stmt->execute();
    }

    static function update(array $data): bool
    {
        self::openDatabaseConnection();

        $sql = '
            UPDATE tasks 
            SET 
              user_name = :user_name, 
              email = :email, 
              description = :description,
              updated = :updated,
              finished = :finished
            WHERE ID = :id
        ';

        $updated = isset($data['updated']) || $data['description'] !== $data['old_description'];
        $finished = isset($data['finished']);

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':user_name', $data['user_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':updated', $updated, PDO::PARAM_BOOL);
        $stmt->bindParam(':finished', $finished, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $data['taskId'], PDO::PARAM_INT);

        self::closeDatabaseConnection();

        return $stmt->execute();
    }

    private static function openDatabaseConnection(): void
    {
        $database = new Database();
        self::$pdo = $database->connection;
    }

    private static function closeDatabaseConnection(): void
    {
        self::$pdo = null;
    }

    private static function white_list($value, $allowed): string
    {
        if ($value === null) {
            return $allowed[0];
        }

        $key = in_array($value, $allowed, true);

        if ($key) {
            return $value;
        } else {
            header('Location: ' . '/404');
            die();
        }
    }
}