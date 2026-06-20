<?php
require_once '../../config/database.php';
if (!isset($_SESSION['user_id'])) { 
    header('Location: /modules/auth/login.php'); 
    exit; 
}

$role = $_SESSION['role'] ?? 'seller';
$user_id = $_SESSION['user_id'];

// Construir consulta según el rol
$sql = "
    SELECT s.*, c.name as client_name, u.fullname as user_name 
    FROM sales s 
    LEFT JOIN clients c ON s.client_id = c.id 
    LEFT JOIN users u ON s.user_id = u.id 
";

// Vendedor solo ve sus propias ventas
if ($role == 'seller') {
    $sql .= " WHERE s.user_id = $user_id";
}

// Cajero ve TODAS las ventas (pendientes y pagadas)
if ($role == 'cashier') {
    $sql .= " WHERE s.status = 'pagado' OR s.status = 'pendiente'";
}

$sql .= " ORDER BY s.sale_date DESC";

$sales = $pdo->query($sql)->fetchAll();

$totalVentas = count($sales);

include '../../includes/header.php';
?>

<style>
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-pendiente { background: #f39c12; color: white; }
    .status-pagado { background: #27ae60; color: white; }
    .status-cancelado { background: #e74c3c; color: white; }
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
    <div class="d-flex justify-content-between align-items-center">
        <h2><i class="fas fa-file-invoice"></i> Historial de Ventas</h2>
        <span class="badge bg-primary" style="font-size: 16px; padding: 8px 15px;">
            <i class="fas fa-chart-bar"></i> Total: <?= $totalVentas ?> ventas
        </span>
    </div>
    
    <?php if ($role == 'seller'): ?>
        <div class="alert alert-info mt-2">
            <i class="fas fa-info-circle"></i> 
            <strong>Nota:</strong> Como vendedor, solo ves las ventas que tú has realizado.
        </div>
    <?php endif; ?>
    
    <?php if ($role == 'cashier'): ?>
        <div class="alert alert-info mt-2">
            <i class="fas fa-info-circle"></i> 
            <strong>Nota:</strong> Como cajero, puedes ver todas las ventas realizadas (pendientes y pagadas).
        </div>
    <?php endif; ?>
    
    <div class="card modern-card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Subtotal</th>
                            <th>ISV 15%</th>
                            <th>Total</th>
                            <th>Vendedor</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($sales)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <?php if ($role == 'seller'): ?>
                                    No has realizado ventas aún.
                                <?php else: ?>
                                    No hay ventas registradas en el sistema.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sales as $s): 
                            $subtotal = $s['subtotal'] ?? $s['total'];
                            $tax = $s['tax'] ?? 0;
                            $tax_rate = $s['tax_rate'] ?? 15;
                            $total = $s['total'];
                            
                            if ($tax == 0 && $subtotal > 0) {
                                $tax = $subtotal * ($tax_rate / 100);
                            }
                        ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($s['invoice_number']) ?></strong></td>
                                <td><?= htmlspecialchars($s['client_name'] ?? 'Cliente ocasional') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($s['sale_date'])) ?></td>
                                <td>L <?= number_format($subtotal,2) ?></td>
                                <td>L <?= number_format($tax,2) ?></td>
                                <td class="text-success"><strong>L <?= number_format($total,2) ?></strong></td>
                                <td><?= htmlspecialchars($s['user_name']) ?></td>
                                <td>
                                    <?php 
                                    $status = $s['status'] ?? 'pendiente';
                                    $statusText = [
                                        'pendiente' => '⏳ Pendiente',
                                        'pagado' => '✅ Pagado',
                                        'cancelado' => '❌ Cancelado'
                                    ];
                                    ?>
                                    <span class="status-badge status-<?= $status ?>">
                                        <?= $statusText[$status] ?? $status ?>
                                    </span>
                                    <?php if ($status == 'pagado' && !empty($s['payment_method'])): ?>
                                        <br><small class="text-muted"><?= $s['payment_method'] ?></small>
                                        <?php if ($s['change_amount'] > 0): ?>
                                            <br><small class="text-success">Cambio: L <?= number_format($s['change_amount'], 2) ?></small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="invoice_detail.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <?php if ($status == 'pendiente' && $role == 'cashier'): ?>
                                        <a href="invoice_detail.php?id=<?= $s['id'] ?>&action=cobrar" class="btn btn-sm btn-success">
                                            <i class="fas fa-money-bill"></i> Cobrar
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>