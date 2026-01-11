<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php?error=invalid_request');
    exit;
}

$email = clean($_POST['email'] ?? '');
$password = clean($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    header("Location: ../login.php?error=empty_fields");
    exit;
}

if (verifyLogin($email, $password)) {
    header('Location: ../dashboard.php');
    exit;
} else {
    header('Location: ../login.php?error=wrong_credentials');
    exit;
}
