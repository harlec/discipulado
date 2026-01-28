<?php
/**
 * Cerrar Sesión
 */
require_once __DIR__ . '/inc/auth.php';

logout();
redirect(SITE_URL . '/index.php');
