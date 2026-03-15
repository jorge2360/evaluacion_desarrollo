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

    public function store(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                jsonResponse(400, [
                    'success' => false,
                    'message' => 'No se recibieron datos validos.'
                ]);
            }

            $nombre = trim($input['nombre'] ?? '');
            $descripcion = trim($input['descripcion'] ?? '');
            $preparado_por = trim($input['preparado_por'] ?? '');
            $fecha_creacion = trim($input['fecha_creacion'] ?? '');
            $fecha_vencimiento = trim($input['fecha_vencimiento'] ?? '');

            if (
                $nombre === '' ||
                $preparado_por === '' ||
                $fecha_creacion === '' ||
                $fecha_vencimiento === ''
            ) {
                jsonResponse(422, [
                    'success' => false,
                    'message' => 'Los campos nombre, preparado_por, fecha_creacion y fecha_vencimiento son obligatorios.'
                ]);
            }

            $sql = "INSERT INTO pastel (nombre, descripcion, preparado_por, fecha_creacion, fecha_vencimiento)
                    VALUES (:nombre, :descripcion, :preparado_por, :fecha_creacion, :fecha_vencimiento)";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':preparado_por' => $preparado_por,
                ':fecha_creacion' => $fecha_creacion,
                ':fecha_vencimiento' => $fecha_vencimiento
            ]);

            jsonResponse(201, [
                'success' => true,
                'message' => 'Pastel creado correctamente.'
            ]);
        } catch (PDOException $e) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Error al crear el pastel.'
            ]);
        }
    }
}