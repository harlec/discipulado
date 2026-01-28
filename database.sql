-- =====================================================
-- SISTEMA DE DISCIPULADO Y LIDERAZGO
-- Base de datos MySQL
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- Tabla: roles
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'admin', 'Administrador del sistema'),
(2, 'maestro', 'Maestro de cursos'),
(3, 'lider', 'Líder de grupo familiar'),
(4, 'alumno', 'Estudiante de la escuela');

-- -----------------------------------------------------
-- Tabla: miembros (Base principal)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `miembros` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `apellidos` VARCHAR(100) NOT NULL,
  `nombres` VARCHAR(100) NOT NULL,
  `fecha_nacimiento` DATE NULL,
  `celular` VARCHAR(20) NULL,
  `email` VARCHAR(100) NULL,
  `password` VARCHAR(255) NULL,
  `fecha_conversion` DATE NULL,
  `bautismo_agua` DATE NULL,
  `bautismo_espiritu_santo` YEAR NULL,
  `dones_habilidades` TEXT NULL,
  `cargo_iglesia` VARCHAR(100) NULL,
  `grupo_familiar` INT UNSIGNED NULL,
  `lider_id` INT UNSIGNED NULL,
  `grado_instruccion` ENUM('sin_estudios', 'inicial', 'primaria', 'secundaria', 'superior') NULL,
  `ocupacion` VARCHAR(100) NULL,
  `estado_civil` ENUM('soltero', 'conviviente', 'casado', 'separado', 'divorciado', 'viudo') NULL,
  `rol_id` INT UNSIGNED NOT NULL DEFAULT 4,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_lider` (`lider_id`),
  INDEX `idx_rol` (`rol_id`),
  INDEX `idx_grupo` (`grupo_familiar`),
  CONSTRAINT `fk_miembro_lider` FOREIGN KEY (`lider_id`) REFERENCES `miembros` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_miembro_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario admin por defecto (password: admin123)
INSERT INTO `miembros` (`apellidos`, `nombres`, `email`, `password`, `rol_id`) VALUES
('Administrador', 'Sistema', 'admin@iglesia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- -----------------------------------------------------
-- Tabla: niveles
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `niveles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `orden` INT NOT NULL,
  `descripcion` TEXT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `niveles` (`id`, `nombre`, `orden`, `descripcion`) VALUES
(1, 'Discipulado Básico', 1, 'Nivel inicial de formación'),
(2, 'Doctrina', 2, 'Fundamentos doctrinales'),
(3, 'Diakonía', 3, 'Servicio y ministerio'),
(4, 'Liderazgo I', 4, 'Primer nivel de liderazgo'),
(5, 'Liderazgo II', 5, 'Segundo nivel de liderazgo'),
(6, 'Liderazgo III', 6, 'Tercer nivel de liderazgo (4 módulos)');

