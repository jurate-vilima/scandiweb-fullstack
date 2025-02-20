<?php
namespace App\Models;

use App\Database;

class Product extends Model {
    protected string $tableName = 'products';

    public function __construct(Database $db, array $data = []) {
        parent::__construct($db, $data); 
    }

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

    public function getGallery(): ?array {
        $productId = $this->getId();
        // var_dump($productId);

        $sql = "
            SELECT image_url 
            FROM galleries 
            WHERE product_id = :product_id
        ";

        $galleryData = $this->db->executeQuery($sql, ['product_id' => $productId], false);

        // return array_column($galleryData, 'image_url') ?: [];
        return $galleryData;
    }

    public function getAttributes(): array {
        return $this->data['attributes'] ?? [];
    }

    public function getPrice(): ?float {
        return $this->data['price'] ?? null;
    }

    public function getCategory(array $fields): ?array {
        $categoryId = $this->getCategoryId();

        $fieldsToFetch = implode(', ', $fields);

        if ($categoryId) {
            $sql = "
                SELECT $fieldsToFetch 
                FROM categories 
                WHERE id = :category_id
            ";
            $categoryData = $this->db->executeQuery($sql, ['category_id' => $categoryId], true);
    
            if ($categoryData) {
                return $categoryData;
            }
        }
        return null;
    }
}