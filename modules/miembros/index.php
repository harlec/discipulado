<?php
/**
 * Lista de Miembros
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../inc/config.php';
requireRole([1, 2, 3]);

$pageTitle = 'Miembros';
$pageSubtitle = 'Gestión de miembros de la iglesia';

// Filtros
$buscar = $_GET['buscar'] ?? '';
$rol = $_GET['rol'] ?? '';
$estado = $_GET['estado'] ?? '1';

// Query base - simplificada
$query = Sdba::table('miembros')
    ->fields('id,apellidos,nombres,celular,email,grupo_familiar,activo,fecha_registro,rol_id');

// Aplicar filtros
if ($buscar) {
    $query->like('apellidos', $buscar);
}

if ($rol) {
    $query->where('rol_id', $rol);
}

if ($estado !== '') {
    $query->where('activo', $estado);
}

$query->order_by('apellidos', 'asc');

// Paginación
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 20;
$total = $query->total();
$totalPaginas = ceil($total / $porPagina);
$inicio = ($pagina - 1) * $porPagina;

$miembros = $query->get($porPagina, $inicio);

global $ROLES;

include TEMPLATES_PATH . '/header.php';
include TEMPLATES_PATH . '/sidebar.php';
?>

<main class="flex-1 flex flex-col overflow-hidden">
    <?php include TEMPLATES_PATH . '/topbar.php'; ?>

    <div class="flex-1 overflow-y-auto p-6">
        <!-- Toolbar -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Search -->
                <form method="GET" class="flex-1 flex gap-2">
                    <input type="text" name="buscar" value="<?= e($buscar) ?>"
                           placeholder="Buscar por nombre, celular o email..."
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <select name="rol" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los roles</option>
                        <?php foreach ($ROLES as $id => $nombre): ?>
                        <option value="<?= $id ?>" <?= $rol == $id ? 'selected' : '' ?>><?= e($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="estado" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="1" <?= $estado === '1' ? 'selected' : '' ?>>Activos</option>
                        <option value="0" <?= $estado === '0' ? 'selected' : '' ?>>Inactivos</option>
                        <option value="" <?= $estado === '' ? 'selected' : '' ?>>Todos</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>

                <!-- Actions -->
                <?php if (hasRole([1])): ?>
                <a href="crear.php" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nuevo Miembro
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Miembro</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($miembros)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No se encontraron miembros
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($miembros as $m): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-medium">
                                        <?= strtoupper(substr($m['nombres'], 0, 1) . substr($m['apellidos'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900"><?= e($m['apellidos'] . ', ' . $m['nombres']) ?></p>
                                        <p class="text-sm text-gray-500">Registrado: <?= formatDate($m['fecha_registro']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900"><?= e($m['celular'] ?: '-') ?></p>
                                <p class="text-sm text-gray-500"><?= e($m['email'] ?: '-') ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    <?php
                                    switch($m['rol_id']) {
                                        case 1: echo 'bg-red-100 text-red-800'; break;
                                        case 2: echo 'bg-blue-100 text-blue-800'; break;
                                        case 3: echo 'bg-green-100 text-green-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?= e($ROLES[$m['rol_id'] ?? 4] ?? 'Alumno') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= $m['grupo_familiar'] ? 'GF ' . $m['grupo_familiar'] : '-' ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?= $m['activo'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                    <?= $m['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="ver.php?id=<?= $m['id'] ?>" class="inline-flex items-center px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded transition">
                                    Ver
                                </a>
                                <?php if (hasRole([1])): ?>
                                <a href="editar.php?id=<?= $m['id'] ?>" class="inline-flex items-center px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded transition">
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

            <!-- Pagination -->
            <?php if ($totalPaginas > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Mostrando <?= $inicio + 1 ?> - <?= min($inicio + $porPagina, $total) ?> de <?= $total ?> miembros
                </p>
                <div class="flex space-x-2">
                    <?php if ($pagina > 1): ?>
                    <a href="?pagina=<?= $pagina - 1 ?>&buscar=<?= urlencode($buscar) ?>&rol=<?= $rol ?>&estado=<?= $estado ?>"
                       class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50 transition">Anterior</a>
                    <?php endif; ?>
                    <?php if ($pagina < $totalPaginas): ?>
                    <a href="?pagina=<?= $pagina + 1 ?>&buscar=<?= urlencode($buscar) ?>&rol=<?= $rol ?>&estado=<?= $estado ?>"
                       class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50 transition">Siguiente</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
