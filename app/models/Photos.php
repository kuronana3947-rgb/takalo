<?php

namespace app\models;

use PDO;

class Photos
{
    protected $table = 'photos';
    protected $pk = 'idPhoto';
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getByObjet($idObjet)
    {
        $sql = "SELECT * FROM {$this->table} WHERE idObjet = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idObjet]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPhoto($idObjet, $img)
    {
        $sql = "INSERT INTO {$this->table} (idObjet, img) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idObjet, $img]);
        return $this->db->lastInsertId();
    }

    public function deletePhoto($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->pk} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    public function deleteByObjet($idObjet)
    {
        $sql = "DELETE FROM {$this->table} WHERE idObjet = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idObjet]);
        return $stmt->rowCount();
    }
}
