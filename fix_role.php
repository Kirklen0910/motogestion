<?php
require_once '../../config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Fix Roles</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='container mt-5'>
    <div class='card'>
        <div class='card-header bg-danger text-white'>
            <h3>🔧 Reparando Roles de Usuario</h3>
        </div>
        <div class='card-body'>
";

try {
    // 1. Ver usuarios actuales
    echo "<h5>Usuarios antes de actualizar:</h5>";
    $users = $pdo->query("SELECT id, username, role FROM users")->fetchAll();
    echo "<table class='table table-bordered'><tr><th>ID</th><th>Usuario</th><th>Rol Actual</th></tr>";
    foreach ($users as $user) {
        echo "<tr><td>{$user['id']}</td><td>{$user['username']}</td><td>{$user['role']}</td></tr>";
    }
    echo "</table>";
    
    // 2. Modificar la columna ENUM para aceptar más roles
    echo "<p>📝 Modificando columna role para aceptar más valores...</p>";
    $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'seller', 'superadmin', 'finance', 'inventory', 'cashier') DEFAULT 'seller'");
    echo "<div class='alert alert-success'>✅ Columna role modificada correctamente</div>";
    
    // 3. Actualizar usuario admin a superadmin
    echo "<p>📝 Actualizando usuario admin a superadmin...</p>";
    $stmt = $pdo->prepare("UPDATE users SET role = 'superadmin' WHERE username = 'admin' OR id = 1");
    $stmt->execute();
    echo "<div class='alert alert-success'>✅ Usuario admin actualizado a superadmin</div>";
    
    // 4. Crear usuarios de ejemplo con diferentes roles
    $passwordHash = '$2y$10$D2ovvdmgC6xINzNgezgevG0ozDRVBRxxvqu.av2KLIi7G93m0CGu'; // ingsof2026
    
    $newUsers = [
        ['vendedor', 'Juan Vendedor', 'seller'],
        ['finanzas', 'Carlos Finanzas', 'finance'],
        ['inventario', 'Ana Inventario', 'inventory'],
        ['cajero', 'Roberto Cajero', 'cashier']
    ];
    
    foreach ($newUsers as $newUser) {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$newUser[0]]);
        if (!$check->fetch()) {
            $insert = $pdo->prepare("INSERT INTO users (username, fullname, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
            $insert->execute([$newUser[0], $newUser[1], $passwordHash, $newUser[2]]);
            echo "<div class='alert alert-info'>✅ Usuario '{$newUser[0]}' creado con rol '{$newUser[2]}'</div>";
        } else {
            echo "<div class='alert alert-warning'>⚠️ Usuario '{$newUser[0]}' ya existe</div>";
        }
    }
    
    // 5. Ver resultados finales
    echo "<h5>Usuarios después de actualizar:</h5>";
    $users = $pdo->query("SELECT id, username, role FROM users ORDER BY id")->fetchAll();
    echo "<table class='table table-bordered'><tr><th>ID</th><th>Usuario</th><th>Rol</th></tr>";
    foreach ($users as $user) {
        $badge = match($user['role']) {
            'superadmin' => 'danger',
            'admin' => 'primary',
            'finance' => 'success',
            'inventory' => 'info',
            'seller' => 'secondary',
            'cashier' => 'warning',
            default => 'dark'
        };
        echo "<tr><td>{$user['id']}</td><td>{$user['username']}</td><td><span class='badge bg-{$badge}'>{$user['role']}</span></td></tr>";
    }
    echo "</table>";
    
    // 6. Actualizar sesión actual si existe
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $currentUser = $stmt->fetch();
        if ($currentUser) {
            $_SESSION['role'] = $currentUser['role'];
            echo "<div class='alert alert-success'>✅ Tu sesión ha sido actualizada con rol: <strong>{$currentUser['role']}</strong></div>";
        }
    }
    
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "
            <hr>
            <div class='alert alert-warning'>
                <strong>⚠️ IMPORTANTE:</strong> 
                <ul>
                    <li><a href='/modules/auth/logout.php'>Cierra sesión</a> y vuelve a iniciar</li>
                    <li><strong>ELIMINA ESTE ARCHIVO</strong> después de usarlo</li>
                </ul>
            </div>
            <a href='/modules/auth/logout.php' class='btn btn-warning'>Cerrar Sesión</a>
            <a href='/modules/dashboard/index.php' class='btn btn-primary'>Ir al Dashboard</a>
        </div>
    </div>
</body>
</html>";
?>