<?php

require_once __DIR__ . '/../utils/response.php';

class PastelController
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function index(): void
    {
        try {
            $sql = "SELECT * FROM pastel ORDER BY id_pastel DESC";
            $stmt = $this->connection->query($sql);
            $pasteles = $stmt->fetchAll();

            jsonResponse(200, [
                'success' => true,
                'data' => $pasteles
            ]);
        } catch (PDOException $e) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Error al obtener los pasteles.'
            ]);
        }
    }
}