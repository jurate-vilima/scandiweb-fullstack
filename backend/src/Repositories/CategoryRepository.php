<?php
namespace App\Repositories;

use App\Database\Database;
use App\Models\Category;

class CategoryRepository {
    private Database $db;
    private string $table = 'categories';

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function findAll(): array {
        $products = $this->db->executeQuery("SELECT * FROM $this->table");
        return array_map(fn($data) => new Category($data), $products);
    }

    public function findById(string $id): ?Category {
        $data = $this->db->executeQuery("SELECT * FROM $this->table WHERE id = ?", [$id], true);
        return $data ? new Category($data) : null;
    }
}
