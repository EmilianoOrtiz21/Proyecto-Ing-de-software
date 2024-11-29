<?php
require_once __DIR__ . '/../app/controllers/AdminPaquetes.php';
require_once __DIR__ . '/../app/models/Sesion.php';

$sesion = new Sesion();

if (!$sesion->estaActiva() || !$sesion->esConductor()) {
    header("Location: login.php");
    exit();
}

$idConductor = $sesion->obtenerIdConductor();
$adminPaquetes = new AdminPaquetes();
$paquetesJson = $adminPaquetes->verPaquetesAsignados($idConductor);
$paquetes = json_decode($paquetesJson, true);

if (isset($paquetes['error'])) {
    $mensajeError = $paquetes['error'];
    $paquetes = [];
} elseif (isset($paquetes['mensaje'])) {
    $mensajeInfo = $paquetes['mensaje'];
    $paquetes = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Paquetes</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Paquetes Asignados</h1>

    <!-- Mostrar errores o información -->
    <?php if (!empty($mensajeError)): ?>
        <p style="color: red;"><?= htmlspecialchars($mensajeError) ?></p>
    <?php elseif (!empty($mensajeInfo)): ?>
        <p><?= htmlspecialchars($mensajeInfo) ?></p>
    <?php endif; ?>

    <!-- Mostrar paquetes si existen -->
    <?php if (!empty($paquetes)): ?>
        <table>
            <thead>
                <tr>
                    <th>Código Único</th>
                    <th>Destinatario</th>
                    <th>Punto de Entrega</th>
                    <th>Fecha de Entrega</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paquetes as $paquete): ?>
                    <tr>
                        <td><?= htmlspecialchars($paquete['codigo_unico']) ?></td>
                        <td><?= htmlspecialchars($paquete['nombre_destinatario']) ?></td>
                        <td><?= htmlspecialchars($paquete['punto_entrega']) ?></td>
                        <td><?= htmlspecialchars($paquete['fecha_entrega']) ?></td>
                        <td><?= htmlspecialchars($paquete['estado_entrega']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <!-- Mostrar mensaje solo si no hay paquetes -->
        <?php if (empty($mensajeInfo) && empty($mensajeError)): ?>
            <p>No tienes paquetes asignados.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>

