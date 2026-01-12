//Endpoint para prueba en Postman para CREAR NUEVO REGISTRO DE CONSUMO DE AGUA

<?php
// Mostrar errores (QA)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Respuesta JSON
header("Content-Type: application/json");

// Conexión BD QA
require_once __DIR__ . "/../db_qa.php";

// Solo POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Leer JSON
$input = json_decode(file_get_contents("php://input"), true);

// Validar datos
if (
    empty($input["user_id"]) ||
    empty($input["volume"])
) {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

$user_id = (int)$input["user_id"];
$volume  = (float)$input["volume"];

// Insertar consumo
$sql = "INSERT INTO water_usage (user_id, volume) VALUES (?, ?)";
$stmt = $conexion->prepare($sql);

try {
    $stmt->execute([$user_id, $volume]);

    echo json_encode([
        "status" => "ok",
        "mensaje" => "Consumo de agua registrado correctamente"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Error al registrar consumo",
        "detalle" => $e->getMessage()
    ]);
}
