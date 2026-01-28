<?php
/**
 * Configuración general del sistema
 */

// Zona horaria
date_default_timezone_set('America/Lima');

// Configuración del sitio
define('SITE_NAME', 'Escuela de Discipulado y Liderazgo');
define('SITE_URL', 'http://localhost/discipulado');

// Rutas
define('BASE_PATH', dirname(__DIR__));
define('INC_PATH', BASE_PATH . '/inc');
define('MODULES_PATH', BASE_PATH . '/modules');
define('TEMPLATES_PATH', BASE_PATH . '/templates');
define('ASSETS_URL', SITE_URL . '/assets');

// Incluir SDBA
require_once INC_PATH . '/sdba/sdba.php';

// Incluir funciones auxiliares
require_once INC_PATH . '/functions.php';

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Grados de instrucción
$GRADOS_INSTRUCCION = [
    'sin_estudios' => 'Sin estudios',
    'inicial' => 'Inicial',
    'primaria' => 'Primaria',
    'secundaria' => 'Secundaria',
    'superior' => 'Superior'
];

// Estados civiles
$ESTADOS_CIVILES = [
    'soltero' => 'Soltero',
    'conviviente' => 'Conviviente',
    'casado' => 'Casado',
    'separado' => 'Separado',
    'divorciado' => 'Divorciado',
    'viudo' => 'Viudo'
];

// Estados de inscripción
$ESTADOS_INSCRIPCION = [
    'inscrito' => 'Inscrito',
    'cursando' => 'Cursando',
    'aprobado' => 'Aprobado',
    'reprobado' => 'Reprobado',
    'retirado' => 'Retirado'
];

// Roles
$ROLES = [
    1 => 'Administrador',
    2 => 'Maestro',
    3 => 'Líder',
    4 => 'Alumno'
];

// Días de la semana
$DIAS_SEMANA = [
    'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'
];
