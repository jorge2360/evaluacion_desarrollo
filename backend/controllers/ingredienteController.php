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

    public function store(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                jsonResponse(400, [
                    'success' => false,
                    'message' => 'No se recibieron datos válidos.'
                ]);
            }

            $nombre = trim($input['nombre'] ?? '');
            $descripcion = trim($input['descripcion'] ?? '');
            $fecha_ingreso = trim($input['fecha_ingreso'] ?? '');
            $fecha_vencimiento = trim($input['fecha_vencimiento'] ?? '');

            if (
                $nombre === '' ||
                $fecha_ingreso === '' ||
                $fecha_vencimiento === ''
            ) {
                jsonResponse(422, [
                    'success' => false,
                    'message' => 'Los campos nombre, fecha_ingreso y fecha_vencimiento son obligatorios.'
                ]);
            }

            $sql = "INSERT INTO ingrediente (nombre, descripcion, fecha_ingreso, fecha_vencimiento)
                    VALUES (:nombre, :descripcion, :fecha_ingreso, :fecha_vencimiento)";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':fecha_ingreso' => $fecha_ingreso,
                ':fecha_vencimiento' => $fecha_vencimiento
            ]);

            jsonResponse(201, [
                'success' => true,
                'message' => 'Ingrediente creado correctamente.'
            ]);
        } catch (PDOException $e) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Error al crear el ingrediente.'
            ]);
        }
    }

    public function show(int $id): void
    {
        try {
            $sql = "SELECT * FROM ingrediente WHERE id_ingrediente = :id LIMIT 1";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':id' => $id]);

            $ingrediente = $stmt->fetch();

            if (!$ingrediente) {
                jsonResponse(404, [
                    'success' => false,
                    'message' => 'Ingrediente no encontrado.'
                ]);
            }

            jsonResponse(200, [
                'success' => true,
                'data' => $ingrediente
            ]);
        } catch (PDOException $e) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Error al obtener el ingrediente.'
            ]);
        }
    }

    public function update(int $id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                jsonResponse(400, [
                    'success' => false,
                    'message' => 'No se recibieron datos válidos.'
                ]);
            }

            $nombre = trim($input['nombre'] ?? '');
            $descripcion = trim($input['descripcion'] ?? '');
            $fecha_ingreso = trim($input['fecha_ingreso'] ?? '');
            $fecha_vencimiento = trim($input['fecha_vencimiento'] ?? '');

            if ($nombre === '' || $fecha_ingreso === '' || $fecha_vencimiento === '') {
                jsonResponse(422, [
                    'success' => false,
                    'message' => 'Los campos nombre, fecha_ingreso y fecha_vencimiento son obligatorios.'
                ]);
            }

            $checkSql = "SELECT id_ingrediente FROM ingrediente WHERE id_ingrediente = :id LIMIT 1";
            $checkStmt = $this->connection->prepare($checkSql);
            $checkStmt->execute([':id' => $id]);
            $ingrediente = $checkStmt->fetch();

            if (!$ingrediente) {
                jsonResponse(404, [
                    'success' => false,
                    'message' => 'Ingrediente no encontrado.'
                ]);
            }

            $sql = "UPDATE ingrediente
                    SET nombre = :nombre,
                        descripcion = :descripcion,
                        fecha_ingreso = :fecha_ingreso,
                        fecha_vencimiento = :fecha_vencimiento
                    WHERE id_ingrediente = :id";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':fecha_ingreso' => $fecha_ingreso,
                ':fecha_vencimiento' => $fecha_vencimiento,
                ':id' => $id
            ]);

            jsonResponse(200, [
                'success' => true,
                'message' => 'Ingrediente actualizado correctamente.'
            ]);
        } catch (PDOException $e) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Error al actualizar el ingrediente.'
            ]);
        }
    }
}