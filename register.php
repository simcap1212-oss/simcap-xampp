<?php
session_start();
require_once "includes/config.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registrar Usuario - SIMCAP</title>

<style>
body {
    font-family: "Segoe UI", Tahoma, Arial, sans-serif;
    background: #f4f6f9;
    color: #2c2c2c;
    margin: 0;
    padding-top: 60px;
    text-align: center;
}

.card {
    width: 380px;
    margin: auto;
    background: #ffffff;
    padding: 32px 28px;
    border-radius: 6px;
    border: 1px solid #dcdcdc;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.card h2 {
    margin-bottom: 22px;
    font-size: 20px;
    font-weight: 600;
    color: #1f3c88;
    letter-spacing: 0.4px;
}

.card input {
    width: 100%;
    padding: 11px 12px;
    margin: 12px 0;
    border-radius: 4px;
    border: 1px solid #c5c8ce;
    font-size: 14px;
    box-sizing: border-box;
}

.card input:focus {
    outline: none;
    border-color: #1f3c88;
    box-shadow: 0 0 0 2px rgba(31, 60, 136, 0.15);
}

.card button {
    width: 100%;
    margin-top: 18px;
    background: #1f3c88;
    color: #ffffff;
    padding: 12px 0;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.card button:hover {
    background: #162e6b;
}

.error {
    margin-top: 15px;
    padding: 10px;
    background: #fdecea;
    color: #b00020;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
    font-size: 13px;
}

.success {
    margin-top: 15px;
    padding: 10px;
    background: #e6f4ea;
    color: #155724;
    border: 1px solid #c3e6cb;
    border-radius: 4px;
    font-size: 13px;
}
</style>
</head>

<body>

<div class="card">
    <h2>Registrar Usuario</h2>

    <form method="POST" action="auth/register_process.php">
        <input type="text" name="username" placeholder="Nombre de usuario" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
        <button type="submit">Registrar</button>
    </form>

    <?php if (isset($_GET['error'])): ?>
        <p class="error"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php elseif (isset($_GET['success'])): ?>
        <p class="success">Registrado correctamente. Inicia sesión.</p>
    <?php endif; ?>
</div>

</body>
</html>
