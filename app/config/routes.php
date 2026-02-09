<?php

use app\controllers\ApiExampleController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\controllers\Authentification;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

	$router->get('/', function() use ($app) {
		$app->render('login');
	});

	// Routes d'authentification
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

	$router->get('/route-iray', function() use ($app) {
		echo '<h1>route iray ve!</h1>';
	});

	$router->get('/hello-world/@name', function($name) {
		echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
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