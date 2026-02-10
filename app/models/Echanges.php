<?php

namespace app\models;

use Flight;
use PDO;
use app\models\Objets;
class Echanges
{
    protected $table = 'echanges';
    protected $pk = 'idEchange';
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll(int $limit = 0, int $offset = 0)
    {
        $sql = "SELECT e.*, 
                       us.email as senderEmail, ur.email as receverEmail,
                       os.titre as objetSenderTitre, orr.titre as objetReceverTitre
                FROM {$this->table} e
                LEFT JOIN users us ON e.idSender = us.idUser
                LEFT JOIN users ur ON e.idRecever = ur.idUser
                LEFT JOIN objets os ON e.idObjetSender = os.idObjet
                LEFT JOIN objets orr ON e.idObjetRecever = orr.idObjet
                ORDER BY e.date DESC";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT e.*, 
                       us.email as senderEmail, ur.email as receverEmail,
                       os.titre as objetSenderTitre, orr.titre as objetReceverTitre
                FROM {$this->table} e
                LEFT JOIN users us ON e.idSender = us.idUser
                LEFT JOIN users ur ON e.idRecever = ur.idUser
                LEFT JOIN objets os ON e.idObjetSender = os.idObjet
                LEFT JOIN objets orr ON e.idObjetRecever = orr.idObjet
                WHERE e.{$this->pk} = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getForUser($userId, int $limit = 0, int $offset = 0)
    {
        $sql = "SELECT e.*, 
                       us.email as senderEmail, ur.email as receverEmail,
                       os.titre as objetSenderTitre, orr.titre as objetReceverTitre
                FROM {$this->table} e
                LEFT JOIN users us ON e.idSender = us.idUser
                LEFT JOIN users ur ON e.idRecever = ur.idUser
                LEFT JOIN objets os ON e.idObjetSender = os.idObjet
                LEFT JOIN objets orr ON e.idObjetRecever = orr.idObjet
                WHERE e.idSender = ? OR e.idRecever = ?
                ORDER BY e.date DESC";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPending($userId)
    {
        $sql = "SELECT e.*, 
                       us.email as senderEmail, ur.email as receverEmail,
                       os.titre as objetSenderTitre, orr.titre as objetReceverTitre
                FROM {$this->table} e
                LEFT JOIN users us ON e.idSender = us.idUser
                LEFT JOIN users ur ON e.idRecever = ur.idUser
                LEFT JOIN objets os ON e.idObjetSender = os.idObjet
                LEFT JOIN objets orr ON e.idObjetRecever = orr.idObjet
                WHERE (e.idSender = ? OR e.idRecever = ?) AND e.isValidate = 0
                ORDER BY e.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getValidated($userId)
    {
        $sql = "SELECT e.*, 
                       us.email as senderEmail, ur.email as receverEmail,
                       os.titre as objetSenderTitre, orr.titre as objetReceverTitre
                FROM {$this->table} e
                LEFT JOIN users us ON e.idSender = us.idUser
                LEFT JOIN users ur ON e.idRecever = ur.idUser
                LEFT JOIN objets os ON e.idObjetSender = os.idObjet
                LEFT JOIN objets orr ON e.idObjetRecever = orr.idObjet
                WHERE (e.idSender = ? OR e.idRecever = ?) AND e.isValidate = 1
                ORDER BY e.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createEchange(array $data)
    {
        $cols = array_keys($data);
        $placeholders = array_fill(0, count($cols), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    public function validate($id)
    {
        $sql = "UPDATE {$this->table} SET isValidate = 1 WHERE {$this->pk} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $object = new Objets(Flight::db());
        $echange = $this->getById($id);
        $object-> echangeOwnership($echange['idObjetSender'], $echange['idObjetRecever']);
        return $stmt->rowCount();
    }

    public function deleteEchange($id)
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

    public function countForUser($userId)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE idSender = ? OR idRecever = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }
}
