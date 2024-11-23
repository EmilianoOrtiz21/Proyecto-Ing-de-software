<?php
require_once 'ConexionBD.php';

class AdminReportes {
    private $conexionBD;

    public function __construct() {
        // Usar la conexión de la clase ConexionBD
        $this->conexionBD = ConexionBD::getInstancia()->getConexion();
    }

    public function crearReporteConductor($usuario, $codigoPaquete, $descripcion) {
        // Validar los parámetros de entrada
        if (empty($usuario) || !is_string($usuario)) {
            throw new InvalidArgumentException(json_encode(['error' => 'El usuario es inválido.']));
        }

        if (empty($codigoPaquete) || !is_string($codigoPaquete)) {
            throw new InvalidArgumentException(json_encode(['error' => 'El código del paquete es inválido.']));
        }

        if (empty($descripcion) || !is_string($descripcion)) {
            throw new InvalidArgumentException(json_encode(['error' => 'La descripción es inválida.']));
        }

        try {
            $query = "
                INSERT INTO reporteconductor (usuario, codigo_paquete, descripcion)
                VALUES (:usuario, :codigo_paquete, :descripcion)
            ";
            $stmt = $this->conexionBD->prepare($query);

            $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $stmt->bindParam(':codigo_paquete', $codigoPaquete, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);

            $stmt->execute();

            return json_encode([
                'mensaje' => 'Reporte de conductor creado con éxito.',
                'id_reporte' => $this->conexionBD->lastInsertId()
            ]);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Error al crear el reporte: ' . $e->getMessage()]);
        }
    }

    public function crearReporteUsuario($codigoPaquete, $descripcion) {
        // Validar los parámetros de entrada
        if (empty($codigoPaquete) || !is_string($codigoPaquete)) {
            throw new InvalidArgumentException(json_encode(['error' => 'El código del paquete es inválido.']));
        }

        if (empty($descripcion) || !is_string($descripcion)) {
            throw new InvalidArgumentException(json_encode(['error' => 'La descripción es inválida.']));
        }

        try {
            $query = "
                INSERT INTO reporteusuario (codigo_paquete, descripcion)
                VALUES (:codigo_paquete, :descripcion)
            ";
            $stmt = $this->conexionBD->prepare($query);

            $stmt->bindParam(':codigo_paquete', $codigoPaquete, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);

            $stmt->execute();

            return json_encode([
                'mensaje' => 'Reporte de usuario creado con éxito.',
                'id_reporte' => $this->conexionBD->lastInsertId()
            ]);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Error al crear el reporte: ' . $e->getMessage()]);
        }
    }

    public function listarReportesConductores() {
        try {
            $query = "
                SELECT * FROM reporteconductor
            ";
            $stmt = $this->conexionBD->prepare($query);
            $stmt->execute();

            return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            return json_encode(['error' => 'Error al listar los reportes: ' . $e->getMessage()]);
        }
    }

    public function listarReportesUsuarios() {
        try {
            $query = "
                SELECT * FROM reporteusuario
            ";
            $stmt = $this->conexionBD->prepare($query);
            $stmt->execute();

            return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            return json_encode(['error' => 'Error al listar los reportes: ' . $e->getMessage()]);
        }
    }
}
?>
