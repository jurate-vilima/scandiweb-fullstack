<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService {
    private ProductRepository $productRepo;

    public function __construct(ProductRepository $productRepo) {
        $this->productRepo = $productRepo;
    }

    public function getAllProducts(array $requestedFields = ['id', 'name', 'description', 'in_stock', 'brand']): array {
        return $this->productRepo->findAll($requestedFields);
    }    
}