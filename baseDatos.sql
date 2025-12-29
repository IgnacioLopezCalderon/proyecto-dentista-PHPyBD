CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido1 VARCHAR(100) NOT NULL,
    apellido2 VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('cliente', 'administrador') DEFAULT 'cliente'
);




INSERT INTO usuario (nombre, apellido1, email, password, tipo_usuario)
VALUES ('Admin', 'Jefe', 'admin@clinica.com', '123456', 'administrador');




CREATE TABLE servicio (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL
);

INSERT INTO servicio (tipo, precio) VALUES
('Limpieza General', 45.00), ('Empaste', 60.00), ('Extracción', 30.00);




CREATE TABLE estado_cita (
    id_estado INT AUTO_INCREMENT PRIMARY KEY,
    estado VARCHAR(50) NOT NULL
);

INSERT INTO estado_cita (estado) VALUES ('Pendiente'), ('Confirmada'), ('Rechazada'), ('Finalizada');






CREATE TABLE cita (
    id_cita INT AUTO_INCREMENT PRIMARY KEY,
    fecha_cita DATETIME NOT NULL UNIQUE,
    id_usuario INT NOT NULL,
    id_estado INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_cita_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    CONSTRAINT fk_cita_estado FOREIGN KEY (id_estado) REFERENCES estado_cita(id_estado)
);




CREATE TABLE cita_tiene_servicio (
    id_cita_servicio INT AUTO_INCREMENT PRIMARY KEY,
    id_cita INT NOT NULL,
    id_servicio INT NOT NULL,
    CONSTRAINT fk_cts_cita FOREIGN KEY (id_cita) REFERENCES cita(id_cita) ON DELETE CASCADE,
    CONSTRAINT fk_cts_servicio FOREIGN KEY (id_servicio) REFERENCES servicio(id_servicio)
);

