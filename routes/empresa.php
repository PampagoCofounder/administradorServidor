<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$decoded = validarJWT();
$db = (new Database())->connect();


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    // 📦 OBTENER EMPRESAS
    case "GET":
        $id_empresa = $_GET['id_empresa'] ?? null;

        if ($id_empresa) {
            $stmt = $db->prepare("SELECT * FROM empresa WHERE id_empresa = ?");
            $stmt->execute([$id_empresa]);
        } else {
            $stmt = $db->prepare("SELECT * FROM empresa");
            $stmt->execute();
        }

        echo json_encode([
            "ok" => true,
            "empresas" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;

    // ➕ CREAR EMPRESA
    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);

        $nombre = $data['nombre'] ?? null;
        $route = $data['route'] ?? null;

        if (!$nombre || !$route) {
            echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO empresa (nombre, route) VALUES (?, ?)");
        $stmt->execute([$nombre, $route]);

        $id_empresa = $db->lastInsertId();

        echo json_encode([
            "ok" => true,
            "empresa" => [
                "id_empresa" => $id_empresa,
                "nombre" => $nombre,
                "route" => $route
            ]
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["ok" => false, "error" => "Método no permitido"]);
}