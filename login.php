<?php
// htdocs/login.php
session_start();

// Si ya está logueado, llevar al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Mostrar mensajes que envía auth/login_process.php
$mensaje = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'invalid_request': $mensaje = 'Solicitud inválida.'; break;
        case 'empty_fields': $mensaje = 'Por favor completa todos los campos.'; break;
        case 'wrong_credentials': $mensaje = 'Usuario o contraseña incorrectos.'; break;
        default: $mensaje = 'Error desconocido.'; break;
    }
} elseif (isset($_GET['success']) && $_GET['success'] === 'registered') {
    $mensaje = 'Registrado correctamente. Ya puedes iniciar sesión.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>SIMCAP - Iniciar Sesión</title>
<style>
/* (mantengo los estilos que ya tenías) */
body {
    font-family: Arial, sans-serif;
    background: #e3f2fd;
    text-align: center;
    padding-top: 50px;
}
.container {
    width: 350px;
    margin: auto;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
}
input {
    width: 90%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #90caf9;
}
button {
    background: #1565c0;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}
button:hover {
    background: #0d47a1;
}
a {
    display: block;
    margin-top: 15px;
    color: #1565c0;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
.mensaje {
    color: red;
    font-weight: bold;
    margin-bottom: 12px;
}
.success {
    color: green;
    font-weight: bold;
    margin-bottom: 12px;
}
</style>
</head>
<body>

<div class="container">
    <h2>Bienvenido a SIMCAP</h2>

    <?php if ($mensaje !== ''): ?>
        <?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
            <p class="success"><?= htmlspecialchars($mensaje) ?></p>
        <?php else: ?>
            <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Formulario envía a auth/login_process.php -->
    <form method="POST" action="auth/login_process.php">
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Iniciar Sesión</button>
    </form>

    <a href="register.php">Crear una cuenta nueva</a>
</div>

</body>
</html>
