<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$decoded = validarJWT(); // obtiene info del admin
$db = (new Database())->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // 📦 OBTENER campanias
    case "GET":
        $id_costo = $_GET['id_costo'] ?? null;

        if ($id_costo) {
            $stmt = $db->prepare("SELECT * FROM campanias WHERE id_campanias = ?");
            $stmt->execute([$id_costo]);
        } else {
            $stmt = $db->prepare("SELECT * FROM campanias");
            $stmt->execute();
        }

        echo json_encode([
            "ok" => true,
            "costos" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;

    // ➕ CREAR COSTO
   
    default:
        http_response_code(405);
        echo json_encode(["ok" => false, "error" => "Método no permitido"]);
}
?>