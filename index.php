<?php
require_once 'Aplicacion.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $app = new Aplicacion();
    $respuesta = $app->procesarSolicitud($data);
    header('Content-Type: application/json');
    echo json_encode($respuesta);
}
?>
