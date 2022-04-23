<?php

namespace Src\App\Models;

use Src\Database\Database;
use \PDO;

class User
{
    public int $id;
    public string $login;
    public string $password;
    public string $hash;

    private static ?PDO $pdo;

    /**
     * @param string $userLogin
     * @return array | bool
     */
    static function find(string $userLogin)
    {
        self::openDatabaseConnection();

        $sql = '
           SELECT id, password FROM users 
           WHERE login = :login
        ';

        $stmt = self::$pdo->prepare($sql, [':login' => $userLogin]);
        $stmt->execute([':login' => $userLogin]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        self::closeDatabaseConnection();
        return $user;
    }
    /**
     * @param string $userHash
     * @return array | bool
     */
    static function findByHash(string $userHash)
    {
        self::openDatabaseConnection();

        $sql = '
           SELECT id, password, hash FROM users 
           WHERE hash = :hash
        ';

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':hash' => $userHash]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        self::closeDatabaseConnection();

        return $user;
    }

    static function updateHash($userId, string $hash): void
    {
        self::openDatabaseConnection();
        $sql = '
            UPDATE users SET hash= :hash
             WHERE id= :userId
         ';

        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':hash', $hash);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        $stmt->execute();

        self::closeDatabaseConnection();
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
}