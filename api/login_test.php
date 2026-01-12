//Endpoint para prueba en Postman para VERIFICAR SI EXISTE LOGIN EXITOSO

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
    empty($input["email"]) ||
    empty($input["password"])
) {
    http_response_code(400);
    echo json_encode(["error" => "Email y contraseña son obligatorios"]);
    exit;
}

$email    = trim($input["email"]);
$password = $input["password"];

// Buscar usuario por email
$sql = "SELECT id, username, email, password FROM users WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si existe el usuario
if (!$user) {
    http_response_code(401);
    echo json_encode(["error" => "Usuario no existe"]);
    exit;
}

// Verificar contraseña
if (!password_verify($password, $user["password"])) {
    http_response_code(401);
    echo json_encode(["error" => "Contraseña incorrecta"]);
    exit;
}

// Login exitoso
echo json_encode([
    "status" => "ok",
    "mensaje" => "Login exitoso",
    "user" => [
        "id" => $user["id"],
        "username" => $user["username"],
        "email" => $user["email"]
    ]
]);
