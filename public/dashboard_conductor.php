<?php
require_once __DIR__ . '/../app/models/Sesion.php';

$sesion = new Sesion();

// Verificar si la sesión está activa y si el usuario es conductor
if (!$sesion->estaActiva() || !$sesion->esConductor()) {
    header("Location: login.php");
    exit();
}

$usuario = $sesion->obtenerUsuario();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard del Conductor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 1rem 0;
            text-align: center;
        }
        main {
            padding: 2rem;
        }
        nav ul {
            list-style: none;
            padding: 0;
        }
        nav ul li {
            margin-bottom: 10px;
        }
        nav ul li a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenido, <?= htmlspecialchars($usuario) ?></h1>
    </header>
    <main>
        <h2>Panel del Conductor</h2>
        <p>Selecciona una de las siguientes opciones:</p>
        <nav>
            <ul>
                <li><a href="ver_paquetes.php">Ver Paquetes Asignados</a></li>
                <li><a href="actualizar_estado_paquete.php">Actualizar Estado de Paquete</a></li>
                <li><a href="reportar_problema.php">Reportar Problema</a></li>
            </ul>
        </nav>
    </main>
    <footer>
        <p>&copy; <?= date('Y') ?> Proyecto Ing. de Software</p>
    </footer>
</body>
</html>
