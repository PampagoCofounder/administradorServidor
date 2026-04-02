<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../vendor/autoload.php";

use Firebase\JWT\JWT;

$data = json_decode(file_get_contents("php://input"));
if (!$data || !isset($data->adminUser) || !isset($data->adminPass)) {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos"]);
    exit();
}

$db = (new Database())->connect();

$stmt = $db->prepare("SELECT * FROM administrador WHERE adminUser=?");
$stmt->execute([$data->adminUser]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($data->adminPass, $user["adminPass"])) {
    http_response_code(401);
    echo json_encode(["error" => "Credenciales inválidas"]);
    exit();
}

$key = "mi_clave_super_secreta_de_32_caracteres_minimo_2026";
$payload = [
    "iat" => time(),
    "exp" => time() + 3600,
    "data" => [
        "id" => $user["id"],
        "user" => $user["adminUser"],
        
    ]
];

$jwt = JWT::encode($payload, $key, "HS256");

echo json_encode([
    "token" => $jwt,
    "adminUser" => [
        "id" => $user["id"],
        "adminUser" => $user["adminUser"]
    ]
]);



?>