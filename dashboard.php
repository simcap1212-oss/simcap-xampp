<?php
require_once "includes/functions.php";
requireLogin();

$period = $_GET['period'] ?? 'daily';
$selected_day = $_GET['day'] ?? date('Y-m-d');
$selected_month = $_GET['month'] ?? date('Y-m');
$pdo = getDB();
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username']; 
$ALERTA_CONSUMO = 900;
$alertas = [];

// Obtener datos según periodo y filtros
switch ($period) {
    case 'weekly':
        $stmt = $pdo->prepare("
            SELECT DATE(timestamp) as date, SUM(volume) as total
            FROM water_usage
            WHERE user_id = ? AND timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(timestamp)
            ORDER BY DATE(timestamp) ASC
        ");
        $stmt->execute([$user_id]);
        break;

    case 'monthly':
        $stmt = $pdo->prepare("
            SELECT DATE_FORMAT(timestamp, '%Y-%m') as date, SUM(volume) as total
            FROM water_usage
            WHERE user_id = ? AND DATE_FORMAT(timestamp, '%Y-%m') = ?
            GROUP BY DATE_FORMAT(timestamp, '%Y-%m')
        ");
        $stmt->execute([$user_id, $selected_month]);
        break;

    case 'daily':
    default:
        $stmt = $pdo->prepare("
            SELECT timestamp as date, volume as total
            FROM water_usage
            WHERE user_id = ? AND DATE(timestamp) = ?
            ORDER BY timestamp ASC
        ");
        $stmt->execute([$user_id, $selected_day]);
        break;
}

$data = $stmt->fetchAll();

// Preparar datos para Chart.js y tabla
$labels = [];
$volumes = [];

foreach ($data as $row) {
    $labels[] = $row['date'];
    $volumes[] = (float)$row['total'];
    if ((float)$row['total'] > $ALERTA_CONSUMO) {
        $alertas[] = $row['date'] . " - " . $row['total'] . " litros";
    }
}

// Promedio diario
$avgStmt = $pdo->prepare("
    SELECT AVG(daily_total) as promedio
    FROM (
        SELECT SUM(volume) as daily_total
        FROM water_usage
        WHERE user_id = ?
        GROUP BY DATE(timestamp)
    ) as daily
");
$avgStmt->execute([$user_id]);
$avgData = $avgStmt->fetch();
$promedio_diario = round($avgData['promedio'], 2);

// ----------------------
// NUEVO: Consumo diario y costos
// ----------------------
$consumo_diario = 0;
$costo_m3 = 0.40;
$costo_diario = 0;

if ($period == 'daily') {
    foreach ($data as $row) {
        $consumo_diario += (float)$row['total'];
    }
    $costo_diario = ($consumo_diario / 1000) * $costo_m3; 
    $costo_diario = round($costo_diario, 4);
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <title>Dashboard SIMCAP</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f2f2f2;
        }
        .sidebar {
            width: 250px;
            background: #263238;
            color: white;
            padding: 20px;
            min-height: 100vh;
        }
        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .sidebar label {
            display: block;
            margin: 10px 0 5px;
        }
        .sidebar input[type="date"],
        .sidebar input[type="month"],
        .sidebar button {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: none;
            border-radius: 4px;
        }
        .sidebar button {
            background-color: #0288d1;
            color: white;
            cursor: pointer;
        }
        .sidebar button:hover {
            background-color: #0277bd;
        }
        .sidebar input[type="radio"] {
            margin-right: 5px;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .navbar {
            background: #0288d1;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .alert-box {
            width: 100%;
            padding: 15px;
            background: #ffebee;
            border: 2px solid #f44336;
            border-radius: 8px;
            color: #b71c1c;
            text-align: left;
            margin-bottom: 20px;
        }
        .alert-row {
            background-color: #ffebee;
            color: #b71c1c;
            font-weight: bold;
        }
        table.usage-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.usage-table th, table.usage-table td {
            border-bottom: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Usuario: <?= htmlspecialchars($username) ?></h2>
    <h2>Filtros de Consumo</h2>

    <form method="get">
        <label><input type="radio" name="period" value="daily" <?= $period=='daily'?'checked':'' ?>> Diario</label>
        <label><input type="radio" name="period" value="weekly" <?= $period=='weekly'?'checked':'' ?>> Semanal</label>
        <label><input type="radio" name="period" value="monthly" <?= $period=='monthly'?'checked':'' ?>> Mensual</label>

        <?php if($period=='daily'): ?>
            <label>Selecciona un día:</label>
            <input type="date" name="day" value="<?= htmlspecialchars($selected_day) ?>">
        <?php elseif($period=='monthly'): ?>
            <label>Selecciona un mes:</label>
            <input type="month" name="month" value="<?= htmlspecialchars($selected_month) ?>">
        <?php endif; ?>

        <button type="submit">Aplicar</button>
    </form>

    <!-- Promedio -->
    <p style="margin-top:20px;">
        Promedio diario: <strong><?= $promedio_diario ?> litros</strong>
    </p>

    <!-- NUEVO: Consumo diario y costos -->
    <?php if ($period == 'daily'): ?>
        <p>Consumo del día seleccionado: 
            <strong><?= $consumo_diario ?> litros</strong>
        </p>

        <p>Costo por m³:
            <strong>$<?= number_format($costo_m3, 2) ?></strong>
        </p>

        <p>Costo del consumo diario:
            <strong>$<?= number_format($costo_diario, 4) ?></strong>
        </p>
    <?php endif; ?>
</div>

<div class="main-content">
    <div class="navbar">
        SIMCAP - Panel de Consumo de Agua
        <a href="logout.php" style="float:right; color:white;">Cerrar Sesión</a>
    </div>

    <!-- Alertas -->
    <?php if (!empty($alertas)): ?>
        <div class="alert-box">
            <h3>⚠️ Alertas de Consumo Excesivo</h3>
            <ul>
                <?php foreach ($alertas as $dia): ?>
                    <li><?= htmlspecialchars($dia) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Gráfica -->
    <canvas id="consumoChart" width="900" height="400"></canvas>

    <!-- Tabla detallada -->
    <?php if (!empty($data)): ?>
        <table class="usage-table">
            <tr>
                <th>Fecha / Hora</th>
                <th>Volumen (Litros)</th>
                <th>Costo (USD)</th>
            </tr>

            <?php foreach ($data as $row): 
                $costo_fila = round(($row['total'] / 1000) * $costo_m3, 4);
            ?>
            <tr <?= ((float)$row['total'] > $ALERTA_CONSUMO) ? "class='alert-row'" : "" ?>>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['total']) ?></td>
                <td>$<?= number_format($costo_fila, 4) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay registros de consumo para este periodo.</p>
    <?php endif; ?>
</div>

<script>
// Colores según alerta
const barColors = <?= json_encode(array_map(function($v) use ($ALERTA_CONSUMO) {
    return $v > $ALERTA_CONSUMO ? 'rgba(244, 67, 54, 0.7)' : 'rgba(2, 136, 209, 0.6)';
}, $volumes)) ?>;

const ctx = document.getElementById('consumoChart').getContext('2d');
const consumoChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Litros consumidos',
            data: <?= json_encode($volumes) ?>,
            backgroundColor: barColors,
            borderColor: barColors.map(c => c.replace('0.7','1')),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            y: { beginAtZero: true, title: { display: true, text: 'Litros' } },
            x: { title: { display: true, text: 'Fecha' } }
        }
    }
});
</script>

</body>
</html>
