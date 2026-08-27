<?php
// web/index.php - Front controller para as telas
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove barras iniciais e finais
$uri = trim($uri, '/');

// Rota raiz ou vazia aponta para login
if ($uri === '' || $uri === 'views' || $uri === 'views/login') {
    require __DIR__ . '/views/login.php';
    exit;
}

// Extrai o nome do arquivo da view
$viewFile = str_replace('views/', '', $uri);
$viewPath = __DIR__ . '/views/' . $viewFile;

if (!str_ends_with($viewPath, '.php')) {
    $viewPath .= '.php';
}

if (file_exists($viewPath)) {
    require $viewPath;
} else {
    http_response_code(404);
    require __DIR__ . '/views/404.php'; // ou redirecione para login.php
}