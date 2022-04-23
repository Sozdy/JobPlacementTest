<?php

namespace Src\Database;

use PDO;

class Database
{
    public PDO $connection;

    public function __construct()
    {
        $servername = "localhost";
        $username = "admin";
        $password = "adminadmin";

        try {
            $this->connection = new PDO("mysql:host=$servername;dbname=u1662518_default", $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function close() : void
    {
        $this->databaseConnecion = null;
    }
}
