<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$decoded = validarJWT();
$db = (new Database())->connect();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $id_empresa = $_GET['id_empresa'] ?? null;

        if ($id_empresa) {
            $stmt = $db->prepare("SELECT * FROM ingresos WHERE id_empresa = ?");
            $stmt->execute([$id_empresa]);
        } else {
            $stmt = $db->prepare("SELECT * FROM ingresos");
            $stmt->execute();
        }

        echo json_encode([
            "ok" => true,
            "ingresos" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);

        $id_empresa = $data['id_empresa'] ?? null;
        $concepto = $data['concepto'] ?? null;
        $monto = $data['monto'] ?? null;

        if (!$id_empresa || !$concepto || !$monto) {
            echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO ingresos (id_empresa, concepto, monto) VALUES (?, ?, ?)");
        $stmt->execute([$id_empresa, $concepto, $monto]);

        echo json_encode([
            "ok" => true,
            "ingreso" => [
                "id_ingreso" => $db->lastInsertId(),
                "id_empresa" => $id_empresa,
                "concepto" => $concepto,
                "monto" => $monto
            ]
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["ok" => false, "error" => "Método no permitido"]);
}