<?php
namespace App\Models;

use PDO;
use PDOException;

class Study {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function logSession(int $userId, string $materia, ?string $conteudo, int $duracao, int $xp, string $data): array {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO study_sessions (user_id, materia, conteudo, duracao_minutos, xp_ganho, data_estudo) 
                VALUES (:u, :m, :c, :d, :xp, :dt)
            ");
            $stmt->execute([
                ':u' => $userId,
                ':m' => $materia,
                ':c' => $conteudo,
                ':d' => $duracao,
                ':xp' => $xp,
                ':dt' => $data
            ]);
            return ["success" => true];
        } catch (PDOException $e) {
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    public function deleteSession(int $userId, int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM study_sessions WHERE id = :id AND user_id = :u");
        return $stmt->execute([':id' => $id, ':u' => $userId]);
    }

    public function getWeeklyStats(int $userId): array {
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_sessoes,
                COALESCE(SUM(duracao_minutos), 0) as minutos_totais,
                COALESCE(SUM(xp_ganho), 0) as xp_acumulado
            FROM study_sessions 
            WHERE user_id = :u AND data_estudo >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
        ");
        $stmt->execute([':u' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_sessoes' => 0, 'minutos_totais' => 0, 'xp_acumulado' => 0];
    }

    public function getHoursBySubject(int $userId): array {
        $stmt = $this->conn->prepare("
            SELECT materia, ROUND(SUM(duracao_minutos) / 60, 1) as horas_totais 
            FROM study_sessions 
            WHERE user_id = :u 
            GROUP BY materia
            ORDER BY horas_totais DESC
        ");
        $stmt->execute([':u' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAll(int $userId, int $limit = 20): array {
        $stmt = $this->conn->prepare("
            SELECT id, materia, conteudo, duracao_minutos, xp_ganho, DATE_FORMAT(data_estudo, '%d/%m/%Y') as data_formatada 
            FROM study_sessions 
            WHERE user_id = :u 
            ORDER BY data_estudo DESC, id DESC 
            LIMIT :lim
        ");
        $stmt->bindValue(':u', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}