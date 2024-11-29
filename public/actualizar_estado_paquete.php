<?php
require_once __DIR__ . '/../app/controllers/AdminPaquetes.php';
require_once __DIR__ . '/../app/models/Sesion.php';

$sesion = new Sesion();
$adminPaquetes = new AdminPaquetes();

$mensaje = '';
$paquetes = [];

// Verificar sesión activa y rol de conductor
if (!$sesion->estaActiva() || !$sesion->esConductor()) {
    header("Location: login.php");
    exit();
}

$idConductor = $sesion->obtenerIdConductor();

// Procesar formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPaquete = $_POST['id_paquete'] ?? null;
    $nuevoEstado = $_POST['nuevo_estado'] ?? null;

    if ($idPaquete && $nuevoEstado) {
        $respuesta = $adminPaquetes->actualizarEstadoPaquete($idPaquete, $nuevoEstado, $idConductor);
        $mensaje = json_decode($respuesta, true)['mensaje'] ?? json_decode($respuesta, true)['error'] ?? 'Error desconocido.';
    } else {
        $mensaje = 'Faltan datos para actualizar el estado del paquete.';
    }
}

// Obtener paquetes asignados al conductor
$paquetes = json_decode($adminPaquetes->verPaquetesAsignados($idConductor), true);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Estado de Paquetes</title>
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
        form {
            margin: 0;
        }
    </style>
</head>
<body>
    <h1>Actualizar Estado de Paquetes</h1>

    <?php if (!empty($mensaje)): ?>
        <p><strong>Respuesta:</strong> <?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <?php if (!empty($paquetes)): ?>
        <table>
            <thead>
                <tr>
                    <th>Código Único</th>
                    <th>Destinatario</th>
                    <th>Punto de Entrega</th>
                    <th>Fecha de Entrega</th>
                    <th>Estado Actual</th>
                    <th>Actualizar Estado</th>
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
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id_paquete" value="<?= htmlspecialchars($paquete['id_paquete']) ?>">
                                <select name="nuevo_estado" required>
                                    <option value="">Seleccionar estado</option>
                                    <option value="En camino">En camino</option>
                                    <option value="Entregado">Entregado</option>
                                    <option value="Fallido">Fallido</option>
                                </select>
                                <button type="submit">Actualizar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tienes paquetes asignados.</p>
    <?php endif; ?>
</body>
</html>
