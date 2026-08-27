<?php
namespace App\Models;

use PDO;

class User {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, nome, email, nivel, xp_total FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // Adiciona ou remove XP e recalcula o nível (100 XP por nível)
    public function addXp(int $userId, int $xpAmount): array {
        // Busca XP atual
        $stmt = $this->db->prepare("SELECT xp_total FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $currentXp = (int)($stmt->fetchColumn() ?: 0);

        $newXp = max(0, $currentXp + $xpAmount);
        $newLevel = (int) floor($newXp / 100) + 1;

        // Atualiza no banco
        $update = $this->db->prepare("UPDATE users SET xp_total = :xp, nivel = :lvl WHERE id = :id");
        $update->execute([
            ':xp' => $newXp,
            ':lvl' => $newLevel,
            ':id' => $userId
        ]);

        return [
            'xp_total' => $newXp,
            'nivel' => $newLevel,
            'xp_progresso' => $newXp % 100,
            'xp_para_proximo_nivel' => 100 - ($newXp % 100)
        ];
    }
}