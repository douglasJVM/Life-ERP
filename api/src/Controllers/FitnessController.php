<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Habit;
use App\Models\User;
use Exception;

class FitnessController {
    private int $userId;

    public function __construct(int $userId = 1) {
        $headers = getallheaders();
        $this->userId = (int)($headers['X-User-Id'] ?? $headers['x-user-id'] ?? $_GET['user_id'] ?? $userId);
    }

    public function index(): void {
        try {
            $db = (new Database())->getConnection();
            $habitModel = new Habit($db);
            $userModel = new User($db);
            $method = $_SERVER['REQUEST_METHOD'];

            if ($method === 'POST') {
                $raw = file_get_contents('php://input');
                $input = json_decode($raw, true) ?? [];

                // Ação: Exclusão de Treino
                if (($input['action'] ?? '') === 'delete') {
                    $id = (int)($input['id'] ?? 0);
                    if ($id > 0 && $habitModel->deleteWorkout($this->userId, $id)) {
                        echo json_encode(["status" => "success", "message" => "Treino removido com sucesso!"]);
                    } else {
                        http_response_code(400);
                        echo json_encode(["status" => "error", "message" => "Falha ao remover treino."]);
                    }
                    return;
                }

                // Ação: Cadastro de Treino
                $tipo = trim($input['tipo'] ?? '');
                $duracao = (int)($input['duracao_minutos'] ?? 45);
                $intensidade = trim($input['intensidade'] ?? 'moderada');
                $data = !empty($input['data_treino']) ? $input['data_treino'] : date('Y-m-d');

                if (empty($tipo)) {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => "Selecione o grupamento/tipo de treino."]);
                    return;
                }

                // Cálculo de XP com multiplicador de intensidade
                $mult = $intensidade === 'intensa' ? 1.4 : ($intensidade === 'leve' ? 0.8 : 1.0);
                $xpGanho = (int) ceil(($duracao * 1.25) * $mult);

                // Grava o treino no banco
                $result = $habitModel->logWorkout($this->userId, $tipo, $duracao, $intensidade, $xpGanho, $data);

                if (!empty($result['success'])) {
                    // Bonifica o jogador com o XP
                    $userModel->addXp($this->userId, $xpGanho);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Treino salvo com sucesso! +{$xpGanho} XP adicionados.",
                        "xp_ganho" => $xpGanho
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(["status" => "error", "message" => $result['error'] ?? "Erro ao salvar no banco."]);
                }
                return;
            }

            // Ação: Leitura GET
            $stats = $habitModel->getWeeklyStats($this->userId);
            $grupos = method_exists($habitModel, 'getWorkoutsByGroup') ? $habitModel->getWorkoutsByGroup($this->userId) : [];
            $historico = $habitModel->getAll($this->userId);

            echo json_encode([
                "status" => "success",
                "fitness" => [
                    "treinos_ultimos_7_dias" => (int)($stats['total_treinos'] ?? 0),
                    "minutos_semana" => (int)($stats['minutos_totais'] ?? 0),
                    "xp_semana" => (int)($stats['xp_acumulado'] ?? $stats['xp_total'] ?? 0),
                    "grupos" => $grupos ?: [],
                    "historico" => $historico ?: []
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Erro PHP Fitness: " . $e->getMessage()
            ]);
        }
    }
}