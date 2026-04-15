<?php

use App\Core\Application;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$app = Application::getInstance();

// Rotas públicas (sem necessidade de login)
$app->router()->get('/test', [App\Controllers\HomeController::class, 'test']);
$app->router()->get('/auth/login', [App\Controllers\AuthController::class, 'login']);

// Rotas protegidas (necessita login)
$app->router()->group('', function($router) {
    $router->get('/', [App\Controllers\HomeController::class, 'index']);
    $router->get('/home', [App\Controllers\HomeController::class, 'index']);
}, [\App\Http\Middleware\AuthMiddleware::class]);

// Grupo de autenticação
$app->router()->group('/auth', function($router) {
    $router->get('/login', [App\Controllers\AuthController::class, 'login']);
    $router->post('/login', [App\Controllers\AuthController::class, 'doLogin']);
    $router->get('/logout', [App\Controllers\AuthController::class, 'logout']);
    $router->post('/logout', [App\Controllers\AuthController::class, 'logout']);
});

// Grupo admin (com middleware de autenticação)
$app->router()->group('/admin', function($router) {
    $router->get('/', [App\Controllers\AdminController::class, 'index']);
    $router->get('/dashboard', [App\Controllers\AdminController::class, 'index']);
}, [\App\Http\Middleware\AuthMiddleware::class]);

// Grupo users (com middleware de autenticação)
$app->router()->group('/users', function($router) {
    $router->get('/', [App\Controllers\UserController::class, 'index']);
    $router->get('/create', [App\Controllers\UserController::class, 'create']);
    $router->post('/store', [App\Controllers\UserController::class, 'store']);
    $router->get('/edit/{id}', [App\Controllers\UserController::class, 'edit']);
    $router->post('/update/{id}', [App\Controllers\UserController::class, 'update']);
    $router->get('/show/{id}', [App\Controllers\UserController::class, 'show']);
    $router->post('/delete/{id}', [App\Controllers\UserController::class, 'delete']);
    $router->post('/toggle/{id}', [App\Controllers\UserController::class, 'toggle']);
}, [\App\Http\Middleware\AuthMiddleware::class]);

$app->run();