<?php
/**
 * ARCHIVO DE MIGRACIÓN - Ejecutar UNA SOLA VEZ
 * Convierte todas las contraseñas en texto plano a hash seguro
 * 
 * IMPORTANTE: Eliminar o mover este archivo después de usarlo
 */

require_once '../../config/database.php';
session_start();

// Verificar que solo el admin pueda ejecutar esto
if (!isset($_SESSION['user_id'])) {
    die('Necesitas iniciar sesión como administrador');
}

// Verificar si el usuario es admin (opcional, por seguridad)
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['role'] !== 'admin') {
    die('Solo el administrador puede ejecutar esta migración');
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Migración de Contraseñas</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='container mt-5'>
    <div class='card'>
        <div class='card-header bg-warning'>
            <h3>⚠️ Migración de Contraseñas</h3>
        </div>
        <div class='card-body'>
";

// Obtener todos los usuarios
$users = $pdo->query("SELECT id, username, password FROM users")->fetchAll();
$migrated = 0;
$errors = 0;

foreach ($users as $user) {
    // Verificar si la contraseña NO está hasheada
    if (strpos($user['password'], '$2y$') !== 0) {
        // Es texto plano, la hasheamos
        $newHash = password_hash($user['password'], PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        
        if ($updateStmt->execute([$newHash, $user['id']])) {
            $migrated++;
            echo "<div class='alert alert-success'>✅ Usuario '{$user['username']}' - Contraseña migrada correctamente</div>";
        } else {
            $errors++;
            echo "<div class='alert alert-danger'>❌ Error al migrar usuario '{$user['username']}'</div>";
        }
    } else {
        echo "<div class='alert alert-info'>ℹ️ Usuario '{$user['username']}' - Ya tiene contraseña hasheada</div>";
    }
}

echo "
        <hr>
        <div class='alert alert-primary'>
            <strong>Resumen:</strong><br>
            Usuarios migrados: $migrated<br>
            Errores: $errors<br>
            Total usuarios procesados: " . count($users) . "
        </div>
        <div class='alert alert-danger'>
            <strong>⚠️ IMPORTANTE:</strong> Elimina o mueve este archivo después de usarlo por seguridad.
        </div>
        <a href='/modules/dashboard/index.php' class='btn btn-primary'>Ir al Dashboard</a>
        </div>
    </div>
</body>
</html>";
?>