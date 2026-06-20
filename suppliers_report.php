<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$suppliers = $pdo->query("
    SELECT s.*, 
           COUNT(p.id) as total_productos,
           COALESCE(SUM(p.stock), 0) as total_stock,
           COALESCE(SUM(p.stock * p.price), 0) as total_valor
    FROM suppliers s
    LEFT JOIN products p ON s.id = p.supplier_id
    GROUP BY s.id
    ORDER BY s.name
")->fetchAll();

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="container mt-4">
    <h2><i class="fas fa-truck"></i> Reporte de Proveedores</h2>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4><?= count($suppliers) ?></h4>
                    <small>Total Proveedores</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4><?= array_sum(array_column($suppliers, 'total_productos')) ?></h4>
                    <small>Total Productos</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4><?= number_format(array_sum(array_column($suppliers, 'total_stock'))) ?></h4>
                    <small>Unidades en Stock</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-success text-white">
            <i class="fas fa-list"></i> Listado de Proveedores
        </div>
        <div class="card-body">
            <?php if (empty($suppliers)): ?>
                <div class="alert alert-warning">No hay proveedores registrados.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Contacto</th>
                                <th>Teléfono</th>
                                <th>Productos</th>
                                <th>Stock Total</th>
                                <th>Valor Inventario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suppliers as $s): ?>
                                <tr>
                                    <td><?= $s['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                                    <td><?= htmlspecialchars($s['contact'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($s['phone'] ?? '-') ?></td>
                                    <td><?= $s['total_productos'] ?> productos</td>
                                    <td><?= number_format($s['total_stock']) ?> unidades</td>
                                    <td>L <?= number_format($s['total_valor'], 2) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/modules/suppliers/edit.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="/modules/reports/purchases_report.php?supplier_id=<?= $s['id'] ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-box"></i> Ver Productos
                                            </a>
                                        </div>
                                    </td>
                                </table>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-info">
                            <tr>
                                <td colspan="4"><strong>TOTALES</strong></td>
                                <td><strong><?= array_sum(array_column($suppliers, 'total_productos')) ?> productos</strong></td>
                                <td><strong><?= number_format(array_sum(array_column($suppliers, 'total_stock'))) ?> unidades</strong></td>
                                <td><strong>L <?= number_format(array_sum(array_column($suppliers, 'total_valor')), 2) ?></strong></td>
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