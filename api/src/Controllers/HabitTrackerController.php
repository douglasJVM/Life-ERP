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

            $userId = (int)(
                $_SERVER['HTTP_X_USER_ID'] ?? 
                $_GET['user_id'] ?? 
                $input['user_id'] ?? 
                (function_exists('getallheaders') ? (getallheaders()['X-User-Id'] ?? getallheaders()['x-user-id'] ?? null) : null) ?? 
                1
            );

            $method = $_SERVER['REQUEST_METHOD'];
            $today = date('Y-m-d');

            // Detecta dinamicamente quais colunas existem na tabela habits
            $stmtCols = $db->query("SHOW COLUMNS FROM habits");
            $columns = $stmtCols->fetchAll(PDO::FETCH_COLUMN);

            $titleField = in_array('titulo', $columns) ? 'titulo' : 'nome';
            $categoryField = in_array('categoria', $columns) ? 'categoria' : (in_array('frequencia', $columns) ? 'frequencia' : "'Rotina'");
            $hasReward = in_array('xp_recompensa', $columns);
            $hasStatus = in_array('status', $columns);

            // LISTAGEM DE HÁBITOS
            if ($method === 'GET') {
                $rewardSelect = $hasReward ? "h.xp_recompensa," : "10 AS xp_recompensa,";
                $statusWhere = $hasStatus ? "AND (h.status = 'ativo' OR h.status IS NULL)" : "";

                $sql = "
                    SELECT 
                        h.id, 
                        h.{$titleField} AS titulo, 
                        h.{$categoryField} AS categoria, 
                        {$rewardSelect}
                        CASE WHEN hl.id IS NOT NULL THEN 1 ELSE 0 END AS concluido_hoje,
                        COALESCE(stats.total, 0) AS total_conclusoes
                    FROM habits h
                    LEFT JOIN habit_logs hl ON h.id = hl.habit_id AND hl.data_conclusao = :today
                    LEFT JOIN (
                        SELECT habit_id, COUNT(DISTINCT data_conclusao) AS total 
                        FROM habit_logs 
                        GROUP BY habit_id
                    ) stats ON h.id = stats.habit_id
                    WHERE h.user_id = :uid {$statusWhere}
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
                    $tituloVal = trim($input['titulo'] ?? $input['nome'] ?? '');
                    $categoriaVal = trim($input['categoria'] ?? $input['frequencia'] ?? 'Rotina');

                    if (empty($tituloVal)) {
                        http_response_code(400);
                        echo json_encode(["status" => "error", "message" => "O título do hábito é obrigatório."]);
                        return;
                    }

                    $insertCols = ['user_id', $titleField];
                    $insertVals = [':u', ':t'];
                    $params = [':u' => $userId, ':t' => $tituloVal];

                    if ($categoryField !== "'Rotina'") {
                        $insertCols[] = $categoryField;
                        $insertVals[] = ':c';
                        $params[':c'] = ($categoryField === 'frequencia' && $categoriaVal === 'semanal') ? 'semanal' : ($categoryField === 'frequencia' ? 'diario' : $categoriaVal);
                    }

                    if ($hasReward) {
                        $insertCols[] = 'xp_recompensa';
                        $insertVals[] = '10';
                    }

                    if ($hasStatus) {
                        $insertCols[] = 'status';
                        $insertVals[] = "'ativo'";
                    }

                    $sqlInsert = "INSERT INTO habits (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', $insertVals) . ")";
                    $stmt = $db->prepare($sqlInsert);
                    $stmt->execute($params);

                    echo json_encode(["status" => "success", "message" => "Hábito criado com sucesso!"]);
                    return;
                }

                if ($action === 'toggle') {
                    $habitId = (int)($input['habit_id'] ?? 0);
                    $check = $db->prepare("SELECT id FROM habit_logs WHERE habit_id = :h AND data_conclusao = :d");
                    $check->execute([':h' => $habitId, ':d' => $today]);
                    $log = $check->fetch(PDO::FETCH_ASSOC);

                    if ($log) {
                        $db->prepare("DELETE FROM habit_logs WHERE id = :id")->execute([':id' => $log['id']]);
                        $db->prepare("UPDATE users SET xp_total = GREATEST(0, xp_total - 10) WHERE id = :u")->execute([':u' => $userId]);
                        echo json_encode(["status" => "success", "checked" => false]);
                    } else {
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