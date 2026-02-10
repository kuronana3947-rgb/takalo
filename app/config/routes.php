<?php

use app\controllers\ApiExampleController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\controllers\Authentification;
use app\models\Users;
use app\models\Categories;
use app\models\Objets;
use app\models\Echanges;
use app\models\Notifications;
use app\models\Photos;



// Helper : vérifier si l'utilisateur est connecté, sinon rediriger
function requireLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['user'])) {
        Flight::redirect('/login');
        return false;
    }
    return $_SESSION['user'];
}

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

	$router->get('/', function() use ($app) {
		$app->render('login');
	});

	
	$router->get('/login', function() use ($app) {
		$authController = new Authentification($app);
		$authController->showLogin();
	});

	$router->post('/login', function() use ($app) {
		$authController = new Authentification($app);
		$authController->login();
	});

	$router->get('/register', function() use ($app) {
		$authController = new Authentification($app);
		$authController->showRegister();
	});

	$router->post('/register', function() use ($app) {
		$authController = new Authentification($app);
		$authController->register();
	});

	$router->get('/logout', function() use ($app) {
		$authController = new Authentification($app);
		$authController->logout();
	});

	$router->get('/api/check-email', function() use ($app) {
		$authController = new Authentification($app);
		$authController->checkEmailAvailability();
	});

	
	$router->get('/home', function() {
		$user = requireLogin();
		if (!$user) return;

		$usersModel    = new Users(Flight::db());
		$echangesModel = new Echanges(Flight::db());
		$objetsModel   = new Objets(Flight::db());
		$catModel      = new Categories(Flight::db());

		$data = [
			'stats' => [
				'totalUsers'      => $usersModel->countAll(),
				'totalEchanges'   => $echangesModel->countAll(),
				'totalObjets'     => $objetsModel->countAll(),
				'totalCategories' => $catModel->countAll()
			],
			'recentEchanges' => $echangesModel->getAll(10)
		];

		Authentification::urlPage('home', $data);
	});

	$router->get('/categories', function() {
		$user = requireLogin();
		if (!$user) return;

		$catModel = new Categories(Flight::db());
		$categories = $catModel->getAll();

		Authentification::urlPage('categories', $categories);
	});

	
	$router->get('/categories/@id', function($id) {
		$user = requireLogin();
		if (!$user) return;

		$catModel    = new Categories(Flight::db());
		$objetsModel = new Objets(Flight::db());
		$photosModel = new Photos(Flight::db());

		$categorie = $catModel->getById($id);
		$objets = $objetsModel->getByCategory($id);

		// Attacher les photos à chaque objet
		foreach ($objets as &$obj) {
			$obj['photos'] = $photosModel->getByObjet($obj['idObjet']);
		}

		Authentification::urlPage('categorie-objets', [
			'categorie' => $categorie,
			'objets'    => $objets
		]);
	});

	// Échanges en cours
	$router->get('/echanges', function() {
		$user = requireLogin();
		if (!$user) return;

		$echangesModel = new Echanges(Flight::db());
		$echanges = $echangesModel->getForUser($user['idUser']);

		Authentification::urlPage('echanges', $echanges);
	});

	$router->get('/profil', function() {
		$user = requireLogin();
		if (!$user) return;

		$objetsModel = new Objets(Flight::db());
		$photosModel = new Photos(Flight::db());
		$objets = $objetsModel->getByOwner($user['idUser']);

		foreach ($objets as &$obj) {
			$obj['photos'] = $photosModel->getByObjet($obj['idObjet']);
		}

		Authentification::urlPage('profil', $objets);
	});

	$router->get('/historique', function() {
		$user = requireLogin();
		if (!$user) return;

		$echangesModel = new Echanges(Flight::db());
		$historique = $echangesModel->getValidated($user['idUser']);

		Authentification::urlPage('historique', $historique);
	});

	$router->get('/objets/@id', function($id) {
		$user = requireLogin();
		if (!$user) return;

		$objetsModel = new Objets(Flight::db());
		$photosModel = new Photos(Flight::db());
		$usersModel  = new Users(Flight::db());
		$catModel    = new Categories(Flight::db());

		$objet = $objetsModel->getById($id);
		if (empty($objet)) {
			Authentification::urlPage('objet', ['objet' => [], 'photos' => [], 'owner' => [], 'mesObjets' => []]);
			return;
		}

		$photos = $photosModel->getByObjet($id);
		$owner  = $usersModel->getById($objet['idProprietaire']);

		$cat = $catModel->getById($objet['idCategorie']);
		$objet['categorieName'] = $cat['categorie'] ?? 'Non classé';

		$mesObjets = [];
		if ($user['idUser'] != $objet['idProprietaire']) {
			$mesObjets = $objetsModel->getByOwner($user['idUser']);
			foreach ($mesObjets as &$mo) {
				$mo['photos'] = $photosModel->getByObjet($mo['idObjet']);
			}
		}

		Authentification::urlPage('objet', [
			'objet'     => $objet,
			'photos'    => $photos,
			'owner'     => $owner,
			'mesObjets' => $mesObjets
		]);
	});

	$router->get('/user/@id', function($id) {
		$user = requireLogin();
		if (!$user) return;

		if ($user['idUser'] == $id) {
			Flight::redirect('/profil');
			return;
		}

		$usersModel  = new Users(Flight::db());
		$objetsModel = new Objets(Flight::db());
		$photosModel = new Photos(Flight::db());

		$owner = $usersModel->getById($id);
		if (empty($owner)) {
			Flight::redirect('/categories');
			return;
		}

		$objets = $objetsModel->getByOwner($id);
		foreach ($objets as &$obj) {
			$obj['photos'] = $photosModel->getByObjet($obj['idObjet']);
		}

		Authentification::urlPage('user-objets', [
			'owner'  => $owner,
			'objets' => $objets
		]);
	});


	$router->post('/objets/add', function() {
		$user = requireLogin();
		if (!$user) return;

		$isAjax = Flight::request()->getHeader('X-Requested-With') === 'XMLHttpRequest';

		$titre       = trim(Flight::request()->data->titre ?? '');
		$descriptions = trim(Flight::request()->data->descriptions ?? '');
		$prix        = floatval(Flight::request()->data->prix ?? 0);
		$idCategorie = intval(Flight::request()->data->idCategorie ?? 0);

		if (!$titre || $prix <= 0 || !$idCategorie) {
			if ($isAjax) {
				Flight::json(['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires.']);
			} else {
				Flight::redirect('/profil?error=champs_requis');
			}
			return;
		}

		$objetsModel = new Objets(Flight::db());
		$idObjet = $objetsModel->createObjet([
			'titre'          => $titre,
			'descriptions'   => $descriptions,
			'prix'           => $prix,
			'idCategorie'    => $idCategorie,
			'isValidate'     => 0,
			'idProprietaire' => $user['idUser']
		]);


		if ($idObjet && !empty($_FILES['photos']['name'][0])) {
			$photosModel = new Photos(Flight::db());
			$uploadDir = __DIR__ . '/../../public/images/objets/';
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0755, true);
			}

			$fileCount = count($_FILES['photos']['name']);
			for ($i = 0; $i < $fileCount; $i++) {
				if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
					$ext = pathinfo($_FILES['photos']['name'][$i], PATHINFO_EXTENSION);
					$filename = 'obj_' . $idObjet . '_' . ($i + 1) . '_' . time() . '.' . $ext;
					$dest = $uploadDir . $filename;

					if (move_uploaded_file($_FILES['photos']['tmp_name'][$i], $dest)) {
						$photosModel->addPhoto($idObjet, $filename);
					}
				}
			}
		}

		if ($isAjax) {
			Flight::json(['success' => true, 'message' => 'Objet ajouté avec succès', 'redirect' => '/profil']);
		} else {
			Flight::redirect('/profil?success=objet_ajoute');
		}
	});

	$router->post('/objets/delete', function() {
		$user = requireLogin();
		if (!$user) return;

		$idObjet = intval(Flight::request()->data->idObjet ?? 0);
		if (!$idObjet) {
			Flight::redirect('/profil');
			return;
		}

		$objetsModel = new Objets(Flight::db());
		$objet = $objetsModel->getById($idObjet);
		if (empty($objet) || $objet['idProprietaire'] != $user['idUser']) {
			Flight::redirect('/profil?error=non_autorise');
			return;
		}

		$photosModel = new Photos(Flight::db());
		$photos = $photosModel->getByObjet($idObjet);
		$uploadDir = __DIR__ . '/../../public/images/objets/';
		foreach ($photos as $photo) {
			$filePath = $uploadDir . $photo['img'];
			if (file_exists($filePath)) {
				unlink($filePath);
			}
		}

		$photosModel->deleteByObjet($idObjet);
		$objetsModel->deleteObjet($idObjet);

		Flight::redirect('/profil?success=objet_supprime');
	});

	$router->post('/echanges/accept', function() {
		$user = requireLogin();
		if (!$user) return;

		$idEchange = intval(Flight::request()->data->idEchange ?? 0);
		if (!$idEchange) {
			Flight::redirect('/echanges');
			return;
		}

		$echangesModel = new Echanges(Flight::db());
		$echange = $echangesModel->getById($idEchange);

		if (!empty($echange) && $echange['idRecever'] == $user['idUser']) {
			$echangesModel->validate($idEchange);

			$notifModel = new Notifications(Flight::db());
			$notifModel->createNotification([
				'idSender'  => $user['idUser'],
				'idRecever' => $echange['idSender'],
				'isRead'    => 0
			]);
		}

		Flight::redirect('/echanges?success=echange_accepte');
	});

	$router->post('/echanges/reject', function() {
		$user = requireLogin();
		if (!$user) return;

		$idEchange = intval(Flight::request()->data->idEchange ?? 0);
		if (!$idEchange) {
			Flight::redirect('/echanges');
			return;
		}

		$echangesModel = new Echanges(Flight::db());
		$echange = $echangesModel->getById($idEchange);

		if (!empty($echange) && $echange['idRecever'] == $user['idUser']) {
			$echangesModel->deleteEchange($idEchange);

			$notifModel = new Notifications(Flight::db());
			$notifModel->createNotification([
				'idSender'  => $user['idUser'],
				'idRecever' => $echange['idSender'],
				'isRead'    => 0
			]);
		}

		Flight::redirect('/echanges?success=echange_refuse');
	});

	$router->post('/echanges/propose', function() {
		$user = requireLogin();
		if (!$user) return;

		$idObjetSender  = intval(Flight::request()->data->idObjetSender ?? 0);
		$idObjetRecever = intval(Flight::request()->data->idObjetRecever ?? 0);
		$idRecever      = intval(Flight::request()->data->idRecever ?? 0);

		if (!$idObjetSender || !$idObjetRecever || !$idRecever) {
			Flight::redirect('/categories?error=champs_requis');
			return;
		}

		$objetsModel = new Objets(Flight::db());
		$monObjet = $objetsModel->getById($idObjetSender);
		if (empty($monObjet) || $monObjet['idProprietaire'] != $user['idUser']) {
			Flight::redirect('/categories?error=non_autorise');
			return;
		}

		$echangesModel = new Echanges(Flight::db());
		$echangesModel->createEchange([
			'idSender'       => $user['idUser'],
			'idRecever'      => $idRecever,
			'idObjetSender'  => $idObjetSender,
			'idObjetRecever' => $idObjetRecever,
			'isValidate'     => 0
		]);

		// Notifier le destinataire
		$notifModel = new Notifications(Flight::db());
		$notifModel->createNotification([
			'idSender'  => $user['idUser'],
			'idRecever' => $idRecever,
			'isRead'    => 0
		]);

		Flight::redirect('/echanges?success=echange_propose');
	});


	$router->group('/api', function() use ($router) {
		$router->get('/users', [ ApiExampleController::class, 'getUsers' ]);
		$router->get('/users/@id:[0-9]', [ ApiExampleController::class, 'getUser' ]);
		$router->post('/users/@id:[0-9]', [ ApiExampleController::class, 'updateUser' ]);
	});

	// Route legacy pour compatibilité
	$router->post('/log', function() use ($app) {
		$authController = new Authentification($app);
		$authController->login();
	});
	
}, [ SecurityHeadersMiddleware::class ]);