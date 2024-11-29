<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/../models/ConexionBD.php';

class AdminConductores {
    private $conexionBD;

    public function __construct() {
        $this->conexionBD = ConexionBD::getInstancia()->getConexion();
    }

    public function crearConductor($nombre_completo, $telefono, $correo, $usuario, $contrasena, $matricula, $id_estadoConductor) {
        try {
            // Validar los datos obligatorios
            if (empty($nombre_completo) || empty($usuario) || empty($contrasena) || empty($id_estadoConductor)) {
                throw new Exception("Faltan datos obligatorios para crear el conductor.");
            }
    
            // Encriptar la contraseña
            $usarEncriptacion = false; // Cambia a true para activar la encriptación

            if ($usarEncriptacion) {
                $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);
            } else {
                $contrasenaHash = $contrasena; // Sin encriptar
            }
            
    
            // Iniciar transacción
            $this->conexionBD->beginTransaction();
    
            // Validar si el estado especificado ya existe en estadoconductor
            $sqlEstado = "SELECT id FROM estadoconductor WHERE id = :id_estadoConductor";
            $stmtEstado = $this->conexionBD->prepare($sqlEstado);
            $stmtEstado->execute([':id_estadoConductor' => $id_estadoConductor]);
    
            if (!$stmtEstado->fetch()) {
                // Si el estado no existe, agregarlo dinámicamente
                $descripcionEstado = $this->obtenerDescripcionEstado($id_estadoConductor);
                $sqlInsertEstado = "INSERT INTO estadoconductor (id, descripcion) VALUES (:id_estadoConductor, :descripcion)";
                $stmtInsertEstado = $this->conexionBD->prepare($sqlInsertEstado);
                $stmtInsertEstado->execute([
                    ':id_estadoConductor' => $id_estadoConductor,
                    ':descripcion' => $descripcionEstado
                ]);
            }
    
            // Insertar en la tabla usuarios
            $sqlUsuario = "INSERT INTO usuarios (nombre_completo, telefono, correo, usuario, contrasena, id_rol)
                           VALUES (:nombre_completo, :telefono, :correo, :usuario, :contrasena,
                                   (SELECT id FROM rol WHERE nombre = 'conductor' LIMIT 1))";
            $stmtUsuario = $this->conexionBD->prepare($sqlUsuario);
            $stmtUsuario->execute([
                ':nombre_completo' => $nombre_completo,
                ':telefono' => $telefono,
                ':correo' => $correo,
                ':usuario' => $usuario,
                ':contrasena' => $contrasenaHash,
            ]);
    
            // Obtener el ID del usuario insertado
            $id_usuario = $this->conexionBD->lastInsertId();
    
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
            return json_encode(["mensaje" => "Conductor creado exitosamente."]);
        } catch (Exception $e) {
            // Verificar si hay una transacción activa antes de hacer rollback
            if ($this->conexionBD->inTransaction()) {
                $this->conexionBD->rollBack();
            }
            return json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
    }
    
    // Método para obtener la descripción del estado según el ID
    private function obtenerDescripcionEstado($id_estadoConductor) {
        switch ($id_estadoConductor) {
            case 1:
                return 'Activo';
            case 2:
                return 'Inhabilitado';
            case 3:
                return 'Inactivo';
            default:
                throw new Exception("Estado desconocido.");
        }
    }
    
    
    public function actualizarConductor($datos) {
        try {
            // Validar que se proporcionen todos los datos necesarios
            if (empty($datos['id_conductor']) || empty($datos['nombre_completo']) || empty($datos['telefono']) || empty($datos['correo']) || empty($datos['id_estadoConductor'])) {
                throw new Exception("Faltan datos obligatorios para actualizar el conductor.");
            }
    
            // Iniciar transacción
            $this->conexionBD->beginTransaction();
    
            // Actualizar la tabla usuarios
            $sqlUsuario = "UPDATE usuarios 
                           SET nombre_completo = :nombre_completo, telefono = :telefono, correo = :correo 
                           WHERE id = (SELECT id_usuario FROM conductor WHERE id = :id_conductor)";
            $stmtUsuario = $this->conexionBD->prepare($sqlUsuario);
            $stmtUsuario->execute([
                ':nombre_completo' => $datos['nombre_completo'],
                ':telefono' => $datos['telefono'],
                ':correo' => $datos['correo'],
                ':id_conductor' => $datos['id_conductor']
            ]);
    
            // Validar si el estado especificado ya existe en estadoconductor
            $sqlEstado = "SELECT id FROM estadoconductor WHERE id = :id_estadoConductor";
            $stmtEstado = $this->conexionBD->prepare($sqlEstado);
            $stmtEstado->execute([':id_estadoConductor' => $datos['id_estadoConductor']]);
    
            if (!$stmtEstado->fetch()) {
                // Si el estado no existe, agregarlo dinámicamente
                $descripcionEstado = $this->obtenerDescripcionEstado($datos['id_estadoConductor']);
                $sqlInsertEstado = "INSERT INTO estadoconductor (id, descripcion) VALUES (:id_estadoConductor, :descripcion)";
                $stmtInsertEstado = $this->conexionBD->prepare($sqlInsertEstado);
                $stmtInsertEstado->execute([
                    ':id_estadoConductor' => $datos['id_estadoConductor'],
                    ':descripcion' => $descripcionEstado
                ]);
            }
    
            // Actualizar el estado en la tabla conductor
            $sqlConductor = "UPDATE conductor 
                             SET id_estadoConductor = :id_estadoConductor 
                             WHERE id = :id_conductor";
            $stmtConductor = $this->conexionBD->prepare($sqlConductor);
            $stmtConductor->execute([
                ':id_estadoConductor' => $datos['id_estadoConductor'],
                ':id_conductor' => $datos['id_conductor']
            ]);
    
            // Confirmar transacción
            $this->conexionBD->commit();
            return json_encode(["mensaje" => "Conductor actualizado exitosamente."]);
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            if ($this->conexionBD->inTransaction()) {
                $this->conexionBD->rollBack();
            }
            return json_encode(['error' => 'Error al actualizar conductor: ' . $e->getMessage()]);
        }
    }    
    


