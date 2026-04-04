<?php 


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function validarJWT() {

    // 🔥 obtener headers correctamente
    $headers = getallheaders();
    $headers = array_change_key_case($headers, CASE_LOWER);

    $authHeader = null;

    if (isset($headers['authorization'])) {
        $authHeader = $headers['authorization'];
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }

    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(["error" => "Token requerido"]);
        exit();
    }

    // 🔐 sacar Bearer
    $token = str_replace("Bearer ", "", $authHeader);

    $key = "mi_clave_super_secreta_de_32_caracteres_minimo_2026";

    try {
        return JWT::decode($token, new Key($key, "HS256"));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "error" => "Token inválido",
            "detalle" => $e->getMessage() // opcional para debug
        ]);
        exit();
    }
}