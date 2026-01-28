<?php
/**
 * Gestión de Clases del Curso
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../inc/config.php';
requireRole([1]);

$curso_id = intval($_GET['curso_id'] ?? 0);

// Obtener curso SIN JOINs
$curso = Sdba::table('cursos')->where('id', $curso_id)->get_one();

if (!$curso) {
    setFlashMessage('error', 'Curso no encontrado');
    redirect(SITE_URL . '/modules/cursos/index.php');
}

// Obtener nivel
$nivel = Sdba::table('niveles')->where('id', $curso['nivel_id'])->get_one();
$curso['nombre'] = $nivel['nombre'] ?? 'Sin nivel';

$pageTitle = 'Gestionar Clases';
$pageSubtitle = $curso['nombre'] . ($curso['modulo'] ? ' - M' . $curso['modulo'] : '');

// Generar clases automáticamente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar'])) {
    $semanas = intval($curso['semanas'] ?? 12);
    $fechaInicio = $curso['fecha_inicio'] ?? date('Y-m-d');

    // Eliminar clases existentes
    Sdba::table('clases')->where('curso_id', $curso_id)->delete();

    // Generar nuevas clases
    $fecha = new DateTime($fechaInicio);
    for ($i = 1; $i <= $semanas; $i++) {
        Sdba::table('clases')->insert([
            'curso_id' => $curso_id,
            'numero' => $i,
            'fecha' => $fecha->format('Y-m-d'),
            'tema' => 'Clase ' . $i
        ]);
        $fecha->modify('+1 week');
    }

    setFlashMessage('success', "Se generaron $semanas clases");
    redirect(SITE_URL . '/modules/cursos/clases.php?curso_id=' . $curso_id);
}

// Guardar cambios en clases
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    $clases = $_POST['clase'] ?? [];
    foreach ($clases as $id => $data) {
        Sdba::table('clases')->where('id', $id)->update([
            'fecha' => $data['fecha'],
            'tema' => limpiarString($data['tema'])
        ]);
    }
    setFlashMessage('success', 'Clases actualizadas');
    redirect(SITE_URL . '/modules/cursos/clases.php?curso_id=' . $curso_id);
}

// Obtener clases existentes
$clases = Sdba::table('clases')
    ->where('curso_id', $curso_id)
    ->order_by('numero', 'asc')
    ->get();

include TEMPLATES_PATH . '/header.php';
include TEMPLATES_PATH . '/sidebar.php';
?>

<main class="flex-1 flex flex-col overflow-hidden">
    <?php include TEMPLATES_PATH . '/topbar.php'; ?>

    <div class="flex-1 overflow-y-auto p-6">
        <div class="max-w-3xl mx-auto">
            <!-- Info Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-800 font-medium">Curso: <?= e($curso['nombre']) ?></p>
                        <p class="text-blue-600 text-sm"><?= $curso['semanas'] ?> semanas | Inicio: <?= formatDate($curso['fecha_inicio']) ?></p>
                    </div>
                    <?php if (empty($clases)): ?>
                    <form method="POST">
                        <button type="submit" name="generar" value="1"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Generar Clases
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($clases)): ?>
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <form method="POST">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tema</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($clases as $clase): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    <?= $clase['numero'] ?>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="date" name="clase[<?= $clase['id'] ?>][fecha]"
                                           value="<?= $clase['fecha'] ?>"
                                           class="px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="clase[<?= $clase['id'] ?>][tema]"
                                           value="<?= e($clase['tema']) ?>"
                                           class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                           placeholder="Tema de la clase">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="p-4 bg-gray-50 border-t flex justify-between">
                        <form method="POST" onsubmit="return confirm('¿Regenerar todas las clases? Se perderán las asistencias registradas.')">
                            <button type="submit" name="generar" value="1"
                                    class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                Regenerar Clases
                            </button>
                        </form>
                        <button type="submit" name="guardar" value="1"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                <p class="text-gray-500 mb-4">No hay clases programadas para este curso.</p>
                <form method="POST">
                    <button type="submit" name="generar" value="1"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Generar <?= $curso['semanas'] ?> Clases
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <div class="mt-6">
                <a href="<?= SITE_URL ?>/modules/cursos/ver.php?id=<?= $curso_id ?>" class="inline-flex items-center text-gray-600 hover:text-gray-800">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al curso
                </a>
            </div>
        </div>
    </div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
