<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/controllers/AdminConductores.php';

$adminConductores = new AdminConductores();
$response = [];

// Procesar solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['error'] = 'Error al decodificar los datos JSON.';
        echo json_encode($response);
        exit;
    }

    if (isset($postData['accion'])) {
        switch ($postData['accion']) {
            case 'agregar':
                $mensaje = $adminConductores->crearConductor(
                    $postData['nombreConductor'],
                    $postData['telefonoConductor'],
                    $postData['correoConductor'],
                    $postData['usuarioConductor'],
                    $postData['contrasenaConductor'],
                    $postData['matriculaConductor'],
                    $postData['estadoConductor']
                );
                $response['mensaje'] = $mensaje;
                break;
            case 'eliminar':
                $mensaje = $adminConductores->eliminarConductor($postData['id_conductor']);
                $response['mensaje'] = $mensaje;
                break;
            case 'editar':
                $resultado = json_decode($adminConductores->buscarConductor('c.id', $postData['id_conductor']), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $response['error'] = 'Error al decodificar los datos del conductor.';
                } elseif (isset($resultado[0])) {
                    $editarConductor = $resultado[0];
                    $response['editarConductor'] = $editarConductor;
                } else {
                    $response['mensaje'] = "No se encontró el conductor para editar.";
                }
                break;
            case 'actualizar':
                $mensaje = $adminConductores->actualizarConductor([
                    'id_conductor' => $postData['id_conductor'],
                    'nombre_completo' => $postData['nombre_completo'],
                    'telefono' => $postData['telefono'],
                    'correo' => $postData['correo'],
                    'id_estadoConductor' => $postData['id_estadoConductor']
                ]);
                $response['mensaje'] = $mensaje;
                break;
            case 'generarUsuario':
                $nombre = $postData['nombre'];
                // Generar usuario y contraseña automáticamente (Ejemplo simple)
                $usuario = strtolower(str_replace(' ', '_', $nombre)) . rand(100, 999);
                $contrasena = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                $response['usuario'] = $usuario;
                $response['contrasena'] = $contrasena;
                break;
            default:
                $response['error'] = 'Acción no válida.';
                break;
        }
    } else {
        $response['error'] = 'No se especificó ninguna acción.';
    }
    echo json_encode($response);
    exit;
}

// Obtener la lista de conductores si es GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conductores = json_decode($adminConductores->listarConductores(), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['error'] = 'Error al decodificar los datos de conductores.';
    } else {
        $response = $conductores;
    }
    echo json_encode($response);
    exit;
}
?>
