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

// Obtener todas las ventas del período
$sales = $pdo->prepare("
    SELECT s.*, c.name as client_name 
    FROM sales s
    LEFT JOIN clients c ON s.client_id = c.id
    WHERE DATE(s.sale_date) BETWEEN ? AND ?
    ORDER BY s.sale_date DESC
");
$sales->execute([$start_date, $end_date]);
$sales = $sales->fetchAll();

$total = array_sum(array_column($sales, 'total'));

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <h2><i class="fas fa-credit-card"></i> Reporte de Pagos</h2>
    
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
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4><?= count($sales) ?></h4>
                    <small>Total Transacciones</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>L <?= number_format($total, 2) ?></h4>
                    <small>Monto Total</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-success text-white">
            <i class="fas fa-list"></i> Listado de Pagos
        </div>
        <div class="card-body">
            <?php if (empty($sales)): ?>
                <div class="alert alert-warning">No hay pagos registrados en el período.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Factura</th>
                                <th>Cliente</th>
                                <th>Monto</th>
                                <th>Método de Pago</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales as $s): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($s['sale_date'])) ?></td>
                                    <td><?= htmlspecialchars($s['invoice_number']) ?></td>
                                    <td><?= htmlspecialchars($s['client_name'] ?? 'Ocasional') ?></td>
                                    <td><strong>L <?= number_format($s['total'], 2) ?></strong></td>
                                    <td>
                                        <span class="badge bg-info">Efectivo / Transferencia</span>
                                    </td>
                                    <td>
                                        <a href="/modules/sales/invoice_detail.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-info">Ver Factura</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-info">
                            <tr>
                                <td colspan="3"><strong>TOTAL</strong></td>
                                <td><strong>L <?= number_format($total, 2) ?></strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>