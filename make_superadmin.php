<?php
/**
 * Archivo: /modules/auth/make_superadmin.php
 * EJECUTAR UNA SOLA VEZ para convertir al admin actual en superadmin
 * ELIMINAR después de usar
 */
require_once '../../config/database.php';
session_start();

// Verificar que hay sesión iniciada
if (!isset($_SESSION['user_id'])) {
    die('Necesitas iniciar sesión primero');
}

$currentUserId = $_SESSION['user_id'];

// Obtener información del usuario actual
$stmt = $pdo->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->execute([$currentUserId]);
$user = $stmt->fetch();

if (!$user) {
    die('Usuario no encontrado');
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Actualizar a SuperAdmin</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='container mt-5'>
    <div class='card'>
        <div class='card-header bg-danger text-white'>
            <h3>👑 Actualizar a Super Administrador</h3>
        </div>
        <div class='card-body'>
";

if ($user['role'] === 'superadmin') {
    echo "<div class='alert alert-info'>✅ El usuario '{$user['username']}' ya es Super Administrador</div>";
} else {
    // Actualizar a superadmin
    $updateStmt = $pdo->prepare("UPDATE users SET role = 'superadmin' WHERE id = ?");
    if ($updateStmt->execute([$currentUserId])) {
        // Actualizar sesión
        $_SESSION['role'] = 'superadmin';
        echo "<div class='alert alert-success'>✅ Usuario '{$user['username']}' actualizado a Super Administrador</div>";
        echo "<div class='alert alert-warning'>⚠️ Cierra sesión y vuelve a iniciar para que los cambios surtan efecto completo</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Error al actualizar el usuario</div>";
    }
}

echo "
        <hr>
        <div class='alert alert-danger'>
            <strong>⚠️ IMPORTANTE:</strong> Elimina este archivo después de usarlo por seguridad.
        </div>
        <a href='/modules/auth/logout.php' class='btn btn-warning'>Cerrar Sesión (recomendado)</a>
        <a href='/modules/dashboard/index.php' class='btn btn-primary'>Ir al Dashboard</a>
        </div>
    </div>
</body>
</html>";
?>