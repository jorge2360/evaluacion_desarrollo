<?php

require_once __DIR__ . '/../utils/response.php';

class ReporteController
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function pastelesConIngredientes(): void
    {
        try {
            $sql = "SELECT 
                        p.id_pastel,
                        p.nombre AS nombre_pastel,
                        p.descripcion AS descripcion_pastel,
                        p.preparado_por,
                        p.fecha_creacion,
                        p.fecha_vencimiento,
                        p.created_at AS pastel_created_at,
                        p.updated_at AS pastel_updated_at,
                        i.id_ingrediente,
                        i.nombre AS nombre_ingrediente,
                        i.descripcion AS descripcion_ingrediente,
                        i.fecha_ingreso,
                        i.fecha_vencimiento AS ingrediente_fecha_vencimiento,
                        pi.id_pastel_ingrediente,
                        pi.created_at AS relacion_created_at
                    FROM pastel p
                    LEFT JOIN pastel_ingrediente pi 
                        ON p.id_pastel = pi.id_pastel
                    LEFT JOIN ingrediente i 
                        ON pi.id_ingrediente = i.id_ingrediente
                    ORDER BY p.id_pastel DESC, pi.id_pastel_ingrediente DESC";

            $stmt = $this->connection->query($sql);
            $rows = $stmt->fetchAll();

            $resultado = [];

            foreach ($rows as $row) {
                $idPastel = $row['id_pastel'];

                if (!isset($resultado[$idPastel])) {
                    $resultado[$idPastel] = [
                        'id_pastel' => $row['id_pastel'],
                        'nombre' => $row['nombre_pastel'],
                        'descripcion' => $row['descripcion_pastel'],
                        'preparado_por' => $row['preparado_por'],
                        'fecha_creacion' => $row['fecha_creacion'],
                        'fecha_vencimiento' => $row['fecha_vencimiento'],
                        'created_at' => $row['pastel_created_at'],
                        'updated_at' => $row['pastel_updated_at'],
                        'ingredientes' => []
                    ];
                }

                if (!empty($row['id_ingrediente'])) {
                    $resultado[$idPastel]['ingredientes'][] = [
                        'id_pastel_ingrediente' => $row['id_pastel_ingrediente'],
                        'id_ingrediente' => $row['id_ingrediente'],
                        'nombre' => $row['nombre_ingrediente'],
                        'descripcion' => $row['descripcion_ingrediente'],
                        'fecha_ingreso' => $row['fecha_ingreso'],
                        'fecha_vencimiento' => $row['ingrediente_fecha_vencimiento'],
                        'created_at' => $row['relacion_created_at']
                    ];
                }
            }

            jsonResponse(200, [
                'success' => true,
                'data' => array_values($resultado)
            ]);
        } catch (PDOException $e) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Error al generar el reporte de pasteles con ingredientes.'
            ]);
        }
    }
}