//Endpoint para prueba en Postman para CREAR UN NUEVO USUARIO

<?php
// Mostrar errores (solo QA)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Permitir JSON
header("Content-Type: application/json");

// Incluir conexión BD QA
require_once __DIR__ . "/../db_qa.php";

// Aceptar solo POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Leer JSON enviado
$input = json_decode(file_get_contents("php://input"), true);

// Validar datos mínimos
if (
    empty($input["username"]) ||
    empty($input["email"]) ||
    empty($input["password"])
) {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

// Limpiar datos
$username = trim($input["username"]);
$email    = trim($input["email"]);
$password = password_hash($input["password"], PASSWORD_DEFAULT);

// Insertar usuario
$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);

try {
    $stmt->execute([$username, $email, $password]);

    echo json_encode([
        "status" => "ok",
        "mensaje" => "Usuario creado correctamente"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Error al crear usuario",
        "detalle" => $e->getMessage()
    ]);
}
