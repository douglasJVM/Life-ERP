<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private string $host = "127.0.0.1";
    private string $db_name = "painel_vida";
    private string $username = "painel_user";
    private string $password = "senha_painel123";

    public function getConnection(): ?PDO {
        try {
            $conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            return $conn;
        } catch (PDOException $exception) {
            http_response_code(500);
            echo json_encode([
                "status" => "error", 
                "message" => "Erro de Conexão com o Banco: " . $exception->getMessage()
            ]);
            exit;
        }
    }
}