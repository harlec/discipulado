<?php
/**
 * Ver Curso
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../inc/config.php';
requireRole([1, 2]);

$id = intval($_GET['id'] ?? 0);

// Obtener curso de forma simple
$curso = Sdba::table('cursos')->where('id', $id)->get_one();

if (!$curso) {
    setFlashMessage('error', 'Curso no encontrado');
    redirect(SITE_URL . '/modules/cursos/index.php');
}

// Obtener nivel
$nivel = Sdba::table('niveles')->where('id', $curso['nivel_id'])->get_one();
$curso['nombre'] = $nivel['nombre'] ?? 'Sin nivel';

// Obtener maestro
$maestro = null;
if ($curso['maestro_id']) {
    $maestro = Sdba::table('miembros')->where('id', $curso['maestro_id'])->get_one();
}

$pageTitle = $curso['nombre'] . ($curso['modulo'] ? ' - Módulo ' . $curso['modulo'] : '');
$pageSubtitle = getNombreCiclo($curso['ciclo']);

// Obtener inscritos de forma simple
$inscritos = Sdba::table('inscripciones')
    ->where('curso_id', $id)
    ->get();

// Agregar datos del miembro a cada inscripción
foreach ($inscritos as &$ins) {
    $miembro = Sdba::table('miembros')->where('id', $ins['miembro_id'])->get_one();
    $ins['apellidos'] = $miembro['apellidos'] ?? '';
    $ins['nombres'] = $miembro['nombres'] ?? '';
    $ins['celular'] = $miembro['celular'] ?? '';
    }

    // Obtener clases
    $clases = Sdba::table('clases')
        ->where('curso_id', $id)
        ->order_by('numero', 'asc')
        ->get();

    global $ESTADOS_INSCRIPCION;

    include TEMPLATES_PATH . '/header.php';
    include TEMPLATES_PATH . '/sidebar.php';
    ?>

    <main class="flex-1 flex flex-col overflow-hidden">
        <?php include TEMPLATES_PATH . '/topbar.php'; ?>

        <div class="flex-1 overflow-y-auto p-6">
            <div class="max-w-6xl mx-auto">
                <!-- Header Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800"><?= e($curso['nombre']) ?></h2>
                            <?php if ($curso['modulo']): ?>
                            <p class="text-lg text-gray-600">Módulo <?= $curso['modulo'] ?></p>
                            <?php endif; ?>
                            <p class="text-gray-500 mt-1"><?= getNombreCiclo($curso['ciclo']) ?></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 text-sm font-medium rounded-full <?= $curso['activo'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $curso['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                            <?php if (hasRole([1])): ?>
                            <a href="<?= SITE_URL ?>/modules/cursos/editar.php?id=<?= $id ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition text-sm">
                                Editar
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Maestro</p>
                            <p class="font-medium"><?= $maestro ? e($maestro['nombres'] . ' ' . $maestro['apellidos']) : 'Sin asignar' ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Horario</p>
                            <p class="font-medium"><?= e($curso['dia_clase']) ?> <?= e($curso['hora_inicio']) ?> - <?= e($curso['hora_fin']) ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Duración</p>
                            <p class="font-medium"><?= $curso['semanas'] ?> semanas / <?= $curso['horas_cronologicas'] ?> hrs</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Período</p>
                            <p class="font-medium"><?= formatDate($curso['fecha_inicio']) ?> - <?= formatDate($curso['fecha_fin']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Lista de Inscritos -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Estudiantes Inscritos (<?= count($inscritos) ?>)</h3>
                            <?php if (hasRole([1])): ?>
                            <a href="<?= SITE_URL ?>/modules/inscripciones/crear.php?curso_id=<?= $id ?>" class="text-sm text-blue-600 hover:text-blue-800">
                                + Agregar estudiante
                            </a>
                            <?php endif; ?>
                        </div>

                        <?php if (empty($inscritos)): ?>
                        <p class="text-gray-500 text-center py-8">No hay estudiantes inscritos.</p>
                        <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Asistencia</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nota</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($inscritos as $i): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <a href="<?= SITE_URL ?>/modules/miembros/ver.php?id=<?= $i['miembro_id'] ?>" class="text-blue-600 hover:underline">
                                                <?= e($i['apellidos'] . ', ' . $i['nombres']) ?>
                                            </a>
                                            <p class="text-sm text-gray-500"><?= e($i['celular'] ?: '') ?></p>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <?= $i['porcentaje_asistencia'] ? $i['porcentaje_asistencia'] . '%' : '-' ?>
                                        </td>
                                        <td class="px-4 py-3 text-center font-medium">
                                            <?= $i['nota_final'] ?? '-' ?>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                <?php
                                                switch($i['estado']) {
                                                    case 'aprobado': echo 'bg-green-100 text-green-800'; break;
                                                    case 'cursando': echo 'bg-blue-100 text-blue-800'; break;
                                                    case 'reprobado': echo 'bg-red-100 text-red-800'; break;
                                                    case 'retirado': echo 'bg-gray-100 text-gray-800'; break;
                                                    default: echo 'bg-yellow-100 text-yellow-800';
                                                }
                                                ?>">
                                                <?= e($ESTADOS_INSCRIPCION[$i['estado']] ?? $i['estado']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Acciones</h3>
                            <div class="space-y-3">
                                <a href="<?= SITE_URL ?>/modules/asistencia/?curso_id=<?= $id ?>" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                    <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                    <span class="font-medium text-blue-700">Tomar Asistencia</span>
                                </a>
                                <a href="<?= SITE_URL ?>/modules/calificaciones/?curso_id=<?= $id ?>" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                                    <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                    </svg>
                                    <span class="font-medium text-green-700">Registrar Notas</span>
                                </a>
                                <?php if (hasRole([1])): ?>
                                <a href="<?= SITE_URL ?>/modules/cursos/clases.php?curso_id=<?= $id ?>" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                                    <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="font-medium text-purple-700">Gestionar Clases</span>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Clases del Curso -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Clases (<?= count($clases) ?>)</h3>
                            <?php if (empty($clases)): ?>
                            <p class="text-gray-500 text-sm">No hay clases programadas.</p>
                            <?php if (hasRole([1])): ?>
                            <a href="<?= SITE_URL ?>/modules/cursos/clases.php?curso_id=<?= $id ?>" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Generar clases</a>
                            <?php endif; ?>
                            <?php else: ?>
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                <?php foreach ($clases as $clase): ?>
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-sm">
                                    <span>Clase <?= $clase['numero'] ?></span>
                                    <span class="text-gray-500"><?= formatDate($clase['fecha']) ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mt-6">
                    <a href="<?= SITE_URL ?>/modules/cursos/index.php" class="inline-flex items-center text-gray-600 hover:text-gray-800">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver a cursos
                    </a>
                </div>
            </div>
        </div>

    <?php include TEMPLATES_PATH . '/footer.php'; ?>
