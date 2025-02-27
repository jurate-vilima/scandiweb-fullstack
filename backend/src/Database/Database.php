<?php
namespace App\Database;

use PDO;
use PDOException;

class Database {
    private PDO $pdo;

    public function __construct(string $host, string $dbName, string $user, string $pass) {
        $dsn = "mysql:host=" . $host . ";dbname=" . $dbName . ";charset=utf8";

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function executeQuery(string $sql, array $params = [], bool $single = false) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    
        return $single ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    

    public function getConnection() {
        return $this->pdo;
    }
}