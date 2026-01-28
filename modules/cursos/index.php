<?php
/**
 * Lista de Cursos
 */
require_once __DIR__ . '/../../inc/config.php';
requireRole([1, 2]);

$pageTitle = 'Cursos';
$pageSubtitle = 'Gestión de cursos de la escuela';

// Filtros
$ciclo = $_GET['ciclo'] ?? getCicloActual();
$nivel = $_GET['nivel'] ?? '';

// Query
$query = Sdba::table('cursos')
    ->left_join('nivel_id', 'niveles', 'id')
    ->left_join('maestro_id', 'miembros', 'id')
    ->fields('id,modulo,ciclo,dia_clase,hora_inicio,hora_fin,fecha_inicio,fecha_fin,activo', false, 'cursos')
    ->fields('nombre,orden', false, 'niveles')
    ->fields('nombres,apellidos', false, 'miembros')
    ->alias('nombre', 'nivel_nombre', 'niveles');

if ($ciclo) {
    $query->where('ciclo', $ciclo, 'cursos');
}

if ($nivel) {
    $query->where('nivel_id', $nivel, 'cursos');
}

// Si es maestro, solo ver sus cursos
if (hasRole([2]) && !hasRole([1])) {
    $query->where('maestro_id', $_SESSION['user_id'], 'cursos');
}

$query->order_by('orden', 'asc', 'niveles');
$query->order_by('modulo', 'asc', 'cursos');

$cursos = $query->get();

// Obtener ciclos disponibles
$ciclosDisponibles = Sdba::table('cursos')->fields('ciclo')->group_by('ciclo')->order_by('ciclo', 'desc')->get_list('ciclo');
$niveles = getNiveles();

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
                    <select name="ciclo" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">Todos los ciclos</option>
                        <?php foreach ($ciclosDisponibles as $c): ?>
                        <option value="<?= $c ?>" <?= $ciclo == $c ? 'selected' : '' ?>><?= getNombreCiclo($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="nivel" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">Todos los niveles</option>
                        <?php foreach ($niveles as $n): ?>
                        <option value="<?= $n['id'] ?>" <?= $nivel == $n['id'] ? 'selected' : '' ?>><?= e($n['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <?php if (hasRole([1])): ?>
                <a href="crear.php" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nuevo Curso
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Courses Grid -->
        <?php if (empty($cursos)): ?>
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <p class="text-gray-500">No hay cursos registrados para este ciclo.</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($cursos as $curso):
                // Contar inscritos
                $inscritos = Sdba::table('inscripciones')->where('curso_id', $curso['id'])->total();
            ?>
            <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800">
                                <?= e($curso['nivel_nombre'] ?? $curso['nombre']) ?>
                                <?php if ($curso['modulo']): ?>
                                <span class="text-gray-500">- M<?= $curso['modulo'] ?></span>
                                <?php endif; ?>
                            </h3>
                            <p class="text-sm text-gray-500"><?= getNombreCiclo($curso['ciclo']) ?></p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $curso['activo'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                            <?= $curso['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </div>

                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <?= e($curso['nombres'] . ' ' . $curso['apellidos']) ?: 'Sin asignar' ?>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <?= e($curso['dia_clase']) ?> <?= e($curso['hora_inicio']) ?>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <?= $inscritos ?> inscrito(s)
                        </div>
                    </div>

                    <div class="flex space-x-2">
                        <a href="ver.php?id=<?= $curso['id'] ?>" class="flex-1 text-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium">
                            Ver Detalles
                        </a>
                        <?php if (hasRole([1])): ?>
                        <a href="editar.php?id=<?= $curso['id'] ?>" class="px-4 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition text-sm">
                            Editar
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
