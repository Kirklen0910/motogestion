<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['seller', 'cashier', 'admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$currentMonth = date('Y-m');
$month = $_GET['month'] ?? $currentMonth;

$sales = $pdo->prepare("
    SELECT s.*, c.name as client_name, u.fullname as user_name,
           DATE(s.sale_date) as sale_day
    FROM sales s 
    LEFT JOIN clients c ON s.client_id = c.id 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE DATE_FORMAT(s.sale_date, '%Y-%m') = ?
    ORDER BY s.sale_date DESC
");
$sales->execute([$month]);
$sales = $sales->fetchAll();

$total = array_sum(array_column($sales, 'total'));
$totalSales = count($sales);

// Ventas por día del mes
$salesByDay = [];
foreach ($sales as $s) {
    $day = date('d', strtotime($s['sale_date']));
    if (!isset($salesByDay[$day])) {
        $salesByDay[$day] = 0;
    }
    $salesByDay[$day] += $s['total'];
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-calendar-alt text-primary"></i> Ventas del Mes</h2>
        <div>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
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
            <div class="card bg-primary text-white shadow">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    <h3><?= number_format($totalSales) ?></h3>
                    <small>Total de Ventas</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body text-center">
                    <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                    <h3>L <?= number_format($total, 2) ?></h3>
                    <small>Monto Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                    <h3>L <?= number_format($totalSales > 0 ? $total / $totalSales : 0, 2) ?></h3>
                    <small>Promedio por Venta</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-chart-bar"></i> Ventas por Día
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-calendar-day"></i> Día</th>
                                <th><i class="fas fa-chart-line"></i> Ventas</th>
                                <th><i class="fas fa-percent"></i> Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $daysInMonth = date('t', strtotime($month . '-01'));
                            $maxSale = max($salesByDay);
                            for ($i = 1; $i <= $daysInMonth; $i++):
                                $day = str_pad($i, 2, '0', STR_PAD_LEFT);
                                $amount = $salesByDay[$day] ?? 0;
                                $barWidth = $maxSale > 0 ? ($amount / $maxSale) * 100 : 0;
                            ?>
                                <tr>
                                    <td><strong><?= $i ?></strong></td>
                                    <td>L <?= number_format($amount, 2) ?></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" style="width: <?= $barWidth ?>%">
                                                <?= $barWidth > 10 ? number_format($barWidth, 1) . '%' : '' ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-list"></i> Listado de Ventas
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($sales)): ?>
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-info-circle"></i> No hay ventas en este mes.
                        </div>
                    <?php else: ?>
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-calendar"></i> Fecha</th>
                                    <th><i class="fas fa-file-invoice"></i> Factura</th>
                                    <th><i class="fas fa-user"></i> Cliente</th>
                                    <th><i class="fas fa-dollar-sign"></i> Total</th>
                                    <th><i class="fas fa-eye"></i> Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales as $s): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($s['sale_date'])) ?></td>
                                        <td><?= htmlspecialchars($s['invoice_number']) ?></td>
                                        <td><?= htmlspecialchars($s['client_name'] ?? 'Ocasional') ?></td>
                                        <td><span class="badge bg-success">L <?= number_format($s['total'], 2) ?></span></td>
                                        <td><a href="/modules/sales/invoice_detail.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-file-invoice"></i> Ver
                                        </a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-info">
                                <tr>
                                    <td colspan="3"><strong>TOTAL GENERAL</strong></td>
                                    <td><strong>L <?= number_format($total, 2) ?></strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>