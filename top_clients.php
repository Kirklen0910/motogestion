<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['seller', 'admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

$clients = $pdo->prepare("
    SELECT c.id, c.name, c.email, c.phone,
           COUNT(s.id) as total_compras,
           SUM(s.total) as total_gastado
    FROM clients c
    JOIN sales s ON c.id = s.client_id
    WHERE DATE(s.sale_date) BETWEEN ? AND ?
    GROUP BY c.id, c.name, c.email, c.phone
    ORDER BY total_gastado DESC
    LIMIT 20
");
$clients->execute([$start_date, $end_date]);
$clients = $clients->fetchAll();

$totalSpent = array_sum(array_column($clients, 'total_gastado'));

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <h2><i class="fas fa-trophy"></i> Mejores Clientes</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-filter"></i> Filtros
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3"><label>Fecha Inicio</label><input type="date" name="start_date" class="form-control" value="<?= $start_date ?>"></div>
                <div class="col-md-3"><label>Fecha Fin</label><input type="date" name="end_date" class="form-control" value="<?= $end_date ?>"></div>
                <div class="col-md-2"><label>&nbsp;</label><button type="submit" class="btn btn-primary d-block">Filtrar</button></div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <i class="fas fa-star"></i> Top Clientes (<?= $start_date ?> al <?= $end_date ?>)
        </div>
        <div class="card-body">
            <?php if (empty($clients)): ?>
                <div class="alert alert-warning">No hay clientes en el período seleccionado.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Compras</th>
                                <th>Total Gastado</th>
                                <th>% del Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            foreach ($clients as $c): 
                                $percentage = $totalSpent > 0 ? ($c['total_gastado'] / $totalSpent) * 100 : 0;
                            ?>
                                <tr>
                                    <td><?= $rank++ ?></td>
                                    <td><?= htmlspecialchars($c['name']) ?></td>
                                    <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($c['phone'] ?? '-') ?></td>
                                    <td><?= number_format($c['total_compras']) ?></td>
                                    <td><strong>L <?= number_format($c['total_gastado'], 2) ?></strong></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: <?= $percentage ?>%">
                                                <?= number_format($percentage, 1) ?>%
                                            </div>
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