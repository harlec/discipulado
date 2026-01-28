<?php
/**
 * Funciones de autenticación
 */

require_once __DIR__ . '/config.php';

/**
 * Intentar iniciar sesión
 */
function login($email, $password) {
    $user = Sdba::table('miembros')
        ->where('email', $email)
        ->where('activo', 1)
        ->get_one();

    if (!$user) {
        return ['success' => false, 'message' => 'Usuario no encontrado'];
    }

    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Contraseña incorrecta'];
    }

    // Guardar en sesión
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nombre'] = $user['nombres'] . ' ' . $user['apellidos'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_rol'] = $user['rol_id'];

    return ['success' => true, 'user' => $user];
}

/**
 * Cerrar sesión
 */
function logout() {
    $_SESSION = [];
    session_destroy();
}

/**
 * Cambiar contraseña
 */
function cambiarPassword($user_id, $password_actual, $password_nueva) {
    $user = Sdba::table('miembros')->where('id', $user_id)->get_one();

    if (!$user) {
        return ['success' => false, 'message' => 'Usuario no encontrado'];
    }

    if (!password_verify($password_actual, $user['password'])) {
        return ['success' => false, 'message' => 'Contraseña actual incorrecta'];
    }

    $hash = password_hash($password_nueva, PASSWORD_DEFAULT);
    Sdba::table('miembros')->where('id', $user_id)->update(['password' => $hash]);

    return ['success' => true, 'message' => 'Contraseña actualizada'];
}

/**
 * Registrar nuevo usuario (para inscripción online)
 */
function registrarUsuario($datos) {
    // Verificar si el email ya existe
    $existe = Sdba::table('miembros')->where('email', $datos['email'])->get_one();

    if ($existe) {
        return ['success' => false, 'message' => 'El correo electrónico ya está registrado'];
    }

    // Encriptar contraseña
    $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
    $datos['rol_id'] = 4; // Alumno por defecto
    $datos['activo'] = 1;
    $datos['fecha_registro'] = date('Y-m-d H:i:s');

    Sdba::table('miembros')->insert($datos);
    $id = Sdba::table('miembros')->insert_id();

    if ($id) {
        return ['success' => true, 'id' => $id, 'message' => 'Registro exitoso'];
    }

    return ['success' => false, 'message' => 'Error al registrar'];
}
