USE Logimap;

-- Crear tablas
CREATE TABLE rol (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO rol (nombre) VALUES
    ('administrador'),
    ('conductor'),
    ('usuario_comun');

CREATE TABLE EstadoConductor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(15) NOT NULL
);

CREATE TABLE EstadoEntrega (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(15) NOT NULL
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    correo VARCHAR(100),
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    id_rol INT NULL,
    FOREIGN KEY (id_rol) REFERENCES rol(id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE INDEX idx_usuarios_rol ON usuarios(id_rol);

CREATE TABLE conductor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_estadoConductor INT NOT NULL,
    matricula VARCHAR(20) NOT NULL UNIQUE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_estadoConductor) REFERENCES EstadoConductor(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE administrador (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Paquete (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_unico VARCHAR(50) UNIQUE NOT NULL,
    nombre_destinatario VARCHAR(255) NOT NULL,
    franja_horaria_min TIME,
    franja_horaria_max TIME,
    punto_entrega VARCHAR(255) NOT NULL
);

CREATE TABLE paquetesConductor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_paquete INT,
    id_conductor INT,
    fecha_entrega TIMESTAMP,
    id_estado_entrega INT NOT NULL,
    FOREIGN KEY (id_paquete) REFERENCES Paquete(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_conductor) REFERENCES conductor(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_estado_entrega) REFERENCES EstadoEntrega(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX idx_paquete_id ON paquetesConductor(id_paquete);
CREATE INDEX idx_conductor_id ON paquetesConductor(id_conductor);

CREATE TABLE ReporteConductor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL,
    codigo_paquete VARCHAR(50) NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (codigo_paquete) REFERENCES Paquete(codigo_unico) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX idx_reporte_conductor_codigo_paquete ON ReporteConductor(codigo_paquete);

CREATE TABLE ReporteUsuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_paquete VARCHAR(50) NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (codigo_paquete) REFERENCES Paquete(codigo_unico) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX idx_reporte_usuario_codigo_paquete ON ReporteUsuario(codigo_paquete);

-- Insertar valores iniciales
INSERT INTO EstadoConductor (descripcion) VALUES
    ('Activo'),
    ('Inhabilitado'),
    ('Inactivo');

INSERT INTO EstadoEntrega (descripcion) VALUES
    ('En camino'),
    ('Asignado'),
    ('Pendiente'),
    ('Entregado'),
    ('Fallido');

-- Crear vista
CREATE VIEW entregas AS
SELECT
    pc.id_paquete,
    ee.descripcion AS estado_entrega,
    p.codigo_unico,
    p.franja_horaria_min,
    p.franja_horaria_max
FROM paquetesConductor pc
JOIN EstadoEntrega ee ON pc.id_estado_entrega = ee.id
JOIN Paquete p ON p.id = pc.id_paquete;
