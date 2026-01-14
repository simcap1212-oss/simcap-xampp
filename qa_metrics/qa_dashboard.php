<?php
require_once("../db_qa.php");

if (!isset($conexion)) {
    die("ERROR: conexión a la base de datos no disponible");
}

/* KPIs */
$totalUsuarios = $conexion->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalConsumos = $conexion->query("SELECT COUNT(*) FROM water_usage")->fetchColumn();
$totalAlertas  = $conexion->query("SELECT COUNT(*) FROM water_usage WHERE volume > 1000")->fetchColumn();

$porcentajeAlertas = ($totalConsumos > 0)
    ? round(($totalAlertas / $totalConsumos) * 100, 2)
    : 0;

/* Semáforo */
$nivel = "Bajo";
$color = "#2ecc71"; // verde

if ($porcentajeAlertas >= 30) {
    $nivel = "Alto";
    $color = "#e74c3c"; // rojo
} elseif ($porcentajeAlertas >= 10) {
    $nivel = "Medio";
    $color = "#f1c40f"; // amarillo
}
/* KPIs adicionales para análisis (OBLIGATORIOS) */
$consumosNormales = $totalConsumos - $totalAlertas;

$porcentajeNormales = ($totalConsumos > 0)
    ? round(($consumosNormales / $totalConsumos) * 100, 2)
    : 0;

$eficienciaSistema = 100 - $porcentajeAlertas;

$alertasPorUsuario = ($totalUsuarios > 0)
    ? round($totalAlertas / $totalUsuarios, 2)
    : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>QA – KPIs SIMCAP</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #eef2f5;
    margin: 0;
    padding: 20px;
}
h1, h2 {
    text-align: center;
}
h2 {
    color: #555;
    margin-top: 5px;
}

.dashboard {
    max-width: 1100px;
    margin: auto;
}

/* KPI cards */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 20px;
    margin-top: 30px;
}
.kpi-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    text-align: center;
}
.kpi-card h3 {
    margin: 0;
    color: #666;
    font-size: 16px;
}
.kpi-value {
    font-size: 34px;
    font-weight: bold;
    margin-top: 10px;
}

/* Barra de progreso */
.progress-box {
    margin-top: 15px;
    background: #ddd;
    border-radius: 20px;
    overflow: hidden;
}
.progress-bar {
    height: 20px;
    width: 0;
    background: #ccc;
    transition: width 1s ease;
}

/* KPI crítico */
.alert-box {
    background: #fff;
    margin-top: 30px;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}
.alert-level {
    font-size: 22px;
    font-weight: bold;

}

.footer {
    margin-top: 40px;
    text-align: center;
    color: #777;
    font-size: 14px;
}
</style>
</head>

<body>
<div class="dashboard">

<h1>SIMCAP – Control de Calidad</h1>
<h2>Indicadores Clave de Rendimiento (KPIs)</h2>

<!-- KPIs generales -->
<div class="kpi-grid">

    <div class="kpi-card">
        <h3>👤 Usuarios registrados</h3>
        <div class="kpi-value"><?= $totalUsuarios ?></div>
    </div>

    <div class="kpi-card">
        <h3>💧 Registros de consumo</h3>
        <div class="kpi-value"><?= $totalConsumos ?></div>
    </div>

    <div class="kpi-card">
        <h3>🚨 Alertas generadas</h3>
        <div class="kpi-value"><?= $totalAlertas ?></div>
    </div>

</div>

<!-- KPI CRÍTICO -->
<div class="alert-box">
    <h3>🔍 Porcentaje de consumos con alerta (>900 L)</h3>

    <div class="progress-box">
        <div class="progress-bar"
            style="width: <?= $porcentajeAlertas ?>%; background: <?= $color ?>;">
        </div>
    </div>

    <p><strong><?= $porcentajeAlertas ?>%</strong> de los consumos superan el umbral permitido.</p>

    <div class="alert-level" style="color: <?=  $color ?>;">
        Nivel de riesgo del sistema: <?= $nivel ?>
    </div>
</div>
<!-- SECCIÓN DE ANÁLISIS AVANZADO -->
<div class="alert-box">
    <h3>📊 Análisis adicional de KPIs</h3>

    <p><strong>Distribución de consumos</strong></p>

    <div class="progress-box">
        <div class="progress-bar" style="width: <?= $porcentajeNormales ?>%; background:#3498db;"></div>
    </div>
    <p>Consumos normales: <?= $porcentajeNormales ?>%</p>

    <div class="progress-box">
        <div class="progress-bar" style="width: <?= $porcentajeAlertas ?>%; background:#e74c3c;"></div>
    </div>
    <p>Consumos excesivos (alertas): <?= $porcentajeAlertas ?>%</p>

    <hr style="margin:20px 0;">

    <p><strong>⚙️ Eficiencia del sistema</strong></p>
    <div class="progress-box">
        <div class="progress-bar"
            style="width: <?= $porcentajeNormales ?>%; background:#3498db;">
        </div>
    </div>
    <p><?= $eficienciaSistema ?>% de los consumos están dentro de los límites permitidos</p>

    <hr style="margin:20px 0;">

    <p><strong>👤 Carga promedio de alertas por usuario</strong></p>
    <div class="kpi-value"><?= $alertasPorUsuario ?></div>
    <p>Alertas generadas en promedio por cada usuario</p>
</div>
<div class="footer">
    Dashboard QA – SIMCAP | Monitoreo de consumo excesivo de agua
</div>

</div>
</body>
</html>
