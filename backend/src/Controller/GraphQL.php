<?php

namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;

use App\Models\Category;

use RuntimeException;
use Throwable;

class GraphQL {
    static public function handle() : string {
        try {
            $categoryType = new ObjectType([
                'name'   => 'Category',
                'fields' => [
                    // 'id'   => Type::int(),
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
                        'resolve' => function() {
                            $category = new Category();
                            // $categories = $category->findAll();
                            // return array_map(fn($cat) => $cat->getData(), $categories);
                            return $category->findAll();
                        },
                    ],

                ],
            ]);

            // $queryType = new ObjectType([
            //     'name' => 'Query',
            //     'fields' => [
            //         'echo' => [
            //             'type' => Type::string(),
            //             'args' => [
            //                 'message' => ['type' => Type::string()],
            //             ],
            //             'resolve' => static fn ($rootValue, array $args): string => $rootValue['prefix'] . $args['message'],
            //         ],
            //     ],
            // ]);
        
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
        
            // See docs on schema options:
            // https://webonyx.github.io/graphql-php/schema-definition/#configuration-options
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
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
            
            $output = $result->toArray();
            // print json_encode($output);
            // var_dump($output);
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