//Endpoint para prueba en Postman para EDITAR DATOS DE LOS REGISTROS DE CONSUMO DE AGUA

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

require_once __DIR__ . "/../db_qa.php";

// Solo PUT
if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Leer JSON
$input = json_decode(file_get_contents("php://input"), true);

// Validar datos
if (
    empty($input["id"]) ||
    !isset($input["volume"]) ||
    empty($input["timestamp"])
) {
    http_response_code(400);
    echo json_encode(["error" => "ID, volume y timestamp son obligatorios"]);
    exit;
}

$id        = (int)$input["id"];
$volume    = (float)$input["volume"];
$timestamp = $input["timestamp"];

// Verificar existe
$check = $conexion->prepare("SELECT id FROM water_usage WHERE id = ?");
$check->execute([$id]);

if (!$check->fetch()) {
    http_response_code(404);
    echo json_encode(["error" => "Registro no encontrado"]);
    exit;
}

// Actualizar
$sql = "UPDATE water_usage SET volume = ?, timestamp = ? WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$volume, $timestamp, $id]);

echo json_encode([
    "status" => "ok",
    "mensaje" => "Consumo actualizado correctamente",
    "consumo_id" => $id
]);
