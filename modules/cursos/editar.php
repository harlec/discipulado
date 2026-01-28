<?php
/**
 * Editar Curso
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../inc/config.php';
requireRole([1]);

$id = intval($_GET['id'] ?? 0);

// Obtener curso
$curso = Sdba::table('cursos')->where('id', $id)->get_one();

if (!$curso) {
    setFlashMessage('error', 'Curso no encontrado');
    redirect('index.php');
}

// Obtener nivel para el título
$nivel = Sdba::table('niveles')->where('id', $curso['nivel_id'])->get_one();

$pageTitle = 'Editar Curso';
$pageSubtitle = $nivel['nombre'] ?? 'Curso';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nivel_id' => intval($_POST['nivel_id'] ?? 0),
        'modulo' => $_POST['modulo'] ?: null,
        'ciclo' => $_POST['ciclo'] ?? getCicloActual(),
        'maestro_id' => $_POST['maestro_id'] ?: null,
        'dia_clase' => $_POST['dia_clase'] ?? '',
        'hora_inicio' => $_POST['hora_inicio'] ?: null,
        'hora_fin' => $_POST['hora_fin'] ?: null,
        'dia_oracion' => $_POST['dia_oracion'] ?? '',
        'fecha_inicio' => $_POST['fecha_inicio'] ?: null,
        'fecha_fin' => $_POST['fecha_fin'] ?: null,
        'horas_cronologicas' => intval($_POST['horas_cronologicas'] ?? 24),
        'semanas' => intval($_POST['semanas'] ?? 12),
        'activo' => isset($_POST['activo']) ? 1 : 0,
    ];

    if (!$datos['nivel_id']) {
        $error = 'Debe seleccionar un nivel';
    } else {
        $result = Sdba::table('cursos')->where('id', $id)->update($datos);
        if ($result !== false) {
            setFlashMessage('success', 'Curso actualizado exitosamente');
            redirect('ver.php?id=' . $id);
        } else {
            $error = 'Error al actualizar el curso';
        }
    }
}

$niveles = getNiveles();
$maestros = getMaestros();
global $DIAS_SEMANA;

include TEMPLATES_PATH . '/header.php';
include TEMPLATES_PATH . '/sidebar.php';
?>

<main class="flex-1 flex flex-col overflow-hidden">
    <?php include TEMPLATES_PATH . '/topbar.php'; ?>

    <div class="flex-1 overflow-y-auto p-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <?= e($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nivel *</label>
                            <select name="nivel_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($niveles as $n): ?>
                                <option value="<?= $n['id'] ?>" <?= $curso['nivel_id'] == $n['id'] ? 'selected' : '' ?>><?= e($n['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Módulo (solo Liderazgo III)</label>
                            <select name="modulo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">No aplica</option>
                                <option value="1" <?= $curso['modulo'] == '1' ? 'selected' : '' ?>>Módulo 1</option>
                                <option value="2" <?= $curso['modulo'] == '2' ? 'selected' : '' ?>>Módulo 2</option>
                                <option value="3" <?= $curso['modulo'] == '3' ? 'selected' : '' ?>>Módulo 3</option>
                                <option value="4" <?= $curso['modulo'] == '4' ? 'selected' : '' ?>>Módulo 4</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ciclo *</label>
                            <select name="ciclo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <?php
                                $year = date('Y');
                                for ($y = $year - 1; $y <= $year + 1; $y++):
                                    foreach (['10' => 'Ciclo I', '20' => 'Ciclo II', '30' => 'Ciclo III'] as $num => $nom):
                                        $val = $y . $num;
                                ?>
                                <option value="<?= $val ?>" <?= $curso['ciclo'] == $val ? 'selected' : '' ?>>
                                    <?= $nom ?> - <?= $y ?>
                                </option>
                                <?php endforeach; endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Maestro</label>
                            <select name="maestro_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sin asignar</option>
                                <?php foreach ($maestros as $m): ?>
                                <option value="<?= $m['id'] ?>" <?= $curso['maestro_id'] == $m['id'] ? 'selected' : '' ?>>
                                    <?= e($m['apellidos'] . ', ' . $m['nombres']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Día de Clase</label>
                            <select name="dia_clase" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($DIAS_SEMANA as $dia): ?>
                                <option value="<?= $dia ?>" <?= $curso['dia_clase'] == $dia ? 'selected' : '' ?>><?= $dia ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Día de Oración</label>
                            <select name="dia_oracion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($DIAS_SEMANA as $dia): ?>
                                <option value="<?= $dia ?>" <?= $curso['dia_oracion'] == $dia ? 'selected' : '' ?>><?= $dia ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora Inicio</label>
                            <input type="time" name="hora_inicio"
                                   value="<?= e($curso['hora_inicio'] ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora Fin</label>
                            <input type="time" name="hora_fin"
                                   value="<?= e($curso['hora_fin'] ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio"
                                   value="<?= e($curso['fecha_inicio'] ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                            <input type="date" name="fecha_fin"
                                   value="<?= e($curso['fecha_fin'] ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Horas Cronológicas</label>
                            <input type="number" name="horas_cronologicas" min="1"
                                   value="<?= e($curso['horas_cronologicas'] ?? 24) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Semanas</label>
                            <input type="number" name="semanas" min="1"
                                   value="<?= e($curso['semanas'] ?? 12) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="activo" id="activo" <?= $curso['activo'] ? 'checked' : '' ?>
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="activo" class="ml-2 text-sm text-gray-700">Curso activo</label>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="ver.php?id=<?= $id ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
