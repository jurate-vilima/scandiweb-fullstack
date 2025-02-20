<?php
namespace App\Models;

use App\Database;

abstract class Model {
    protected array $data = [];
    protected Database $db;
    protected string $tableName;

    public function __construct(Database $db, array $data = []) {
        $this->db = $db;
        $this->data = $data;
    }

    public function getData(): array {
        return $this->data;
    }

    public function findAllFields(array $fields): array {
        $fields = array_filter($fields, function ($field) {
            return $field !== 'gallery'; // Exclude 'gallery'
        });
        
        $fields = array_map(function ($field) {
            return $field === 'category' ? 'category_id' : $field;
        }, $fields);
        

        // var_dump($fields);
        // exit();

        $fieldsStr = implode(',', $fields);
        $sql = "SELECT $fieldsStr FROM $this->tableName";
        $results = $this->db->executeQuery($sql);

        // var_dump(array_map(function ($row) {
        //     return new $this($this->db, $row);
        // }, $results));
        // exit();

        return array_map(function ($row) {
            return new $this($this->db, $row);
        }, $results);
    }

    public function findById($id) {
        $sql = "SELECT * FROM $this->tableName WHERE id = :id";
        $row = $this->db->executeQuery($sql, ['id' => $id], true); 

        if ($row) {
            return new static($this->db, $row);
        }

        return null;
    }

}