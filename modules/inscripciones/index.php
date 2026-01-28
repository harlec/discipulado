<?php
/**
 * Lista de Inscripciones
 */
require_once __DIR__ . '/../../inc/config.php';
requireLogin();

$pageTitle = 'Inscripciones';
$pageSubtitle = 'Gestión de inscripciones a cursos';

// Filtros
$ciclo = $_GET['ciclo'] ?? getCicloActual();
$estado = $_GET['estado'] ?? '';

// Query base
$query = Sdba::table('inscripciones')
    ->left_join('miembro_id', 'miembros', 'id')
    ->left_join('curso_id', 'cursos', 'id')
    ->left_join('nivel_id', 'niveles', 'id', 'cursos')
    ->fields('id,estado,nota_final,fecha_inscripcion', false, 'inscripciones')
    ->fields('id,apellidos,nombres', false, 'miembros')
    ->fields('ciclo,modulo', false, 'cursos')
    ->fields('nombre', false, 'niveles')
    ->alias('id', 'miembro_id', 'miembros');

// Filtros según rol
if (hasRole([4])) {
    // Alumno solo ve sus inscripciones
    $query->where('miembro_id', $_SESSION['user_id'], 'inscripciones');
} elseif (hasRole([2]) && !hasRole([1])) {
    // Maestro solo ve inscripciones de sus cursos
    $query->where('maestro_id', $_SESSION['user_id'], 'cursos');
}

if ($ciclo) {
    $query->where('ciclo', $ciclo, 'cursos');
}

if ($estado) {
    $query->where('estado', $estado, 'inscripciones');
}

$query->order_by('fecha_inscripcion', 'desc', 'inscripciones');

$inscripciones = $query->get();

// Ciclos disponibles
$ciclosDisponibles = Sdba::table('cursos')->fields('ciclo')->group_by('ciclo')->order_by('ciclo', 'desc')->get_list('ciclo');

global $ESTADOS_INSCRIPCION;

include TEMPLATES_PATH . '/header.php';
include TEMPLATES_PATH . '/sidebar.php';
?>

<main class="flex-1 flex flex-col overflow-hidden">
    <?php include TEMPLATES_PATH . '/topbar.php'; ?>

    <div class="flex-1 overflow-y-auto p-6">
        <!-- Toolbar -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <form method="GET" class="flex gap-2">
                    <select name="ciclo" class="px-4 py-2 border border-gray-300 rounded-lg" onchange="this.form.submit()">
                        <option value="">Todos los ciclos</option>
                        <?php foreach ($ciclosDisponibles as $c): ?>
                        <option value="<?= $c ?>" <?= $ciclo == $c ? 'selected' : '' ?>><?= getNombreCiclo($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="estado" class="px-4 py-2 border border-gray-300 rounded-lg" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <?php foreach ($ESTADOS_INSCRIPCION as $key => $val): ?>
                        <option value="<?= $key ?>" <?= $estado == $key ? 'selected' : '' ?>><?= e($val) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <?php if (hasRole([1])): ?>
                <a href="crear.php" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nueva Inscripción
                </a>
                <?php elseif (hasRole([4])): ?>
                <a href="inscribirse.php" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Inscribirme a un Curso
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <?php if (!hasRole([4])): ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                            <?php endif; ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Curso</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ciclo</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nota</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($inscripciones)): ?>
                        <tr>
                            <td colspan="<?= hasRole([4]) ? 5 : 6 ?>" class="px-6 py-12 text-center text-gray-500">
                                No hay inscripciones registradas
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($inscripciones as $i): ?>
                        <tr class="hover:bg-gray-50">
                            <?php if (!hasRole([4])): ?>
                            <td class="px-6 py-4">
                                <a href="<?= SITE_URL ?>/modules/miembros/ver.php?id=<?= $i['miembro_id'] ?>" class="text-blue-600 hover:underline">
                                    <?= e($i['apellidos'] . ', ' . $i['nombres']) ?>
                                </a>
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-4">
                                <?= e($i['nombre']) ?>
                                <?php if ($i['modulo']): ?>
                                <span class="text-gray-500">- M<?= $i['modulo'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500"><?= getNombreCiclo($i['ciclo']) ?></td>
                            <td class="px-6 py-4 text-center font-medium"><?= $i['nota_final'] ?? '-' ?></td>
                            <td class="px-6 py-4 text-center">
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
                            <td class="px-6 py-4 text-right">
                                <?php if (hasRole([1])): ?>
                                <a href="editar.php?id=<?= $i['id'] ?>" class="text-sm text-blue-600 hover:text-blue-800">
                                    Editar
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
