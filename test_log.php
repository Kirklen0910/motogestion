<?php
session_start();
require_once '../../config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Logs</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='container mt-5'>
    <div class='card'>
        <div class='card-header bg-info'>
            <h3>Prueba de Registro de Logs</h3>
        </div>
        <div class='card-body'>
";

// Verificar que la tabla existe
try {
    $result = $pdo->query("SHOW TABLES LIKE 'system_logs'");
    if ($result->rowCount() > 0) {
        echo "<div class='alert alert-success'>✅ La tabla 'system_logs' existe</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ La tabla 'system_logs' NO existe. Ejecuta el SQL primero.</div>";
    }
    
    // Probar registrar un log
    $testResult = registerLog($pdo, 'TEST', 'test', 1, 'Este es un log de prueba');
    
    if ($testResult) {
        echo "<div class='alert alert-success'>✅ Log de prueba registrado correctamente</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Error al registrar log de prueba</div>";
    }
    
    // Mostrar los últimos logs
    $logs = $pdo->query("SELECT * FROM system_logs ORDER BY id DESC LIMIT 10")->fetchAll();
    
    if (count($logs) > 0) {
        echo "<h4>Últimos logs registrados:</h4>";
        echo "<table class='table table-bordered'>";
        echo "<thead><tr><th>ID</th><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Detalles</th></tr></thead><tbody>";
        foreach ($logs as $log) {
            echo "<tr>";
            echo "<td>{$log['id']}</td>";
            echo "<td>{$log['created_at']}</td>";
            echo "<td>{$log['username']}</td>";
            echo "<td>{$log['action']}</td>";
            echo "<td>" . htmlspecialchars($log['details'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<div class='alert alert-warning'>⚠️ No hay logs registrados aún</div>";
    }
    
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}

echo "
            <hr>
            <a href='/modules/admin/logs.php' class='btn btn-primary'>Ir a Bitácora</a>
            <a href='/modules/dashboard/index.php' class='btn btn-secondary'>Volver al Dashboard</a>
        </div>
    </div>
</body>
</html>";
?>