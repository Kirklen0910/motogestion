<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['seller', 'admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$days = $_GET['days'] ?? 90;

// Clientes que no han comprado en los últimos X días
$clients = $pdo->prepare("
    SELECT c.*, 
           MAX(s.sale_date) as ultima_compra,
           COUNT(s.id) as total_compras,
           COALESCE(SUM(s.total), 0) as total_gastado
    FROM clients c
    LEFT JOIN sales s ON c.id = s.client_id
    GROUP BY c.id
    HAVING ultima_compra < DATE_SUB(NOW(), INTERVAL ? DAY) OR ultima_compra IS NULL
    ORDER BY ultima_compra ASC
");
$clients->execute([$days]);
$clients = $clients->fetchAll();

$totalClients = count($clients);

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <h2><i class="fas fa-user-slash"></i> Clientes Inactivos</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-filter"></i> Filtros
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label>Mostrar clientes inactivos por más de:</label>
                    <select name="days" class="form-control">
                        <option value="30" <?= $days == 30 ? 'selected' : '' ?>>30 días (1 mes)</option>
                        <option value="60" <?= $days == 60 ? 'selected' : '' ?>>60 días (2 meses)</option>
                        <option value="90" <?= $days == 90 ? 'selected' : '' ?>>90 días (3 meses)</option>
                        <option value="180" <?= $days == 180 ? 'selected' : '' ?>>180 días (6 meses)</option>
                        <option value="365" <?= $days == 365 ? 'selected' : '' ?>>365 días (1 año)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block">Filtrar</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4><?= $totalClients ?></h4>
                    <small>Clientes Inactivos</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4><?= $days ?> días</h4>
                    <small>Período de inactividad</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-danger text-white">
            <i class="fas fa-list"></i> Listado de Clientes Inactivos
        </div>
        <div class="card-body">
            <?php if (empty($clients)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> ¡Excelente! No hay clientes inactivos en el período seleccionado.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Contacto</th>
                                <th>Última Compra</th>
                                <th>Días Inactivo</th>
                                <th>Total Compras</th>
                                <th>Total Gastado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $c): 
                                $diasInactivo = $c['ultima_compra'] ? (new DateTime($c['ultima_compra']))->diff(new DateTime())->days : $days + 1;
                                $badgeClass = $diasInactivo > 180 ? 'danger' : ($diasInactivo > 90 ? 'warning' : 'info');
                            ?>
                                <tr>
                                    <td><?= $c['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($c['name']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($c['email'] ?? 'Sin email') ?></small>
                                    </td>
                                    <td>
                                        <i class="fas fa-phone"></i> <?= htmlspecialchars($c['phone'] ?? 'N/A') ?><br>
                                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars(substr($c['address'] ?? '', 0, 30)) ?>
                                    </td>
                                    <td>
                                        <?php if ($c['ultima_compra']): ?>
                                            <?= date('d/m/Y', strtotime($c['ultima_compra'])) ?>
                                        <?php else: ?>
                                            <span class="badge bg-dark">Nunca ha comprado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $badgeClass ?>"><?= $diasInactivo ?> días</span>
                                    </td>
                                    <td><?= number_format($c['total_compras']) ?> compras</td>
                                    <td><strong>L <?= number_format($c['total_gastado'], 2) ?></strong></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/modules/clients/edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="/modules/sales/index.php?client_id=<?= $c['id'] ?>" class="btn btn-sm btn-success">
                                                <i class="fas fa-shopping-cart"></i> Vender
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>