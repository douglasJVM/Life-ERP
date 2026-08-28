<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;
use Exception;

class TaskController {
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

            // LISTAGEM DO KANBAN
            if ($method === 'GET') {
                $stmt = $db->prepare("
                    SELECT id, titulo, descricao, status, prioridade, prazo, xp_recompensa 
                    FROM tasks 
                    WHERE user_id = :uid 
                    ORDER BY id DESC
                ");
                $stmt->execute([':uid' => $userId]);
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "status" => "success",
                    "tarefas" => $tasks ?: [],
                    "tasks" => $tasks ?: []
                ]);
                return;
            }

            // CRIAÇÃO / ATUALIZAÇÃO / DELETE
            if ($method === 'POST') {
                $action = $input['action'] ?? 'create';

                if ($action === 'create') {
                    $titulo = trim($input['titulo'] ?? $input['title'] ?? '');
                    $descricao = trim($input['descricao'] ?? $input['description'] ?? '');
                    $status = $input['status'] ?? 'todo';
                    $prioridade = $input['prioridade'] ?? 'media';
                    $prazo = !empty($input['prazo']) ? $input['prazo'] : null;

                    if (empty($titulo)) {
                        http_response_code(400);
                        echo json_encode(["status" => "error", "message" => "O título da tarefa é obrigatório."]);
                        return;
                    }

                    $stmt = $db->prepare("
                        INSERT INTO tasks (user_id, titulo, descricao, status, prioridade, prazo, xp_recompensa) 
                        VALUES (:u, :t, :d, :s, :p, :pr, 15)
                    ");
                    $stmt->execute([
                        ':u' => $userId,
                        ':t' => $titulo,
                        ':d' => $descricao,
                        ':s' => $status,
                        ':p' => $prioridade,
                        ':pr' => $prazo
                    ]);

                    echo json_encode(["status" => "success", "message" => "Tarefa criada com sucesso!"]);
                    return;
                }

                // Atualizar Status (Arrastar no Kanban)
                if ($action === 'update_status' || $action === 'move') {
                    $taskId = (int)($input['task_id'] ?? $input['id'] ?? 0);
                    $newStatus = $input['status'] ?? 'todo';

                    $stmt = $db->prepare("UPDATE tasks SET status = :s WHERE id = :id AND user_id = :u");
                    $stmt->execute([':s' => $newStatus, ':id' => $taskId, ':u' => $userId]);

                    // Se foi concluída (done), concede XP
                    if ($newStatus === 'done') {
                        $db->prepare("UPDATE users SET xp_total = xp_total + 15 WHERE id = :u")->execute([':u' => $userId]);
                    }

                    echo json_encode(["status" => "success", "message" => "Status atualizado!"]);
                    return;
                }

                // Deletar Tarefa
                if ($action === 'delete') {
                    $taskId = (int)($input['task_id'] ?? $input['id'] ?? 0);
                    $stmt = $db->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :u");
                    $stmt->execute([':id' => $taskId, ':u' => $userId]);

                    echo json_encode(["status" => "success", "message" => "Tarefa removida com sucesso!"]);
                    return;
                }
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Erro PHP Kanban: " . $e->getMessage()]);
        }
    }
}