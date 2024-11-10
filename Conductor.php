<?php
require_once 'ConexionBD.php';
require_once 'Paquete.php';

class Conductor {
    private $conexionBD;
    private $estadoEntrega;
    private $rutaActual;
    private $mejorPaquete;

    public function __construct($conexion) {
        // Inicializar conexión a la base de datos
    }

    public function calculaMejorPaquete() {
        // Lógica para seleccionar el mejor paquete
    }

    public function dameRutaActual() {
        // Devuelve la ruta actual
    }

    public function fijaEstadoEntrega($nuevoEstado) {
        // Cambiar el estado de la entrega
    }

    public function damePaquetes() {
        // Devuelve los paquetes asignados al conductor
    }

    public function dameUbicacionDestino() {
        // Retorna la ubicación del mejor paquete
    }
}
?>
