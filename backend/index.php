<?php

require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $connection = $database->connect();

    echo json_encode([
        'success' => true,
        'message' => 'Conexion a la base de datos establecida correctamente.'
    ]);
} catch (Exception $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Error al conectar con la base de datos.'
    ]);
}