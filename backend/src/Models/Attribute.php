<?php
namespace App\Models;

use App\Database;

class Attribute extends Model {
    protected string $tableName = 'attributes';

    public string $id;
    public string $name;
    public string $type; 
    public array $items = []; 

    public function __construct(Database $db, array $data = []) {
        parent::__construct($db, $data); 
    }
}