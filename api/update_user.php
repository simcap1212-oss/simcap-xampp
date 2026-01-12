//Endpoint para prueba en Postman para EDITAR DATOS DE USUARIO

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

// Validaciones mínimas
if (empty($input["id"]) || empty($input["username"]) || empty($input["email"])) {
    http_response_code(400);
    echo json_encode(["error" => "ID, username y email son obligatorios"]);
    exit;
}

$id       = (int)$input["id"];
$username = trim($input["username"]);
$email    = trim($input["email"]);

// Verificar usuario existe
$check = $conexion->prepare("SELECT id FROM users WHERE id = ?");
$check->execute([$id]);

if (!$check->fetch()) {
    http_response_code(404);
    echo json_encode(["error" => "Usuario no encontrado"]);
    exit;
}

// Construir SQL dinámico
if (!empty($input["password"])) {
    $hashedPassword = password_hash($input["password"], PASSWORD_DEFAULT);
    $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
    $params = [$username, $email, $hashedPassword, $id];
} else {
    $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $params = [$username, $email, $id];
}

// Ejecutar update
$stmt = $conexion->prepare($sql);
$stmt->execute($params);

echo json_encode([
    "status" => "ok",
    "mensaje" => "Usuario actualizado correctamente",
    "user_id" => $id
]);
