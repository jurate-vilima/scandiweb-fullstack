<?php
namespace App;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database {
    private $connection;

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../config/.env'); 
        $dotenv->load();

        try {
            $this->connection = new PDO("mysql:host={" . $_ENV['DB_HOST'] . ";dbname={" . $_ENV['DB_NAME'] . "," . $_ENV['DB_USER'] , "," , $_ENV['DB_PASS']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}