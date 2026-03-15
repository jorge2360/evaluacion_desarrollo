<?php

require_once __DIR__ . '/../utils/response.php';

class PastelIngredienteController
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function store(int $idPastel): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                jsonResponse(400, [
                    'success' => false,
                    'message' => 'No se recibieron datos validos.'
                ]);
            }

            $idIngrediente = (int)($input['id_ingrediente'] ?? 0);

            if ($idIngrediente <= 0) {
                jsonResponse(422, [
                    'success' => false,
                    'message' => 'El campo id_ingrediente es obligatorio    .'
                ]);
            }

            $pastelSql = "SELECT id_pastel FROM pastel WHERE id_pastel = :id";
            $pastelStmt = $this->connection->prepare($pastelSql);
            $pastelStmt->execute(['id' => $idPastel]);
            $pastel = $pastelStmt->fetch();

            if (!$pastel) {
                jsonResponse(404, [
                    'success' => false,
                    'message' => 'Pastel no encontrado.'
                ]);
            }

            $ingredienteSql = "SELECT id_ingrediente FROM ingrediente WHERE id_ingrediente = :id";
            $ingredienteStmt = $this->connection->prepare($ingredienteSql);
            $ingredienteStmt->execute([':id' => $idIngrediente]);
            $ingrediente = $ingredienteStmt->fetch();  

            if (!$ingrediente) {
                jsonResponse(404, [
                    'success' => false,
                    'message' => 'Ingrediente no encontrado.'
                ]);
            }

            $relationSql = "SELECT id_pastel_ingrediente FROM pastel_ingrediente WHERE id_pastel = :id_pastel 
                            AND id_ingrediente = :id_ingrediente";
            $relationStmt = $this->connection->prepare($relationSql);
            $relationStmt->execute([
                ':id_pastel' => $idPastel,
                ':id_ingrediente' => $idIngrediente
            ]);

            if ($relationStmt->fetch()) {
                jsonResponse(409, [
                    'success' => false,
                    'message' => 'El ingrediente ya está asociado al pastel.'
                ]);
            }

            $sql = "INSERT INTO pastel_ingrediente (id_pastel, id_ingrediente) 
                    VALUES (:id_pastel, :id_ingrediente)";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':id_pastel' => $idPastel,
                ':id_ingrediente' => $idIngrediente
            ]);

            jsonResponse(201, [
                'success' => true,
                'message' => 'Ingrediente asociado al pastel exitosamente.'
            ]);
        } catch (PDOException $e) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Error al asignar ingrediente al pastel.'
            ]);
        }
    }

    public function indexByPastel(int $idPastel): void
    {
        try {
            $pastelSql = "SELECT id_pastel, nombre FROM pastel WHERE id_pastel = :id";
            $pastelStmt = $this->connection->prepare($pastelSql);
            $pastelStmt->execute([':id' => $idPastel]);
            $pastel = $pastelStmt->fetch();

            if (!$pastel) {
                jsonResponse(404, [
                    'success' => false,
                    'message' => 'Pastel no encontrado.'
                ]);
            }   

            $sql = "SELECT 
                    pi.id_pastel_ingrediente,
                    i.id_ingrediente,
                    i.nombre,
                    i.descripcion,
                    i.fecha_ingreso,
                    i.fecha_vencimiento,
                    pi.created_at
                FROM pastel_ingrediente pi
                INNER JOIN ingrediente i 
                    ON pi.id_ingrediente = i.id_ingrediente
                WHERE pi.id_pastel = :id_pastel
                ORDER BY pi.id_pastel_ingrediente DESC";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':id_pastel' => $idPastel]);
            $ingredientes = $stmt->fetchAll();

            jsonResponse(200, [
                'success' => true,
                'data' => $ingredientes
            ]);
        } catch (PDOException $e) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Error al obtener los ingredientes del pastel.'
            ]);
        }
    }

    public function destroy(int $idPastel, int $idIngrediente): void
    {
        try {
            $sql = "SELECT id_pastel_ingrediente
                    FROM pastel_ingrediente
                    WHERE id_pastel = :id_pastel
                    AND id_ingrediente = :id_ingrediente
                    LIMIT 1";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':id_pastel' => $idPastel,
                ':id_ingrediente' => $idIngrediente
            ]);

            $relation = $stmt->fetch();

            if (!$relation) {
                jsonResponse(404, [
                    'success' => false,
                    'message' => 'La relación entre pastel e ingrediente no existe.'
                ]);
            }

            $deleteSql = "DELETE FROM pastel_ingrediente
                        WHERE id_pastel = :id_pastel
                            AND id_ingrediente = :id_ingrediente";

            $deleteStmt = $this->connection->prepare($deleteSql);
            $deleteStmt->execute([
                ':id_pastel' => $idPastel,
                ':id_ingrediente' => $idIngrediente
            ]);

            jsonResponse(200, [
                'success' => true,
                'message' => 'Ingrediente desasociado del pastel correctamente.'
            ]);
        } catch (PDOException $e) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Error al desasociar el ingrediente del pastel.'
            ]);
        }
    }
}