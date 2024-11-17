<?php
require_once 'ConexionBD.php';
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
            }
            else{
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
            $query->bindParam(':codigo', $_SESSION['codigoBuscado']  );
            $query->execute();
            $respuesta = $query->fetch(PDO::FETCH_ASSOC);
            // Si el paquete tiene horarios nulos, se puede actualizar
            if ($respuesta && ($respuesta['franja_horaria_min'] === NULL && $respuesta['franja_horaria_max'] === NULL)) {
                $query = $this->conexionBD->prepare("UPDATE paquete SET franja_horaria_min = :franja_horaria_min, franja_horaria_max = :franja_horaria_max WHERE codigo_unico = :codigo");
                if (isset($_SESSION['codigoBuscado'])) {
                    $query->bindParam(':codigo', $_SESSION['codigoBuscado']);
                    $query->bindParam(':franja_horaria_min', $horario['franja_horaria_min']);
                    $query->bindParam(':franja_horaria_max', $horario['franja_horaria_max']);
                    $query->execute();
                    if ($query->rowCount() > 0) {
                        $respuestaJson =  json_encode(['resultado' => "Horario actualizado"]);
                    } else {
                        $respuestaJson = json_encode(['resultado' => "No se pudo actualizar el horario"]);
                    }
                }
                else{
                    $respuestaJson = json_encode(['error' => 'No se ha encontrado el código del paquete en la sesión']);
                }
            }
            else{
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
            }
            else{
                $respuestaJson = json_encode($respuesta);
            }
        } catch (PDOException $e) {
             $respuestaJson = json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
        return $respuestaJson;
    }
}
?>
