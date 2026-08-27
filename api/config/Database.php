<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    private int $port;
    private ?PDO $conn = null;

    public function __construct() {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'defaultdb';
        $this->username = getenv('DB_USER') ?: 'avnadmin';
        $this->password = getenv('DB_PASS') ?: '';
        $this->port = (int)(getenv('DB_PORT') ?: 3306);
    }

    public function getConnection(): ?PDO {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => false
            ]);
        } catch (PDOException $e) {
            error_log("Erro de Conexão: " . $e->getMessage());
            throw new \Exception("Erro ao conectar no banco de dados.");
        }

        return $this->conn;
    }
}