<?php
namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Error\DebugFlag;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use App\Database;

use App\Models\Category;
use App\Models\Product;

use RuntimeException;
use Throwable;

class GraphQL {
    static public function handle() : string { 
        try {
            $db = new Database();

            $categoryType = self::createCategoryType();
            $attributeType = self::createAttributeType();
            $productType = self::createProductType($db, $categoryType, $attributeType);

            $queryType = self::createQueryType($db, $productType, $categoryType);

            $mutationType = self::createMutationType();

            $schema = self::createSchema($queryType, $mutationType);

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $rootValue = ['prefix' => 'You said: '];
            $result = GraphQLBase::executeQuery(
                $schema,
                $query,
                $rootValue,
                null,
                $variableValues
            );

            // Include debug details for error tracing
            $output = $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE);
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
                'name' => Type::string(),
                'id' => Type::int(),
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

    private static function createProductType(Database $db, ObjectType $categoryType, ObjectType $attributeType) : ObjectType {
        return new ObjectType([
            'name'   => 'Product',
            'fields' => function() use ($db, $categoryType, $attributeType) {
                return [
                    'id'          => Type::string(),
                    'name'        => Type::string(),
                    'in_stock'    => Type::boolean(),
                    'description' => Type::string(),
                    'brand'       => Type::string(),
                    'gallery'     => Type::listOf(Type::string()),
                    'category'    => $categoryType,
                ];
            },
        ]);
    }

    private static function createQueryType(Database $db, ObjectType $productType, ObjectType $categoryType) : ObjectType {
        return new ObjectType([
            'name'   => 'Query',
            'fields' => [
                'products' => [
                    'type' => Type::listOf($productType),
                    'resolve' => function($root, $args, $context, $info) use ($db) {
                        $fields = self::getRequestedFields($info);
                        $product = new Product($db);

                        return $product->findAllFields($fields);
                    },
                ],

                'categories' => [
                    'type' => Type::listOf($categoryType),
                    'resolve' => function($root, $args, $context, $info) use ($db) {
                        $fields = self::getRequestedFields($info);
                        $category = new Category($db);

                        return $category->findAllFields($fields);
                    },
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

    private static function createSchema(ObjectType $queryType, ObjectType $mutationType) : Schema {
        return new Schema(
            (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType)
        );
    }
}