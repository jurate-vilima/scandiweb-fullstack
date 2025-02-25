<?php
namespace App\Models;

use App\Database;

class Product {
    // protected string $tableName = 'products';

    private string $id;
    private string $name;
    private bool $inStock;
    private string $description;
    private string $brand;
    private int $categoryId;

    public function __construct(array $data = []) {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->inStock= $data['in_stock'];
        $this->categoryId = $data['category_id'];
        $this->description = $data['description'];
        $this->brand = $data['brand'];
    }

    public function getId(): ?string {
        return $this->id ?? null;
    }

    public function getName(): ?string {
        return $this->name ?? null;
    }

    public function isInStock(): bool {
        return $this->inStock ?? false;
    }

    public function getDescription(): ?string {
        return $this->description ?? null;
    }

    public function getCategoryId(): ?int {
        return $this->categoryId ?? null;
    }

    public function getBrand(): ?string {
        return $this->brand ?? null;
    }
}