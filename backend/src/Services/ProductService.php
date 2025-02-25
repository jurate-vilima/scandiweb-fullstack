<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService {
    private ProductRepository $productRepo;

    public function __construct(ProductRepository $productRepo) {
        $this->productRepo = $productRepo;
    }

    public function getAllProducts(): array {
        // $prod = $this->productRepo->findAll();
        // return $prod;

        $products = $this->productRepo->findAll();
        return array_map(fn($product) => [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'in_stock' => $product->isInStock(),
            'brand' => $product->getBrand(),
        ], $products);
    }
}