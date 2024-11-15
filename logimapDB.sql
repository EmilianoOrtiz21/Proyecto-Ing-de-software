CREATE DATABASE Logimap;

CREATE TABLE rol (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO rol (nombre) VALUES
    ('administrador'),
    ('conductor'),
    ('usuario_comun');

CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    correo VARCHAR(100),
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES rol(id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE INDEX idx_usuarios_rol ON usuarios(id_rol);

CREATE TABLE conductor (
    id SERIAL PRIMARY KEY,
    id_usuario INT NOT NULL,
    matricula VARCHAR(20) NOT NULL UNIQUE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_estadoConductor) REFERENCES EstadoConductor(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE administrador (
    id SERIAL PRIMARY KEY,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE EstadoConductor (
    id SERIAL PRIMARY KEY,
    descripcion VARCHAR(15) NOT NULL
);

CREATE TABLE EstadoEntrega (
    id SERIAL PRIMARY KEY,
    descripcion VARCHAR(15) NOT NULL
);

CREATE TABLE Paquete (
    id SERIAL PRIMARY KEY,
    codigo_unico VARCHAR(50) UNIQUE NOT NULL,
    nombre_destinatario VARCHAR(255) NOT NULL,
    franja_horaria_min TIME,
    franja_horaria_max TIME,
    punto_entrega VARCHAR(255) NOT NULL
);

CREATE TABLE paquetesConductor (
    id SERIAL PRIMARY KEY,
    id_paquete INT NOT NULL REFERENCES Paquete(id) ON DELETE CASCADE ON UPDATE CASCADE,
    id_conductor INT NOT NULL REFERENCES Conductor(id) ON DELETE CASCADE ON UPDATE CASCADE,
    id_estado_entrega INT REFERENCES EstadoEntrega(id)
);

CREATE INDEX idx_paquete_id ON paquetesConductor(id_paquete);
CREATE INDEX idx_conductor_id ON paquetesConductor(id_conductor);

CREATE TABLE ReporteConductor (
    id SERIAL PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL,
    codigo_paquete VARCHAR(50) NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (codigo_paquete) REFERENCES Paquete(codigo_unico) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX idx_reporte_conductor_codigo_paquete ON ReporteConductor(codigo_paquete);

CREATE TABLE ReporteUsuario (
    id SERIAL PRIMARY KEY,
    codigo_paquete VARCHAR(50) NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (codigo_paquete) REFERENCES Paquete(codigo_unico) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX idx_reporte_usuario_codigo_paquete ON ReporteUsuario(codigo_paquete);

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
