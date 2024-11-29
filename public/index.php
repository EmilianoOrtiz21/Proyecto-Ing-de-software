<?php
// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Procesar la ruta solicitada
$uri = str_replace('/Proyecto-Ing-de-software-main/public/index.php', '', $_SERVER['REQUEST_URI']);
$uri = str_replace('/Proyecto-Ing-de-software-main/public/', '', $uri);
$uri = trim($uri, '/');

// Página principal (ruta raíz)
if ($uri === '' || $uri === 'home') {
    require_once __DIR__ . '/../app/views/index.html';
    exit;
}

// Rutas permitidas
$rutas = [
    'login' => __DIR__ . '/../app/views/interfazPrueba_login.html',
    'dashboard_admin' => __DIR__ . '/../public/dashboard_admin.php',
    'conductores' => __DIR__ . '/../public/gestion_conductores.php',
    'prueba' => __DIR__ . '/../public/prueba.php',
];



// Obtener la ruta desde el parámetro `route`
$route = $_GET['route'] ?? '';

if (isset($rutas[$route])) {
    $archivo = $rutas[$route];
    if (file_exists($archivo)) {
        require_once $archivo; // Carga la vista nueva
    } else {
        echo "Archivo no encontrado: " . htmlspecialchars($archivo);
    }
} else {
    http_response_code(404);
    echo "Página no encontrada";
}

$route = $_GET['route'] ?? '';
var_dump($route); // Depuración: muestra la ruta solicitada

