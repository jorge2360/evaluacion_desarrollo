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
            $ingredienteStmt->execute(['id' => $idIngrediente]);
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
}