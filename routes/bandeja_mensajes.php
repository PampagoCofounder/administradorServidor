<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$decoded = validarJWT();
$db = (new Database())->connect();


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    // 📦 OBTENER MENSAJES
    case "GET":
        $id = $_GET['id'] ?? null;

        if ($id) {
            $stmt = $db->prepare("SELECT * FROM solicitud_comex WHERE id = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $db->prepare("SELECT * FROM solicitud_comex");
            $stmt->execute();
        }

        echo json_encode([
            "ok" => true,
            "solicitud" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;
}
