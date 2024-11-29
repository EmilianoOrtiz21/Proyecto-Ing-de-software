<?php
require_once __DIR__ . '/../models/ConexionBD.php';

class Sesion {
    private $usuario;
    private $rol;
    private $idConductor;
    private $conexionBD;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->conexionBD = ConexionBD::getInstancia()->getConexion();
        
        if ($this->estaActiva()) {
            $this->usuario = $_SESSION['usuario'];
            $this->rol = $_SESSION['rol'];
            
            if ($this->esConductor()) {
                $this->idConductor = $this->cargarIdConductor();
            }
        }
    }

    public function iniciarSesion($usuario, $contrasena) {
        try {
            $sql = "SELECT u.usuario, u.id_rol, u.contrasena 
                    FROM usuarios u 
                    WHERE u.usuario = :usuario";
            echo "Consulta SQL: $sql<br>";
    
            $stmt = $this->conexionBD->prepare($sql);
            echo "Consulta preparada.<br>";
    
            $stmt->execute([':usuario' => $usuario]);
            echo "Consulta ejecutada.<br>";
    
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($resultado) {
                echo "Resultado encontrado: ";
                var_dump($resultado);
    
                if (trim($contrasena) === trim($resultado['contrasena'])) {
                    echo "Contraseña válida, creando sesión...<br>";
                    $_SESSION['usuario'] = $resultado['usuario'];
                    $_SESSION['rol'] = $resultado['id_rol']; // Guardar id_rol en sesión
                    return true;
                } else {
                    echo "Contraseña inválida.<br>";
                }
            } else {
                echo "Usuario no encontrado.<br>";
            }
            return false;
        } catch (Exception $e) {
            echo "Excepción capturada: " . $e->getMessage() . "<br>";
            return false;
        }
    }
    
        
    
    

    public function cerrarSesion() {
        session_destroy();
    }

    public function estaActiva() {
        return isset($_SESSION['usuario']);
    }

    public function obtenerUsuario() {
        return $this->usuario;
    }

    public function obtenerRol() {
        return $this->rol;
    }

    public function esAdmin() {
        return $this->rol == 1; // id_rol para admin
    }
    
    public function esConductor() {
        return $this->rol == 2; // id_rol para conductor
    }
    
    public function esCliente() {
        return $this->rol == 3; // id_rol para cliente
    }
    

    public function obtenerIdConductor() {
        return $this->idConductor;
    }

    private function cargarIdConductor() {
        try {
            $sql = "SELECT c.id
                    FROM conductor c
                    JOIN usuarios u ON c.id_usuario = u.id
                    WHERE u.usuario = :usuario";
            $stmt = $this->conexionBD->prepare($sql);
            $stmt->execute([':usuario' => $this->usuario]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("Error al cargar ID del conductor: " . $e->getMessage());
            return null;
        }
    }
}
?>
