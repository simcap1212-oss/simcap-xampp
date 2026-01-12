//Endpoint para prueba en Postman para SABER SI EXISTE CONEXIÓN A LA BASE DE DATOS

<?php
require_once __DIR__ . "/../db_qa.php";

// Contar usuarios
$stmt = $conexion->query("SELECT COUNT(*) AS total FROM users");
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Respuesta JSON
echo json_encode([
    "entorno" => "QA LOCAL",
    "base_datos" => "simcap_db",
    "tabla" => "users",
    "total_usuarios" => $data["total"]
]);
