<?php
namespace App;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database {
    private PDO $pdo;

    public function __construct() {
        $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'] . ";charset=utf8";

        try {
            $this->pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
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