<?php
namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Error\DebugFlag;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;

use App\Services\ProductService;
use App\Schema\QueryType;
use App\Schema\MutationType;


use RuntimeException;
use Throwable;

class GraphQLController {
    private ProductService $productService;
    private QueryType $queryType;
    private MutationType $mutationType;

    public function __construct(ProductService $productService, QueryType $queryType, MutationType $mutationType)
    {
        $this->productService = $productService;
        $this->queryType = $queryType;
        $this->mutationType = $mutationType;
    }

    public function handle() { 
        try {
            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($this->queryType)
                    ->setMutation($this->mutationType)
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            // $context = [
            //     'productService' => $this->productService,
            //     // 'categoryService' => $categoryService,
            // ];

            // $result = GraphQLBase::executeQuery($schema, $query, null, $context, $variableValues);
            $result = GraphQLBase::executeQuery($schema, $query, null, $variableValues);

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

    private static function getRequestedFields($info): array {
        return array_map(
            fn($field) => $field->name->value,
            iterator_to_array($info->fieldNodes[0]->selectionSet->selections)
        );
    }    
}