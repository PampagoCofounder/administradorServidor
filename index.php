<?php

header("Access-Control-Allow-Origin: https://administrador.pampago.site");
//header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
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
    
    case 'empresa':
        require_once __DIR__ . "/routes/empresa.php";
        break;
    
    case 'administrador_costos':
        require_once __DIR__ . "/routes/costos.php";
        break;
    
    case 'ingresos':
        require_once __DIR__ . "/routes/ingreso.php";
        break;
    
    case 'usuarios':
        require_once __DIR__ . "/routes/usuarios.php";
        break;


    default:
        http_response_code(404);
        echo json_encode([
            "error" => "Ruta no encontrada",
            "route" => $route
        ]);
}