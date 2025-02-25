<?php
namespace App\Models;

use App\Database;

class Category extends Model {
    protected string $tableName = 'categories';

    private int $id;
    private string $name;

    public function __construct(array $data = []) {
        $this->id = $data['id'];
        $this->name = $data['name'];
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
}