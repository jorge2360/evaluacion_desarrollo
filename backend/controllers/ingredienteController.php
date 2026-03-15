<?php

require_once __DIR__ . '/../utils/response.php';

class IngredienteController
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function index(): void
    {
        try {
            $sql = "SELECT * FROM ingrediente ORDER BY id_ingrediente DESC";
            $stmt = $this->connection->query($sql);
            $ingredientes = $stmt->fetchAll();

            jsonResponse(200, [
                'success' => true,
                'data' => $ingredientes
            ]);
        } catch (PDOException $e) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Error al obtener los ingredientes.'
            ]);
        }
    }
}