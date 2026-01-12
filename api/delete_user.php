//Endpoint para prueba en Postman para ELIMINAR USUARIOS

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

require_once __DIR__ . "/../db_qa.php";

// Solo DELETE
if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Leer JSON
$input = json_decode(file_get_contents("php://input"), true);

// Validar ID
if (empty($input["id"])) {
    http_response_code(400);
    echo json_encode(["error" => "ID de usuario obligatorio"]);
    exit;
}

$user_id = (int)$input["id"];

// Verificar si existe usuario
$checkUser = $conexion->prepare("SELECT id FROM users WHERE id = ?");
$checkUser->execute([$user_id]);

if (!$checkUser->fetch()) {
    http_response_code(404);
    echo json_encode(["error" => "Usuario no encontrado"]);
    exit;
}

// Verificar consumos asociados
$checkUsage = $conexion->prepare(
    "SELECT COUNT(*) FROM water_usage WHERE user_id = ?"
);
$checkUsage->execute([$user_id]);
$usageCount = $checkUsage->fetchColumn();

if ($usageCount > 0) {
    http_response_code(409);
    echo json_encode([
        "error" => "No se puede eliminar el usuario",
        "detalle" => "El usuario tiene consumos asociados"
    ]);
    exit;
}

// Eliminar usuario
$stmt = $conexion->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

echo json_encode([
    "status" => "ok",
    "mensaje" => "Usuario eliminado correctamente",
    "user_id" => $user_id
]);
