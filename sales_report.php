<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos para ver reportes de ventas
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['seller', 'cashier', 'admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

$sales = $pdo->prepare("
    SELECT s.*, c.name as client_name, u.fullname as user_name 
    FROM sales s 
    LEFT JOIN clients c ON s.client_id = c.id 
    LEFT JOIN users u ON s.user_id = u.id 
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
    <h2>Reporte de Ventas</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-filter"></i> Filtros
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3"><label>Fecha Inicio</label><input type="date" name="start_date" class="form-control" value="<?= $start_date ?>"></div>
                <div class="col-md-3"><label>Fecha Fin</label><input type="date" name="end_date" class="form-control" value="<?= $end_date ?>"></div>
                <div class="col-md-2"><label>&nbsp;</label><button type="submit" class="btn btn-primary d-block">Filtrar</button></div>
                <div class="col-md-2"><label>&nbsp;</label><button type="button" class="btn btn-success d-block" onclick="window.print()">Imprimir</button></div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white">
            <h4>Ventas del <?= $start_date ?> al <?= $end_date ?></h4>
        </div>
        <div class="card-body">
            <p><strong>Total Vendido: L <?= number_format($total,2) ?></strong></p>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr><th>Factura</th><th>Cliente</th><th>Fecha</th><th>Vendedor</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($sales as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['invoice_number']) ?></td>
                            <td><?= htmlspecialchars($s['client_name'] ?? 'Ocasional') ?></td>
                            <td><?= $s['sale_date'] ?></td>
                            <td><?= htmlspecialchars($s['user_name']) ?></td>
                            <td>L <?= number_format($s['total'],2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>