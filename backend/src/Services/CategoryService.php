<?php

namespace App\Services;

use App\Repositories\CategoryRepository;

class CategoryService {
    private CategoryRepository $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo) {
        $this->categoryRepo = $categoryRepo;
    }

    public function getAllCategories(): array {
        return $this->categoryRepo->findAll();
    }
}