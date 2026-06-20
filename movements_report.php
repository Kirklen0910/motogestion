<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['inventory', 'admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Obtener ventas (salidas)
$sales = $pdo->prepare("
    SELECT p.name as product_name, sd.quantity, sd.unit_price, s.sale_date, 'OUT' as type, s.invoice_number as reference
    FROM sale_details sd
    JOIN products p ON sd.product_id = p.id
    JOIN sales s ON sd.sale_id = s.id
    WHERE DATE(s.sale_date) BETWEEN ? AND ?
    ORDER BY s.sale_date DESC
    LIMIT 200
");
$sales->execute([$start_date, $end_date]);
$sales = $sales->fetchAll();

// Obtener entradas desde inventory_movements
$entries = $pdo->prepare("
    SELECT p.name as product_name, im.quantity, NULL as unit_price, im.created_at as sale_date, 'IN' as type, im.reference
    FROM inventory_movements im
    JOIN products p ON im.product_id = p.id
    WHERE DATE(im.created_at) BETWEEN ? AND ?
    AND im.type = 'IN'
    ORDER BY im.created_at DESC
    LIMIT 200
");
$entries->execute([$start_date, $end_date]);
$entries = $entries->fetchAll();

// Unir y ordenar
$movements = array_merge($sales, $entries);
usort($movements, function($a, $b) {
    return strtotime($b['sale_date']) - strtotime($a['sale_date']);
});

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <h2><i class="fas fa-exchange-alt"></i> Movimientos de Inventario</h2>
    
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
    
    <div class="card">
        <div class="card-header bg-info text-white">
            <i class="fas fa-list"></i> Movimientos (<?= $start_date ?> al <?= $end_date ?>)
        </div>
        <div class="card-body">
            <?php if (empty($movements)): ?>
                <div class="alert alert-warning">No hay movimientos en el período seleccionado.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Referencia</th>
                                <th>Valor Unitario</th>
                                <th>Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movements as $mov): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($mov['sale_date'])) ?></td>
                                    <td><?= htmlspecialchars($mov['product_name']) ?></td>
                                    <td>
                                        <?php if ($mov['type'] == 'OUT'): ?>
                                            <span class="badge bg-danger">SALIDA</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">ENTRADA</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $mov['quantity'] ?></td>
                                    <td><?= htmlspecialchars($mov['reference']) ?></td>
                                    <td>
                                        <?php if ($mov['type'] == 'OUT' && isset($mov['unit_price'])): ?>
                                            L <?= number_format($mov['unit_price'], 2) ?>
                                        <?php else: ?>
                                            ---
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($mov['type'] == 'OUT' && isset($mov['unit_price'])): ?>
                                            L <?= number_format($mov['quantity'] * $mov['unit_price'], 2) ?>
                                        <?php else: ?>
                                            ---
                                        <?php endif; ?>
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