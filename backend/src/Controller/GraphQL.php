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
use RuntimeException;
use Throwable;

class GraphQL {
    static public function handle() : string { 
        try {
            // Create a local Database instance
            $db = new Database();

            $categoryType = new ObjectType([
                'name'   => 'Category',
                'fields' => [
                    'name' => Type::string(),
                ],
            ]);

            $attributeType = new ObjectType([
                'name'   => 'Attribute',
                'fields' => [
                    'name'  => Type::string(),
                    'type'  => Type::string(),
                    'value' => Type::string(),
                ],
            ]);

            $productType = new ObjectType([
                'name'   => 'Product',
                'fields' => function() use ($categoryType, $attributeType) {
                    return [
                        'id'          => Type::string(),
                        'name'        => Type::string(),
                        'inStock'     => Type::boolean(),
                        'description' => Type::string(),
                        'brand'       => Type::string(),
                        'gallery'     => Type::listOf(Type::string()),
                        'category'    => $categoryType,
                        'attributes'  => Type::listOf($attributeType),
                    ];
                },
            ]);

            $queryType = new ObjectType([
                'name'   => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf($categoryType),
                        'resolve' => function($root, $args, $context, $info) use ($db) {
                           //die(json_encode($info, JSON_PRETTY_PRINT));

                            $fields = array_map(fn($field) => $field->name->value, iterator_to_array($info->fieldNodes[0]->selectionSet->selections));
                            // return $fields;
                            // $params = $info->fieldNodes[0]->selectionSet->selections;
                            print_r($fields); 
                            exit();

                            // return $fields;
                            $category = new Category($db);
                            return $category->findAll($fields);
                        },
                    ],
                ],
            ]);

            $mutationType = new ObjectType([
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
}