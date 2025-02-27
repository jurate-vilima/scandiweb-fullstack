<?php
use DI\ContainerBuilder;
use App\Database\Database;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use App\Controller\GraphQLController;
use App\Controller\Main;
use App\Schema\QueryType;
use App\Schema\MutationType;

return (function () {
    $builder = new ContainerBuilder();

    $builder->useAutowiring(false);

    $builder->addDefinitions([
        Database::class => function() {
            $host = $_ENV['DB_HOST'];
            $dbName = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];

            return new Database($host, $dbName, $user, $pass);
        },

        ProductRepository::class => function($c) {
            return new ProductRepository($c->get(Database::class));
        },
        CategoryRepository::class => function($c) {
            return new CategoryRepository($c->get(Database::class));
        },

        ProductService::class => function($c) {
            return new ProductService($c->get(ProductRepository::class));
        },
        CategoryService::class => function($c) {
            return new CategoryService($c->get(CategoryRepository::class));
        },

        Main::class => function() {
            return new Main();
        },

        QueryType::class => function($c) {
            return new QueryType($c->get(ProductService::class));
        },
        MutationType::class => function($c) {
            return new MutationType($c->get(ProductService::class));
        },

        GraphQLController::class => function($c) {
            return new GraphQLController(
                $c->get(ProductService::class),
                $c->get(QueryType::class),
                $c->get(MutationType::class)
            );
        },
    ]);

    $container = $builder->build();

    return $container;
})();
