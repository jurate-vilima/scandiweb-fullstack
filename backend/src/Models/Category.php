<?php
namespace App\Models;

class Category extends Model {
    private string $name;
    protected string $tableName = 'categories';

    public function __construct() {
        //$this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function findAll() : array {
        return [
            ['name' => 'Category 1'],
            ['name' => 'Category 2'],
        ];
    }
}