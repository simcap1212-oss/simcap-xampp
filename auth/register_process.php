<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../register.php?error=invalid_request");
    exit;
}

$username = clean($_POST['username'] ?? '');
$email    = clean($_POST['email'] ?? '');
$password = clean($_POST['password'] ?? '');
$confirm  = clean($_POST['confirm_password'] ?? '');

if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
    header("Location: ../register.php?error=empty_fields");
    exit;
}

if ($password !== $confirm) {
    header("Location: ../register.php?error=password_mismatch");
    exit;
}

// Revisar si el email ya existe
if (getUserByEmail($email)) {
    header("Location: ../register.php?error=email_exists");
    exit;
}

// Crear usuario
if (createUser($username, $email, $password)) {
    header("Location: ../login.php?success=registered");
    exit;
}

header("Location: ../register.php?error=unknown_error");
exit;
