<?php
use DI\Container;
use App\Database\Database;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;

use App\Controller\Main;

return (function() {
    $container = new Container();

    $container->set(Database::class, fn() => new Database());

    $container->set(ProductRepository::class, fn($c) => new ProductRepository($c->get(Database::class)));
    $container->set(ProductService::class, fn($c) => new ProductService($c->get(ProductRepository::class)));

    $container->set(CategoryRepository::class, fn($c) => new CategoryRepository($c->get(Database::class)));
    $container->set(CategoryService::class, fn($c) => new CategoryService($c->get(CategoryRepository::class)));

    $container->set(Main::class, fn($c) => new Main);

    return $container;
})();
