<?php
namespace App;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database {
    private PDO $pdo;

    public function __construct() {
        // $dotenv = Dotenv::createImmutable(__DIR__ . '/../config/.env'); 
        // $dotenv->load();

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

    public function fetchAll(string $sql, array $params = []) {
        if (!$this->pdo) {
            throw new \Exception("DB connection is not established.");
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return ($stmt->fetchAll(\PDO::FETCH_ASSOC));
        //return [ ['name' => 'Test Category'] ];
    }

    public function getConnection() {
        return $this->pdo;
    }
}