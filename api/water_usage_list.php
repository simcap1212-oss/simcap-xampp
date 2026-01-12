//Endpoint para prueba en Postman para ORDENAR LOS CONSUMOS DE AGUA POR USUARIO

<?php
// Mostrar errores (QA)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Respuesta JSON
header("Content-Type: application/json");

// Conexión BD QA
require_once __DIR__ . "/../db_qa.php";

// Solo GET
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Validar parámetro user_id
if (!isset($_GET["user_id"])) {
    http_response_code(400);
    echo json_encode(["error" => "Parámetro user_id requerido"]);
    exit;
}

$user_id = (int) $_GET["user_id"];

// Consultar consumos
$sql = "SELECT id, volume, timestamp 
        FROM water_usage 
        WHERE user_id = ?
        ORDER BY timestamp DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute([$user_id]);

$consumos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Respuesta
echo json_encode([
    "user_id" => $user_id,
    "total_registros" => count($consumos),
    "consumos" => $consumos
]);
