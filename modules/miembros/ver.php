<?php
/**
 * Ver Miembro
 */
require_once __DIR__ . '/../../inc/config.php';
requireRole([1, 2, 3, 4]);

$id = intval($_GET['id'] ?? 0);

// Si es alumno, solo puede ver su propio perfil
if (hasRole([4]) && $id != $_SESSION['user_id']) {
    redirect('ver.php?id=' . $_SESSION['user_id']);
}

$miembro = Sdba::table('miembros')
    ->left_join('lider_id', 'miembros', 'id', 'miembros', 'lider')
    ->where('id', $id, 'miembros')
    ->get_one();

if (!$miembro) {
    setFlashMessage('error', 'Miembro no encontrado');
    redirect('index.php');
}

$pageTitle = $miembro['apellidos'] . ', ' . $miembro['nombres'];
$pageSubtitle = 'Ficha del miembro';

// Obtener historial de cursos
$historial = Sdba::table('inscripciones')
    ->left_join('curso_id', 'cursos', 'id')
    ->left_join('nivel_id', 'niveles', 'id', 'cursos')
    ->where('miembro_id', $id, 'inscripciones')
    ->order_by('fecha_inscripcion', 'desc', 'inscripciones')
    ->fields('id,estado,nota_final,porcentaje_asistencia,fecha_inscripcion', false, 'inscripciones')
    ->fields('ciclo,modulo', false, 'cursos')
    ->fields('nombre,orden', false, 'niveles')
    ->get();

// Obtener líder
$lider = null;
if ($miembro['lider_id']) {
    $lider = Sdba::table('miembros')
        ->where('id', $miembro['lider_id'])
        ->fields('id,apellidos,nombres')
        ->get_one();
}

global $GRADOS_INSTRUCCION, $ESTADOS_CIVILES, $ROLES;

include TEMPLATES_PATH . '/header.php';
include TEMPLATES_PATH . '/sidebar.php';
?>

<main class="flex-1 flex flex-col overflow-hidden">
    <?php include TEMPLATES_PATH . '/topbar.php'; ?>

    <div class="flex-1 overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">
            <!-- Header Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl font-bold">
                            <?= strtoupper(substr($miembro['nombres'], 0, 1) . substr($miembro['apellidos'], 0, 1)) ?>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-2xl font-bold text-gray-800"><?= e($miembro['apellidos'] . ', ' . $miembro['nombres']) ?></h2>
                            <p class="text-gray-500">
                                <?= e($ROLES[$miembro['rol_id']] ?? 'Alumno') ?>
                                <?php if ($miembro['cargo_iglesia']): ?>
                                - <?= e($miembro['cargo_iglesia']) ?>
                                <?php endif; ?>
                            </p>
                            <div class="flex items-center mt-2 space-x-4 text-sm">
                                <?php if ($miembro['celular']): ?>
                                <span class="text-gray-600">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <?= e($miembro['celular']) ?>
                                </span>
                                <?php endif; ?>
                                <?php if ($miembro['email']): ?>
                                <span class="text-gray-600">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <?= e($miembro['email']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if (hasRole([1])): ?>
                    <div class="flex space-x-2">
                        <a href="editar.php?id=<?= $id ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition text-sm">
                            Editar
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Datos Personales -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Datos Personales</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Fecha de Nacimiento</dt>
                            <dd class="text-gray-900"><?= formatDate($miembro['fecha_nacimiento']) ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Edad</dt>
                            <dd class="text-gray-900"><?= calcularEdad($miembro['fecha_nacimiento']) ?? '-' ?> años</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Estado Civil</dt>
                            <dd class="text-gray-900"><?= e($ESTADOS_CIVILES[$miembro['estado_civil']] ?? '-') ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Grado de Instrucción</dt>
                            <dd class="text-gray-900"><?= e($GRADOS_INSTRUCCION[$miembro['grado_instruccion']] ?? '-') ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Ocupación</dt>
                            <dd class="text-gray-900"><?= e($miembro['ocupacion'] ?: '-') ?></dd>
                        </div>
                    </dl>
                </div>

                <!-- Datos Espirituales -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Datos Espirituales</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Fecha de Conversión</dt>
                            <dd class="text-gray-900"><?= formatDate($miembro['fecha_conversion']) ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Bautismo en Agua</dt>
                            <dd class="text-gray-900"><?= formatDate($miembro['bautismo_agua']) ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Bautismo Espíritu Santo</dt>
                            <dd class="text-gray-900"><?= e($miembro['bautismo_espiritu_santo'] ?: '-') ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Grupo Familiar</dt>
                            <dd class="text-gray-900"><?= $miembro['grupo_familiar'] ? 'GF ' . $miembro['grupo_familiar'] : '-' ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Líder</dt>
                            <dd class="text-gray-900">
                                <?php if ($lider): ?>
                                <a href="ver.php?id=<?= $lider['id'] ?>" class="text-blue-600 hover:underline">
                                    <?= e($lider['apellidos'] . ', ' . $lider['nombres']) ?>
                                </a>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </dd>
                        </div>
                        <?php if ($miembro['dones_habilidades']): ?>
                        <div>
                            <dt class="text-gray-500 mb-1">Dones / Habilidades</dt>
                            <dd class="text-gray-900"><?= e($miembro['dones_habilidades']) ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <!-- Historial de Cursos -->
            <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Historial de Discipulado</h3>
                <?php if (empty($historial)): ?>
                <p class="text-gray-500">No hay cursos registrados.</p>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nivel</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ciclo</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Asistencia</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nota</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($historial as $h): ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <?= e($h['nombre']) ?>
                                    <?php if ($h['modulo']): ?>
                                    <span class="text-gray-500">- M<?= $h['modulo'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-gray-500"><?= getNombreCiclo($h['ciclo']) ?></td>
                                <td class="px-4 py-3 text-center"><?= $h['porcentaje_asistencia'] ? $h['porcentaje_asistencia'] . '%' : '-' ?></td>
                                <td class="px-4 py-3 text-center font-medium"><?= $h['nota_final'] ?? '-' ?></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        <?php
                                        switch($h['estado']) {
                                            case 'aprobado': echo 'bg-green-100 text-green-800'; break;
                                            case 'cursando': echo 'bg-blue-100 text-blue-800'; break;
                                            case 'reprobado': echo 'bg-red-100 text-red-800'; break;
                                            case 'retirado': echo 'bg-gray-100 text-gray-800'; break;
                                            default: echo 'bg-yellow-100 text-yellow-800';
                                        }
                                        ?>">
                                        <?= ucfirst($h['estado']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="index.php" class="inline-flex items-center text-gray-600 hover:text-gray-800">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a la lista
                </a>
            </div>
        </div>
    </div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
