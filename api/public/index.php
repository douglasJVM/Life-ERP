<?php
// api/public/index.php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 1. Tratamento do Autoload do Composer
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

// 2. Normalização da Rota
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Remove prefixos comuns da Vercel e da API
$uri = preg_replace('#^/(api/public|api|public)#', '', $uri);
$uri = trim($uri, '/');

// Se a URI ficar vazia ou for 'auth', aponta para autenticação
switch ($uri) {
    case 'auth':
        // Inclua seu arquivo ou controller de autenticação aqui
        // Exemplo:
        if (file_exists(__DIR__ . '/../routes/auth.php')) {
            require_once __DIR__ . '/../routes/auth.php';
        } elseif (file_exists(__DIR__ . '/../controllers/AuthController.php')) {
            require_once __DIR__ . '/../controllers/AuthController.php';
        } else {
            // Caso sua lógica de auth esteja em outro arquivo, ajuste o require:
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Arquivo do AuthController não encontrado.']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Rota não encontrada: ' . ($_SERVER['REQUEST_URI'] ?? ''),
            'normalized_uri' => $uri
        ]);
        break;
}