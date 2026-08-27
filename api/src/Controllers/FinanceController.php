<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Transaction;

class FinanceController {
    private int $userId;

    public function __construct(int $userId = 1) {
        $this->userId = $userId;
    }

    public function index(): void {
        $db = (new Database())->getConnection();
        $transModel = new Transaction($db);
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];

            // Ação de exclusão
            if (($input['action'] ?? '') === 'delete') {
                $id = (int)($input['id'] ?? 0);
                if ($transModel->delete($this->userId, $id)) {
                    echo json_encode(["status" => "success", "message" => "Registro removido"]);
                } else {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => "Erro ao excluir"]);
                }
                return;
            }

            // Ação de cadastro
            $descricao = trim($input['descricao'] ?? '');
            $valor = (float)($input['valor'] ?? 0);
            $tipo = $input['tipo'] ?? 'despesa';
            $categoria = trim($input['categoria'] ?? 'Geral');
            $metodo = trim($input['metodo_pagamento'] ?? 'Pix');
            $data = $input['data_transacao'] ?? date('Y-m-d');

            if (!empty($descricao) && $valor > 0) {
                $result = $transModel->create($this->userId, $descricao, $valor, $tipo, $categoria, $metodo, $data);
                
                if ($result['success']) {
                    echo json_encode(["status" => "success", "message" => "Transação adicionada!"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["status" => "error", "message" => $result['error']]);
                }
                return;
            }

            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Dados inválidos."]);
            return;
        }

        // Listagem GET
        $metrics = $transModel->getMetrics($this->userId);
        $categorias = $transModel->getExpensesByCategory($this->userId);
        $historico = $transModel->getAll($this->userId);

        echo json_encode([
            "status" => "success",
            "financas" => array_merge($metrics, [
                "categorias" => $categorias,
                "historico" => $historico
            ])
        ]);
    }
}