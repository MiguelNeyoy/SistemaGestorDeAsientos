CREATE TABLE IF NOT EXISTS config_asignacion (
    id INT PRIMARY KEY DEFAULT 1,
    publicado TINYINT(1) NOT NULL DEFAULT 0,
    fecha_asignacion DATETIME DEFAULT NULL,
    fecha_publicacion DATETIME DEFAULT NULL
);

INSERT INTO config_asignacion (id, publicado, fecha_asignacion, fecha_publicacion)
VALUES (1, 0, NULL, NULL)
ON DUPLICATE KEY UPDATE id=id;
