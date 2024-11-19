<?php
class ConexionBD {
    private $host = '127.0.0.1';
    private $dbname = 'logimap';
    private $user = 'postgres';
    private $password = '';
    private $conexion;
    private static $instancia = null;

    // Constructor privado para evitar instanciaci�n directa
    private function __construct() {
        $this->conectar();
    }

    // M�todo para obtener la instancia �nica (Singleton)
    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new ConexionBD();
        }
        return self::$instancia;
    }

    // M�todo para conectar a la base de datos
    private function conectar() {
        try {
            $this->conexion = new PDO(
                "pgsql:host={$this->host};dbname={$this->dbname}",
                $this->user,
                $this->password
            );
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Error de conexi�n: " . $exception->getMessage();
            $this->conexion = null;
        }
    }

    // M�todo para obtener la conexi�n
    public function getConexion() {
        return $this->conexion;
    }

    // M�todo para cerrar la conexi�n
    public function cerrarConexion() {
        $this->conexion = null;
    }
}
?>
