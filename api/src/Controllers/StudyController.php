<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Study;
use App\Models\User;
use PDO;
use Exception;

class StudyController {
    public function index(): void {
        try {
            $db = (new Database())->getConnection();
            $headers = getallheaders();
            $userId = (int)($headers['X-User-Id'] ?? $headers['x-user-id'] ?? $_GET['user_id'] ?? 1);
            $method = $_SERVER['REQUEST_METHOD'];

            $studyModel = new Study($db);
            $userModel = new User($db);

            if ($method === 'GET') {
                $stats = $studyModel->getWeeklyStats($userId);
                $historico = $studyModel->getAll($userId, 50);

                echo json_encode([
                    "status" => "success",
                    "estudos" => [
                        "sessoes_semana" => (int)($stats['total_sessoes'] ?? 0),
                        "horas_totais" => round(((int)($stats['minutos_totais'] ?? 0)) / 60, 1),
                        "xp_semana" => (int)($stats['xp_total'] ?? 0),
                        "historico" => $historico ?: []
                    ]
                ]);
                return;
            }

            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true) ?? [];
                $action = $input['action'] ?? 'create';

                if ($action === 'create') {
                    $materia = trim($input['materia'] ?? '');
                    $conteudo = trim($input['conteudo'] ?? '');
                    $duracao = (int)($input['duracao_minutos'] ?? 0);
                    $dataEstudo = !empty($input['data_estudo']) ? $input['data_estudo'] : date('Y-m-d');

                    if (empty($materia) || $duracao <= 0) {
                        http_response_code(400);
                        echo json_encode(["status" => "error", "message" => "Matéria e duração são obrigatórias."]);
                        return;
                    }

                    // Cálculo do XP: ~1.15 XP por minuto de estudo focado
                    $xpGanho = (int) ceil($duracao * 1.15);

                    $stmt = $db->prepare("
                        INSERT INTO study_sessions (user_id, materia, conteudo, duracao_minutos, xp_ganho, data_estudo)
                        VALUES (:uid, :mat, :cont, :dur, :xp, :dt)
                    ");
                    $stmt->execute([
                        ':uid' => $userId,
                        ':mat' => $materia,
                        ':cont' => $conteudo,
                        ':dur' => $duracao,
                        ':xp' => $xpGanho,
                        ':dt' => $dataEstudo
                    ]);

                    // Adiciona o XP ao usuário
                    $userModel->addXp($userId, $xpGanho);

                    echo json_encode(["status" => "success", "message" => "Sessão registrada com sucesso!", "xp_ganho" => $xpGanho]);
                    return;
                }

                if ($action === 'delete') {
                    $id = (int)($input['id'] ?? 0);
                    $stmt = $db->prepare("DELETE FROM study_sessions WHERE id = :id AND user_id = :uid");
                    $stmt->execute([':id' => $id, ':uid' => $userId]);

                    echo json_encode(["status" => "success", "message" => "Sessão removida com sucesso!"]);
                    return;
                }
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Erro PHP Estudos: " . $e->getMessage()]);
        }
    }
}