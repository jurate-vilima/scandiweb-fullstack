<?php
namespace App\Models;

use App\Database;

abstract class Model {
    protected array $data = [];
    private Database $db;

    public function __construct(Database $db, array $data = []) {
        $this->db = $db;
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    public function findAll() {
        $sql = "SELECT * FROM $this->tableName";
        return $this->db->fetchAll($sql);
    }
}
