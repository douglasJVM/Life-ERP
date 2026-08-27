<?php
namespace App\Models;

use PDO;
use PDOException;

class Transaction {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function create(int $userId, string $descricao, float $valor, string $tipo, string $categoria, string $metodo, string $data): array {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO transactions (user_id, descricao, valor, tipo, categoria, metodo_pagamento, data_transacao) 
                VALUES (:u, :d, :v, :t, :c, :m, :dt)
            ");
            $stmt->execute([
                ':u' => $userId,
                ':d' => $descricao,
                ':v' => $valor,
                ':t' => $tipo,
                ':c' => $categoria,
                ':m' => $metodo,
                ':dt' => $data
            ]);
            return ["success" => true];
        } catch (PDOException $e) {
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    public function getAll(int $userId, int $limit = 20): array {
        $stmt = $this->conn->prepare("
            SELECT id, descricao, valor, tipo, categoria, metodo_pagamento, DATE_FORMAT(data_transacao, '%d/%m/%Y') as data_formatada 
            FROM transactions 
            WHERE user_id = :u 
            ORDER BY data_transacao DESC, id DESC 
            LIMIT :lim
        ");
        $stmt->bindValue(':u', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function delete(int $userId, int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM transactions WHERE id = :id AND user_id = :u");
        return $stmt->execute([':id' => $id, ':u' => $userId]);
    }

    public function getMetrics(int $userId): array {
        $stmt = $this->conn->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END), 0) as total_receitas,
                COALESCE(SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END), 0) as total_despesas
            FROM transactions 
            WHERE user_id = :u
        ");
        $stmt->execute([':u' => $userId]);
        $row = $stmt->fetch() ?: ['total_receitas' => 0, 'total_despesas' => 0];

        $receitas = (float)$row['total_receitas'];
        $despesas = (float)$row['total_despesas'];

        return [
            'total_receitas' => $receitas,
            'total_despesas' => $despesas,
            'saldo_liquido' => $receitas - $despesas
        ];
    }

    public function getExpensesByCategory(int $userId): array {
        $stmt = $this->conn->prepare("
            SELECT categoria, SUM(valor) as total 
            FROM transactions 
            WHERE user_id = :u AND tipo = 'despesa' 
            GROUP BY categoria
        ");
        $stmt->execute([':u' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}