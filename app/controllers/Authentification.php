<?php

namespace app\controllers;

use flight\Engine;
use app\models\Users;
use Flight;

class Authentification {

	protected Engine $app;

	public function __construct($app) {
		$this->app = $app;
	}

	public function getUsers() {
		// You could actually pull data from the database if you had one set up
		// $users = $this->app->db()->fetchAll("SELECT * FROM users");
		$userModel = new UserModels() ;
		$users = $userModel->getUsers();

		// You actually could overwrite the json() method if you just wanted to
		// to ->json($users); and it would auto set pretty print for you.
		// https://flightphp.com/learn#overriding
		$this->app->json($users, 200, true, 'utf-8', JSON_PRETTY_PRINT);
	}

	public function getUser($id) {
		// You could actually pull data from the database if you had one set up
		// $user = $this->app->db()->fetchRow("SELECT * FROM users WHERE id = ?", [ $id ]);
		$userModel = new UserModels() ;
		$users = $userModel->getUsers();
		
		$users_filtered = array_filter($users, function($data) use ($id) {
			return $data['id'] === (int) $id;
		});
		if($users_filtered) {
			$user = array_pop($users_filtered);
		}
		$this->app->json($user, 200, true, 'utf-8', JSON_PRETTY_PRINT);
	}

	public function updateUser($id) {
		// You could actually update data from the database if you had one set up
		// $statement = $this->app->db()->runQuery("UPDATE users SET email = ? WHERE id = ?", [ $this->app->data['email'], $id ]);
		$this->app->json([ 'success' => true, 'id' => $id ], 200, true, 'utf-8', JSON_PRETTY_PRINT);
	}

    public static function urlPage($namePage, $data) {
        $User = new Users(Flight::db());
        Flight::render('model', [
            'namePage' => $namePage,
            'data' => $data,
            'user' => $User->getSessionUser()
        ]);
    }

    public function showLogin() {
        Flight::render('login');
    }

    public function showRegister() {
        // Afficher la vue d'inscription
        Flight::render('register');
    }
    
    public function login() {
        $email = Flight::request()->data->email ?? null;
        $password = Flight::request()->data->password ?? null;
        $remember = Flight::request()->data->remember ?? false;

  
        if ($remember && $email) {
            setcookie('remembered_email', $email, time() + (86400 * 30), '/', '', true, true);
        }

  
        $users = new Users(Flight::db());
        $users->check($email, $password);
    }

    public function register() {
        try {
            $email = trim(Flight::request()->data->email ?? '');
            $password = Flight::request()->data->password ?? '';
            $newsletter = Flight::request()->data->newsletter ?? false;


            $errors = [];


            if (!$email) {
                $errors[] = 'L\'email est requis';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Format d\'email invalide';
            }


            // if (!$password) {
            //     $errors[] = 'Le mot de passe est requis';
            // } elseif (strlen($password) < 8) {
            //     $errors[] = 'Le mot de passe doit contenir au moins 8 caractères';
            // } elseif (!preg_match('/[A-Z]/', $password)) {
            //     $errors[] = 'Le mot de passe doit contenir au moins une majuscule';
            // } elseif (!preg_match('/[a-z]/', $password)) {
            //     $errors[] = 'Le mot de passe doit contenir au moins une minuscule';
            // } elseif (!preg_match('/\d/', $password)) {
            //     $errors[] = 'Le mot de passe doit contenir au moins un chiffre';
            // } elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
            //     $errors[] = 'Le mot de passe doit contenir au moins un caractère spécial';
            // }

            if (!empty($errors)) {
                if (Flight::request()->getHeader('X-Requested-With') === 'XMLHttpRequest') {
                    Flight::json([
                        'success' => false,
                        'message' => implode(', ', $errors)
                    ]);
                    return;
                }
                Flight::redirect('/register?error=' . urlencode(implode(', ', $errors)));
                return;
            }

            $users = new Users(Flight::db());
            
            if ($users->emailExists($email)) {
                if (Flight::request()->getHeader('X-Requested-With') === 'XMLHttpRequest') {
                    Flight::json([
                        'success' => false,
                        'message' => 'Cet email est déjà utilisé'
                    ]);
                    return;
                }
                Flight::redirect('/register?error=email_exists'.$email);
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userId = $users->create($email, $hashedPassword);

            if ($userId) {
                if ($newsletter) {
                    
                }

                if (Flight::request()->getHeader('X-Requested-With') === 'XMLHttpRequest') {
                    Flight::json([
                        'success' => true,
                        'message' => 'Compte créé avec succès',
                        'redirect' => '/login?success=account_created'
                    ]);
                } else {
                    Flight::redirect('/login?success=account_created');
                }
            } else {
                throw new Exception('Erreur lors de la création du compte');
            }

        } catch (Exception $e) {
            error_log('Erreur d\'inscription: ' . $e->getMessage());
            
            if (Flight::request()->getHeader('X-Requested-With') === 'XMLHttpRequest') {
                Flight::json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la création du compte'
                ]);
            } else {
                Flight::redirect('/register?error=system_error');
            }
        }
    }

    public function logout() {
        $users = new Users(Flight::db());
        $users->logout();
        

        setcookie('remembered_email', '', time() - 3600, '/', '', true, true);
        
        Flight::redirect('/login?success=logged_out');
    }

    public function checkEmailAvailability() {
        $email = Flight::request()->query->email ?? '';
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flight::json(['available' => false, 'message' => 'Email invalide']);
            return;
        }

        $users = new Users(Flight::db());
        $exists = $users->emailExists($email);
        
        Flight::json(['available' => !$exists]);
    }

    public function chekLog() {
        $this->login();
    }
}