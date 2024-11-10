<?php
require_once 'ConexionBD.php';

class AdminPaquetes {
    private $conexionBD;
    private $paqueteBuscado;

    public function __construct() {
        $this->conexionBD = ConexionBD::getInstancia()->getConexion();
    }

    public function buscarPaquete($codigo) {
        // Lógica para buscar un paquete
    }

    public function fijaHorario($horario) {
        // Establecer el horario de entrega
    }

    public function listarPaquetes() {
        // Lógica para listar paquetes
    }

    public function dameEstadoEntrega($codigo) {
        // Retorna el estado de entrega de un paquete
    }
}
?>
