<?php
/**
 * Crear Inscripción
 */
require_once __DIR__ . '/../../inc/config.php';
requireRole([1]);

$pageTitle = 'Nueva Inscripción';
$pageSubtitle = 'Inscribir un estudiante a un curso';

$error = '';
$curso_id = $_GET['curso_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'miembro_id' => intval($_POST['miembro_id'] ?? 0),
        'curso_id' => intval($_POST['curso_id'] ?? 0),
        'fecha_inscripcion' => $_POST['fecha_inscripcion'] ?: date('Y-m-d'),
        'estado' => $_POST['estado'] ?? 'inscrito',
    ];

    if (!$datos['miembro_id'] || !$datos['curso_id']) {
        $error = 'Debe seleccionar un estudiante y un curso';
    } else {
        // Verificar si ya está inscrito
        $existe = Sdba::table('inscripciones')
            ->where('miembro_id', $datos['miembro_id'])
            ->where('curso_id', $datos['curso_id'])
            ->get_one();

        if ($existe) {
            $error = 'El estudiante ya está inscrito en este curso';
        } else {
            Sdba::table('inscripciones')->insert($datos);
            $id = Sdba::table('inscripciones')->insert_id();
            if ($id) {
                setFlashMessage('success', 'Inscripción registrada exitosamente');
                redirect('index.php');
            } else {
                $error = 'Error al registrar la inscripción';
            }
        }
    }
}

// Obtener miembros activos
$miembros = Sdba::table('miembros')
    ->where('activo', 1)
    ->order_by('apellidos')
    ->fields('id,apellidos,nombres')
    ->get();

// Obtener cursos activos
$cursos = Sdba::table('cursos')
    ->left_join('nivel_id', 'niveles', 'id')
    ->where('activo', 1, 'cursos')
    ->order_by('ciclo', 'desc', 'cursos')
    ->order_by('orden', 'asc', 'niveles')
    ->fields('id,ciclo,modulo', false, 'cursos')
    ->fields('nombre', false, 'niveles')
    ->get();

global $ESTADOS_INSCRIPCION;

include TEMPLATES_PATH . '/header.php';
include TEMPLATES_PATH . '/sidebar.php';
?>

<main class="flex-1 flex flex-col overflow-hidden">
    <?php include TEMPLATES_PATH . '/topbar.php'; ?>

    <div class="flex-1 overflow-y-auto p-6">
        <div class="max-w-xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <?= e($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estudiante *</label>
                        <select name="miembro_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar estudiante...</option>
                            <?php foreach ($miembros as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= ($_POST['miembro_id'] ?? '') == $m['id'] ? 'selected' : '' ?>>
                                <?= e($m['apellidos'] . ', ' . $m['nombres']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Curso *</label>
                        <select name="curso_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar curso...</option>
                            <?php foreach ($cursos as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($_POST['curso_id'] ?? $curso_id) == $c['id'] ? 'selected' : '' ?>>
                                <?= e($c['nombre']) ?>
                                <?php if ($c['modulo']): ?> - M<?= $c['modulo'] ?><?php endif; ?>
                                (<?= getNombreCiclo($c['ciclo']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inscripción</label>
                        <input type="date" name="fecha_inscripcion"
                               value="<?= e($_POST['fecha_inscripcion'] ?? date('Y-m-d')) ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <?php foreach ($ESTADOS_INSCRIPCION as $key => $val): ?>
                            <option value="<?= $key ?>" <?= ($_POST['estado'] ?? 'inscrito') == $key ? 'selected' : '' ?>><?= e($val) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="index.php" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Registrar Inscripción
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
