<?php
class ConexionBD {
    private static $instancia = null;
    private $conexion;

    private $host = 'localhost';
    private $db = 'logimap';
    private $user = 'root';
    private $pass = '';

    private function __construct() {
        try {
            $this->conexion = new PDO(
                "mysql:host=$this->host;dbname=$this->db;charset=utf8",
                $this->user,
                $this->pass
            );
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Error en la conexión a la base de datos: ' . $e->getMessage());
        }
    }

    public static function getInstancia() {
        if (!self::$instancia) {
            self::$instancia = new ConexionBD();
        }
        return self::$instancia;
    }

    public function getConexion() {
        return $this->conexion;
    }
}
