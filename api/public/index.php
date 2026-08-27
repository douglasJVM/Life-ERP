<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers de CORS completos (incluindo X-User-Id)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-User-Id");
header("Content-Type: application/json; charset=UTF-8");

// Trata pre-flight OPTIONS do navegador
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\FinanceController;
use App\Controllers\FitnessController;
use App\Controllers\StudyController;
use App\Controllers\HabitTrackerController;
use App\Controllers\TaskController;

try {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    switch ($uri) {
        case '/auth':
            (new AuthController())->index();
            break;
        case '/dashboard':
            (new DashboardController())->index();
            break;
        case '/financas':
            (new FinanceController())->index();
            break;
        case '/fitness':
            (new FitnessController())->index();
            break;
        case '/estudos':
            (new StudyController())->index();
            break;
        case '/habitos':
            (new HabitTrackerController())->index();
            break;
        case '/tarefas':
            (new TaskController())->index();
            break;
        default:
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Rota não encontrada: " . $uri]);
            break;
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Erro PHP: " . $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ]);
}