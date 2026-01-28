<?php
/**
 * Dashboard Principal
 */
require_once __DIR__ . '/inc/config.php';
requireLogin();

$pageTitle = 'Dashboard';
$user = getCurrentUser();

// Estadísticas generales
$totalMiembros = Sdba::table('miembros')->where('activo', 1)->total();
$totalCursos = Sdba::table('cursos')->where('activo', 1)->total();
$totalInscritos = Sdba::table('inscripciones')
    ->where_in('estado', ['inscrito', 'cursando'])
    ->total();

// Cursos del ciclo actual
$cicloActual = getCicloActual();
$cursosActuales = Sdba::table('cursos')
    ->left_join('nivel_id', 'niveles', 'id')
    ->left_join('maestro_id', 'miembros', 'id')
    ->where('ciclo', $cicloActual, 'cursos')
    ->where('activo', 1, 'cursos')
    ->order_by('orden', 'asc', 'niveles')
    ->fields('id,modulo,dia_clase,hora_inicio', false, 'cursos')
    ->fields('nombre', false, 'niveles')
    ->fields('nombres,apellidos', false, 'miembros')
    ->get();

// Mis cursos (si es maestro)
$misCursos = [];
if (hasRole([2])) {
    $misCursos = Sdba::table('cursos')
        ->left_join('nivel_id', 'niveles', 'id')
        ->where('maestro_id', $_SESSION['user_id'], 'cursos')
        ->where('activo', 1, 'cursos')
        ->get();
}

// Mi progreso (si es alumno)
$miProgreso = [];
if (hasRole([4])) {
    $miProgreso = getHistorialAlumno($_SESSION['user_id']);
}

include TEMPLATES_PATH . '/header.php';
include TEMPLATES_PATH . '/sidebar.php';
?>

<!-- Main Content -->
<main class="flex-1 flex flex-col overflow-hidden">
    <?php include TEMPLATES_PATH . '/topbar.php'; ?>

    <div class="flex-1 overflow-y-auto p-6">
        <!-- Welcome -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-6 text-white mb-6">
            <h2 class="text-2xl font-bold">Bienvenido, <?= e($user['nombres']) ?>!</h2>
            <p class="opacity-90 mt-1">
                <?php
                global $ROLES;
                echo $ROLES[$_SESSION['user_rol']] ?? 'Usuario';
                ?>
                - <?= getNombreCiclo($cicloActual) ?>
            </p>
        </div>

        <?php if (hasRole([1, 2, 3])): ?>
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total Miembros</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $totalMiembros ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Cursos Activos</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $totalCursos ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Estudiantes Activos</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $totalInscritos ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Cursos del Ciclo -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Cursos del Ciclo Actual</h3>
                <?php if (empty($cursosActuales)): ?>
                <p class="text-gray-500">No hay cursos programados para este ciclo.</p>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($cursosActuales as $curso): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">
                                <?= e($curso['nombre']) ?>
                                <?php if ($curso['modulo']): ?>
                                <span class="text-sm text-gray-500">- Módulo <?= $curso['modulo'] ?></span>
                                <?php endif; ?>
                            </p>
                            <p class="text-sm text-gray-500">
                                <?= e($curso['nombres'] . ' ' . $curso['apellidos']) ?>
                            </p>
                        </div>
                        <div class="text-right text-sm text-gray-500">
                            <p><?= e($curso['dia_clase']) ?></p>
                            <p><?= e($curso['hora_inicio']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if (hasRole([4]) && !empty($miProgreso)): ?>
            <!-- Mi Progreso (Alumno) -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Mi Progreso</h3>
                <div class="space-y-3">
                    <?php foreach ($miProgreso as $prog): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800"><?= e($prog['nombre']) ?></p>
                            <p class="text-sm text-gray-500"><?= getNombreCiclo($prog['ciclo']) ?></p>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full
                            <?php
                            switch($prog['estado']) {
                                case 'aprobado': echo 'bg-green-100 text-green-800'; break;
                                case 'cursando': echo 'bg-blue-100 text-blue-800'; break;
                                case 'reprobado': echo 'bg-red-100 text-red-800'; break;
                                default: echo 'bg-gray-100 text-gray-800';
                            }
                            ?>">
                            <?= ucfirst($prog['estado']) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (hasRole([2]) && !empty($misCursos)): ?>
            <!-- Mis Cursos (Maestro) -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Mis Cursos</h3>
                <div class="space-y-3">
                    <?php foreach ($misCursos as $curso): ?>
                    <a href="<?= SITE_URL ?>/modules/cursos/ver.php?id=<?= $curso['id'] ?>"
                       class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <p class="font-medium text-gray-800"><?= e($curso['nombre']) ?></p>
                        <p class="text-sm text-gray-500"><?= getNombreCiclo($curso['ciclo']) ?></p>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (hasRole([1])): ?>
            <!-- Accesos Rápidos (Admin) -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Accesos Rápidos</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="<?= SITE_URL ?>/modules/miembros/crear.php"
                       class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <span class="text-sm font-medium text-blue-700">Nuevo Miembro</span>
                    </a>
                    <a href="<?= SITE_URL ?>/modules/cursos/crear.php"
                       class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="text-sm font-medium text-green-700">Nuevo Curso</span>
                    </a>
                    <a href="<?= SITE_URL ?>/modules/inscripciones/crear.php"
                       class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="text-sm font-medium text-purple-700">Nueva Inscripción</span>
                    </a>
                    <a href="<?= SITE_URL ?>/modules/asistencia/"
                       class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                        <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <span class="text-sm font-medium text-orange-700">Tomar Asistencia</span>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
