<?php

namespace app\models;

use Flight;
use flight\database\PdoWrapper;
use PDO;

class Objets
{
    protected $table = 'objets';
    protected $pk = 'idObjet';
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

    public function createObjet(array $data)
    {
        $cols = array_keys($data);
        $placeholders = array_fill(0, count($cols), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    public function updateObjet($id, array $data)
    {
        $cols = array_keys($data);
        $sets = array_map(function ($c) { return "{$c} = ?"; }, $cols);
        $sql = "UPDATE {$this->table} SET " . implode(',', $sets) . " WHERE {$this->pk} = ?";
        $params = array_merge(array_values($data), [$id]);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function deleteObjet($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->pk} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    public function setValidate($id, $isValidate = 1)
    {
        $sql = "UPDATE {$this->table} SET isValidate = ? WHERE {$this->pk} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$isValidate, $id]);
        return $stmt->rowCount();
    }

    public function getByCategory($categoryId, int $limit = 0, int $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table} WHERE idCategorie = ?";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByOwner($ownerId, int $limit = 0, int $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table} WHERE idProprietaire = ?";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }
    public function UpdateIdProprietaire($idObjet, $newOwnerId)
    {
        $sql = "UPDATE {$this->table} SET idProprietaire = ? WHERE {$this->pk} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$newOwnerId, $idObjet]);
        return $stmt->rowCount();
    }
    public function echangeOwnership($idObjet1, $idObjet2)
    {
        // Récupérer les objets
        $objet1 = $this->getById($idObjet1);
        $objet2 = $this->getById($idObjet2);

        if (empty($objet1) || empty($objet2)) {
            return false; 
        }


        $tempOwner = $objet1['idProprietaire'];
        $this->UpdateIdProprietaire($idObjet1, $objet2['idProprietaire']);
        $this->UpdateIdProprietaire($idObjet2, $tempOwner);

        return true;
    }
}