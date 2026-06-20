<?php
session_start();
require_once '../../config/database.php';

// Verificar si hay sesión activa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener datos actualizados del usuario desde la base de datos
$stmt = $pdo->prepare("SELECT id, username, fullname, role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user) {
    // Actualizar la sesión con los datos más recientes
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['role'] = $user['role'];
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Actualizando Sesión</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <meta http-equiv='refresh' content='2;url=/modules/dashboard/index.php'>
    </head>
    <body class='container mt-5'>
        <div class='alert alert-success'>
            <h4>✅ Sesión actualizada correctamente</h4>
            <p>Usuario: {$user['username']}</p>
            <p>Rol: <strong>{$user['role']}</strong></p>
            <p>Redirigiendo al dashboard...</p>
        </div>
    </body>
    </html>";
} else {
    session_destroy();
    header('Location: login.php');
}
?>