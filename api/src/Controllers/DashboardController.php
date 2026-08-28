<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;
use Exception;

class DashboardController {
    public function index(): void {
        try {
            $db = (new Database())->getConnection();

            $userId = (int)(
                $_SERVER['HTTP_X_USER_ID'] ?? 
                $_GET['user_id'] ?? 
                (function_exists('getallheaders') ? (getallheaders()['X-User-Id'] ?? getallheaders()['x-user-id'] ?? null) : null) ?? 
                1
            );

            // 1. FINANÇAS: Saldo, Receitas, Despesas e Distribuição
            $stmtFin = $db->prepare("
                SELECT 
                    COALESCE(SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END), 0) AS total_receitas,
                    COALESCE(SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END), 0) AS total_despesas
                FROM transactions 
                WHERE user_id = :uid
            ");
            $stmtFin->execute([':uid' => $userId]);
            $finRes = $stmtFin->fetch(PDO::FETCH_ASSOC);

            $receitas = (float)($finRes['total_receitas'] ?? 0);
            $despesas = (float)($finRes['total_despesas'] ?? 0);
            $saldoAtual = $receitas - $despesas;

            // Despesas por Categoria para o Gráfico
            $stmtCat = $db->prepare("
                SELECT categoria, SUM(valor) AS total 
                FROM transactions 
                WHERE user_id = :uid AND tipo = 'despesa'
                GROUP BY categoria
            ");
            $stmtCat->execute([':uid' => $userId]);
            $despesasCat = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

            // 2. FITNESS: Treinos nos últimos 7 dias
            $stmtFit = $db->prepare("
                SELECT 
                    COUNT(id) AS treinos_semana,
                    COALESCE(SUM(duracao_minutos), 0) AS minutos_semana
                FROM workouts 
                WHERE user_id = :uid 
                  AND data_treino >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ");
            $stmtFit->execute([':uid' => $userId]);
            $fitRes = $stmtFit->fetch(PDO::FETCH_ASSOC);

            // 3. ESTUDOS: Horas e Sessões nos últimos 7 dias
            $stmtStd = $db->prepare("
                SELECT 
                    COUNT(id) AS sessoes_semana,
                    ROUND(COALESCE(SUM(duracao_minutos), 0) / 60, 1) AS horas_semana
                FROM study_sessions 
                WHERE user_id = :uid 
                  AND data_estudo >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ");
            $stmtStd->execute([':uid' => $userId]);
            $stdRes = $stmtStd->fetch(PDO::FETCH_ASSOC);

            // 4. FEED RECENTE: Últimas atividades unificadas
            $feed = [];

            $stmtRecentTrans = $db->prepare("SELECT descricao AS titulo, CONCAT('R$ ', valor) AS subtitulo, DATE_FORMAT(data_transacao, '%d/%m') AS data, tipo FROM transactions WHERE user_id = :uid ORDER BY id DESC LIMIT 2");
            $stmtRecentTrans->execute([':uid' => $userId]);
            while ($row = $stmtRecentTrans->fetch(PDO::FETCH_ASSOC)) {
                $feed[] = [
                    'titulo' => $row['titulo'],
                    'subtitulo' => $row['subtitulo'],
                    'data' => $row['data'],
                    'badge' => ucfirst($row['tipo']),
                    'color' => $row['tipo'] === 'receita' ? 'emerald' : 'rose'
                ];
            }

            $stmtRecentFit = $db->prepare("SELECT tipo AS titulo, CONCAT(duracao_minutos, ' min • ', intensidade) AS subtitulo, DATE_FORMAT(data_treino, '%d/%m') AS data FROM workouts WHERE user_id = :uid ORDER BY id DESC LIMIT 2");
            $stmtRecentFit->execute([':uid' => $userId]);
            while ($row = $stmtRecentFit->fetch(PDO::FETCH_ASSOC)) {
                $feed[] = [
                    'titulo' => $row['titulo'],
                    'subtitulo' => $row['subtitulo'],
                    'data' => $row['data'],
                    'badge' => 'Treino',
                    'color' => 'cyan'
                ];
            }

            $stmtRecentStd = $db->prepare("SELECT materia AS titulo, CONCAT(duracao_minutos, ' min • ', COALESCE(conteudo, '')) AS subtitulo, DATE_FORMAT(data_estudo, '%d/%m') AS data FROM study_sessions WHERE user_id = :uid ORDER BY id DESC LIMIT 2");
            $stmtRecentStd->execute([':uid' => $userId]);
            while ($row = $stmtRecentStd->fetch(PDO::FETCH_ASSOC)) {
                $feed[] = [
                    'titulo' => $row['titulo'],
                    'subtitulo' => $row['subtitulo'],
                    'data' => $row['data'],
                    'badge' => 'Estudo',
                    'color' => 'purple'
                ];
            }

            // Resposta JSON exata esperada pelo loadDashboard() do script.js
            echo json_encode([
                'status' => 'success',
                'dashboard' => [
                    'financas' => [
                        'saldo_atual' => $saldoAtual,
                        'total_receitas' => $receitas,
                        'total_despesas' => $despesas,
                        'despesas_categoria' => $despesasCat
                    ],
                    'fitness' => [
                        'treinos_semana' => (int)($fitRes['treinos_semana'] ?? 0),
                        'minutos_semana' => (int)($fitRes['minutos_semana'] ?? 0)
                    ],
                    'estudos' => [
                        'horas_semana' => (float)($stdRes['horas_semana'] ?? 0),
                        'sessoes_semana' => (int)($stdRes['sessoes_semana'] ?? 0)
                    ],
                    'feed' => $feed
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Erro PHP Dashboard: ' . $e->getMessage()
            ]);
        }
    }
}