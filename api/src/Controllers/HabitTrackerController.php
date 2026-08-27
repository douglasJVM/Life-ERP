<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;
use Exception;

class HabitTrackerController {
    public function index(): void {
        try {
            $db = (new Database())->getConnection();
            $headers = getallheaders();
            $userId = (int)($headers['X-User-Id'] ?? $headers['x-user-id'] ?? $_GET['user_id'] ?? 1);
            $method = $_SERVER['REQUEST_METHOD'];
            $today = date('Y-m-d');

            if ($method === 'GET') {
                $sql = "
                    SELECT 
                        h.id, 
                        h.titulo, 
                        h.categoria, 
                        h.xp_recompensa,
                        CASE WHEN hl.id IS NOT NULL THEN 1 ELSE 0 END AS concluido_hoje,
                        COALESCE(stats.total, 0) AS total_conclusoes
                    FROM habits h
                    LEFT JOIN habit_logs hl ON h.id = hl.habit_id AND hl.data_conclusao = :today
                    LEFT JOIN (
                        SELECT habit_id, COUNT(DISTINCT data_conclusao) AS total 
                        FROM habit_logs 
                        GROUP BY habit_id
                    ) stats ON h.id = stats.habit_id
                    WHERE h.user_id = :uid
                    ORDER BY h.id DESC
                ";

                $stmt = $db->prepare($sql);
                $stmt->execute([':today' => $today, ':uid' => $userId]);
                $habitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "status" => "success", 
                    "habitos" => $habitos ?: []
                ]);
                return;
            }

            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true) ?? [];
                $action = $input['action'] ?? 'create';

                if ($action === 'create') {
                    $titulo = trim($input['titulo'] ?? '');
                    $categoria = trim($input['categoria'] ?? 'Rotina');

                    if (empty($titulo)) {
                        http_response_code(400);
                        echo json_encode(["status" => "error", "message" => "O título é obrigatório."]);
                        return;
                    }

                    $stmt = $db->prepare("INSERT INTO habits (user_id, titulo, categoria, xp_recompensa) VALUES (:u, :t, :c, 10)");
                    $stmt->execute([':u' => $userId, ':t' => $titulo, ':c' => $categoria]);

                    echo json_encode(["status" => "success", "message" => "Hábito criado com sucesso!"]);
                    return;
                }

                if ($action === 'toggle') {
                    $habitId = (int)($input['habit_id'] ?? 0);
                    $check = $db->prepare("SELECT id FROM habit_logs WHERE habit_id = :h AND data_conclusao = :d");
                    $check->execute([':h' => $habitId, ':d' => $today]);
                    $log = $check->fetch(PDO::FETCH_ASSOC);

                   $userModel = new \App\Models\User($db);

if ($log) {
    // Desmarcou -> remove 10 XP
    $db->prepare("DELETE FROM habit_logs WHERE id = :id")->execute([':id' => $log['id']]);
    $userModel->addXp($userId, -10);
    echo json_encode(["status" => "success", "checked" => false]);
} else {
    // Marcou -> adiciona 10 XP
    $db->prepare("INSERT INTO habit_logs (habit_id, user_id, data_conclusao) VALUES (:h, :u, :d)")
       ->execute([':h' => $habitId, ':u' => $userId, ':d' => $today]);
    $userModel->addXp($userId, 10);
    echo json_encode(["status" => "success", "checked" => true]);
}
                    return;
                }

                if ($action === 'delete') {
                    $habitId = (int)($input['habit_id'] ?? 0);
                    $db->prepare("DELETE FROM habits WHERE id = :id AND user_id = :u")->execute([':id' => $habitId, ':u' => $userId]);
                    echo json_encode(["status" => "success", "message" => "Hábito removido com sucesso!"]);
                    return;
                }
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Erro PHP Habitos: " . $e->getMessage()]);
        }
    }
}