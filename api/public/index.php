<?php
// api/public/index.php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-User-Id');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 1. Carrega o Autoload do Composer ou registra fallback PSR-4
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $baseDir = __DIR__ . '/../src/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) return;
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });

    // Inclusão manual garantida do Database
    $dbPath = __DIR__ . '/../src/Config/Database.php';
    if (file_exists($dbPath)) {
        require_once $dbPath;
    }
}

// 2. Normaliza a rota
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = preg_replace('#^/(api/public|api|public)#', '', $uri);
$uri = trim($uri, '/');

// 3. Roteador de Controladores
switch ($uri) {
    case 'dashboard':
        $file = __DIR__ . '/../src/Controllers/DashboardController.php';
        if (file_exists($file)) {
            require_once $file;
            (new \App\Controllers\DashboardController())->index();
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'DashboardController não encontrado.']);
        }
        break;

    // KANBAN / TAREFAS
    case 'tarefas':
    case 'tasks':
    case 'kanban':
        $file = __DIR__ . '/../src/Controllers/TaskController.php';
        if (file_exists($file)) {
            require_once $file;
            (new \App\Controllers\TaskController())->index();
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'TaskController não encontrado.']);
        }
        break;

    // HÁBITOS E ROTINAS
    case 'habitos':
    case 'habits':
    case 'stacker':
        $file = __DIR__ . '/../src/Controllers/HabitTrackerController.php';
        if (file_exists($file)) {
            require_once $file;
            (new \App\Controllers\HabitTrackerController())->index();
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'HabitTrackerController não encontrado.']);
        }
        break;

    case 'auth':
        $file = __DIR__ . '/../src/Controllers/AuthController.php';
        if (file_exists($file)) {
            require_once $file;
            (new \App\Controllers\AuthController())->index();
        }
        break;

    case 'financas':
    case 'finance':
        $file = __DIR__ . '/../src/Controllers/FinanceController.php';
        if (file_exists($file)) {
            require_once $file;
            (new \App\Controllers\FinanceController())->index();
        }
        break;

    case 'fitness':
        $file = __DIR__ . '/../src/Controllers/FitnessController.php';
        if (file_exists($file)) {
            require_once $file;
            (new \App\Controllers\FitnessController())->index();
        }
        break;

    case 'study':
    case 'estudos':
        $file = __DIR__ . '/../src/Controllers/StudyController.php';
        if (file_exists($file)) {
            require_once $file;
            (new \App\Controllers\StudyController())->index();
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Rota não encontrada: ' . $uri]);
        break;
}