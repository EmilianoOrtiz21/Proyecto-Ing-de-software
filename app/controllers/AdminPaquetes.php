<?php
require_once __DIR__ . '/../models/ConexionBD.php';



$paquetes = json_decode($adminPaquetes->verPaquetesAsignados($idConductor), true);

var_dump($paquetes);
die();

if (is_null($paquetes)) {
    echo "Error al obtener paquetes asignados.";
    $paquetes = [];
} elseif (isset($paquetes['mensaje'])) {
    echo htmlspecialchars($paquetes['mensaje']);
    $paquetes = [];
}



class AdminPaquetes {
    private $conexionBD;

    public function __construct() {
        $this->conexionBD = ConexionBD::getInstancia()->getConexion();
    }

    public function dameEstadoEntrega($codigo) {
        $respuestaJson = null;
        try {
            //obtener el estado de la entrega
            $query = $this->conexionBD->prepare("SELECT
            codigo_unico,
            estado_entrega,
            franja_horaria_min,
            franja_horaria_max
            FROM entregas WHERE id_paquete = (SELECT id FROM paquete WHERE codigo_unico = :codigo)");
            $query->bindParam(':codigo', $codigo);
            $query->execute();
            $respuesta = $query->fetch(PDO::FETCH_ASSOC);
            if (!$respuesta) {
                $respuestaJson = json_encode(['error' => 'No se encontró la entrega asociada al paquete']);
            } else {
                $respuestaJson = json_encode($respuesta);
            }
        } catch (PDOException $e) {
            $respuestaJson = json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
        //Almacenar el codigo del paquete, por si se requieren mas operaciones
        $_SESSION['codigoBuscado'] = $codigo;
        return $respuestaJson;
    }

    public function fijaHorario($horario) {
        $respuestaJson = null;
        try {
            $query = $this->conexionBD->prepare("SELECT franja_horaria_min, franja_horaria_max FROM paquete WHERE codigo_unico = :codigo");
            $query->bindParam(':codigo', $_SESSION['codigoBuscado']);
            $query->execute();
            $respuesta = $query->fetch(PDO::FETCH_ASSOC);
            if ($respuesta && ($respuesta['franja_horaria_min'] === NULL && $respuesta['franja_horaria_max'] === NULL)) {
                $query = $this->conexionBD->prepare("UPDATE paquete SET franja_horaria_min = :franja_horaria_min, franja_horaria_max = :franja_horaria_max WHERE codigo_unico = :codigo");
                if (isset($_SESSION['codigoBuscado'])) {
                    $query->bindParam(':codigo', $_SESSION['codigoBuscado']);
                    $query->bindParam(':franja_horaria_min', $horario['franja_horaria_min']);
                    $query->bindParam(':franja_horaria_max', $horario['franja_horaria_max']);
                    $query->execute();
                    if ($query->rowCount() > 0) {
                        $respuestaJson = json_encode(['resultado' => "Horario actualizado"]);
                    } else {
                        $respuestaJson = json_encode(['resultado' => "No se pudo actualizar el horario"]);
                    }
                } else {
                    $respuestaJson = json_encode(['error' => 'No se ha encontrado el código del paquete en la sesión']);
                }
            } else {
                $respuestaJson = json_encode(['resultado' => "El paquete ya tiene un horario asignado"]);
            }
        } catch (PDOException $e) {
            $respuestaJson = json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
        return $respuestaJson;
    }

    public function listarPaquetes() {
        $respuestaJson = null;
        try {
            $query = $this->conexionBD->prepare("SELECT codigo_unico, nombre_destinatario,  franja_horaria_min, franja_horaria_max FROM paquete");
            $query->execute();
            $respuesta = $query->fetchAll(PDO::FETCH_ASSOC);
            if (!$respuesta) {
                $respuestaJson = json_encode(['error' => "No se encontraron paquetes"]);
            } else {
                $respuestaJson = json_encode($respuesta);
            }
        } catch (PDOException $e) {
            $respuestaJson = json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
        return $respuestaJson;
    }

    // Método para ver paquetes asignados a un conductor

    public function verPaquetesAsignados($idConductor) {
        try {
            $sql = "SELECT
                        pc.id_paquete,
                        p.codigo_unico,
                        p.nombre_destinatario,
                        p.punto_entrega,
                        pc.fecha_entrega,
                        e.descripcion AS estado_entrega
                    FROM paquetesConductor pc
                    JOIN paquete p ON pc.id_paquete = p.id
                    JOIN estadoentrega e ON pc.id_estado_entrega = e.id
                    WHERE pc.id_conductor = :id_conductor";

            $stmt = $this->conexionBD->prepare($sql);
            $stmt->execute([':id_conductor' => $idConductor]);
            $paquetes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Aquí depuramos la variable
            var_dump($paquetes);
            die();

            if (empty($paquetes)) {
                return json_encode(["mensaje" => "No tienes paquetes asignados."]);
            }

            return json_encode($paquetes);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
    }

    


    public function actualizarEstadoPaquete($idPaquete, $nuevoEstado, $idConductor) {
        try {
            // Validar si el paquete está asignado al conductor
            $sqlVerificacion = "SELECT id_paquete FROM paquetesConductor WHERE id_paquete = :id_paquete AND id_conductor = :id_conductor";
            $stmtVerificacion = $this->conexionBD->prepare($sqlVerificacion);
            $stmtVerificacion->execute([
                ':id_paquete' => $idPaquete,
                ':id_conductor' => $idConductor
            ]);
            $paquete = $stmtVerificacion->fetch(PDO::FETCH_ASSOC);
    
            if (!$paquete) {
                throw new Exception("El paquete no está asignado a este conductor.");
            }
    
            // Actualizar estado del paquete
            $sqlActualizar = "UPDATE paquetesConductor SET id_estado_entrega = (SELECT id FROM estadoentrega WHERE descripcion = :nuevo_estado LIMIT 1)
                              WHERE id_paquete = :id_paquete";
            $stmtActualizar = $this->conexionBD->prepare($sqlActualizar);
            $stmtActualizar->execute([
                ':nuevo_estado' => $nuevoEstado,
                ':id_paquete' => $idPaquete
            ]);
    
            if ($stmtActualizar->rowCount() > 0) {
                return json_encode(["mensaje" => "Estado del paquete actualizado correctamente."]);
            } else {
                return json_encode(["error" => "No se pudo actualizar el estado del paquete."]);
            }
        } catch (Exception $e) {
            return json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
    }
    
}



?>
