<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;
use Exception;

class AuthController {
    public function index(): void {
        try {
            $db = (new Database())->getConnection();
            $method = $_SERVER['REQUEST_METHOD'];

            if ($method !== 'POST') {
                http_response_code(405);
                echo json_encode(["status" => "error", "message" => "Método não permitido."]);
                return;
            }

            $raw = file_get_contents('php://input');
            $input = json_decode($raw, true) ?? [];
            $action = $input['action'] ?? 'login';

            // CADASTRO DE USUÁRIO
            if ($action === 'register') {
                $nome = trim($input['nome'] ?? '');
                $email = trim($input['email'] ?? '');
                $senha = $input['senha'] ?? '';

                if (empty($nome) || empty($email) || empty($senha)) {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => "Preencha todos os campos obrigatórios."]);
                    return;
                }

                // Verifica duplicidade de e-mail
                $check = $db->prepare("SELECT id FROM users WHERE email = :e");
                $check->execute([':e' => $email]);
                if ($check->fetch()) {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => "Este e-mail já está cadastrado."]);
                    return;
                }

                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (nome, email, senha, nivel, xp_total) VALUES (:n, :e, :s, 1, 0)");
                $stmt->execute([
                    ':n' => $nome,
                    ':e' => $email,
                    ':s' => $hash
                ]);
                $newId = (int)$db->lastInsertId();

                echo json_encode([
                    "status" => "success",
                    "message" => "Conta criada com sucesso!",
                    "user" => [
                        "id" => $newId,
                        "nome" => $nome,
                        "email" => $email,
                        "nivel" => 1,
                        "xp_total" => 0
                    ]
                ]);
                return;
            }

            // LOGIN DE USUÁRIO
            if ($action === 'login') {
                $email = trim($input['email'] ?? '');
                $senha = $input['senha'] ?? '';

                if (empty($email) || empty($senha)) {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => "Informe seu e-mail e senha."]);
                    return;
                }

                $stmt = $db->prepare("SELECT id, nome, email, senha, nivel, xp_total FROM users WHERE email = :e");
                $stmt->execute([':e' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($senha, $user['senha'])) {
                    echo json_encode([
                        "status" => "success",
                        "message" => "Login realizado com sucesso!",
                        "user" => [
                            "id" => (int)$user['id'],
                            "nome" => $user['nome'],
                            "email" => $user['email'],
                            "nivel" => (int)$user['nivel'],
                            "xp_total" => (int)$user['xp_total']
                        ]
                    ]);
                    return;
                }

                http_response_code(401);
                echo json_encode(["status" => "error", "message" => "E-mail ou senha incorretos."]);
                return;
            }

            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Ação inválida."]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Erro PHP Auth: " . $e->getMessage()
            ]);
        }
    }
}