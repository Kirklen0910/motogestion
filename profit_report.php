<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['finance', 'admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Obtener ventas del período
$sales = $pdo->prepare("
    SELECT s.*, c.name as client_name 
    FROM sales s
    LEFT JOIN clients c ON s.client_id = c.id
    WHERE DATE(s.sale_date) BETWEEN ? AND ?
    ORDER BY s.sale_date DESC
");
$sales->execute([$start_date, $end_date]);
$salesData = $sales->fetchAll();

$totalVentas = array_sum(array_column($salesData, 'total'));
$totalCosto = array_sum(array_column($salesData, 'total_cost'));
$totalGanancia = array_sum(array_column($salesData, 'total_profit'));
$margenGanancia = $totalVentas > 0 ? ($totalGanancia / $totalVentas) * 100 : 0;

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-chart-line text-success"></i> Reporte de Ganancias</h2>
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-filter"></i> Filtros
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label><i class="fas fa-calendar-alt"></i> Fecha Inicio</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                </div>
                <div class="col-md-3">
                    <label><i class="fas fa-calendar-alt"></i> Fecha Fin</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-3x mb-2"></i>
                    <h3>L <?= number_format($totalVentas, 2) ?></h3>
                    <small>Total Ventas</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white shadow">
                <div class="card-body text-center">
                    <i class="fas fa-boxes fa-3x mb-2"></i>
                    <h3>L <?= number_format($totalCosto, 2) ?></h3>
                    <small>Costo de Ventas</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x mb-2"></i>
                    <h3>L <?= number_format($totalGanancia, 2) ?></h3>
                    <small>Ganancia Bruta</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4 shadow">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-chart-pie"></i> Resumen Financiero
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <i class="fas fa-percent"></i>
                        <strong>Margen de Ganancia:</strong> <?= number_format($margenGanancia, 2) ?>%
                    </div>
                    <div class="progress mb-3" style="height: 35px;">
                        <div class="progress-bar bg-success" style="width: <?= $margenGanancia ?>%">
                            <i class="fas fa-chart-line"></i> Ganancia: <?= number_format($margenGanancia, 1) ?>%
                        </div>
                        <div class="progress-bar bg-secondary" style="width: <?= 100 - $margenGanancia ?>%">
                            <i class="fas fa-boxes"></i> Costo: <?= number_format(100 - $margenGanancia, 1) ?>%
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <i class="fas fa-chart-line"></i>
                        <strong>Rentabilidad:</strong> 
                        Por cada Lempira vendido, se gana <strong>L <?= number_format($margenGanancia / 100, 4) ?></strong>
                    </div>
                    <div class="alert alert-secondary">
                        <i class="fas fa-chart-bar"></i>
                        <strong>Resumen:</strong><br>
                        Total Ventas: <?= count($salesData) ?> transacciones<br>
                        Ticket Promedio: L <?= number_format(count($salesData) > 0 ? $totalVentas / count($salesData) : 0, 2) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <i class="fas fa-list"></i> Detalle de Ventas
        </div>
        <div class="card-body">
            <?php if (empty($salesData)): ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                    No hay ventas en el período seleccionado.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-file-invoice"></i> Factura</th>
                                <th><i class="fas fa-calendar"></i> Fecha</th>
                                <th><i class="fas fa-user"></i> Cliente</th>
                                <th><i class="fas fa-dollar-sign"></i> Total Venta</th>
                                <th><i class="fas fa-boxes"></i> Costo</th>
                                <th><i class="fas fa-chart-line"></i> Ganancia</th>
                                <th><i class="fas fa-percent"></i> Margen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salesData as $sale): 
                                $margen = $sale['total'] > 0 ? ($sale['total_profit'] / $sale['total']) * 100 : 0;
                                $gananciaClass = $sale['total_profit'] >= 0 ? 'success' : 'danger';
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($sale['invoice_number']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($sale['sale_date'])) ?></td>
                                    <td><?= htmlspecialchars($sale['client_name'] ?? 'Ocasional') ?></td>
                                    <td><span class="badge bg-info">L <?= number_format($sale['total'], 2) ?></span></td>
                                    <td>L <?= number_format($sale['total_cost'], 2) ?></td>
                                    <td><span class="badge bg-<?= $gananciaClass ?>">L <?= number_format($sale['total_profit'], 2) ?></span></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-<?= $gananciaClass ?>" style="width: <?= abs($margen) ?>%">
                                                <?= number_format($margen, 1) ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-info">
                            <tr>
                                <td colspan="3"><strong>TOTALES</strong></td>
                                <td><strong>L <?= number_format($totalVentas, 2) ?></strong></td>
                                <td><strong>L <?= number_format($totalCosto, 2) ?></strong></td>
                                <td><strong>L <?= number_format($totalGanancia, 2) ?></strong></td>
                                <td><strong><?= number_format($margenGanancia, 2) ?>%</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>