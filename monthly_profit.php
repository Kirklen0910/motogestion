<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['finance', 'admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$currentMonth = date('Y-m');
$month = $_GET['month'] ?? $currentMonth;

// Obtener ventas del mes
$sales = $pdo->prepare("
    SELECT s.id, s.invoice_number, s.sale_date, s.client_id,
           s.total as total_venta,
           s.total_cost as costo_total,
           s.total_profit as ganancia_total
    FROM sales s
    WHERE DATE_FORMAT(s.sale_date, '%Y-%m') = ?
    ORDER BY s.sale_date DESC
");
$sales->execute([$month]);
$salesData = $sales->fetchAll();

$totalVentas = array_sum(array_column($salesData, 'total_venta'));
$totalCosto = array_sum(array_column($salesData, 'costo_total'));
$totalGanancia = array_sum(array_column($salesData, 'ganancia_total'));
$margenGanancia = $totalVentas > 0 ? ($totalGanancia / $totalVentas) * 100 : 0;

// Obtener nombre del mes
$monthNames = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];
$monthYear = explode('-', $month);
$monthName = $monthNames[$monthYear[1]] . ' ' . $monthYear[0];

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-calendar-alt text-success"></i> Ganancias del Mes</h2>
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-filter"></i> Seleccionar Mes
        </div>
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <input type="month" name="month" class="form-control" value="<?= $month ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
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
            <i class="fas fa-chart-pie"></i> Resumen Financiero - <?= $monthName ?>
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
                        <strong>Total de Ventas:</strong> <?= count($salesData) ?> transacciones<br>
                        <strong>Ticket Promedio:</strong> L <?= number_format(count($salesData) > 0 ? $totalVentas / count($salesData) : 0, 2) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <i class="fas fa-list"></i> Detalle de Ventas del Mes
        </div>
        <div class="card-body">
            <?php if (empty($salesData)): ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                    No hay ventas registradas en <?= $monthName ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-file-invoice"></i> Factura</th>
                                <th><i class="fas fa-calendar"></i> Fecha</th>
                                <th><i class="fas fa-dollar-sign"></i> Total Venta</th>
                                <th><i class="fas fa-boxes"></i> Costo</th>
                                <th><i class="fas fa-chart-line"></i> Ganancia</th>
                                <th><i class="fas fa-percent"></i> Margen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salesData as $sale): 
                                $margen = $sale['total_venta'] > 0 ? ($sale['ganancia_total'] / $sale['total_venta']) * 100 : 0;
                                $gananciaClass = $sale['ganancia_total'] >= 0 ? 'success' : 'danger';
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($sale['invoice_number']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($sale['sale_date'])) ?></td>
                                    <td><span class="badge bg-info">L <?= number_format($sale['total_venta'], 2) ?></span></td>
                                    <td>L <?= number_format($sale['costo_total'], 2) ?></td>
                                    <td><span class="badge bg-<?= $gananciaClass ?>">L <?= number_format($sale['ganancia_total'], 2) ?></span></td>
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
                                <td colspan="2"><strong>TOTALES</strong></td>
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