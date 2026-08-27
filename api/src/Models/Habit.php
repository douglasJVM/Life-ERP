<?php
namespace App\Models;

use PDO;
use PDOException;

class Habit {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function logWorkout(int $userId, string $tipo, int $duracao, string $intensidade, int $xp, string $data): array {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO workouts (user_id, tipo, duracao_minutos, intensidade, xp_ganho, data_treino) 
                VALUES (:u, :t, :d, :i, :xp, :dt)
            ");
            $stmt->execute([
                ':u' => $userId,
                ':t' => $tipo,
                ':d' => $duracao,
                ':i' => $intensidade,
                ':xp' => $xp,
                ':dt' => $data
            ]);
            return ["success" => true];
        } catch (PDOException $e) {
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    public function deleteWorkout(int $userId, int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM workouts WHERE id = :id AND user_id = :u");
        return $stmt->execute([':id' => $id, ':u' => $userId]);
    }

    public function getWeeklyStats(int $userId): array {
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_treinos,
                COALESCE(SUM(duracao_minutos), 0) as minutos_totais,
                COALESCE(SUM(xp_ganho), 0) as xp_acumulado
            FROM workouts 
            WHERE user_id = :u AND data_treino >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
        ");
        $stmt->execute([':u' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_treinos' => 0, 'minutos_totais' => 0, 'xp_acumulado' => 0];
    }

    public function getWorkoutsByGroup(int $userId): array {
        $stmt = $this->conn->prepare("
            SELECT tipo as grupo, COUNT(*) as total 
            FROM workouts 
            WHERE user_id = :u 
            GROUP BY tipo
            ORDER BY total DESC
        ");
        $stmt->execute([':u' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAll(int $userId, int $limit = 30): array {
        $stmt = $this->conn->prepare("
            SELECT id, tipo, duracao_minutos, intensidade, xp_ganho, DATE_FORMAT(data_treino, '%d/%m/%Y') as data_formatada 
            FROM workouts 
            WHERE user_id = :u 
            ORDER BY data_treino DESC, id DESC 
            LIMIT :lim
        ");
        $stmt->bindValue(':u', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}