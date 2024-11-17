<?php
require_once 'ConexionBD.php';

class AdminConductores {
    private $conexionBD;

    public function __construct() {
        $this->conexionBD = ConexionBD::getInstancia()->getConexion();
    }

    public function crearConductor($nombre_completo, $telefono, $correo, $usuario, $contrasena, $matricula, $id_estadoConductor) {
        $respuestaJson = null;
        try {
            // Iniciar transacción
            $this->conexionBD->beginTransaction();

            // Insertar en la tabla usuarios
            $sqlUsuario = "INSERT INTO usuarios (nombre_completo, telefono, correo, usuario, contrasena, id_rol)
                           VALUES (:nombre_completo, :telefono, :correo, :usuario, :contrasena,
                                   (SELECT id FROM rol WHERE nombre = 'conductor' LIMIT 1))
                           RETURNING id";
            $stmtUsuario = $this->conexionBD->prepare($sqlUsuario);
            $stmtUsuario->execute([
                ':nombre_completo' => $nombre_completo,
                ':telefono' => $telefono,
                ':correo' => $correo,
                ':usuario' => $usuario,
                ':contrasena' => $contrasena,
            ]);

            // Obtener el id del usuario insertado
            $id_usuario = $stmtUsuario->fetchColumn();

            // Insertar en la tabla conductor
            $sqlConductor = "INSERT INTO conductor (id_usuario, id_estadoConductor, matricula)
                             VALUES (:id_usuario, :id_estadoConductor, :matricula)";
            $stmtConductor = $this->conexionBD->prepare($sqlConductor);
            $stmtConductor->execute([
                ':id_usuario' => $id_usuario,
                ':id_estadoConductor' => $id_estadoConductor,
                ':matricula' => $matricula,
            ]);

            // Confirmar transacción
            $this->conexionBD->commit();
            $respuestaJson = json_encode(["mensaje" => "Conductor creado exitosamente."]);
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->conexionBD->rollBack();
            $respuestaJson = json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
        return $respuestaJson;
    }

    public function dameEstadosEntrega() {
        try {
            $query = $this->conexionBD->prepare("SELECT id, descripcion FROM EstadoConductor");
            $query->execute();
            $resultados = $query->fetchAll(PDO::FETCH_ASSOC);
            return json_encode($resultados);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
    }

    public function asignaPaquetes() {
        try {
            // Obtener el conteo de paquetes pendientes
            $query = $this->conexionBD->prepare("SELECT id_paquete FROM entregas WHERE estado_entrega = 'Pendiente'");
            $query->execute();
            $paquetesPendientes = $query->fetchAll(PDO::FETCH_ASSOC);
            // Obtener la lista de conductores
            $query = $this->conexionBD->prepare("SELECT c.id FROM conductor c JOIN estadoConductor e ON c.id_estadoConductor = e.id;");
            $query->execute();
            $conductores = $query->fetchAll(PDO::FETCH_ASSOC);
            // Verificar si hay paquetes y conductores disponibles
            if (!empty($paquetesPendientes) && !empty($conductores)) {
                $numPaquetes = count($paquetesPendientes);
                $numConductores = count($conductores);
                $fechaEntrega = date('Y-m-d H:i:s');
                // Variables para asignar paquetes
                $indexConductor = 0;
                foreach ($paquetesPendientes as $paquete) {
                    $idPaquete = $paquete['id_paquete'];
                    $idConductor = $conductores[$indexConductor]['id'];
                    // Actualizar la tabla paquetesConductor con el id del conductor y la fecha de entrega
                    $query = $this->conexionBD->prepare("
                        UPDATE paquetesConductor
                        SET id_conductor = :id_conductor, fecha_entrega = :fecha_entrega
                        WHERE id_paquete = :id_paquete
                    ");
                    $query->bindParam(':id_conductor', $idConductor);
                    $query->bindParam(':fecha_entrega', $fechaEntrega);
                    $query->bindParam(':id_paquete', $idPaquete);
                    $query->execute();
                    $indexConductor = ($indexConductor + 1) % $numConductores;
                }
            } else {
                echo json_encode(['error' => 'No hay paquetes pendientes o conductores disponibles']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
    }


    public function actualizarConductor($datos) {
        // Lógica para actualizar conductor
    }

    public function eliminarConductor($idConductor) {
        // Lógica para eliminar conductor
    }

    public function buscarConductor($criterio, $valor) {
        // Lógica para buscar conductor
    }

    public function listarConductores() {
        // Lógica para listar todos los conductores
    }
}
?>
