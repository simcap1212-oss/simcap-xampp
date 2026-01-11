<?php
session_start();
require_once "includes/config.php";
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <title>Registrar Usuario - SIMCAP</title>
</head>
<body>
<div class="card">
    <h2>Registrar Usuario</h2>
    <form method="POST" action="auth/register_process.php">
        <input type="text" name="username" placeholder="Nombre de usuario" required><br>
        <input type="email" name="email" placeholder="Correo electrónico" required><br>
        <input type="password" name="password" placeholder="Contraseña" required><br>
        <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required><br>
        <button type="submit">Registrar</button>
    </form>
    <?php
    if (isset($_GET['error'])) {
        echo "<p class='error'>{$_GET['error']}</p>";
    } elseif (isset($_GET['success'])) {
        echo "<p style='color:green'>Registrado correctamente. Inicia sesión.</p>";
    }
    ?>
</div>
</body>
</html>
