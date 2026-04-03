<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$decoded = validarJWT(); // obtiene info del admin
$db = (new Database())->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // 📦 OBTENER COSTOS
    case "GET":
        $id_administrador = $_GET['id_administrador'] ?? null;

        if ($id_administrador) {
            $stmt = $db->prepare("SELECT * FROM administrador_costos WHERE id_administrador = ?");
            $stmt->execute([$id_administrador]);
        } else {
            $stmt = $db->prepare("SELECT * FROM administrador_costos");
            $stmt->execute();
        }

        echo json_encode([
            "ok" => true,
            "costos" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;

    // ➕ CREAR COSTO
    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);

        $id_administrador = $data['id_administrador'] ?? null;
        $servidores = $data['servidores'] ?? 0;
        $equipo_desarrollo = $data['equipo_desarrollo'] ?? 0;
        $marketing = $data['marketing'] ?? 0;
        $administracion = $data['administracion'] ?? 0;
        $soporte = $data['soporte'] ?? 0;

        if (!$id_administrador) {
            echo json_encode(["ok" => false, "error" => "Falta id_administrador"]);
            exit;
        }

        $stmt = $db->prepare("
            INSERT INTO administrador_costos 
            (id_administrador, servidores, equipo_desarrollo, marketing, administracion, soporte)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$id_administrador, $servidores, $equipo_desarrollo, $marketing, $administracion, $soporte]);

        $id_costo = $db->lastInsertId();

        echo json_encode([
            "ok" => true,
            "costo" => [
                "id_costo" => $id_costo,
                "id_administrador" => $id_administrador,
                "servidores" => $servidores,
                "equipo_desarrollo" => $equipo_desarrollo,
                "marketing" => $marketing,
                "administracion" => $administracion,
                "soporte" => $soporte
            ]
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["ok" => false, "error" => "Método no permitido"]);
}
?>