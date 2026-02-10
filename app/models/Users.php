<?php

namespace app\models;
// session_start();

use Flight;
use flight\database\PdoWrapper;
use PDO;
use app\controllers\Authentification;
use app\models\Categories;

class Users
{
    protected $table = 'users';
    protected $pk = 'idUser';
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

    public function findByEmail(string $email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function createUser(array $data)
    {
        $cols = array_keys($data);
        $placeholders = array_fill(0, count($cols), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    public function updateUser($id, array $data)
    {
        $cols = array_keys($data);
        $sets = array_map(function ($c) { return "{$c} = ?"; }, $cols);
        $sql = "UPDATE {$this->table} SET " . implode(',', $sets) . " WHERE {$this->pk} = ?";
        $params = array_merge(array_values($data), [$id]);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function deleteUser($id)
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

    public function isAdmin($id)
    {
        $sql = "SELECT isAdmin FROM {$this->table} WHERE {$this->pk} = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return !empty($row) && !empty($row['isAdmin']);
    }

    public static function SessionUser($user){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user'] = $user;
    }

    public static function getSessionUser(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }

    public function authenticate($email, $password) {
        try {

            $user = $this->findByEmail($email);
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ];
            }

           
            $isValidPassword = false;
            if (password_verify($password, $user['mdp'])) {
                $isValidPassword = true;
            } elseif ($password === $user['mdp']) {
                
                $isValidPassword = true;
                
               
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $this->updateUser($user['idUser'], ['mdp' => $hashedPassword]);
            }

            if ($isValidPassword) {
                
                $this->SessionUser($user);
                
                return [
                    'success' => true,
                    'user' => $user,
                    'message' => 'Connexion réussie'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ];
            }

        } catch (Exception $e) {
            error_log('Erreur d\'authentification: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'authentification'
            ];
        }
    }

    public function emailExists($email) {
        $user = $this->findByEmail($email);
        return !empty($user);
    }

    public function create($email, $hashedPassword, $isAdmin = false) {
        try {
            $data = [
                'email' => $email,
                'mdp' => $hashedPassword,
                'isAdmin' => $isAdmin ? 1 : 0
            ];
            
            $userId = $this->createUser($data);
            return $userId;

        } catch (Exception $e) {
            error_log('Erreur de création d\'utilisateur: ' . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
 
        $_SESSION = array();
        

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        

        session_destroy();
    }

    public function isLoggedIn() {
        return $this->getSessionUser() !== null;
    }

    public function getCurrentUser() {
        return $this->getSessionUser();
    }

    public function updateLastLogin($userId) {
        try {
            $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE {$this->pk} = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return true;
        } catch (Exception $e) {
            error_log('Erreur de mise à jour last_login: ' . $e->getMessage());
            return false;
        }
    }


    public function getUser($email, $mdp) {
        $result = $this->authenticate($email, $mdp);
        return $result['success'] ? $result['user'] : false;
    }

    public function check($email, $mdp) {
        $result = $this->authenticate($email, $mdp);
        
        if ($result['success']) {
            $categories = new Categories(Flight::db());
            $categorie = $categories->getAll();
            Authentification::urlPage('home', $categorie);
        } else {
            Flight::render('login');
        }
    }

}