<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos para ver reportes de inventario
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['inventory', 'admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$products = $pdo->query("SELECT p.*, s.name as supplier_name FROM products p LEFT JOIN suppliers s ON p.supplier_id = s.id ORDER BY p.name")->fetchAll();
$totalProducts = count($products);
$totalStock = array_sum(array_column($products, 'stock'));
$totalValue = array_sum(array_map(function($p) { return $p['price'] * $p['stock']; }, $products));
$lowStockCount = count(array_filter($products, function($p) { return $p['stock'] <= $p['min_stock']; }));

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <h2><i class="fas fa-boxes"></i> Reporte de Inventario</h2>
    
    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4><?= $totalProducts ?></h4>
                    <small>Total Productos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4><?= number_format($totalStock) ?></h4>
                    <small>Unidades en Stock</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>L <?= number_format($totalValue, 2) ?></h4>
                    <small>Valor del Inventario</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4><?= $lowStockCount ?></h4>
                    <small>Stock Bajo</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-success text-white">
            <i class="fas fa-list"></i> Listado de Productos
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Proveedor</th>
                            <th>Stock</th>
                            <th>Min Stock</th>
                            <th>Precio</th>
                            <th>Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr class="<?= $p['stock'] <= $p['min_stock'] ? 'table-danger' : '' ?>">
                                <td><?= htmlspecialchars($p['code']) ?></td>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= htmlspecialchars($p['supplier_name'] ?? 'N/A') ?></td>
                                <td class="fw-bold"><?= $p['stock'] ?></td>
                                <td><?= $p['min_stock'] ?></td>
                                <td>L <?= number_format($p['price'], 2) ?></td>
                                <td>L <?= number_format($p['price'] * $p['stock'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>