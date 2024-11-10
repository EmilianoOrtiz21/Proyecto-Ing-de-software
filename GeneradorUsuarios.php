<?php
require_once 'ConexionBD.php';

class GeneradorUsuarios {
    private $conexionBD;

    public function __construct() {
        $this->conexionBD = ConexionBD::getInstancia()->getConexion();
    }

    public function generarUsuario($datos) {
        // Lógica para crear un nuevo usuario
    }

    public function generarContrasena() {
        // Generar una contraseña segura
    }
}
?>
