<?php
namespace App\Models;

use PDO;

class Goal {
    private ?PDO $conn;
    private string $table = "goals";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function countActive(int $userId): int {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM {$this->table} WHERE user_id = :user_id AND status = 'pendente'");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public function getAll(int $userId): array {
        $stmt = $this->conn->prepare("SELECT id, titulo, status, created_at FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }
}