<?php
require_once 'ConexionBD.php';

class Sesion {
    private $usuario;
    private $rol;
    private $conexionBD;

    public function __construct() {
        session_start();
        $this->conexionBD = ConexionBD::getInstancia()->getConexion();
    }

    public function iniciarSesion($usuario, $contrasena) {
        // Lógica para validar usuario y contraseña
    }

    public function cerrarSesion() {
        // Lógica para cerrar sesión
    }

    public function estaActiva() {
        // Verifica si hay sesión activa
    }

    public function obtenerUsuario() {
        return $this->usuario;
    }

    public function obtenerRol() {
        return $this->rol;
    }
}
?>
