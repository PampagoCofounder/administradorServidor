<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$decoded = validarJWT();
$db = (new Database())->connect();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $id = $_GET['id'] ?? null;

        if ($id) {
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $db->prepare("SELECT * FROM usuarios");
            $stmt->execute();
        }

        echo json_encode([
            "ok" => true,
            "usuarios" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'] ?? null;
        $name_users = $data['name_users'] ?? null;
        $pass_users = $data['pass_users'] ?? null;

        if (!$id || !$name_users || !$pass_users) {
            echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO usuarios (id, name_users, pass_users) VALUES (?, ?, ?)");
        $stmt->execute([$id, $name_users, $pass_users]);

        echo json_encode([
            "ok" => true,
            "usuario" => [
                "id" => $db->lastInsertId(),
                "name_users" => $name_users,
                "pass_users" => $pass_users
            ]
        ]);
        break;
    
    case 'DELETE':
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo json_encode(["ok" => false, "error" => "Falta id"]);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(["ok" => true, "message" => "Usuario eliminado"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["ok" => false, "error" => "Método no permitido"]);
}