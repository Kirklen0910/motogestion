<?php
require_once '../../config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test de Usuarios</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='container mt-5'>
    <div class='card'>
        <div class='card-header bg-warning'>
            <h3>🔍 Prueba de Usuarios y Contraseñas</h3>
        </div>
        <div class='card-body'>
";

// Obtener todos los usuarios
$users = $pdo->query("SELECT id, username, fullname, role, password FROM users")->fetchAll();

echo "<table class='table table-bordered'>
        <thead>
            <tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Rol</th><th>Hash Password</th><th>Verificación</th></tr>
        </thead>
        <tbody>";

$testPassword = 'ingsof2026';

foreach ($users as $user) {
    $passwordValid = password_verify($testPassword, $user['password']);
    
    $status = $passwordValid ? '✅ Válida' : '❌ Inválida';
    $class = $passwordValid ? 'table-success' : 'table-danger';
    
    echo "<tr class='$class'>
            <td>{$user['id']}</td>
            <td><strong>{$user['username']}</strong></td>
            <td>{$user['fullname']}</td>
            <td>{$user['role']}</td>
            <td><small>" . substr($user['password'], 0, 30) . "...</small></td>
            <td><strong>$status</strong> (probando: $testPassword)</td>
          </tr>";
}

echo "</tbody></table>";

// Probar login manual para vendedor
echo "<hr>";
echo "<h4>Prueba de login manual para 'vendedor':</h4>";

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute(['vendedor']);
$vendedor = $stmt->fetch();

if ($vendedor) {
    if (password_verify('ingsof2026', $vendedor['password'])) {
        echo "<div class='alert alert-success'>✅ Contraseña correcta para 'vendedor'</div>";
        
        // Simular login
        $_SESSION['test_id'] = $vendedor['id'];
        $_SESSION['test_username'] = $vendedor['username'];
        $_SESSION['test_role'] = $vendedor['role'];
        
        echo "<div class='alert alert-info'>Sesión de prueba creada con rol: <strong>{$vendedor['role']}</strong></div>";
        echo "<a href='/modules/dashboard/index.php' class='btn btn-primary'>Ir al Dashboard con rol de vendedor</a>";
        
    } else {
        echo "<div class='alert alert-danger'>❌ Contraseña incorrecta para 'vendedor'</div>";
        echo "<p>Regenerando hash correcto para vendedor...</p>";
        
        $newHash = password_hash('ingsof2026', PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'vendedor'");
        $update->execute([$newHash]);
        
        echo "<div class='alert alert-success'>✅ Hash regenerado para vendedor. Intenta de nuevo.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>❌ Usuario 'vendedor' no existe</div>";
}

echo "
        </div>
    </div>
</body>
</html>";
?>