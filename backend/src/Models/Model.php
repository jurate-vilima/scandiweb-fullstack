<?php
namespace App\Models;

use App\Database;

abstract class Model {
    protected array $data = []; 
    private Database $db;

    public function __construct(array $data = []) {
        try {
            $this->db = new Database();
        } catch (\PDOException $e) {
            throw new \Exception("DB connection failed in Model: " . $e->getMessage());
        }

        $this->data = $data;
    }

    // abstract public function save();
    // abstract public function validate();

    public function getData() {
        return $this->data;
    }

    // public function findAll() {

    // }
}