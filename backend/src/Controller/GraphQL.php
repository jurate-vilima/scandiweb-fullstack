<?php
namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Error\DebugFlag;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use App\Database\Database;

use App\Services\ProductService;
use App\Repositories\ProductRepository;
use App\Services\CategoryService;
use App\Repositories\CategoryRepository;

use RuntimeException;
use Throwable;

class GraphQL {
    static public function handle() { 
        try {
            $db = new Database();
            $productRepo = new ProductRepository($db);
            $productService = new ProductService($productRepo);

            $categoryRepo = new CategoryRepository($db);
            $categoryService = new CategoryService($categoryRepo);

            $queryType = self::createQueryType();
            $mutationType = self::createMutationType();

            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $context = [
                'productService' => $productService,
                'categoryService' => $categoryService,
            ];

            $result = GraphQLBase::executeQuery($schema, $query, null, $context, $variableValues);

            $output = $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE);
            var_dump($output);
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }

    private static function getRequestedFields($info) {
        return array_map(
            fn($field) => $field->name->value,
            iterator_to_array($info->fieldNodes[0]->selectionSet->selections)
        );
    }

    private static function createCategoryType() : ObjectType {
        return new ObjectType([
            'name'   => 'Category',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'resolve' => fn($category) => $category->getId(),
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn($category) => $category->getName(),
                ],
            ],
        ]);
    }

    private static function createAttributeType() : ObjectType {
        return new ObjectType([
            'name'   => 'Attribute',
            'fields' => [
                'name'  => Type::string(),
                'type'  => Type::string(),
                'value' => Type::string(),
            ],
        ]);
    }

    private static function createProductType() : ObjectType {
        return new ObjectType([
            'name'   => 'Product',
            'fields' => function() {
                return [
                    'id'          => [
                        'type' => Type::nonNull(Type::id()),
                        // 'resolve' => fn($product) => $product->getId(),
                    ],
                    'name'        => [
                        'type' => Type::string(),
                        // 'resolve' => function ($product) {
                        //     return $product->getName();
                        // },
                    ],
                    'in_stock'    => [
                        'type' => Type::boolean(),
                        'resolve' => function ($product) {
                            return $product->isInStock();
                        },
                    ],
                    'description' => [
                        'type' => Type::string(),
                        'resolve' => function ($product) {
                            return $product->getDescription();
                        },
                    ],
                    'brand'       => [
                        'type' => Type::string(),
                        'resolve' => function ($product) {
                            return $product->getBrand();
                        },
                    ],
                    'gallery'     => [
                        'type' => Type::listOf(Type::string()),
                        'resolve' => function ($product) {
                            $galleryData = $product->getGallery(); 
                            $galleryUrls = array_map(fn($image) => $image['image_url'], $galleryData);
                            return $galleryUrls;
                        },
                    ],
                    'prices'     => [
                        'type' => Type::listOf(Type::string()),
                        'resolve' => function ($product) {
                            $galleryData = $product->getGallery(); 
                            $galleryUrls = array_map(fn($image) => $image['image_url'], $galleryData);
                            return $galleryUrls;
                            
                            // return $product->getGallery();
                        },
                    ],
                    // 'category' => [
                    //     'type' => $categoryType,
                    //     'resolve' => function ($product, $args, $context, $info) {
                    //         $fields = self::getRequestedFields($info);
                    //         // var_dump($product);
                    //         // exit();
                    //         return $product->getCategory($fields);
                    //         // return ['name' => 'clothes'];
                    //     },
                    // ],
                ];
            },
        ]);
    }

    private static function createQueryType() : ObjectType {
        return new ObjectType([
            'name'   => 'Query',
            'fields' => [
                'products' => [
                    'type' => Type::listOf(self::createProductType()),
                    'resolve' => fn($root, $args, $context) => $context['productService']->getAllProducts(),
                ],

                'categories' => [
                    'type' => Type::listOf(self::createCategoryType()),
                    'resolve' => fn($root, $args, $context) => $context['categoryService']->getAllCategories(),
                ],
            ],
        ]);
    }

    private static function createMutationType() : ObjectType {
        return new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'sum' => [
                    'type' => Type::int(),
                    'args' => [
                        'x' => ['type' => Type::int()],
                        'y' => ['type' => Type::int()],
                    ],
                    'resolve' => static fn ($calc, array $args): int => $args['x'] + $args['y'],
                ],
            ],
        ]);
    }
}