<?php
class ConexionBD {
    private $host = '127.0.0.1';
    private $dbname = 'logimap';
    private $user = 'postgres';
    private $password = '';
    private $conexion;
    private static $instancia = null;

    // Constructor privado para evitar instanciación directa
    private function __construct() {
        $this->conectar();
    }

    // Método para obtener la instancia única (Singleton)
    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new ConexionBD();
        }
        return self::$instancia;
    }

    // Método para conectar a la base de datos
    private function conectar() {
        try {
            $this->conexion = new PDO(
                "pgsql:host={$this->host};dbname={$this->dbname}",
                $this->user,
                $this->password
            );
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
            $this->conexion = null;
        }
    }

    // Método para obtener la conexión
    public function getConexion() {
        return $this->conexion;
    }

    // Método para cerrar la conexión
    public function cerrarConexion() {
        $this->conexion = null;
    }
}
?>
