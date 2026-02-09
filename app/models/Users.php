<?php

namespace app\models;
// session_start();

use Flight;
use flight\database\PdoWrapper;
use PDO;
use app\controllers\Authentification;

class Users
{
    protected $table = 'users';
    protected $pk = 'idUser';
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

    public function findByEmail(string $email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        return $this->fetchRow($sql, [$email]);
    }

    public function createUser(array $data)
    {
        $cols = array_keys($data);
        $placeholders = array_fill(0, count($cols), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
        $this->runQuery($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    public function updateUser($id, array $data)
    {
        $cols = array_keys($data);
        $sets = array_map(function ($c) { return "{$c} = ?"; }, $cols);
        $sql = "UPDATE {$this->table} SET " . implode(',', $sets) . " WHERE {$this->pk} = ?";
        $params = array_merge(array_values($data), [$id]);
        $stmt = $this->runQuery($sql, $params);
        return $stmt->rowCount();
    }

    public function deleteUser($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->pk} = ?";
        $stmt = $this->runQuery($sql, [$id]);
        return $stmt->rowCount();
    }

    public function isAdmin($id)
    {
        $sql = "SELECT isAdmin FROM {$this->table} WHERE {$this->pk} = ? LIMIT 1";
        $row = $this->fetchRow($sql, [$id]);
        return !empty($row) && !empty($row['isAdmin']);
    }

    public static function SessionUser($user){
        session_start();
        $_SESSION['user'] = $user;
    }

    public static function getSessionUser(){
        //  session_start();
        return $_SESSION['user'] ?? "";
    }

    public function getUser($email, $mdp) {
        $sql = 'SELECT * FROM '.$this->table.' WHERE email = ? AND mdp = ? LIMIT 1';
        echo $sql;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email, $mdp]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function check($email, $mdp) {
        // $this->getUser($email, $mdp);
        if($this->getUser($email, $mdp)) {
            $this->SessionUser($this->getUser($email, $mdp));
            echo "<pre>";
            print_r($this->getUser($email, $mdp));
            echo "</pre>";
            $categories = new Categories(Flight::db());
            $categorie = $categories->getAll();
            Authentification::urlPage('home', $categorie);
        }
            else {
            Flight::render('login');
        }
        // header('Location: ' . Flight::url('/'));
        // exit();
    }

}