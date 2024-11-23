<?php
header('Content-Type: application/json');

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
                break;
            case 'asignarPaquetes':
                return $this->adminConductores->asignaPaquetes();
                break;
            case 'obtenerAsignacionPaquetes':
                return $this->adminConductores->dameAsignacionPaquetes();
            case 'asignarHorario':
                $horario['franja_horaria_min'] = $solicitud['franja_horaria_min'];
                $horario['franja_horaria_max'] = $solicitud['franja_horaria_max'];
                return $this->adminPaquetes->fijaHorario($horario);
            case 'obtenerEstadoEntrega':
                return $this->adminPaquetes->dameEstadoEntrega($solicitud['codigo']);
            case 'listarPaquetes':
                return $this->adminPaquetes->listarPaquetes();
            case 'crearReporteConductor':
                return $this->adminReportes->crearReporteConductor(
                    $solicitud['usuario'],
                    $solicitud['codigoPaquete'],
                    $solicitud['descripcion']
                );

            case 'crearReporteUsuario':
                return $this->adminReportes->crearReporteUsuario(
                    $solicitud['codigoPaquete'],
                    $solicitud['descripcion']
                );

            case 'listarReportesConductores':
                return $this->adminReportes->listarReportesConductores();

            case 'listarReportesUsuarios':
                return $this->adminReportes->listarReportesUsuarios();

            default:
                return "Acción no reconocda";
        }
    }
}
?>
