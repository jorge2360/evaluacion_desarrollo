<?php

class Database
{
    private string $host = '127.0.0.1';
    private string $db_name = 'examen_pasteles';
    private string $username = 'root';
    private string $password = 'Ubuntu1.2';
    private string $charset = 'utf8mb4';

    public function connect(): PDO
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $pdo = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            return $pdo;
        } catch (PDOException $e) {
            http_response_code(500);
            die('Error de conexion a la base de datos: ' . $e->getMessage());
        }
    }
}