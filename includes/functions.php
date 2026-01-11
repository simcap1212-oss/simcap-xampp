<?php
require_once __DIR__ . '/database.php';
session_start();

// Sanitizar entrada
function clean($value)
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

// Obtener usuario por email
function getUserByEmail($email)
{
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

// Crear usuario
function createUser($username, $email, $password)
{
    $pdo = getDB();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
    return $stmt->execute([$username, $email, $hash]);
}

// Validar login
function verifyLogin($email, $password)
{
    $user = getUserByEmail($email);
    if (!$user) return false;

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }

    return false;
}

// Proteger páginas privadas
function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Obtener datos de consumo de agua de un usuario
function getWaterUsageByUser($user_id)
{
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT volume, timestamp FROM water_usage WHERE user_id = ? ORDER BY timestamp DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}
