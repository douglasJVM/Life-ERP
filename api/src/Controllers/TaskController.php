<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;
use Exception;

class TaskController {
    public function index(): void {
        try {
            $db = (new Database())->getConnection();
            $headers = getallheaders();
            $userId = (int)($headers['X-User-Id'] ?? $_GET['user_id'] ?? 1);
            $method = $_SERVER['REQUEST_METHOD'];

            if ($method === 'GET') {
                $stmt = $db->prepare("SELECT * FROM tasks WHERE user_id = :uid ORDER BY id DESC");
                $stmt->execute([':uid' => $userId]);
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode(["status" => "success", "tasks" => $tasks]);
                return;
            }

            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true) ?? [];
                $action = $input['action'] ?? 'create';

                if ($action === 'create') {
                    $titulo = trim($input['titulo'] ?? '');
                    $descricao = trim($input['descricao'] ?? '');
                    $prioridade = $input['prioridade'] ?? 'media';
                    $prazo = !empty($input['prazo']) ? $input['prazo'] : null;

                    if (empty($titulo)) {
                        http_response_code(400);
                        echo json_encode(["status" => "error", "message" => "Título é obrigatório."]);
                        return;
                    }

                    $stmt = $db->prepare("INSERT INTO tasks (user_id, titulo, descricao, prioridade, prazo, status) VALUES (:u, :t, :d, :p, :pr, 'todo')");
                    $stmt->execute([':u' => $userId, ':t' => $titulo, ':d' => $descricao, ':p' => $prioridade, ':pr' => $prazo]);
                    echo json_encode(["status" => "success", "message" => "Tarefa criada!"]);
                    return;
                }

                // Atualizar Status (Arrastar ou Mudar coluna: todo -> doing -> done)
                if ($action === 'update_status') {
                    $taskId = (int)($input['task_id'] ?? 0);
                    $newStatus = $input['status'] ?? 'todo';

                    // Se mudou para "done", bonifica com XP
                    $userModel = new \App\Models\User($db);

if ($newStatus === 'done') {
    $userModel->addXp($userId, 15); // +15 XP por tarefa concluída
}

                    $stmt = $db->prepare("UPDATE tasks SET status = :s WHERE id = :id AND user_id = :u");
                    $stmt->execute([':s' => $newStatus, ':id' => $taskId, ':u' => $userId]);
                    echo json_encode(["status" => "success"]);
                    return;
                }

                if ($action === 'delete') {
                    $taskId = (int)($input['task_id'] ?? 0);
                    $db->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :u")->execute([':id' => $taskId, ':u' => $userId]);
                    echo json_encode(["status" => "success", "message" => "Tarefa removida!"]);
                    return;
                }
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}