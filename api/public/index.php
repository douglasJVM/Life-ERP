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

// 1. Carrega o Autoload do Composer ou as classes manualmente
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
} else {
    // Inclusão manual das dependências caso vendor/ não esteja na nuvem
    $dbPath = __DIR__ . '/../src/Config/Database.php';
    if (file_exists($dbPath)) {
        require_once $dbPath;
    }
}

// 2. Normaliza a rota
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = preg_replace('#^/(api/public|api|public)#', '', $uri);
$uri = trim($uri, '/');

switch ($uri) {
    case 'auth':
        $controllerPath = __DIR__ . '/../src/Controllers/AuthController.php';

        if (file_exists($controllerPath)) {
            require_once $controllerPath;

            $controller = new \App\Controllers\AuthController();
            $controller->index();
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Arquivo api/src/Controllers/AuthController.php não encontrado.'
            ]);
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