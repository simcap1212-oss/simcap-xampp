//Endpoint para prueba en Postman para ELIMINAR REGISTROS DE CONSUMO DE AGUA

<?php
// Mostrar errores (QA)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Respuesta JSON
header("Content-Type: application/json");

// Conexión BD QA
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
    echo json_encode(["error" => "ID es obligatorio"]);
    exit;
}

$id = (int)$input["id"];

// Verificar si existe el registro
$check = $conexion->prepare("SELECT id FROM water_usage WHERE id = ?");
$check->execute([$id]);

if (!$check->fetch()) {
    http_response_code(404);
    echo json_encode(["error" => "Registro no encontrado"]);
    exit;
}

// Eliminar registro
$stmt = $conexion->prepare("DELETE FROM water_usage WHERE id = ?");
$stmt->execute([$id]);

echo json_encode([
    "status" => "ok",
    "mensaje" => "Registro de consumo eliminado",
    "id_eliminado" => $id
]);
