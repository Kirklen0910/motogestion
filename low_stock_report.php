<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['inventory', 'admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$products = $pdo->query("
    SELECT p.*, s.name as supplier_name 
    FROM products p 
    LEFT JOIN suppliers s ON p.supplier_id = s.id 
    WHERE p.stock <= p.min_stock 
    ORDER BY (p.stock / p.min_stock) ASC
")->fetchAll();

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <h2><i class="fas fa-exclamation-triangle"></i> Productos con Stock Bajo</h2>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4><?= count($products) ?></h4>
                    <small>Productos con Stock Bajo</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4><?= count(array_filter($products, function($p) { return $p['stock'] == 0; })) ?></h4>
                    <small>Productos Agotados</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4><?= count(array_filter($products, function($p) { return $p['stock'] > 0; })) ?></h4>
                    <small>Stock Crítico</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-danger text-white">
            <i class="fas fa-list"></i> Listado de Productos
        </div>
        <div class="card-body">
            <?php if (empty($products)): ?>
                <div class="alert alert-success">✅ No hay productos con stock bajo.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Proveedor</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Faltante</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): 
                                $falta = $p['min_stock'] - $p['stock'];
                                $status = $p['stock'] == 0 ? 'Agotado' : ($p['stock'] <= $p['min_stock'] / 2 ? 'Crítico' : 'Bajo');
                                $statusClass = $p['stock'] == 0 ? 'danger' : ($p['stock'] <= $p['min_stock'] / 2 ? 'warning' : 'info');
                            ?>
                                <tr class="table-<?= $statusClass ?>">
                                    <td><?= htmlspecialchars($p['code']) ?></td>
                                    <td><?= htmlspecialchars($p['name']) ?></td>
                                    <td><?= htmlspecialchars($p['supplier_name'] ?? 'N/A') ?></td>
                                    <td class="fw-bold"><?= $p['stock'] ?></td>
                                    <td><?= $p['min_stock'] ?></td>
                                    <td><?= $falta ?></td>
                                    <td><span class="badge bg-<?= $statusClass ?>"><?= $status ?></span></td>
                                    <td><a href="/modules/products/edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Reabastecer</a></td>
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