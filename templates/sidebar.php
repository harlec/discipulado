<!-- Sidebar -->
<aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-primary-800 text-white transition-all duration-300 flex flex-col">
    <!-- Logo -->
    <div class="p-4 border-b border-primary-700">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-primary-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span x-show="sidebarOpen" class="font-semibold text-lg">EDYL</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-2">
        <!-- Dashboard -->
        <a href="<?= SITE_URL ?>/dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-primary-700 transition <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-primary-700' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span x-show="sidebarOpen">Dashboard</span>
        </a>

        <!-- Miembros -->
        <?php if (hasRole([1, 2, 3])): ?>
        <a href="<?= SITE_URL ?>/modules/miembros/" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-primary-700 transition <?= strpos($_SERVER['PHP_SELF'], '/miembros/') !== false ? 'bg-primary-700' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span x-show="sidebarOpen">Miembros</span>
        </a>
        <?php endif; ?>

        <!-- Cursos -->
        <?php if (hasRole([1, 2])): ?>
        <a href="<?= SITE_URL ?>/modules/cursos/" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-primary-700 transition <?= strpos($_SERVER['PHP_SELF'], '/cursos/') !== false ? 'bg-primary-700' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <span x-show="sidebarOpen">Cursos</span>
        </a>
        <?php endif; ?>

        <!-- Inscripciones -->
        <a href="<?= SITE_URL ?>/modules/inscripciones/" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-primary-700 transition <?= strpos($_SERVER['PHP_SELF'], '/inscripciones/') !== false ? 'bg-primary-700' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span x-show="sidebarOpen">Inscripciones</span>
        </a>

        <!-- Asistencia -->
        <?php if (hasRole([1, 2])): ?>
        <a href="<?= SITE_URL ?>/modules/asistencia/" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-primary-700 transition <?= strpos($_SERVER['PHP_SELF'], '/asistencia/') !== false ? 'bg-primary-700' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span x-show="sidebarOpen">Asistencia</span>
        </a>
        <?php endif; ?>

        <!-- Calificaciones -->
        <?php if (hasRole([1, 2])): ?>
        <a href="<?= SITE_URL ?>/modules/calificaciones/" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-primary-700 transition <?= strpos($_SERVER['PHP_SELF'], '/calificaciones/') !== false ? 'bg-primary-700' : '' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
            </svg>
            <span x-show="sidebarOpen">Calificaciones</span>
        </a>
        <?php endif; ?>

        <!-- Mi Historial (para alumnos) -->
        <?php if (hasRole([4])): ?>
        <a href="<?= SITE_URL ?>/modules/miembros/historial.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-primary-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span x-show="sidebarOpen">Mi Historial</span>
        </a>
        <?php endif; ?>
    </nav>

    <!-- Toggle Button -->
    <div class="p-4 border-t border-primary-700">
        <button @click="sidebarOpen = !sidebarOpen" class="w-full flex items-center justify-center p-2 rounded-lg hover:bg-primary-700 transition">
            <svg x-show="sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
            </svg>
            <svg x-show="!sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>
</aside>
