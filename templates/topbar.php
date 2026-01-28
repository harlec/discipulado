<!-- Top Bar -->
<header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800"><?= e($pageTitle ?? 'Dashboard') ?></h1>
            <?php if (isset($pageSubtitle)): ?>
            <p class="text-sm text-gray-500 mt-1"><?= e($pageSubtitle) ?></p>
            <?php endif; ?>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Ciclo Actual -->
            <div class="text-sm text-gray-500">
                <span class="font-medium"><?= getNombreCiclo(getCicloActual()) ?></span>
            </div>

            <!-- User Menu -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white font-medium">
                        <?= strtoupper(substr($_SESSION['user_nombre'] ?? 'U', 0, 1)) ?>
                    </div>
                    <span class="text-gray-700"><?= e($_SESSION['user_nombre'] ?? 'Usuario') ?></span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                    <a href="<?= SITE_URL ?>/modules/miembros/perfil.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Mi Perfil
                    </a>
                    <a href="<?= SITE_URL ?>/modules/miembros/cambiar_password.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Cambiar Contraseña
                    </a>
                    <hr class="my-1">
                    <a href="<?= SITE_URL ?>/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<?php
// Mostrar mensajes flash
$flash = getFlashMessage();
if ($flash):
?>
<div class="mx-6 mt-4">
    <div class="p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800' : ($flash['type'] === 'error' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') ?>">
        <?= e($flash['message']) ?>
    </div>
</div>
<?php endif; ?>
