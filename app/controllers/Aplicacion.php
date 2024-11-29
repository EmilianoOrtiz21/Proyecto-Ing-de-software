<?php
require_once __DIR__ . '/../models/conexionBD.php';
require_once __DIR__ . '/../models/Sesion.php';
require_once __DIR__ . '/AdminConductores.php';
require_once __DIR__ . '/AdminPaquetes.php';
require_once __DIR__ . '/AdminReportes.php';

class Aplicacion {
    private $sesion;
    private $adminConductores;
    private $adminPaquetes;
    private $adminReportes;
    private $conexionBD;

    public function __construct() {
        $this->conexionBD = new ConexionBD();
        $this->sesion = new Sesion();
        $this->adminConductores = new AdminConductores();
        $this->adminPaquetes = new AdminPaquetes();
        $this->adminReportes = new AdminReportes();
    }

    public function procesarSolicitud($solicitud) {
        if (empty($solicitud['accion'])) {
            return "Acción no especificada";
        }

        $accion = $solicitud['accion'];
        switch ($accion) {
            case 'iniciarSesion':
                return $this->sesion->iniciarSesion($solicitud['usuario'], $solicitud['contrasena']);
            case 'cerrarSesion':
                return $this->sesion->cerrarSesion();
            case 'crearConductor':
                $nombre = $solicitud['datos']['nombreConductor'];
                $telefono = $solicitud['datos']['telefonoConductor'];
                $correo = $solicitud['datos']['correoConductor'];
                $usuario = $solicitud['datos']['usuarioConductor'];
                $contrasena = $solicitud['datos']['contrasenaConductor'];
                $matricula = $solicitud['datos']['matriculaConductor'];
                $estado = $solicitud['datos']['estadoConductor'];
                return $this->adminConductores->CrearConductor($nombre, $telefono, $correo, $usuario, $contrasena, $matricula, $estado);
            case 'cargarEstados':
                return $this->adminConductores->dameEstadosEntrega();
            case 'asignarHorario':
                return $this->adminPaquetes->asignarFranjaHoraria($solicitud['franja_horaria_min'], $solicitud['franja_horaria_max']);
            case 'obtenerEstadoEntrega':
                return $this->adminPaquetes->dameEstadoEntrega($solicitud['codigo']);
            case 'listarPaquetes':
                return $this->adminPaquetes->listarPaquetes();
            case 'crearReporteConductor':
                return $this->adminReportes->crearReporteConductor($solicitud['idConductor'], $solicitud['datos']);
            default:
                return "Acción no reconocida";
        }
    }
}
?>
