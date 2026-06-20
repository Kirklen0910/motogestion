<?php
session_start();
require_once '../../config/database.php';

// Solo superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$days = (int)($_GET['days'] ?? 30);
$deleted = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $days = (int)$_POST['days'];
    $stmt = $pdo->prepare("DELETE FROM system_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
    $stmt->execute([$days]);
    $deleted = $stmt->rowCount();
    
    registerLog($pdo, 'CLEAN_LOGS', 'system_logs', null, "Se eliminaron $deleted registros antiguos (más de $days días)");
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-warning">
            <h3><i class="fas fa-trash-alt"></i> Limpiar Logs Antiguos</h3>
        </div>
        <div class="card-body">
            <?php if ($deleted > 0): ?>
                <div class="alert alert-success">
                    ✅ Se eliminaron <?= $deleted ?> registros de logs con más de <?= $days ?> días.
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="row">
                    <div class="col-md-4">
                        <label>Eliminar logs más antiguos de:</label>
                        <div class="input-group">
                            <input type="number" name="days" class="form-control" value="30" min="1" max="365">
                            <span class="input-group-text">días</span>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-end">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar logs antiguos?')">
                            <i class="fas fa-trash"></i> Limpiar Logs
                        </button>
                        <a href="logs.php" class="btn btn-secondary">Volver a Bitácora</a>
                    </div>
                </div>
            </form>
            
            <hr>
            <div class="alert alert-info">
                <strong>📊 Estadísticas actuales:</strong><br>
                <?php
                $totalLogs = $pdo->query("SELECT COUNT(*) FROM system_logs")->fetchColumn();
                $oldestLog = $pdo->query("SELECT MIN(created_at) FROM system_logs")->fetchColumn();
                $newestLog = $pdo->query("SELECT MAX(created_at) FROM system_logs")->fetchColumn();
                ?>
                <ul>
                    <li>Total de registros: <?= number_format($totalLogs) ?></li>
                    <li>Log más antiguo: <?= $oldestLog ? date('d/m/Y', strtotime($oldestLog)) : 'N/A' ?></li>
                    <li>Log más reciente: <?= $newestLog ? date('d/m/Y H:i', strtotime($newestLog)) : 'N/A' ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>