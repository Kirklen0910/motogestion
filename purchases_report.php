<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$supplier_id = $_GET['supplier_id'] ?? 0;

$suppliers = $pdo->query("SELECT id, name FROM suppliers ORDER BY name")->fetchAll();

$products = [];
$supplier_name = '';
$total_stock_value = 0;

if ($supplier_id > 0) {
    $stmt = $pdo->prepare("SELECT name FROM suppliers WHERE id = ?");
    $stmt->execute([$supplier_id]);
    $sup = $stmt->fetch();
    $supplier_name = $sup['name'] ?? '';
    
    // SOLO productos de este proveedor
    $products = $pdo->prepare("
        SELECT p.*, 
               COALESCE(SUM(sd.quantity), 0) as total_vendido
        FROM products p
        LEFT JOIN sale_details sd ON p.id = sd.product_id
        WHERE p.supplier_id = ?
        GROUP BY p.id
        ORDER BY p.name
    ");
    $products->execute([$supplier_id]);
    $products = $products->fetchAll();
    
    $total_stock_value = array_sum(array_map(function($p) { 
        return $p['stock'] * $p['price']; 
    }, $products));
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <h2><i class="fas fa-shopping-cart"></i> Productos por Proveedor</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-filter"></i> Seleccionar Proveedor
        </div>
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-6">
                    <select name="supplier_id" class="form-control" required>
                        <option value="">-- Seleccione un proveedor --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $supplier_id == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if ($supplier_id > 0): ?>
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4><i class="fas fa-box"></i> Productos de: <?= htmlspecialchars($supplier_name) ?></h4>
            </div>
            <div class="card-body">
                <?php if (empty($products)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> No hay productos registrados de este proveedor.
                    </div>
                <?php else: ?>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4><?= count($products) ?></h4>
                                    <small>Total Productos</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4><?= number_format(array_sum(array_column($products, 'stock'))) ?></h4>
                                    <small>Unidades en Stock</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>L <?= number_format($total_stock_value, 2) ?></h4>
                                    <small>Valor en Inventario</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Stock Actual</th>
                                    <th>Stock Mínimo</th>
                                    <th>Precio</th>
                                    <th>Total Vendido</th>
                                    <th>Valor en Stock</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): 
                                    $estado = $p['stock'] <= 0 ? 'Agotado' : ($p['stock'] <= $p['min_stock'] ? 'Stock Bajo' : 'Normal');
                                    $estadoClass = $p['stock'] <= 0 ? 'danger' : ($p['stock'] <= $p['min_stock'] ? 'warning' : 'success');
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['code']) ?></td>
                                        <td><?= htmlspecialchars($p['name']) ?></td>
                                        <td><strong><?= $p['stock'] ?></strong> uni.</td>
                                        <td><?= $p['min_stock'] ?> uni.</td>
                                        <td>L <?= number_format($p['price'], 2) ?></td>
                                        <td><?= number_format($p['total_vendido']) ?> uni.</td>
                                        <td>L <?= number_format($p['stock'] * $p['price'], 2) ?></td>
                                        <td><span class="badge bg-<?= $estadoClass ?>"><?= $estado ?></span></td>
                                        <td>
                                            <a href="/modules/products/edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        </div>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-info">
                                <tr>
                                    <td colspan="6"><strong>TOTALES</strong></td>
                                    <td><strong><?= number_format(array_sum(array_column($products, 'stock'))) ?> uni.</strong></td>
                                    <td><strong>L <?= number_format($total_stock_value, 2) ?></strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include '../../includes/footer.php'; ?>