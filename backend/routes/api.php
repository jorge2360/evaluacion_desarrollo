<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ingredienteController.php';

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

jsonResponse(404, [
    'success' => false,
    'message' => 'Ruta no encontrada.'
]);