-- -----------------------------------------------------
-- Tabla: cursos
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cursos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nivel_id` INT UNSIGNED NOT NULL,
  `modulo` TINYINT NULL COMMENT 'Solo para Liderazgo III (1-4)',
  `ciclo` VARCHAR(10) NOT NULL COMMENT 'Ej: 202610, 202620, 202630',
  `maestro_id` INT UNSIGNED NULL,
  `dia_clase` VARCHAR(20) NULL,
  `hora_inicio` TIME NULL,
  `hora_fin` TIME NULL,
  `dia_oracion` VARCHAR(20) NULL,
  `fecha_inicio` DATE NULL,
  `fecha_fin` DATE NULL,
  `horas_cronologicas` INT DEFAULT 24,
  `semanas` INT DEFAULT 12,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_nivel` (`nivel_id`),
  INDEX `idx_maestro` (`maestro_id`),
  INDEX `idx_ciclo` (`ciclo`),
  CONSTRAINT `fk_curso_nivel` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`id`),
  CONSTRAINT `fk_curso_maestro` FOREIGN KEY (`maestro_id`) REFERENCES `miembros` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Tabla: inscripciones
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `inscripciones` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `miembro_id` INT UNSIGNED NOT NULL,
  `curso_id` INT UNSIGNED NOT NULL,
  `fecha_inscripcion` DATE NOT NULL,
  `estado` ENUM('inscrito', 'cursando', 'aprobado', 'reprobado', 'retirado') NOT NULL DEFAULT 'inscrito',
  `nota_final` DECIMAL(4,2) NULL,
  `porcentaje_asistencia` DECIMAL(5,2) NULL,
  `observaciones` TEXT NULL,
  `fecha_actualizacion` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_miembro_curso` (`miembro_id`, `curso_id`),
  INDEX `idx_miembro` (`miembro_id`),
  INDEX `idx_curso` (`curso_id`),
  INDEX `idx_estado` (`estado`),
  CONSTRAINT `fk_inscripcion_miembro` FOREIGN KEY (`miembro_id`) REFERENCES `miembros` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_inscripcion_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Tabla: clases (sesiones de cada curso)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clases` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `curso_id` INT UNSIGNED NOT NULL,
  `numero` INT NOT NULL,
  `fecha` DATE NOT NULL,
  `tema` VARCHAR(255) NULL,
  `observaciones` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_curso` (`curso_id`),
  CONSTRAINT `fk_clase_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Tabla: asistencias
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `asistencias` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `inscripcion_id` INT UNSIGNED NOT NULL,
  `clase_id` INT UNSIGNED NOT NULL,
  `asistio` TINYINT(1) NOT NULL DEFAULT 0,
  `justificado` TINYINT(1) NOT NULL DEFAULT 0,
  `observacion` VARCHAR(255) NULL,
  `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_inscripcion_clase` (`inscripcion_id`, `clase_id`),
  INDEX `idx_inscripcion` (`inscripcion_id`),
  INDEX `idx_clase` (`clase_id`),
  CONSTRAINT `fk_asistencia_inscripcion` FOREIGN KEY (`inscripcion_id`) REFERENCES `inscripciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_asistencia_clase` FOREIGN KEY (`clase_id`) REFERENCES `clases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Tabla: tipo_calificaciones
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tipo_calificaciones` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `porcentaje` DECIMAL(5,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tipo_calificaciones` (`nombre`, `porcentaje`) VALUES
('Tareas', 30.00),
('Examen', 40.00),
('Participación', 20.00),
('Asistencia', 10.00);

-- -----------------------------------------------------
-- Tabla: calificaciones
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `calificaciones` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `inscripcion_id` INT UNSIGNED NOT NULL,
  `tipo_id` INT UNSIGNED NOT NULL,
  `descripcion` VARCHAR(255) NULL,
  `nota` DECIMAL(4,2) NOT NULL,
  `fecha` DATE NOT NULL,
  `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_inscripcion` (`inscripcion_id`),
  INDEX `idx_tipo` (`tipo_id`),
  CONSTRAINT `fk_calificacion_inscripcion` FOREIGN KEY (`inscripcion_id`) REFERENCES `inscripciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_calificacion_tipo` FOREIGN KEY (`tipo_id`) REFERENCES `tipo_calificaciones` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Vista: historial_alumno
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `v_historial_alumno` AS
SELECT
  m.id AS miembro_id,
  CONCAT(m.apellidos, ', ', m.nombres) AS alumno,
  n.nombre AS nivel,
  c.modulo,
  c.ciclo,
  i.estado,
  i.nota_final,
  i.porcentaje_asistencia,
  c.fecha_inicio,
  c.fecha_fin
FROM miembros m
JOIN inscripciones i ON m.id = i.miembro_id
JOIN cursos c ON i.curso_id = c.id
JOIN niveles n ON c.nivel_id = n.id
ORDER BY m.id, n.orden, c.modulo;

-- -----------------------------------------------------
-- Vista: resumen_curso
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `v_resumen_curso` AS
SELECT
  c.id AS curso_id,
  n.nombre AS nivel,
  c.modulo,
  c.ciclo,
  CONCAT(m.apellidos, ', ', m.nombres) AS maestro,
  c.dia_clase,
  c.hora_inicio,
  c.hora_fin,
  c.fecha_inicio,
  c.fecha_fin,
  (SELECT COUNT(*) FROM inscripciones WHERE curso_id = c.id) AS total_inscritos,
  (SELECT COUNT(*) FROM inscripciones WHERE curso_id = c.id AND estado = 'aprobado') AS total_aprobados
FROM cursos c
JOIN niveles n ON c.nivel_id = n.id
LEFT JOIN miembros m ON c.maestro_id = m.id;

SET FOREIGN_KEY_CHECKS = 1;
