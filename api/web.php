<?php
// api/web.php - Ponto de entrada Serverless para carregar as views
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = trim($uri, '/');

// Rota raiz ou /views -> login
if ($uri === '' || $uri === 'views' || $uri === 'views/login' || $uri === 'login') {
    require __DIR__ . '/../web/views/login.php';
    exit;
}

// Extrai o nome da view
$viewFile = str_replace('views/', '', $uri);
$viewPath = __DIR__ . '/../web/views/' . $viewFile;

if (!str_ends_with($viewPath, '.php')) {
    $viewPath .= '.php';
}

if (file_exists($viewPath)) {
    require $viewPath;
} else {
    http_response_code(404);
    echo "Página não encontrada.";
}