<?php
require_once 'ConexionBD.php';
require_once 'Sesion.php';
require_once 'AdminConductores.php';
require_once 'AdminPaquetes.php';
require_once 'AdminReportes.php';

class Aplicacion {
    private $sesion;
    private $adminConductores;
    private $adminPaquetes;
    private $adminReportes;
    private $conexionBD;

    public function __construct() {
        // Crear una sola instancia de conexión a la base de datos
        $this->conexionBD = new ConexionDB();

        // Pasar la conexión a cada instancia de las clases administradoras
        $this->sesion = new Sesion();
        $this->adminConductores = new AdminConductores();
        $this->adminPaquetes = new AdminPaquetes();
        $this->adminReportes = new AdminReportes();
    }

    public function procesarSolicitud($solicitud) {
        $accion = $solicitud['accion'];
        switch ($accion) {
            case 'iniciarSesion':
                return $this->sesion->iniciarSesion($solicitud['usuario'], $solicitud['contrasena']);
            case 'cerrarSesion':
                return $this->sesion->cerrarSesion();
            case 'crearConductor':
                return $this->adminConductores->crearConductor($solicitud['datos']);
            case 'asignarPaquetes':
                return $this->adminConductores->asignaPaquetes();
            case 'buscarPaquete':
                return $this->adminPaquetes->buscarPaquete($solicitud['codigo']);
            case 'crearReporteConductor':
                return $this->adminReportes->crearReporteConductor($solicitud['idConductor'], $solicitud['datos']);
            default:
                return "Acción no reconocida";
        }
    }
}
?>
