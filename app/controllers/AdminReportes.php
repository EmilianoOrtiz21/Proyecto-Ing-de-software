<?php
require_once 'ConexionBD.php';

class AdminReportes {
    private $conexionBD;

    public function __construct() {
        $this->conexionBD = ConexionBD::getInstancia()->getConexion();
    }

    public function crearReporteConductor($idConductor, $datos) {
        // Lógica para crear reporte de conductor
    }

    public function crearReporteCliente($codigoPaquete, $datos) {
        // Lógica para crear reporte de cliente
    }

    public function listarReportesConductores() {
        // Lógica para listar reportes de conductores
    }

    public function listarReportesClientes() {
        // Lógica para listar reportes de clientes
    }
}
?>
