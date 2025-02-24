<?php
namespace App\Repositories;

use App\Database\Database;
use App\Models\Product;

class ProductRepository {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function findAll(): array {
        $products = $this->db->executeQuery("SELECT * FROM products");
        return array_map(fn($data) => new Product($data), $products);
    }

    public function findById(string $id): ?Product {
        $data = $this->db->executeQuery("SELECT * FROM products WHERE id = ?", [$id], true);
        return $data ? new Product($data) : null;
    }
}
