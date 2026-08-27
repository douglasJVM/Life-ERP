<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\User;
use App\Models\Finance;
use App\Models\Habit;
use App\Models\Study;
use PDO;
use Exception;

class DashboardController {
    public function index(): void {
        try {
            $db = (new Database())->getConnection();

            // Pega o ID enviado pelo header da requisição ou parâmetro GET
            $headers = getallheaders();
            $userId = isset($headers['X-User-Id']) ? (int)$headers['X-User-Id'] : (int)($_GET['user_id'] ?? 0);

            if ($userId <= 0) {
                // Fallback para o primeiro se não for passado
                $stmt = $db->query("SELECT id FROM users ORDER BY id ASC LIMIT 1");
                $userId = (int)($stmt->fetchColumn() ?: 1);
            }

            // Busca o usuário específico do login
            $userModel = new User($db);
            $user = $userModel->findById($userId);

            if (!$user) {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Usuário não encontrado."]);
                return;
            }

            $xpTotal = (int)($user['xp_total'] ?? 0);
            $nivel = (int) floor($xpTotal / 100) + 1;
            $xpProgresso = $xpTotal % 100;
            $xpFaltante = 100 - $xpProgresso;

            $financeModel = new Finance($db);
            $habitModel = new Habit($db);
            $studyModel = new Study($db);

            $financas = $financeModel->getMonthlySummary($userId);
            $categorias = $financeModel->getExpensesByCategory($userId);
            $fitnessStats = $habitModel->getWeeklyStats($userId);
            $studyStats = $studyModel->getWeeklyStats($userId);

            $transacoes = $financeModel->getAll($userId, 2);
            $treinos = $habitModel->getAll($userId, 2);
            $estudos = $studyModel->getAll($userId, 2);

            $feed = [];
            foreach ($transacoes as $t) {
                $feed[] = [
                    'tipo' => 'finance',
                    'titulo' => $t['descricao'],
                    'subtitulo' => ($t['tipo'] === 'receita' ? '+ ' : '- ') . 'R$ ' . number_format($t['valor'], 2, ',', '.'),
                    'data' => $t['data_formatada'],
                    'badge' => $t['categoria'],
                    'color' => $t['tipo'] === 'receita' ? 'emerald' : 'rose'
                ];
            }
            foreach ($treinos as $w) {
                $feed[] = [
                    'tipo' => 'fitness',
                    'titulo' => 'Treino: ' . $w['tipo'],
                    'subtitulo' => $w['duracao_minutos'] . ' min (' . $w['intensidade'] . ')',
                    'data' => $w['data_formatada'],
                    'badge' => '+' . $w['xp_ganho'] . ' XP',
                    'color' => 'cyan'
                ];
            }
            foreach ($estudos as $s) {
                $feed[] = [
                    'tipo' => 'study',
                    'titulo' => 'Estudo: ' . $s['materia'],
                    'subtitulo' => $s['duracao_minutos'] . ' min (' . ($s['conteudo'] ?: 'Geral') . ')',
                    'data' => $s['data_formatada'],
                    'badge' => '+' . $s['xp_ganho'] . ' XP',
                    'color' => 'purple'
                ];
            }

            echo json_encode([
                "status" => "success",
                "dashboard" => [
                    "user" => [
                        "id" => (int)$user['id'],
                        "nome" => $user['nome'],
                        "email" => $user['email'] ?? '',
                        "nivel" => $nivel,
                        "xp_total" => $xpTotal,
                        "xp_progresso" => $xpProgresso,
                        "xp_para_proximo_nivel" => $xpFaltante
                    ],
                    "financas" => [
                        "saldo_atual" => (float)($financas['saldo_liquido'] ?? 0),
                        "total_receitas" => (float)($financas['total_receitas'] ?? 0),
                        "total_despesas" => (float)($financas['total_despesas'] ?? 0),
                        "despesas_categoria" => $categorias ?: []
                    ],
                    "fitness" => [
                        "treinos_semana" => (int)($fitnessStats['total_treinos'] ?? 0),
                        "minutos_semana" => (int)($fitnessStats['minutos_totais'] ?? 0)
                    ],
                    "estudos" => [
                        "sessoes_semana" => (int)($studyStats['total_sessoes'] ?? 0),
                        "horas_semana" => round(($studyStats['minutos_totais'] ?? 0) / 60, 1)
                    ],
                    "feed" => $feed
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Erro PHP Dashboard: " . $e->getMessage()]);
        }
    }
}