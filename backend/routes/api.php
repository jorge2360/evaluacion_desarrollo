<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ingredienteController.php';
require_once __DIR__ . '/../controllers/pastelController.php';

$database = new Database();
$connection = $database->connect();

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/' || $uri === '') {
    jsonResponse(200, [
        'success' => true,
        'message' => 'API de examen pasteles funcionando correctamente.'
    ]);
}

if ($uri === '/ingredientes' && $method === 'GET') {
    $controller = new IngredienteController($connection);
    $controller->index();
}

if ($uri === '/ingredientes' && $method === 'POST') {
    $controller = new IngredienteController($connection);
    $controller->store();
}

if (preg_match('#^/ingredientes/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $controller = new IngredienteController($connection);
    $controller->show((int) $matches[1]);
}

if (preg_match('#^/ingredientes/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $controller = new IngredienteController($connection);
    $controller->update((int) $matches[1]);
}

if (preg_match('#^/ingredientes/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $controller = new IngredienteController($connection);
    $controller->destroy((int) $matches[1]);
}

if ($uri === '/pasteles' && $method === 'GET') {
    $controller = new PastelController($connection);
    $controller->index();
}

if ($uri === '/pasteles' && $method === 'POST') {
    $controller = new PastelController($connection);
    $controller->store();
}

if (preg_match('#^/pasteles/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $controller = new PastelController($connection);
    $controller->show((int) $matches[1]);
}

if (preg_match('#^/pasteles/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    $controller = new PastelController($connection);
    $controller->update((int) $matches[1]);
}

if (preg_match('#^/pasteles/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $controller = new PastelController($connection);
    $controller->destroy((int) $matches[1]);
}

jsonResponse(404, [
    'success' => false,
    'message' => 'Ruta no encontrada.'
]);