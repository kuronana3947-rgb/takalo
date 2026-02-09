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

    protected function fetchAll(string $sql, array $params = [])
    {
        if (method_exists($this->db, 'fetchAll')) {
            return $this->db->fetchAll($sql, $params);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function fetchRow(string $sql, array $params = [])
    {
        if (method_exists($this->db, 'fetchRow')) {
            return $this->db->fetchRow($sql, $params);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    protected function runQuery(string $sql, array $params = [])
    {
        if (method_exists($this->db, 'runQuery')) {
            return $this->db->runQuery($sql, $params);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function getAll(int $limit = 0, int $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        return $this->fetchAll($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->pk} = ? LIMIT 1";
        return $this->fetchRow($sql, [$id]);
    }

    public function createCategory(array $data)
    {
        $cols = array_keys($data);
        $placeholders = array_fill(0, count($cols), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
        $this->runQuery($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    public function updateCategory($id, array $data)
    {
        $cols = array_keys($data);
        $sets = array_map(function ($c) { return "{$c} = ?"; }, $cols);
        $sql = "UPDATE {$this->table} SET " . implode(',', $sets) . " WHERE {$this->pk} = ?";
        $params = array_merge(array_values($data), [$id]);
        $stmt = $this->runQuery($sql, $params);
        return $stmt->rowCount();
    }

    public function deleteCategory($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->pk} = ?";
        $stmt = $this->runQuery($sql, [$id]);
        return $stmt->rowCount();
    }
}

