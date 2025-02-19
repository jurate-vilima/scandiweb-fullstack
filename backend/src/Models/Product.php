<?php
namespace App\Models;

use App\Database;

class Product extends Model {
    protected string $tableName = 'products';

    public function getId(): ?string {
        return $this->data['id'] ?? null;
    }

    public function getName(): ?string {
        return $this->data['name'] ?? null;
    }

    public function isInStock(): bool {
        return $this->data['in_stock'] ?? false;
    }

    public function getDescription(): ?string {
        return $this->data['description'] ?? null;
    }

    public function getCategoryId(): ?int {
        return $this->data['category_id'] ?? null;
    }

    public function getBrand(): ?string {
        return $this->data['brand'] ?? null;
    }

    public function getGalleries(): array {
        return $this->data['galleries'] ?? [];
    }

    public function getAttributes(): array {
        return $this->data['attributes'] ?? [];
    }

    public function getPrice(): ?float {
        return $this->data['price'] ?? null;
    }

    public function getCategory(): ?Category {
        $categoryId = $this->getCategoryId();
        if ($categoryId) {
            // Fetch category name using the category_id
            $sql = "
                SELECT c.name AS category_name 
                FROM categories c 
                WHERE c.id = :category_id
            ";
            $categoryData = $this->db->executeQuery($sql, ['category_id' => $categoryId]);

            if ($categoryData) {
                return $categoryData[0]['category_name'];
            }
        }
        return null;
    }
}
