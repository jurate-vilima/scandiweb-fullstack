<?php
namespace App\Repositories;

use App\Database\Database;
use App\Models\Product;

class ProductRepository {
    private Database $db;
    private string $table = 'products';

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function findAll(array $requestedFields = ['id', 'name', 'description', 'in_stock', 'brand']): array {
        $fields = implode(',', $requestedFields);
        $sql = "SELECT $fields FROM $this->table";
        
        return $this->db->executeQuery($sql); 
    }    

    public function findById(string $id): ?Product {
        $data = $this->db->executeQuery("SELECT * FROM $this->table WHERE id = ?", [$id], true);
        return $data ? new Product($data) : null;
    }
}
