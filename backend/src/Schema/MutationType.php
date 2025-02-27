<?php
namespace App\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Services\OrderService;

class MutationType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Mutation',
            'fields' => [
                'createOrder' => [
                    'type' => Type::string(), // Определим OrderType позже
                    'args' => [
                        'input' => ['type' => Type::nonNull(type::string())]
                    ],
                    'resolve' => function ($root, $args) {
                        // Вызываем сервис для создания заказа
                        // return $this->orderService->createOrder($args['input']);
                    }
                ],
                // 'createOrder' => [
                //     'type' => OrderType::instance(), // Определим OrderType позже
                //     'args' => [
                //         'input' => ['type' => Type::nonNull(OrderInputType::instance())]
                //     ],
                //     'resolve' => function ($root, $args) {
                //         // Вызываем сервис для создания заказа
                //         // return $this->orderService->createOrder($args['input']);
                //     }
                // ],
                // При необходимости — другие мутации
            ],
        ];

        parent::__construct($config);
    }
}