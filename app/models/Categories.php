<?php

namespace app\models;

use Flight;
use flight\database\PdoWrapper;
use PDO;

class Categories
{
    protected $table = 'categories';
    protected $pk = 'idCategorie';
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll(int $limit = 0, int $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->pk} = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function createCategory(array $data)
    {
        $cols = array_keys($data);
        $placeholders = array_fill(0, count($cols), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    public function updateCategory($id, array $data)
    {
        $cols = array_keys($data);
        $sets = array_map(function ($c) { return "{$c} = ?"; }, $cols);
        $sql = "UPDATE {$this->table} SET " . implode(',', $sets) . " WHERE {$this->pk} = ?";
        $params = array_merge(array_values($data), [$id]);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function deleteCategory($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->pk} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    public function countAll()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }
}