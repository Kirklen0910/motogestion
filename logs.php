<?php
session_start();
require_once '../../config/database.php';

// Solo superadmin puede ver los logs
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: /modules/dashboard/index.php');
    exit;
}

// Filtros (opcionales)
$actionFilter = $_GET['action'] ?? '';
$tableFilter = $_GET['table'] ?? '';
$userFilter = $_GET['user'] ?? '';

// Construir query con filtros
$sql = "SELECT * FROM system_logs WHERE 1=1";
$params = [];

if (!empty($actionFilter)) {
    $sql .= " AND action = ?";
    $params[] = $actionFilter;
}
if (!empty($tableFilter)) {
    $sql .= " AND table_name = ?";
    $params[] = $tableFilter;
}
if (!empty($userFilter)) {
    $sql .= " AND username LIKE ?";
    $params[] = "%$userFilter%";
}

$sql .= " ORDER BY id DESC LIMIT 500";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Obtener estadísticas básicas
$totalLogs = $pdo->query("SELECT COUNT(*) FROM system_logs")->fetchColumn();
$totalToday = $pdo->query("SELECT COUNT(*) FROM system_logs WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$totalCreates = $pdo->query("SELECT COUNT(*) FROM system_logs WHERE action = 'CREATE'")->fetchColumn();
$totalUpdates = $pdo->query("SELECT COUNT(*) FROM system_logs WHERE action = 'UPDATE'")->fetchColumn();
$totalDeletes = $pdo->query("SELECT COUNT(*) FROM system_logs WHERE action = 'DELETE'")->fetchColumn();

// Obtener valores para filtros
$actions = $pdo->query("SELECT DISTINCT action FROM system_logs ORDER BY action")->fetchAll();
$tables = $pdo->query("SELECT DISTINCT table_name FROM system_logs WHERE table_name IS NOT NULL ORDER BY table_name")->fetchAll();

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<style>
    .log-table pre {
        margin: 0;
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 12px;
        max-width: 300px;
    }
    .log-badge {
        font-size: 11px;
        padding: 3px 8px;
    }
    .stats-card {
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-3px);
    }
</style>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3><i class="fas fa-history"></i> Bitácora del Sistema</h3>
                    <small>Registro de todos los eventos y cambios en el sistema</small>
                </div>
                <div class="card-body">
                    
                    <!-- Estadísticas rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-2 col-sm-6 mb-2">
                            <div class="card bg-primary text-white stats-card">
                                <div class="card-body text-center">
                                    <h4><?= number_format($totalLogs) ?></h4>
                                    <small>Total Eventos</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2">
                            <div class="card bg-info text-white stats-card">
                                <div class="card-body text-center">
                                    <h4><?= number_format($totalToday) ?></h4>
                                    <small>Eventos Hoy</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2">
                            <div class="card bg-success text-white stats-card">
                                <div class="card-body text-center">
                                    <h4><?= number_format($totalCreates) ?></h4>
                                    <small>Creaciones</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2">
                            <div class="card bg-warning text-white stats-card">
                                <div class="card-body text-center">
                                    <h4><?= number_format($totalUpdates) ?></h4>
                                    <small>Actualizaciones</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2">
                            <div class="card bg-danger text-white stats-card">
                                <div class="card-body text-center">
                                    <h4><?= number_format($totalDeletes) ?></h4>
                                    <small>Eliminaciones</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2">
                            <div class="card bg-secondary text-white stats-card">
                                <div class="card-body text-center">
                                    <h4><?= count($logs) ?></h4>
                                    <small>Mostrando</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filtros -->
                    <div class="card mb-4 bg-light">
                        <div class="card-header bg-dark text-white">
                            <i class="fas fa-filter"></i> Filtrar Registros
                        </div>
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label>Acción</label>
                                    <select name="action" class="form-control">
                                        <option value="">-- Todas --</option>
                                        <?php foreach ($actions as $a): ?>
                                            <option value="<?= $a['action'] ?>" <?= $actionFilter == $a['action'] ? 'selected' : '' ?>><?= $a['action'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Tabla</label>
                                    <select name="table" class="form-control">
                                        <option value="">-- Todas --</option>
                                        <?php foreach ($tables as $t): ?>
                                            <option value="<?= $t['table_name'] ?>" <?= $tableFilter == $t['table_name'] ? 'selected' : '' ?>><?= $t['table_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Usuario</label>
                                    <input type="text" name="user" class="form-control" placeholder="Nombre de usuario" value="<?= htmlspecialchars($userFilter) ?>">
                                </div>
                                <div class="col-md-12 mt-2">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrar</button>
                                    <a href="logs.php" class="btn btn-secondary"><i class="fas fa-undo"></i> Limpiar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tabla de logs -->
                    <?php if (empty($logs)): ?>
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-info-circle"></i> No hay registros para mostrar con los filtros seleccionados.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha/Hora</th>
                                        <th>Usuario</th>
                                        <th>Acción</th>
                                        <th>Tabla</th>
                                        <th>Registro ID</th>
                                        <th>Detalles</th>
                                        <th>IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?= $log['id'] ?></td>
                                            <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($log['username']) ?></strong>
                                                <?php if ($log['user_id']): ?>
                                                    <br><small class="text-muted">ID: <?= $log['user_id'] ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($log['action'] == 'CREATE') {
                                                    echo '<span class="badge bg-success">CREAR</span>';
                                                } elseif ($log['action'] == 'UPDATE') {
                                                    echo '<span class="badge bg-warning">EDITAR</span>';
                                                } elseif ($log['action'] == 'DELETE') {
                                                    echo '<span class="badge bg-danger">ELIMINAR</span>';
                                                } elseif ($log['action'] == 'LOGIN_EXITOSO') {
                                                    echo '<span class="badge bg-info">LOGIN OK</span>';
                                                } elseif ($log['action'] == 'LOGIN_FALLIDO') {
                                                    echo '<span class="badge bg-dark">LOGIN FAIL</span>';
                                                } elseif ($log['action'] == 'LOGOUT') {
                                                    echo '<span class="badge bg-secondary">LOGOUT</span>';
                                                } else {
                                                    echo '<span class="badge bg-primary">' . htmlspecialchars($log['action']) . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($log['table_name']): ?>
                                                    <code><?= htmlspecialchars($log['table_name']) ?></code>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $log['record_id'] ?? '-' ?></td>
                                            <td>
                                                <small><?= htmlspecialchars($log['details'] ?? '-') ?></small>
                                            </td>
                                            <td>
                                                <code><?= htmlspecialchars($log['ip_address'] ?? '-') ?></code>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3 text-muted">
                            <small>Mostrando <?= count($logs) ?> registros de <?= number_format($totalLogs) ?> totales</small>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>