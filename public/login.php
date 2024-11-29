<?php
session_start();
include_once realpath(__DIR__ . '/../app/models/Sesion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    $sesion = new Sesion();

    // Depuración: verifica que los datos lleguen correctamente
    var_dump($usuario, $contrasena);
    var_dump($_POST);


    if ($sesion->iniciarSesion($usuario, $contrasena)) {
        // Depuración: verifica que se entra en la redirección
        echo "Inicio de sesión exitoso, redirigiendo...";

        // Redirigir según el rol
        if ($sesion->esAdmin()) {
            header("Location: ../app/views/dashboard_admin.html");
            exit;
        } elseif ($sesion->esConductor()) {
            header("Location: ../app/views/panel_repartidor.html");
            exit;
        } else {
            header("Location: dashboard_cliente.php");
            exit;
        }
    } else {
        // Depuración: verifica que el inicio de sesión falló
        echo "Inicio de sesión fallido.";
        $error = urlencode("Credenciales incorrectas");
        header("Location: ../app/views/interfazPrueba_login.html?error=$error");
        exit;
    }
}
?>