<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;
use Exception;

class HabitTrackerController {
    public function index(): void {
        try {
            $db = (new Database())->getConnection();
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true) ?? [];

            // Captura segura do ID do usuário
            $userId = (int)(
                $_SERVER['HTTP_X_USER_ID'] ?? 
                $_GET['user_id'] ?? 
                $input['user_id'] ?? 
                (function_exists('getallheaders') ? (getallheaders()['X-User-Id'] ?? getallheaders()['x-user-id'] ?? null) : null) ?? 
                1
            );

            $method = $_SERVER['REQUEST_METHOD'];
            $today = date('Y-m-d');

            // LISTAGEM DE HÁBITOS
            if ($method === 'GET') {
                $sql = "
                    SELECT 
                        h.id, 
                        h.nome AS titulo, 
                        h.frequencia AS categoria, 
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
                    WHERE h.user_id = :uid AND h.status = 'ativo'
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

            // CRIAÇÃO / TOGGLE / DELETE
            if ($method === 'POST') {
                $action = $input['action'] ?? 'create';

                if ($action === 'create') {
                    $nome = trim($input['titulo'] ?? $input['nome'] ?? '');
                    $frequencia = trim($input['frequencia'] ?? $input['categoria'] ?? 'diario');

                    if (empty($nome)) {
                        http_response_code(400);
                        echo json_encode(["status" => "error", "message" => "O título/nome do hábito é obrigatório."]);
                        return;
                    }

                    $stmt = $db->prepare("INSERT INTO habits (user_id, nome, frequencia, xp_recompensa, status) VALUES (:u, :n, :f, 10, 'ativo')");
                    $stmt->execute([
                        ':u' => $userId, 
                        ':n' => $nome, 
                        ':f' => ($frequencia === 'semanal' ? 'semanal' : 'diario')
                    ]);

                    echo json_encode(["status" => "success", "message" => "Hábito criado com sucesso!"]);
                    return;
                }

                if ($action === 'toggle') {
                    $habitId = (int)($input['habit_id'] ?? 0);
                    $check = $db->prepare("SELECT id FROM habit_logs WHERE habit_id = :h AND data_conclusao = :d");
                    $check->execute([':h' => $habitId, ':d' => $today]);
                    $log = $check->fetch(PDO::FETCH_ASSOC);

                    if ($log) {
                        // Desmarcar -> Remove o log e subtrai 10 XP
                        $db->prepare("DELETE FROM habit_logs WHERE id = :id")->execute([':id' => $log['id']]);
                        $db->prepare("UPDATE users SET xp_total = GREATEST(0, xp_total - 10) WHERE id = :u")->execute([':u' => $userId]);
                        echo json_encode(["status" => "success", "checked" => false]);
                    } else {
                        // Marcar -> Cria log e adiciona 10 XP
                        $db->prepare("INSERT INTO habit_logs (habit_id, user_id, data_conclusao) VALUES (:h, :u, :d)")
                           ->execute([':h' => $habitId, ':u' => $userId, ':d' => $today]);
                        $db->prepare("UPDATE users SET xp_total = xp_total + 10 WHERE id = :u")->execute([':u' => $userId]);
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