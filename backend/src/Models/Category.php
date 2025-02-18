<?php
namespace App\Models;

use App\Database;

class Category extends Model {
    protected string $tableName = 'categories';

    public function __construct(Database $db) {
        parent::__construct($db);
    }

    public function getName() {
        // Assuming $this->data contains a 'name' key from the DB row
        return $this->data['name'] ?? null;
    }
}