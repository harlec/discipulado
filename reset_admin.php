<?php
/**
 * Script para resetear contraseña del admin
 * ELIMINAR DESPUÉS DE USAR
 */
require_once __DIR__ . '/inc/sdba/sdba.php';

// Nueva contraseña
$nueva_password = 'admin123';
$hash = password_hash($nueva_password, PASSWORD_DEFAULT);

echo "<h2>Reset de Contraseña Admin</h2>";
echo "<p>Hash generado: <code>$hash</code></p>";

// Actualizar en la base de datos
try {
    $result = Sdba::table('miembros')
        ->where('email', 'admin@iglesia.com')
        ->update(['password' => $hash]);

    if ($result !== false) {
        echo "<p style='color:green;font-weight:bold;'>Contraseña actualizada correctamente!</p>";
        echo "<p>Usuario: <strong>admin@iglesia.com</strong></p>";
        echo "<p>Contraseña: <strong>admin123</strong></p>";
        echo "<hr>";
        echo "<p style='color:red;'>IMPORTANTE: Elimina este archivo (reset_admin.php) después de usarlo.</p>";
        echo "<p><a href='index.php'>Ir al Login</a></p>";
    } else {
        echo "<p style='color:orange;'>No se encontró el usuario o no hubo cambios.</p>";

        // Verificar si existe el usuario
        $user = Sdba::table('miembros')->where('email', 'admin@iglesia.com')->get_one();
        if (!$user) {
            echo "<p>Creando usuario admin...</p>";
            Sdba::table('miembros')->insert([
                'apellidos' => 'Administrador',
                'nombres' => 'Sistema',
                'email' => 'admin@iglesia.com',
                'password' => $hash,
                'rol_id' => 1,
                'activo' => 1
            ]);
            echo "<p style='color:green;'>Usuario admin creado!</p>";
            echo "<p><a href='index.php'>Ir al Login</a></p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>
