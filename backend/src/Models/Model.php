<?php
namespace App\Models;

use App\Database;

abstract class Model {
    protected array $data = [];
    private Database $db;
    protected string $tableName;

    public function __construct(Database $db, array $data = []) {
        $this->db = $db;
        $this->data = $data;
    }

    public function getData(): array {
        return $this->data;
    }

   public function findAllFields(array $fields): array {
    // Replace 'category' with 'category_id' in the fields array
    $fields = array_map(function ($field) {
        return $field === 'category' ? 'category_id' : $field;
    }, $fields);

    $fieldsStr = implode(',', $fields);
    $sql = "SELECT $fieldsStr FROM $this->tableName";
    $results = $this->db->executeQuery($sql);

    // Map results to instances of the current class
    return array_map(function ($row) {
        return new static($this->db, $row);
    }, $results);
}

    // Example usage in the Model class:
    public function findById($id) {
        $sql = "SELECT * FROM $this->tableName WHERE id = :id";
        $row = $this->db->executeQuery($sql, ['id' => $id], true); // Fetch single result

        if ($row) {
            return new static($this->db, $row);
        }

        return null;
    }

}