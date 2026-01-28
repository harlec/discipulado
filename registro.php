<?php
/**
 * Página de Registro (Inscripción Online)
 */
require_once __DIR__ . '/inc/auth.php';

// Si ya está logueado, redirigir
if (isLoggedIn()) {
    redirect(SITE_URL . '/dashboard.php');
}

$error = '';
$success = '';

// Procesar registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'apellidos' => limpiarString($_POST['apellidos'] ?? ''),
        'nombres' => limpiarString($_POST['nombres'] ?? ''),
        'email' => limpiarString($_POST['email'] ?? ''),
        'celular' => limpiarString($_POST['celular'] ?? ''),
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
        'password' => $_POST['password'] ?? '',
    ];

    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validaciones
    if (empty($datos['apellidos']) || empty($datos['nombres']) || empty($datos['email']) || empty($datos['password'])) {
        $error = 'Por favor complete todos los campos obligatorios';
    } elseif (!validarEmail($datos['email'])) {
        $error = 'El correo electrónico no es válido';
    } elseif (strlen($datos['password']) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } elseif ($datos['password'] !== $password_confirm) {
        $error = 'Las contraseñas no coinciden';
    } else {
        $result = registrarUsuario($datos);
        if ($result['success']) {
            $success = 'Registro exitoso. Ahora puede iniciar sesión.';
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-900 to-blue-700 min-h-screen flex items-center justify-center font-sans py-8">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg mx-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Crear Cuenta</h1>
            <p class="text-gray-500 mt-1">Escuela de Discipulado y Liderazgo</p>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?= e($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <?= e($success) ?>
            <a href="<?= SITE_URL ?>/index.php" class="underline font-medium">Ir al login</a>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
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
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico *</label>
                <input type="email" name="email" required
                       value="<?= e($_POST['email'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                    <input type="tel" name="celular"
                           value="<?= e($_POST['celular'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento"
                           value="<?= e($_POST['fecha_nacimiento'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña *</label>
                    <input type="password" name="password_confirm" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 mt-6">
                Crear Cuenta
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">
                ¿Ya tienes cuenta?
                <a href="<?= SITE_URL ?>/index.php" class="text-blue-600 hover:text-blue-800 font-medium">
                    Inicia sesión
                </a>
            </p>
        </div>
    </div>
</body>
</html>
