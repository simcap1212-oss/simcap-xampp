<?php
$message = "";
$message_class = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $volume = $_POST['volume'];
    $timestamp_input = $_POST['timestamp'] ?? null;

    // URL real del API
    $url = "https://simcap1212.infinityfreeapp.com/api/receive.php";

    // Preparar datos para el API
    $data = [
        "user_id" => $user_id,
        "volume" => $volume
    ];

    if ($timestamp_input) {
        $data['timestamp'] = date('Y-m-d H:i:s', strtotime($timestamp_input));
    }

    // Llamada HTTP POST
    $options = [
        "http" => [
            "header"  => "Content-type: application/x-www-form-urlencoded\r\n",
            "method"  => "POST",
            "content" => http_build_query($data),
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result !== false) {
        $response = json_decode($result, true);

        if (isset($response['status']) && $response['status'] === "OK") {
            $message = "Datos enviados correctamente. Volumen: {$response['volume']} litros";
            $message_class = "success";
        } 
        else if (isset($response['error'])) {
            $message = "Error: {$response['error']}";
            $message_class = "error";
        } 
        else {
            $message = "Error desconocido al enviar los datos.";
            $message_class = "error";
        }
    } 
    else {
        $message = "No se pudo conectar con la API.";
        $message_class = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Enviar datos</title>

<style>
    body {
        margin: 0;
        background-image: url("sensor_caudal.jpg");
        background-size: cover;
        background-repeat: no-repeat;
        background-position: top center;
        font-family: Arial, sans-serif;
        height: 100vh;
    }

    .pantalla {
        position: absolute;
        top: 130px;
        left: 410px;
        width: 440px;
        height: 275px;
        background: #D3D3D3;
        border-radius: 8px;
        padding: 10px 20px;
        box-sizing: border-box;
        text-align: left;
        overflow: hidden;
    }

    .titulo {
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 10px;
    }

    .success {
        text-align: center;
        color: green;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .error {
        text-align: center;
        color: red;
        font-weight: bold;
        margin-bottom: 10px;
    }

    input {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        font-size: 16px;
        border-radius: 5px;
        border: 1px solid gray;
        box-sizing: border-box;
    }

    .btn-enviar {
        position: absolute;
        top: 142px;
        left: 870px;
        width: 110px;
        height: 75px;
        background: #0d6efd;
        color: white;
        font-weight: bold;
        border-radius: 12px;
        border: 3px solid white;
        cursor: pointer;
        font-size: 16px;
    }

    .btn-clean {
        position: absolute;
        top: 260px;
        left: 870px;
        width: 110px;
        height: 75px;
        background: #0d6efd;
        color: white;
        font-weight: bold;
        border-radius: 12px;
        border: 3px solid white;
        cursor: pointer;
        font-size: 16px;
    }
</style>
</head>
<body>

<div class="pantalla">
    <?php if ($message != "") echo "<div class='$message_class'>$message</div>"; ?>

    <div class="titulo">Enviar datos al API</div>

    <form method="POST">

        <label>User ID:</label>
        <input type="number" name="user_id" required value="1">

        <label>Volumen (Litros):</label>
        <input type="text" name="volume" required value="12.5">

        <label>Fecha y hora (opcional):</label>
        <input type="datetime-local" name="timestamp">

        <button type="submit" style="display:none;">Enviar</button>
    </form>
</div>

<button class="btn-enviar" onclick="document.querySelector('form').submit()">Enviar Datos</button>
<button class="btn-clean" onclick="document.querySelector('form').reset()">Clean</button>

</body>
</html>
