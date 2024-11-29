<?php
// Archivo: C:\xampp\htdocs\Proyecto-Ing-de-software-main\app\models\Conductor.php

require_once 'ConexionBD.php';

class Conductor {
    private $conexionBD;

    public function __construct() {
        $this->conexionBD = ConexionBD::getInstancia()->getConexion();
    }

    public function generarUsuario($nombreConductor) {
        $usuarioBase = strtolower(str_replace(' ', '.', $nombreConductor));
        $usuario = $usuarioBase . rand(100, 999);
        $contrasena = bin2hex(random_bytes(4));

        return [
            'usuario' => $usuario,
            'contrasena' => $contrasena
        ];
    }

    public function agregarConductor($datos) {
        try {
            $query = $this->conexionBD->prepare("SELECT id FROM conductor WHERE email = :email");
            $query->execute([':email' => $datos['correoConductor']]);
            if ($query->fetch(PDO::FETCH_ASSOC)) {
                return ['error' => 'El correo electrónico ya está registrado.'];
            }

            $insert = $this->conexionBD->prepare("
                INSERT INTO conductor (nombre, email, telefono, usuario, contrasena, matricula, estado)
                VALUES (:nombre, :email, :telefono, :usuario, :contrasena, :matricula, :estado)
            ");
            $insert->execute([
                ':nombre' => $datos['nombreConductor'],
                ':email' => $datos['correoConductor'],
                ':telefono' => $datos['telefonoConductor'],
                ':usuario' => $datos['usuarioConductor'],
                ':contrasena' => password_hash($datos['contrasenaConductor'], PASSWORD_BCRYPT),
                ':matricula' => $datos['matriculaConductor'],
                ':estado' => $datos['estadoConductor']
            ]);

            if ($insert->rowCount() > 0) {
                return ['mensaje' => 'Conductor creado exitosamente.'];
            } else {
                return ['error' => 'No se pudo crear el conductor.'];
            }
        } catch (PDOException $e) {
            return ['error' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
}
?>
