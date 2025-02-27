<?php
namespace App\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Services\CategoryService;
use App\Services\ProductService;

class QueryType extends ObjectType {

    public function __construct(
        // private CategoryService $categoryService,
        private ProductService $productService
    ) {
        $config = [
            'name' => 'Query',
            'fields' => [
                'categories' => [
                    'type' => Type::listOf(CategoryType::instance()),
                    'resolve' => function () {
                        return $this->categoryService->getAllCategories();
                    }
                ],
                'products' => [
                    'type' => Type::listOf(ProductType::instance()),
                    'resolve' => function () {
                        return $this->productService->getAllProducts();
                    }
                ],
            ],
        ];
        parent::__construct($config);
    }
}