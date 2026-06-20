<?php
require_once '../../config/database.php';
if (!isset($_SESSION['user_id'])) { 
    header('Location: /modules/auth/login.php'); 
    exit; 
}

$role = $_SESSION['role'] ?? 'seller';

// Solo admin, superadmin, inventory pueden ver productos
if (!in_array($role, ['admin', 'superadmin', 'inventory'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

// Obtener filtros
$search = $_GET['search'] ?? '';
$supplier_id = $_GET['supplier_id'] ?? '';
$stock_filter = $_GET['stock_filter'] ?? '';

// Construir query con filtros
$sql = "SELECT p.*, s.name as supplier_name 
        FROM products p 
        LEFT JOIN suppliers s ON p.supplier_id = s.id 
        WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.code LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($supplier_id)) {
    $sql .= " AND p.supplier_id = ?";
    $params[] = $supplier_id;
}

if ($stock_filter == 'low') {
    $sql .= " AND p.stock <= p.min_stock AND p.stock > 0";
} elseif ($stock_filter == 'out') {
    $sql .= " AND p.stock = 0";
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Obtener lista de proveedores para el filtro
$suppliers = $pdo->query("SELECT id, name FROM suppliers ORDER BY name")->fetchAll();

// Estadísticas
$totalProducts = count($products);
$totalStock = array_sum(array_column($products, 'stock'));
$totalValue = array_sum(array_map(function($p) { return $p['price'] * $p['stock']; }, $products));
$totalCost = array_sum(array_map(function($p) { return $p['cost'] * $p['stock']; }, $products));
$totalProfit = $totalValue - $totalCost;
$lowStockCount = count(array_filter($products, function($p) { return $p['stock'] <= $p['min_stock']; }));

include '../../includes/header.php';
?>

<style>
    .product-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
    .product-img-placeholder {
        width: 60px;
        height: 60px;
        background-color: #f0f0f0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 24px;
    }
    .modern-card {
        border: none;
        border-radius: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-box"></i> Productos</h2>
        <a href="create.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Producto
        </a>
    </div>
    
    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white modern-card">
                <div class="card-body text-center">
                    <h4><?= $totalProducts ?></h4>
                    <small>Total Productos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white modern-card">
                <div class="card-body text-center">
                    <h4><?= number_format($totalStock) ?></h4>
                    <small>Unidades en Stock</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white modern-card">
                <div class="card-body text-center">
                    <h4>L <?= number_format($totalValue, 2) ?></h4>
                    <small>Valor Venta</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white modern-card">
                <div class="card-body text-center">
                    <h4>L <?= number_format($totalProfit, 2) ?></h4>
                    <small>Ganancia Potencial</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="card modern-card mb-4">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-filter"></i> Filtros de búsqueda
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label>Buscar por nombre o código</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Ej: aceite, filtro, MOT-001..." 
                           value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <label>Filtrar por proveedor</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">-- Todos los proveedores --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $supplier_id == $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Filtrar por stock</label>
                    <select name="stock_filter" class="form-control">
                        <option value="">-- Todos --</option>
                        <option value="low" <?= $stock_filter == 'low' ? 'selected' : '' ?>>Stock Bajo (≤ mínimo)</option>
                        <option value="out" <?= $stock_filter == 'out' ? 'selected' : '' ?>>Productos Agotados</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="index.php" class="btn btn-secondary w-100 ms-2">
                        <i class="fas fa-undo"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tabla de productos -->
    <div class="card modern-card">
        <div class="card-header bg-info text-white">
            <i class="fas fa-list"></i> Listado de Productos
        </div>
        <div class="card-body">
            <?php if (empty($products)): ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-info-circle"></i> No hay productos que coincidan con los filtros seleccionados.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Imagen</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Proveedor</th>
                                <th>Costo</th>
                                <th>Precio</th>
                                <th>Ganancia</th>
                                <th>Margen</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): 
                                $ganancia = $p['price'] - $p['cost'];
                                $margen = $p['price'] > 0 ? ($ganancia / $p['price']) * 100 : 0;
                                $estado = '';
                                $estadoClass = '';
                                if ($p['stock'] <= 0) {
                                    $estado = 'Agotado';
                                    $estadoClass = 'danger';
                                } elseif ($p['stock'] <= $p['min_stock']) {
                                    $estado = 'Stock Bajo';
                                    $estadoClass = 'warning';
                                } else {
                                    $estado = 'Normal';
                                    $estadoClass = 'success';
                                }
                                
                                $hasImage = !empty($p['image']) && file_exists('../../' . $p['image']);
                                $gananciaClass = $ganancia > 0 ? 'text-success' : ($ganancia < 0 ? 'text-danger' : 'text-muted');
                            ?>
                                <tr class="table-<?= $estadoClass ?>">
                                    <td class="text-center">
                                        <?php if ($hasImage): ?>
                                            <img src="/<?= $p['image'] ?>" class="product-img" alt="<?= htmlspecialchars($p['name']) ?>">
                                        <?php else: ?>
                                            <div class="product-img-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($p['code']) ?></td>
                                    <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                                    <td><?= htmlspecialchars($p['supplier_name'] ?? 'N/A') ?></td>
                                    <td>L <?= number_format($p['cost'], 2) ?></td>
                                    <td>L <?= number_format($p['price'], 2) ?></td>
                                    <td class="<?= $gananciaClass ?>">
                                        <strong>L <?= number_format($ganancia, 2) ?></strong>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-<?= $ganancia > 0 ? 'success' : ($ganancia < 0 ? 'danger' : 'secondary') ?>" 
                                                 style="width: <?= abs($margen) ?>%">
                                                <?= number_format($margen, 1) ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td><strong><?= $p['stock'] ?></strong> uni.</td>
                                    <td><span class="badge bg-<?= $estadoClass ?>"><?= $estado ?></span></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" 
                                               onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </a>
                                        </div>
                                    </td>
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