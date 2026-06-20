<?php
require_once '../../config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Corregir todas las contraseñas</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <meta http-equiv='refresh' content='3;url=/modules/auth/login.php'>
</head>
<body class='container mt-5'>
    <div class='card'>
        <div class='card-header bg-danger text-white'>
            <h3>🔧 Corrigiendo todas las contraseñas</h3>
        </div>
        <div class='card-body'>
";

$password = 'ingsof2026';
$newHash = password_hash($password, PASSWORD_DEFAULT);

// Obtener todos los usuarios
$users = $pdo->query("SELECT id, username, role FROM users")->fetchAll();

foreach ($users as $user) {
    $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->execute([$newHash, $user['id']]);
    
    echo "<div class='alert alert-success'>✅ Usuario '{$user['username']}' (rol: {$user['role']}) - Contraseña actualizada a: <strong>$password</strong></div>";
}

echo "
            <hr>
            <div class='alert alert-warning'>
                <strong>⚠️ Ahora puedes iniciar sesión con:</strong><br>
                Cualquier usuario y la contraseña: <code>ingsof2026</code>
            </div>
            <div class='alert alert-info'>
                <strong>Usuarios disponibles:</strong><br>
                - admin (superadmin)<br>
                - vendedor (seller)<br>
                - finanzas (finance)<br>
                - inventario (inventory)<br>
                - cajero (cashier)
            </div>
            <a href='/modules/auth/login.php' class='btn btn-primary'>Ir al Login</a>
        </div>
    </div>
</body>
</html>";
?>