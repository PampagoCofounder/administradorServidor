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

        $nombre_empresa = $data['nombre_empresa'] ?? null;
        $tipo_empresa = $data['tipo_empresa'] ?? null;
        $cuit = $data['cuit'] ?? null;
        $estado_comercializacion = $data['estado_comercializacion'] ?? null;
        $direccion = $data['direccion'] ?? null;
        $localidad = $data['localidad'] ?? null;
        $provincia = $data['provincia'] ?? null;
        $telefono = $data['telefono'] ?? null;



        if (!$nombre_empresa || !$tipo_empresa || !$cuit || !$estado_comercializacion || !$direccion || !$localidad || !$provincia || !$telefono) {
            echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO empresa (nombre_empresa, tipo_empresa, cuit, estado_comercializacion, direccion, localidad, provincia, telefono) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre_empresa, $tipo_empresa, $cuit, $estado_comercializacion, $direccion, $localidad, $provincia, $telefono]);

        $id_empresa = $db->lastInsertId();

        echo json_encode([
            "ok" => true,
            "empresa" => [
                "id_empresa" => $id_empresa,
                "nombre_empresa" => $nombre_empresa,
                "tipo_empresa" => $tipo_empresa,
                "cuit" => $cuit,
                "estado_comercializacion" => $estado_comercializacion,
                "direccion" => $direccion,
                "localidad" => $localidad,
                "provincia" => $provincia,
                "telefono" => $telefono
            ]
        ]);
        break;

    case "DELETE":

        // obtener id por query (?id_empresa=1)
        $id_empresa = $_GET['id_empresa'] ?? null;

        if (!$id_empresa) {
            echo json_encode([
                "ok" => false,
                "error" => "ID requerido"
            ]);
            exit;
        }

        // validar que sea número
        if (!is_numeric($id_empresa)) {
            echo json_encode([
                "ok" => false,
                "error" => "ID inválido"
            ]);
            exit;
        }

        // ejecutar delete
        $stmt = $db->prepare("DELETE FROM empresa WHERE id_empresa = ?");
        $stmt->execute([$id_empresa]);

        echo json_encode([
            "ok" => true,
            "message" => "Empresa eliminada"
        ]);
        break;
    case "PUT":

        $data = json_decode(file_get_contents("php://input"), true);

        $id_empresa = $data['id_empresa'] ?? null;
        $nombre_empresa = $data['nombre_empresa'] ?? null;
        $tipo_empresa = $data['tipo_empresa'] ?? null;
        $cuit = $data['cuit'] ?? null;
        $estado_comercializacion = $data['estado_comercializacion'] ?? null;
        $direccion = $data['direccion'] ?? null;
        $localidad = $data['localidad'] ?? null;
        $provincia = $data['provincia'] ?? null;
        $telefono = $data['telefono'] ?? null;

        // validar
        if (
            !$id_empresa ||
            !$nombre_empresa ||
            !$tipo_empresa ||
            !$cuit ||
            !$estado_comercializacion ||
            !$direccion ||
            !$localidad ||
            !$provincia ||
            !$telefono
        ) {
            echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
            exit;
        }

        // update seguro
        $stmt = $db->prepare("
        UPDATE empresa 
        SET 
            nombre_empresa = ?, 
            tipo_empresa = ?, 
            cuit = ?, 
            estado_comercializacion = ?, 
            direccion = ?, 
            localidad = ?, 
            provincia = ?, 
            telefono = ?
        WHERE id_empresa = ?
    ");

        $stmt->execute([
            $nombre_empresa,
            $tipo_empresa,
            $cuit,
            $estado_comercializacion,
            $direccion,
            $localidad,
            $provincia,
            $telefono,
            $id_empresa
        ]);

        echo json_encode([
            "ok" => true,
            "empresa" => $data
        ]);

        break;

    default:
        http_response_code(405);
        echo json_encode(["ok" => false, "error" => "Método no permitido"]);
}
