<?php
/**
 * Registro de Calificaciones
 */
require_once __DIR__ . '/../../inc/config.php';
requireRole([1, 2]);

$pageTitle = 'Calificaciones';
$pageSubtitle = 'Registro de notas y evaluaciones';

$curso_id = $_GET['curso_id'] ?? '';

// Obtener cursos
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

// Obtener tipos de calificaciones
$tipos = Sdba::table('tipo_calificaciones')->get();

// Si hay curso seleccionado
$inscritos = [];
if ($curso_id) {
    $inscritos = Sdba::table('inscripciones')
        ->left_join('miembro_id', 'miembros', 'id')
        ->where('curso_id', $curso_id, 'inscripciones')
        ->where_in('estado', ['inscrito', 'cursando'], 'inscripciones')
        ->fields('id,nota_final', false, 'inscripciones')
        ->fields('id,apellidos,nombres', false, 'miembros')
        ->alias('id', 'inscripcion_id', 'inscripciones')
        ->order_by('apellidos', 'asc', 'miembros')
        ->get();

    // Obtener calificaciones por tipo para cada inscrito
    foreach ($inscritos as &$ins) {
        $ins['notas'] = [];
        foreach ($tipos as $tipo) {
            $nota = Sdba::table('calificaciones')
                ->where('inscripcion_id', $ins['inscripcion_id'])
                ->where('tipo_id', $tipo['id'])
                ->get_single('nota');
            $ins['notas'][$tipo['id']] = $nota;
        }
    }
}

// Procesar guardado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $curso_id) {
    $notas = $_POST['nota'] ?? [];

    foreach ($notas as $inscripcion_id => $tiposNotas) {
        foreach ($tiposNotas as $tipo_id => $nota) {
            if ($nota === '') continue;

            $nota = floatval($nota);

            // Verificar si existe
            $existe = Sdba::table('calificaciones')
                ->where('inscripcion_id', $inscripcion_id)
                ->where('tipo_id', $tipo_id)
                ->get_one();

            if ($existe) {
                Sdba::table('calificaciones')
                    ->where('id', $existe['id'])
                    ->update(['nota' => $nota]);
            } else {
                Sdba::table('calificaciones')->insert([
                    'inscripcion_id' => $inscripcion_id,
                    'tipo_id' => $tipo_id,
                    'nota' => $nota,
                    'fecha' => date('Y-m-d')
                ]);
            }
        }

        // Calcular y actualizar nota final
        $notaFinal = calcularNotaFinal($inscripcion_id);
        Sdba::table('inscripciones')
            ->where('id', $inscripcion_id)
            ->update(['nota_final' => $notaFinal]);
    }

    setFlashMessage('success', 'Calificaciones guardadas correctamente');
    redirect("index.php?curso_id=$curso_id");
}

include TEMPLATES_PATH . '/header.php';
include TEMPLATES_PATH . '/sidebar.php';
?>

<main class="flex-1 flex flex-col overflow-hidden">
    <?php include TEMPLATES_PATH . '/topbar.php'; ?>

    <div class="flex-1 overflow-y-auto p-6">
        <!-- Selector de Curso -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
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
            </form>
        </div>

        <?php if ($curso_id && !empty($inscritos)): ?>
        <!-- Tabla de Calificaciones -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 bg-gray-50 border-b">
                <p class="text-sm text-gray-500">
                    Ingrese las notas del 0 al 20. La nota final se calcula automáticamente según los porcentajes.
                </p>
            </div>

            <form method="POST" action="">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                                <?php foreach ($tipos as $tipo): ?>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                    <?= e($tipo['nombre']) ?>
                                    <br><span class="font-normal">(<?= $tipo['porcentaje'] ?>%)</span>
                                </th>
                                <?php endforeach; ?>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase bg-blue-50">
                                    Nota Final
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($inscritos as $ins): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">
                                    <?= e($ins['apellidos'] . ', ' . $ins['nombres']) ?>
                                </td>
                                <?php foreach ($tipos as $tipo): ?>
                                <td class="px-4 py-3 text-center">
                                    <input type="number" step="0.01" min="0" max="20"
                                           name="nota[<?= $ins['inscripcion_id'] ?>][<?= $tipo['id'] ?>]"
                                           value="<?= $ins['notas'][$tipo['id']] ?? '' ?>"
                                           class="w-20 px-2 py-1 border border-gray-300 rounded text-center focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </td>
                                <?php endforeach; ?>
                                <td class="px-4 py-3 text-center font-bold bg-blue-50">
                                    <?= $ins['nota_final'] ?? '-' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        Total: <?= count($inscritos) ?> estudiantes
                    </div>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Guardar Calificaciones
                    </button>
                </div>
            </form>
        </div>
        <?php elseif ($curso_id && empty($inscritos)): ?>
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <p class="text-gray-500">No hay estudiantes inscritos en este curso.</p>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
            </svg>
            <p class="text-gray-500">Seleccione un curso para registrar calificaciones.</p>
        </div>
        <?php endif; ?>
    </div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
