<?php
namespace App\Models;

class Product extends Model {
    private string $id;
    private string $name;
    private bool $inStock;
    private ?string $description;
    private int $categoryId;
    private ?string $brand;

    private array $galleries;

    private array $attributes;
    private Price $price;

    public function __construct(
        string $id,
        string $name,
        bool $inStock,
        ?string $description = null,
        int $categoryId,
        ?string $brand = null,
        array $galleries = [],
        array $attributes = [],
        Price $price = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->inStock = $inStock;
        $this->description = $description;
        $this->categoryId = $categoryId;
        $this->brand = $brand;
        $this->galleries = $galleries;
        $this->attributes = $attributes;
        $this->price = $price;
    }


    public function getId(): string {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function isInStock(): bool {
        return $this->inStock;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getCategoryId(): ?int {
        return $this->categoryId;
    }

    public function getBrand(): ?string {
        return $this->brand;
    }

    public function getGalleries(): array {
        return $this->galleries;
    }

    public function getAttributes(): array {
        return $this->attributes;
    }

    public function getPrice(): ?Price {
        return $this->price;
    }

    public function setGalleries(array $galleries): void {
        $this->galleries = $galleries;
    }

    public function setAttributes(array $attributes): void {
        $this->attributes = $attributes;
    }

    public function setPrice(Price $price): void {
        $this->price = $price;
    }
}