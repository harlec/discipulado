<?php
/**
 * Funciones auxiliares del sistema
 */

/**
 * Redireccionar a una URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Verificar si el usuario está autenticado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Obtener usuario actual
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return Sdba::table('miembros')->where('id', $_SESSION['user_id'])->get_one();
}

/**
 * Verificar rol del usuario
 */
function hasRole($roles) {
    if (!isLoggedIn()) return false;
    if (!is_array($roles)) $roles = [$roles];
    return in_array($_SESSION['user_rol'], $roles);
}

/**
 * Requerir autenticación
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect(SITE_URL . '/index.php?error=login_required');
    }
}

/**
 * Requerir roles específicos
 */
function requireRole($roles) {
    requireLogin();
    if (!hasRole($roles)) {
        redirect(SITE_URL . '/dashboard.php?error=access_denied');
    }
}

/**
 * Escapar HTML
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Formatear fecha
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '-';
    return date($format, strtotime($date));
}

/**
 * Formatear fecha y hora
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime)) return '-';
    return date($format, strtotime($datetime));
}

/**
 * Calcular edad
 */
function calcularEdad($fechaNacimiento) {
    if (empty($fechaNacimiento)) return null;
    $nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    return $nacimiento->diff($hoy)->y;
}

/**
 * Generar código de ciclo actual
 */
function getCicloActual() {
    $year = date('Y');
    $month = date('n');

    if ($month <= 4) {
        $ciclo = '10'; // Primer ciclo
    } elseif ($month <= 8) {
        $ciclo = '20'; // Segundo ciclo
    } else {
        $ciclo = '30'; // Tercer ciclo
    }

    return $year . $ciclo;
}

/**
 * Obtener nombre del ciclo
 */
function getNombreCiclo($ciclo) {
    $year = substr($ciclo, 0, 4);
    $num = substr($ciclo, 4, 2);

    $nombres = [
        '10' => 'Ciclo I',
        '20' => 'Ciclo II',
        '30' => 'Ciclo III'
    ];

    return ($nombres[$num] ?? 'Ciclo ?') . ' - ' . $year;
}

/**
 * Mostrar mensaje flash
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Obtener y limpiar mensaje flash
 */
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Generar nombre completo
 */
function nombreCompleto($apellidos, $nombres) {
    return trim($apellidos . ', ' . $nombres);
}

/**
 * Validar email
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Limpiar string
 */
function limpiarString($string) {
    return trim(strip_tags($string));
}

/**
 * Obtener niveles para select
 */
function getNiveles() {
    return Sdba::table('niveles')->order_by('orden')->get();
}

/**
 * Obtener maestros para select
 */
function getMaestros() {
    return Sdba::table('miembros')
        ->where('rol_id', 2)
        ->where('activo', 1)
        ->order_by('apellidos')
        ->get();
}

/**
 * Obtener líderes para select
 */
function getLideres() {
    return Sdba::table('miembros')
        ->where_in('rol_id', [1, 2, 3])
        ->where('activo', 1)
        ->order_by('apellidos')
        ->get();
}

/**
 * Obtener cursos activos
 */
function getCursosActivos() {
    return Sdba::table('cursos')
        ->left_join('nivel_id', 'niveles', 'id')
        ->left_join('maestro_id', 'miembros', 'id')
        ->where('activo', 1, 'cursos')
        ->order_by('orden', 'asc', 'niveles')
        ->get();
}

/**
 * Calcular porcentaje de asistencia
 */
function calcularPorcentajeAsistencia($inscripcion_id) {
    $total = Sdba::table('asistencias')
        ->where('inscripcion_id', $inscripcion_id)
        ->total();

    if ($total == 0) return 0;

    $asistidas = Sdba::table('asistencias')
        ->where('inscripcion_id', $inscripcion_id)
        ->where('asistio', 1)
        ->total();

    return round(($asistidas / $total) * 100, 2);
}

/**
 * Calcular nota final
 */
function calcularNotaFinal($inscripcion_id) {
    $tipos = Sdba::table('tipo_calificaciones')->get();
    $notaFinal = 0;

    foreach ($tipos as $tipo) {
        $promedio = Sdba::table('calificaciones')
            ->where('inscripcion_id', $inscripcion_id)
            ->where('tipo_id', $tipo['id'])
            ->get_single('nota');

        if ($promedio) {
            $notaFinal += ($promedio * $tipo['porcentaje'] / 100);
        }
    }

    return round($notaFinal, 2);
}

/**
 * Obtener historial del alumno
 */
function getHistorialAlumno($miembro_id) {
    return Sdba::table('inscripciones')
        ->left_join('curso_id', 'cursos', 'id')
        ->left_join('nivel_id', 'niveles', 'id', 'cursos')
        ->where('miembro_id', $miembro_id, 'inscripciones')
        ->order_by('orden', 'asc', 'niveles')
        ->get();
}
