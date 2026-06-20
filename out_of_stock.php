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
    WHERE p.stock = 0 
    ORDER BY p.name
")->fetchAll();

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-ban text-danger"></i> Productos Agotados</h2>
        <div>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="low_stock_report.php" class="btn btn-warning">
                <i class="fas fa-exclamation-triangle"></i> Ver Stock Bajo
            </a>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white shadow">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-3x mb-2"></i>
                    <h2><?= count($products) ?></h2>
                    <small>Productos Agotados</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark shadow">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x mb-2"></i>
                    <h2><?= number_format(array_sum(array_column($products, 'min_stock'))) ?></h2>
                    <small>Unidades necesarias para stock mínimo</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body text-center">
                    <i class="fas fa-truck fa-3x mb-2"></i>
                    <h2><?= count(array_unique(array_column($products, 'supplier_name'))) ?></h2>
                    <small>Proveedores afectados</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <i class="fas fa-list"></i> Listado de Productos Agotados
        </div>
        <div class="card-body">
            <?php if (empty($products)): ?>
                <div class="alert alert-success text-center">
                    <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                    <strong>¡Excelente!</strong> No hay productos agotados.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-image"></i> Imagen</th>
                                <th><i class="fas fa-barcode"></i> Código</th>
                                <th><i class="fas fa-box"></i> Producto</th>
                                <th><i class="fas fa-truck"></i> Proveedor</th>
                                <th><i class="fas fa-chart-line"></i> Stock Mínimo</th>
                                <th><i class="fas fa-clock"></i> Última Venta</th>
                                <th><i class="fas fa-cogs"></i> Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): 
                                // Obtener última venta de este producto
                                $lastSale = $pdo->prepare("
                                    SELECT s.sale_date 
                                    FROM sale_details sd 
                                    JOIN sales s ON sd.sale_id = s.id 
                                    WHERE sd.product_id = ? 
                                    ORDER BY s.sale_date DESC 
                                    LIMIT 1
                                ");
                                $lastSale->execute([$p['id']]);
                                $lastSaleDate = $lastSale->fetchColumn();
                                
                                $hasImage = !empty($p['image']) && file_exists('../../' . $p['image']);
                            ?>
                                <tr class="table-danger">
                                    <td class="text-center">
                                        <?php if ($hasImage): ?>
                                            <img src="/<?= $p['image'] ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($p['code']) ?></td>
                                    <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                                    <td><?= htmlspecialchars($p['supplier_name'] ?? 'N/A') ?></td>
                                    <td><?= $p['min_stock'] ?> unidades</td>
                                    <td>
                                        <?php if ($lastSaleDate): ?>
                                            <i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($lastSaleDate)) ?>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="fas fa-history"></i> Sin ventas</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/modules/products/edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="/modules/products/create.php?copy=<?= $p['id'] ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-copy"></i> Duplicar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-danger mt-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Nota:</strong> Estos productos necesitan ser reabastecidos urgentemente para no perder ventas.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>