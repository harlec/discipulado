<?php
/**
 * Control de Asistencia
 */
require_once __DIR__ . '/../../inc/config.php';
requireRole([1, 2]);

$pageTitle = 'Control de Asistencia';
$pageSubtitle = 'Registrar asistencia de estudiantes';

$curso_id = $_GET['curso_id'] ?? '';
$clase_id = $_GET['clase_id'] ?? '';

// Obtener cursos (filtrado según rol)
$queryCursos = Sdba::table('cursos')
    ->left_join('nivel_id', 'niveles', 'id')
    ->where('activo', 1, 'cursos')
    ->fields('id,ciclo,modulo', false, 'cursos')
    ->fields('nombre', false, 'niveles')
    ->order_by('ciclo', 'desc', 'cursos')
    ->order_by('orden', 'asc', 'niveles');

if (hasRole([2]) && !hasRole([1])) {
    $queryCursos->where('maestro_id', $_SESSION['user_id'], 'cursos');
}

$cursos = $queryCursos->get();

// Si hay curso seleccionado, obtener clases e inscritos
$clases = [];
$inscritos = [];
$claseActual = null;

if ($curso_id) {
    $clases = Sdba::table('clases')
        ->where('curso_id', $curso_id)
        ->order_by('numero', 'asc')
        ->get();

    if ($clase_id) {
        $claseActual = Sdba::table('clases')->where('id', $clase_id)->get_one();
    }

    $inscritos = Sdba::table('inscripciones')
        ->left_join('miembro_id', 'miembros', 'id')
        ->where('curso_id', $curso_id, 'inscripciones')
        ->where_in('estado', ['inscrito', 'cursando'], 'inscripciones')
        ->fields('id', false, 'inscripciones')
        ->fields('id,apellidos,nombres', false, 'miembros')
        ->alias('id', 'inscripcion_id', 'inscripciones')
        ->alias('id', 'miembro_id', 'miembros')
        ->order_by('apellidos', 'asc', 'miembros')
        ->get();

    // Obtener asistencias existentes para esta clase
    if ($clase_id) {
        foreach ($inscritos as &$ins) {
            $asistencia = Sdba::table('asistencias')
                ->where('inscripcion_id', $ins['inscripcion_id'])
                ->where('clase_id', $clase_id)
                ->get_one();
            $ins['asistio'] = $asistencia ? $asistencia['asistio'] : null;
            $ins['justificado'] = $asistencia ? $asistencia['justificado'] : 0;
        }
    }
}

// Procesar guardado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $clase_id) {
    $asistencias = $_POST['asistencia'] ?? [];
    $justificados = $_POST['justificado'] ?? [];

    foreach ($inscritos as $ins) {
        $asistio = isset($asistencias[$ins['inscripcion_id']]) ? 1 : 0;
        $justificado = isset($justificados[$ins['inscripcion_id']]) ? 1 : 0;

        // Verificar si ya existe
        $existe = Sdba::table('asistencias')
            ->where('inscripcion_id', $ins['inscripcion_id'])
            ->where('clase_id', $clase_id)
            ->get_one();

        if ($existe) {
            Sdba::table('asistencias')
                ->where('id', $existe['id'])
                ->update(['asistio' => $asistio, 'justificado' => $justificado]);
        } else {
            Sdba::table('asistencias')->insert([
                'inscripcion_id' => $ins['inscripcion_id'],
                'clase_id' => $clase_id,
                'asistio' => $asistio,
                'justificado' => $justificado
            ]);
        }

        // Actualizar porcentaje de asistencia
        $porcentaje = calcularPorcentajeAsistencia($ins['inscripcion_id']);
        Sdba::table('inscripciones')
            ->where('id', $ins['inscripcion_id'])
            ->update(['porcentaje_asistencia' => $porcentaje]);
    }

    setFlashMessage('success', 'Asistencia guardada correctamente');
    redirect("index.php?curso_id=$curso_id&clase_id=$clase_id");
}

include TEMPLATES_PATH . '/header.php';
include TEMPLATES_PATH . '/sidebar.php';
?>

<main class="flex-1 flex flex-col overflow-hidden">
    <?php include TEMPLATES_PATH . '/topbar.php'; ?>

    <div class="flex-1 overflow-y-auto p-6">
        <!-- Selector de Curso y Clase -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Curso</label>
                    <select name="curso_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" onchange="this.form.submit()">
                        <option value="">Seleccionar curso...</option>
                        <?php foreach ($cursos as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $curso_id == $c['id'] ? 'selected' : '' ?>>
                            <?= e($c['nombre']) ?>
                            <?php if ($c['modulo']): ?> - M<?= $c['modulo'] ?><?php endif; ?>
                            (<?= getNombreCiclo($c['ciclo']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($curso_id && !empty($clases)): ?>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Clase</label>
                    <select name="clase_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" onchange="this.form.submit()">
                        <option value="">Seleccionar clase...</option>
                        <?php foreach ($clases as $cl): ?>
                        <option value="<?= $cl['id'] ?>" <?= $clase_id == $cl['id'] ? 'selected' : '' ?>>
                            Clase <?= $cl['numero'] ?> - <?= formatDate($cl['fecha']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($curso_id && empty($clases)): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
            <p class="text-yellow-800">Este curso no tiene clases programadas.</p>
            <?php if (hasRole([1])): ?>
            <a href="<?= SITE_URL ?>/modules/cursos/clases.php?curso_id=<?= $curso_id ?>" class="text-blue-600 hover:underline mt-2 inline-block">
                Generar clases
            </a>
            <?php endif; ?>
        </div>
        <?php elseif ($curso_id && $clase_id && !empty($inscritos)): ?>
        <!-- Formulario de Asistencia -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 bg-gray-50 border-b">
                <h3 class="font-semibold text-gray-800">
                    Clase <?= $claseActual['numero'] ?> - <?= formatDate($claseActual['fecha']) ?>
                </h3>
                <?php if ($claseActual['tema']): ?>
                <p class="text-sm text-gray-500"><?= e($claseActual['tema']) ?></p>
                <?php endif; ?>
            </div>

            <form method="POST" action="">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Asistió</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Justificado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($inscritos as $ins): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <?= e($ins['apellidos'] . ', ' . $ins['nombres']) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" name="asistencia[<?= $ins['inscripcion_id'] ?>]" value="1"
                                       <?= $ins['asistio'] ? 'checked' : '' ?>
                                       class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            </td>
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" name="justificado[<?= $ins['inscripcion_id'] ?>]" value="1"
                                       <?= $ins['justificado'] ? 'checked' : '' ?>
                                       class="w-5 h-5 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        Total: <?= count($inscritos) ?> estudiantes
                    </div>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Guardar Asistencia
                    </button>
                </div>
            </form>
        </div>
        <?php elseif ($curso_id && $clase_id && empty($inscritos)): ?>
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <p class="text-gray-500">No hay estudiantes inscritos en este curso.</p>
        </div>
        <?php elseif ($curso_id && !$clase_id): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-center">
            <p class="text-blue-800">Seleccione una clase para tomar asistencia.</p>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <p class="text-gray-500">Seleccione un curso para comenzar a tomar asistencia.</p>
        </div>
        <?php endif; ?>
    </div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
