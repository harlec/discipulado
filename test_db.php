<?php
/**
 * Test de conexión a base de datos
 * ELIMINAR DESPUÉS DE USAR
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de Conexión</h2>";

// Test 1: Cargar SDBA
echo "<h3>1. Cargando SDBA...</h3>";
try {
    require_once __DIR__ . '/inc/sdba/sdba.php';
    echo "<p style='color:green;'>OK - SDBA cargado</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Conexión a BD
echo "<h3>2. Conectando a la base de datos...</h3>";
try {
    $test = Sdba::table('miembros')->total();
    echo "<p style='color:green;'>OK - Conexión exitosa</p>";
    echo "<p>Total de miembros en la tabla: <strong>$test</strong></p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>Error de conexión: " . $e->getMessage() . "</p>";
    exit;
}

// Test 3: Obtener miembros
echo "<h3>3. Obteniendo miembros...</h3>";
try {
    $miembros = Sdba::table('miembros')->get(5);
    echo "<p style='color:green;'>OK - Query ejecutado</p>";

    if (empty($miembros)) {
        echo "<p>No hay miembros en la base de datos.</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Apellidos</th><th>Nombres</th><th>Email</th></tr>";
        foreach ($miembros as $m) {
            echo "<tr>";
            echo "<td>" . ($m['id'] ?? '-') . "</td>";
            echo "<td>" . ($m['apellidos'] ?? '-') . "</td>";
            echo "<td>" . ($m['nombres'] ?? '-') . "</td>";
            echo "<td>" . ($m['email'] ?? '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}

// Test 4: Verificar tablas
echo "<h3>4. Verificando tablas...</h3>";
try {
    $tablas = ['miembros', 'roles', 'niveles', 'cursos', 'inscripciones', 'clases', 'asistencias', 'calificaciones'];
    foreach ($tablas as $tabla) {
        $count = Sdba::table($tabla)->total();
        echo "<p>Tabla <strong>$tabla</strong>: $count registros</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr><p><a href='index.php'>Ir al Login</a> | <a href='modules/miembros/'>Ir a Miembros</a></p>";
echo "<p style='color:orange;'>Recuerda eliminar este archivo (test_db.php) después de usarlo.</p>";
?>
