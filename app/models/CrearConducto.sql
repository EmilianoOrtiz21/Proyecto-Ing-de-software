-- Archivo: C:\xampp\htdocs\Proyecto-Ing-de-software-main\app\models\CrearConductor.sql

CREATE TABLE conductor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    matricula VARCHAR(20) NOT NULL,
    estado ENUM('1', '2', '3') NOT NULL, -- 1: Activo, 2: Inhabilitado, 3: Inactivo
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
