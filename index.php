<?php

header("Access-Control-Allow-Origin: https://administrador.pampago.site/");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . "/vendor/autoload.php";

// obtener ruta
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), "/");
$parts = explode("/", $uri);

// detectar endpoint
$route = end($parts);

switch ($route) {
    case "login":
        require_once __DIR__ . "/routes/login.php";
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "error" => "Ruta no encontrada",
            "route" => $route
        ]);
}