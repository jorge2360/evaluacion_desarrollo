CREATE DATABASE IF NOT EXISTS examen_pasteles;
USE examen_pasteles;

CREATE TABLE ingrediente (
    id_ingrediente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_ingreso DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE pastel (
    id_pastel INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    preparado_por VARCHAR(100) NOT NULL,
    fecha_creacion DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE pastel_ingrediente (
    id_pastel_ingrediente INT AUTO_INCREMENT PRIMARY KEY,
    id_pastel INT NOT NULL,
    id_ingrediente INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX (id_pastel),
    INDEX (id_ingrediente),

    FOREIGN KEY (id_pastel) 
        REFERENCES pastel(id_pastel) 
        ON DELETE CASCADE,

    FOREIGN KEY (id_ingrediente) 
        REFERENCES ingrediente(id_ingrediente) 
        ON DELETE CASCADE,

    UNIQUE (id_pastel, id_ingrediente)
);