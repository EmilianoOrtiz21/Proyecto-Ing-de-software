<?php
// Archivo: C:\xampp\htdocs\Proyecto-Ing-de-software-main\api\gestion_conductores.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../app/models/Conductor.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['accion'])) {
    echo json_encode(['error' => 'Acción no especificada.']);
    exit;
}

$accion = $input['accion'];
$conductor = new Conductor();

switch ($accion) {
    case 'generarUsuario':
        if (!isset($input['nombre']) || empty(trim($input['nombre']))) {
            echo json_encode(['error' => 'Nombre del conductor no proporcionado.']);
            exit;
        }

        $nombreConductor = trim($input['nombre']);
        $usuarioDatos = $conductor->generarUsuario($nombreConductor);

        echo json_encode($usuarioDatos);
        break;

    case 'agregar':
        $camposRequeridos = [
            'nombreConductor',
            'correoConductor',
            'telefonoConductor',
            'usuarioConductor',
            'contrasenaConductor',
            'matriculaConductor',
            'estadoConductor'
        ];

        foreach ($camposRequeridos as $campo) {
            if (!isset($input[$campo]) || empty(trim($input[$campo]))) {
                echo json_encode(['error' => "El campo {$campo} es requerido."]);
                exit;
            }
        }

        $datos = [
            'nombreConductor' => trim($input['nombreConductor']),
            'correoConductor' => trim($input['correoConductor']),
            'telefonoConductor' => trim($input['telefonoConductor']),
            'usuarioConductor' => trim($input['usuarioConductor']),
            'contrasenaConductor' => trim($input['contrasenaConductor']),
            'matriculaConductor' => trim($input['matriculaConductor']),
            'estadoConductor' => trim($input['estadoConductor'])
        ];

        $resultado = $conductor->agregarConductor($datos);
        echo json_encode($resultado);
        break;

    default:
        echo json_encode(['error' => 'Acción no reconocida.']);
        break;
}
?>
