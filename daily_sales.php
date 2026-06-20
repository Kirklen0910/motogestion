<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['seller', 'cashier', 'admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$today = date('Y-m-d');

$sales = $pdo->prepare("
    SELECT s.*, c.name as client_name, u.fullname as user_name 
    FROM sales s 
    LEFT JOIN clients c ON s.client_id = c.id 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE DATE(s.sale_date) = ?
    ORDER BY s.sale_date DESC
");
$sales->execute([$today]);
$sales = $sales->fetchAll();

$total = array_sum(array_column($sales, 'total'));
$totalSales = count($sales);

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <h2><i class="fas fa-calendar-day"></i> Ventas del Día</h2>
    <p>Fecha: <strong><?= date('d/m/Y') ?></strong></p>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4><?= $totalSales ?></h4>
                    <small>Total de Ventas</small>
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
        <div class="card-header bg-info text-white">
            <i class="fas fa-list"></i> Listado de Ventas
        </div>
        <div class="card-body">
            <?php if (empty($sales)): ?>
                <div class="alert alert-warning">No hay ventas registradas hoy.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Factura</th>
                                <th>Cliente</th>
                                <th>Hora</th>
                                <th>Vendedor</th>
                                <th>Total</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales as $s): ?>
                                <tr>
                                    <td><?= htmlspecialchars($s['invoice_number']) ?></td>
                                    <td><?= htmlspecialchars($s['client_name'] ?? 'Ocasional') ?></td>
                                    <td><?= date('H:i:s', strtotime($s['sale_date'])) ?></td>
                                    <td><?= htmlspecialchars($s['user_name']) ?></td>
                                    <td>L <?= number_format($s['total'], 2) ?></td>
                                    <td><a href="/modules/sales/invoice_detail.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-info">Ver</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-info">
                            <tr>
                                <td colspan="4"><strong>TOTAL</strong></td>
                                <td><strong>L <?= number_format($total, 2) ?></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>