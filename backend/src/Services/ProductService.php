<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService {
    private ProductRepository $productRepo;

    public function __construct(ProductRepository $productRepo) {
        $this->productRepo = $productRepo;
    }

    public function getAllProducts(): array {
        return $this->productRepo->findAll();
    }
}