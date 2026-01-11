<?php
header("Content-Type: application/json");
require_once "../includes/database.php";

$pdo = getDB();

// Verificar método POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Validar parámetros obligatorios
if (!isset($_POST["user_id"]) || !isset($_POST["volume"])) {
    echo json_encode(["error" => "Faltan parámetros"]);
    exit;
}

$user_id = intval($_POST["user_id"]);
$volume = floatval($_POST["volume"]);

// Validar usuario
$stmtCheck = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmtCheck->execute([$user_id]);
if (!$stmtCheck->fetch()) {
    echo json_encode(["error" => "Usuario no encontrado"]);
    exit;
}

// Validar volumen
if($volume < 0 || $volume > 1000){
    echo json_encode(["error" => "Volumen inválido"]);
    exit;
}

// Fecha y hora opcional
$timestamp = $_POST['timestamp'] ?? date('Y-m-d H:i:s'); // si no envían, usa NOW()

// Insertar registro
$stmt = $pdo->prepare("INSERT INTO water_usage (user_id, volume, timestamp) VALUES (?, ?, ?)");
if ($stmt->execute([$user_id, $volume, $timestamp])) {
    $alert = $volume > 50 ? "⚠️ Consumo excesivo" : "";
    echo json_encode([
        "status" => "OK",
        "user_id" => $user_id,
        "volume" => $volume,
        "timestamp" => $timestamp,
        "alert" => $alert
    ]);
} else {
    echo json_encode(["error" => "No se pudo insertar en la BD"]);
}
