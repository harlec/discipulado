<?php
/**
 * Crear Miembro
 */
require_once __DIR__ . '/../../inc/config.php';
requireRole([1]);

$pageTitle = 'Nuevo Miembro';
$pageSubtitle = 'Registrar un nuevo miembro';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'apellidos' => limpiarString($_POST['apellidos'] ?? ''),
        'nombres' => limpiarString($_POST['nombres'] ?? ''),
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?: null,
        'celular' => limpiarString($_POST['celular'] ?? ''),
        'email' => limpiarString($_POST['email'] ?? ''),
        'fecha_conversion' => $_POST['fecha_conversion'] ?: null,
        'bautismo_agua' => $_POST['bautismo_agua'] ?: null,
        'bautismo_espiritu_santo' => $_POST['bautismo_espiritu_santo'] ?: null,
        'dones_habilidades' => limpiarString($_POST['dones_habilidades'] ?? ''),
        'cargo_iglesia' => limpiarString($_POST['cargo_iglesia'] ?? ''),
        'grupo_familiar' => $_POST['grupo_familiar'] ?: null,
        'lider_id' => $_POST['lider_id'] ?: null,
        'grado_instruccion' => $_POST['grado_instruccion'] ?: null,
        'ocupacion' => limpiarString($_POST['ocupacion'] ?? ''),
        'estado_civil' => $_POST['estado_civil'] ?: null,
        'rol_id' => $_POST['rol_id'] ?? 4,
        'activo' => isset($_POST['activo']) ? 1 : 0,
    ];

    // Password opcional
    if (!empty($_POST['password'])) {
        $datos['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // Validaciones
    if (empty($datos['apellidos']) || empty($datos['nombres'])) {
        $error = 'Apellidos y nombres son obligatorios';
    } elseif (!empty($datos['email'])) {
        $existe = Sdba::table('miembros')->where('email', $datos['email'])->get_one();
        if ($existe) {
            $error = 'El correo electrónico ya está registrado';
        }
    }

    if (!$error) {
        Sdba::table('miembros')->insert($datos);
        $id = Sdba::table('miembros')->insert_id();
        if ($id) {
            setFlashMessage('success', 'Miembro creado exitosamente');
            redirect('ver.php?id=' . $id);
        } else {
            $error = 'Error al guardar el miembro';
        }
    }
}

$lideres = getLideres();
global $GRADOS_INSTRUCCION, $ESTADOS_CIVILES, $ROLES;

include TEMPLATES_PATH . '/header.php';
include TEMPLATES_PATH . '/sidebar.php';
?>

<main class="flex-1 flex flex-col overflow-hidden">
    <?php include TEMPLATES_PATH . '/topbar.php'; ?>

    <div class="flex-1 overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <?= e($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-6">
                    <!-- Datos Personales -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Datos Personales</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                                <input type="text" name="apellidos" required
                                       value="<?= e($_POST['apellidos'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                                <input type="text" name="nombres" required
                                       value="<?= e($_POST['nombres'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento"
                                       value="<?= e($_POST['fecha_nacimiento'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                                <input type="tel" name="celular"
                                       value="<?= e($_POST['celular'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                <input type="email" name="email"
                                       value="<?= e($_POST['email'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado Civil</label>
                                <select name="estado_civil" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($ESTADOS_CIVILES as $key => $val): ?>
                                    <option value="<?= $key ?>" <?= ($_POST['estado_civil'] ?? '') == $key ? 'selected' : '' ?>><?= e($val) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grado de Instrucción</label>
                                <select name="grado_instruccion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($GRADOS_INSTRUCCION as $key => $val): ?>
                                    <option value="<?= $key ?>" <?= ($_POST['grado_instruccion'] ?? '') == $key ? 'selected' : '' ?>><?= e($val) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ocupación / Profesión</label>
                                <input type="text" name="ocupacion"
                                       value="<?= e($_POST['ocupacion'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Datos Espirituales -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Datos Espirituales</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Conversión</label>
                                <input type="date" name="fecha_conversion"
                                       value="<?= e($_POST['fecha_conversion'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bautismo en Agua</label>
                                <input type="date" name="bautismo_agua"
                                       value="<?= e($_POST['bautismo_agua'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bautismo en el Espíritu Santo (Año)</label>
                                <input type="number" name="bautismo_espiritu_santo" min="1950" max="<?= date('Y') ?>"
                                       value="<?= e($_POST['bautismo_espiritu_santo'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cargo en la Iglesia</label>
                                <input type="text" name="cargo_iglesia"
                                       value="<?= e($_POST['cargo_iglesia'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grupo Familiar Nº</label>
                                <input type="number" name="grupo_familiar" min="1"
                                       value="<?= e($_POST['grupo_familiar'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Líder</label>
                                <select name="lider_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($lideres as $lider): ?>
                                    <option value="<?= $lider['id'] ?>" <?= ($_POST['lider_id'] ?? '') == $lider['id'] ? 'selected' : '' ?>>
                                        <?= e($lider['apellidos'] . ', ' . $lider['nombres']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dones o Habilidades</label>
                                <textarea name="dones_habilidades" rows="2"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= e($_POST['dones_habilidades'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del Sistema -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Datos del Sistema</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                                <select name="rol_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <?php foreach ($ROLES as $id => $nombre): ?>
                                    <option value="<?= $id ?>" <?= ($_POST['rol_id'] ?? 4) == $id ? 'selected' : '' ?>><?= e($nombre) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña (opcional)</label>
                                <input type="password" name="password" minlength="6"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Dejar vacío si no requiere acceso">
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="activo" id="activo" checked
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="activo" class="ml-2 text-sm text-gray-700">Miembro activo</label>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="index.php" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Guardar Miembro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
