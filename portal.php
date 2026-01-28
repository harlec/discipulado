<?php
/**
 * Portal Principal - Mapa de Procesos
 * Gestión Educativa - Tabernáculo de la Fe
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/inc/config.php';
requireLogin();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal - <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .card-strategic {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        .card-operative {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        }
        .card-support {
            background: linear-gradient(135deg, #facc15 0%, #eab308 100%);
            color: #1f2937;
        }
        .card-school {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        .process-card {
            transition: all 0.3s ease;
        }
        .process-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }
        .flow-arrow {
            color: #6b7280;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 to-slate-200 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Tabernáculo de la Fe</h1>
                    <p class="text-sm text-gray-500">Sistema de Gestión Educativa</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">
                    Bienvenido, <strong><?= e($user['nombres']) ?></strong>
                </span>
                <a href="<?= SITE_URL ?>/logout.php" class="text-sm text-red-600 hover:text-red-800">
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <!-- Título del Mapa -->
        <div class="text-center mb-8">
            <div class="inline-block bg-blue-600 text-white px-8 py-3 rounded-full text-lg font-semibold shadow-lg">
                Mapa de Procesos
            </div>
            <p class="mt-3 text-gray-600">Gestión Educativa en la Institución Religiosa</p>
        </div>

        <!-- Contenedor Principal con bordes -->
        <div class="relative border-2 border-gray-300 rounded-2xl p-6 bg-white/50">

            <!-- Etiqueta Izquierda -->
            <div class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-1/2 bg-blue-100 text-blue-800 px-3 py-2 rounded-lg text-xs font-medium writing-vertical transform -rotate-180" style="writing-mode: vertical-rl;">
                Necesidades y Expectativas de los Actores Educativos
            </div>

            <!-- Etiqueta Derecha -->
            <div class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-1/2 bg-green-100 text-green-800 px-3 py-2 rounded-lg text-xs font-medium" style="writing-mode: vertical-rl;">
                Satisfacción de los Actores Educativos
            </div>

            <!-- NIVEL ESTRATÉGICO -->
            <div class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl mx-auto">
                    <a href="#" class="process-card card-strategic text-white rounded-xl p-5 text-center shadow-lg">
                        <h3 class="font-semibold text-lg">Gestión de Planificación</h3>
                        <p class="text-sm opacity-90 mt-1">Estratégica</p>
                    </a>
                    <a href="#" class="process-card card-strategic text-white rounded-xl p-5 text-center shadow-lg">
                        <h3 class="font-semibold text-lg">Gestión de la</h3>
                        <p class="text-sm opacity-90 mt-1">Calidad</p>
                    </a>
                </div>
            </div>

            <!-- Flecha hacia abajo -->
            <div class="text-center mb-4">
                <svg class="w-6 h-6 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </div>

            <!-- NIVEL OPERATIVO -->
            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                <!-- Gestión Pedagógica -->
                <div class="text-center mb-4">
                    <span class="inline-block bg-orange-100 text-orange-800 px-4 py-2 rounded-full text-sm font-semibold">
                        Gestión Pedagógica
                    </span>
                </div>

                <!-- Flujo de Procesos -->
                <div class="flex justify-center items-center gap-2 mb-6 overflow-x-auto pb-2">
                    <a href="#" class="process-card card-operative text-white rounded-lg px-4 py-3 text-center shadow-lg flex-shrink-0">
                        <span class="font-semibold">Admisión</span>
                    </a>
                    <svg class="w-5 h-5 flow-arrow flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <a href="#" class="process-card card-operative text-white rounded-lg px-4 py-3 text-center shadow-lg flex-shrink-0">
                        <span class="font-semibold">Matrícula</span>
                    </a>
                    <svg class="w-5 h-5 flow-arrow flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <a href="#" class="process-card card-operative text-white rounded-lg px-4 py-3 text-center shadow-lg flex-shrink-0">
                        <div class="font-semibold">Planificación</div>
                        <div class="text-xs opacity-90">Enseñanza-Aprendizaje</div>
                    </a>
                    <svg class="w-5 h-5 flow-arrow flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <a href="#" class="process-card card-operative text-white rounded-lg px-4 py-3 text-center shadow-lg flex-shrink-0">
                        <div class="font-semibold">Enseñanza</div>
                        <div class="text-xs opacity-90">Aprendizaje</div>
                    </a>
                    <svg class="w-5 h-5 flow-arrow flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <a href="#" class="process-card card-operative text-white rounded-lg px-4 py-3 text-center shadow-lg flex-shrink-0">
                        <div class="font-semibold">Evaluación</div>
                    </a>
                    <svg class="w-5 h-5 flow-arrow flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <a href="#" class="process-card card-operative text-white rounded-lg px-4 py-3 text-center shadow-lg flex-shrink-0">
                        <span class="font-semibold">Graduación</span>
                    </a>
                    <svg class="w-5 h-5 flow-arrow flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <a href="#" class="process-card card-operative text-white rounded-lg px-4 py-3 text-center shadow-lg flex-shrink-0">
                        <div class="font-semibold">Seguimiento</div>
                        <div class="text-xs opacity-90">al Egresado</div>
                    </a>
                </div>

                <!-- Sub-proceso: Consejería -->
                <div class="text-center mb-6">
                    <span class="inline-block bg-orange-50 text-orange-700 px-3 py-1 rounded-full text-xs">
                        (Consejería Pastoral)
                    </span>
                </div>

                <!-- Flecha hacia escuelas -->
                <div class="text-center mb-4">
                    <svg class="w-6 h-6 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>

                <!-- Las Tres Escuelas -->
                <div class="max-w-2xl mx-auto">
                    <div class="grid grid-cols-3 gap-3">
                        <a href="#" class="process-card card-school text-white rounded-lg p-3 text-center shadow-md">
                            <svg class="w-6 h-6 mx-auto mb-1 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <h3 class="font-medium text-sm">Escuela Dominical</h3>
                        </a>
                        <a href="#" class="process-card card-school text-white rounded-lg p-3 text-center shadow-md">
                            <svg class="w-6 h-6 mx-auto mb-1 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                            </svg>
                            <h3 class="font-medium text-sm">Escuela Castillo del Rey</h3>
                        </a>
                        <a href="<?= SITE_URL ?>/dashboard.php" class="process-card card-school text-white rounded-lg p-3 text-center shadow-md relative overflow-hidden">
                            <div class="absolute top-1 right-1 bg-white/20 px-1.5 py-0.5 rounded text-xs">
                                Activo
                            </div>
                            <svg class="w-6 h-6 mx-auto mb-1 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="font-medium text-sm">Escuela de Discipulado</h3>
                            <p class="text-xs opacity-90">y Liderazgo</p>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Flecha hacia abajo -->
            <div class="text-center mb-4">
                <svg class="w-6 h-6 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                </svg>
            </div>

            <!-- NIVEL DE SOPORTE -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <a href="#" class="process-card card-support rounded-xl p-4 text-center shadow-md">
                    <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h4 class="font-medium text-sm">Tutoría y Consejería</h4>
                    <p class="text-xs opacity-75">Pastoral</p>
                </a>
                <a href="#" class="process-card card-support rounded-xl p-4 text-center shadow-md">
                    <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h4 class="font-medium text-sm">Gestión de</h4>
                    <p class="text-xs opacity-75">Recursos</p>
                </a>
                <a href="#" class="process-card card-support rounded-xl p-4 text-center shadow-md">
                    <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <h4 class="font-medium text-sm">Talento</h4>
                    <p class="text-xs opacity-75">Humano</p>
                </a>
                <a href="#" class="process-card card-support rounded-xl p-4 text-center shadow-md">
                    <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <h4 class="font-medium text-sm">Sistemas de</h4>
                    <p class="text-xs opacity-75">Información</p>
                </a>
                <a href="#" class="process-card card-support rounded-xl p-4 text-center shadow-md">
                    <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <h4 class="font-medium text-sm">Salud y</h4>
                    <p class="text-xs opacity-75">Bienestar</p>
                </a>
                <a href="#" class="process-card card-support rounded-xl p-4 text-center shadow-md">
                    <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                    <h4 class="font-medium text-sm">Servicio</h4>
                    <p class="text-xs opacity-75">Comunitario</p>
                </a>
            </div>
        </div>

        <!-- Leyenda -->
        <div class="mt-6 bg-white rounded-xl p-4 shadow-sm">
            <h4 class="font-semibold text-gray-700 mb-3">Leyenda</h4>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center">
                    <span class="w-4 h-4 bg-red-500 rounded mr-2"></span>
                    <span class="text-sm text-gray-600">Procesos Estratégicos</span>
                </div>
                <div class="flex items-center">
                    <span class="w-4 h-4 bg-orange-500 rounded mr-2"></span>
                    <span class="text-sm text-gray-600">Procesos Operativos</span>
                </div>
                <div class="flex items-center">
                    <span class="w-4 h-4 bg-blue-500 rounded mr-2"></span>
                    <span class="text-sm text-gray-600">Escuelas</span>
                </div>
                <div class="flex items-center">
                    <span class="w-4 h-4 bg-yellow-400 rounded mr-2"></span>
                    <span class="text-sm text-gray-600">Procesos de Soporte</span>
                </div>
            </div>
        </div>

        <!-- Mejora Continua -->
        <div class="mt-4 text-center">
            <span class="inline-flex items-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Mejora Continua - Servicio Social
            </span>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-8">
        <div class="max-w-7xl mx-auto px-4 py-4 text-center text-sm text-gray-500">
            <?= date('Y') ?> - Tabernáculo de la Fe - Sistema de Gestión Educativa
        </div>
    </footer>
</body>
</html>
