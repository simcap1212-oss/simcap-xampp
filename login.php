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
/* ===========================
   ESTILO FORMAL – SIMCAP
   =========================== */

body {
    font-family: "Segoe UI", Tahoma, Arial, sans-serif;
    background: #f4f6f9;
    color: #2c2c2c;
    text-align: center;
    padding-top: 60px;
    margin: 0;
}

/* Contenedor principal */
.container {
    width: 360px;
    margin: auto;
    background: #ffffff;
    padding: 32px 28px;
    border-radius: 6px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    border: 1px solid #dcdcdc;
}

/* Título */
.container h2 {
    margin-bottom: 22px;
    font-size: 20px;
    font-weight: 600;
    color: #1f3c88;
    letter-spacing: 0.5px;
}

/* Inputs */
input {
    width: 100%;
    padding: 11px 12px;
    margin: 12px 0;
    border-radius: 4px;
    border: 1px solid #c5c8ce;
    font-size: 14px;
    box-sizing: border-box;
    transition: border-color 0.2s, box-shadow 0.2s;
}

input:focus {
    outline: none;
    border-color: #1f3c88;
    box-shadow: 0 0 0 2px rgba(31, 60, 136, 0.15);
}

/* Botón */
button {
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
    letter-spacing: 0.4px;
    transition: background 0.2s, transform 0.1s;
}

button:hover {
    background: #162e6b;
}

button:active {
    transform: scale(0.98);
}

/* Mensajes de error */
.mensaje {
    color: #b00020;
    background: #fdecea;
    border: 1px solid #f5c6cb;
    padding: 10px;
    border-radius: 4px;
    font-size: 13px;
    margin-bottom: 15px;
}

/* Mensajes de éxito */
.success {
    color: #155724;
    background: #e6f4ea;
    border: 1px solid #c3e6cb;
    padding: 10px;
    border-radius: 4px;
    font-size: 13px;
    margin-bottom: 15px;
}

/* Enlace de registro */
a {
    display: block;
    margin-top: 18px;
    font-size: 13px;
    color: #1f3c88;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
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
