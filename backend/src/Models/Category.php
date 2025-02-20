<?php
namespace App\Models;

use App\Database;

class Category extends Model {
    protected string $tableName = 'categories';

    public function __construct(Database $db, array $data = []) {
        parent::__construct($db, $data); 
    }
}