    // Eliminar un conductor y sus relaciones
    public function eliminarConductor($idConductor) {
        try {
            if (empty($idConductor)) {
                throw new Exception("ID de conductor no proporcionado.");
            }
    
            // Iniciar transacción
            $this->conexionBD->beginTransaction();
    
            // Verificar si el conductor existe y obtener su id_usuario
            $sqlConductor = "SELECT id_usuario FROM conductor WHERE id = :id_conductor";
            $stmtConductor = $this->conexionBD->prepare($sqlConductor);
            $stmtConductor->execute([':id_conductor' => $idConductor]);
            $idUsuario = $stmtConductor->fetchColumn();
    
            if (!$idUsuario) {
                throw new Exception("No se encontró el conductor especificado.");
            }
    
            // Eliminar relaciones en paquetesConductor
            $sqlDeletePaquetes = "DELETE FROM paquetesConductor WHERE id_conductor = :id_conductor";
            $stmtDeletePaquetes = $this->conexionBD->prepare($sqlDeletePaquetes);
            $stmtDeletePaquetes->execute([':id_conductor' => $idConductor]);
    
            // Eliminar el conductor
            $sqlDeleteConductor = "DELETE FROM conductor WHERE id = :id_conductor";
            $stmtDeleteConductor = $this->conexionBD->prepare($sqlDeleteConductor);
            $stmtDeleteConductor->execute([':id_conductor' => $idConductor]);
    
            // Eliminar el usuario asociado
            $sqlDeleteUsuario = "DELETE FROM usuarios WHERE id = :id_usuario";
            $stmtDeleteUsuario = $this->conexionBD->prepare($sqlDeleteUsuario);
            $stmtDeleteUsuario->execute([':id_usuario' => $idUsuario]);
    
            // Confirmar transacción
            $this->conexionBD->commit();
            return json_encode(["mensaje" => "Conductor y registros relacionados eliminados exitosamente."]);
        } catch (Exception $e) {
            $this->conexionBD->rollBack();
            return json_encode(['error' => 'Error al eliminar conductor: ' . $e->getMessage()]);
        }
    }
    

    // Listar conductores
    public function listarConductores() {
        try {
            $sql = "SELECT c.id, u.nombre_completo, u.telefono, u.correo, c.matricula, e.descripcion AS estado
                    FROM conductor c
                    JOIN usuarios u ON c.id_usuario = u.id
                    JOIN estadoconductor e ON c.id_estadoConductor = e.id";
            $stmt = $this->conexionBD->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                return json_encode(['mensaje' => 'No se encontraron conductores registrados.']);
            }
            return json_encode($resultados);
        } catch (Exception $e) {
            return json_encode(['error' => 'Error al listar conductores: ' . $e->getMessage()]);
        }
    }

    // Buscar un conductor
    public function buscarConductor($criterio, $valor) {
        try {
            $sql = "SELECT c.id, u.nombre_completo, u.telefono, u.correo, c.matricula, e.descripcion AS estado
                    FROM conductor c
                    JOIN usuarios u ON c.id_usuario = u.id
                    JOIN estadoconductor e ON c.id_estadoConductor = e.id
                    WHERE $criterio = :valor";
            $stmt = $this->conexionBD->prepare($sql);
            $stmt->execute([':valor' => $valor]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                return json_encode(['mensaje' => 'No se encontraron conductores con ese criterio.']);
            }
            return json_encode($resultados);
        } catch (Exception $e) {
            return json_encode(['error' => 'Error al buscar conductor: ' . $e->getMessage()]);
        }
    }
}
