<?php
require_once '../../config/database.php';
if (!isset($_SESSION['user_id'])) { 
    header('Location: /modules/auth/login.php'); 
    exit; 
}

$role = $_SESSION['role'] ?? 'seller';

if (!in_array($role, ['cashier', 'admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

// Obtener ventas pendientes con detalles
$sales = $pdo->query("
    SELECT s.*, c.name as client_name, u.fullname as user_name 
    FROM sales s 
    LEFT JOIN clients c ON s.client_id = c.id 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE s.status = 'pendiente'
    ORDER BY s.sale_date ASC
")->fetchAll();

// Para cada venta, obtener sus productos
foreach ($sales as &$sale) {
    $details = $pdo->prepare("
        SELECT p.name as product_name, sd.quantity, sd.unit_price, sd.subtotal
        FROM sale_details sd
        JOIN products p ON sd.product_id = p.id
        WHERE sd.sale_id = ?
    ");
    $details->execute([$sale['id']]);
    $sale['products'] = $details->fetchAll();
}

include '../../includes/header.php';
?>

<style>
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        background: #f39c12;
        color: white;
    }
    .pending-card {
        border-left: 4px solid #f39c12;
        transition: transform 0.2s;
        margin-bottom: 15px;
    }
    .pending-card:hover {
        transform: translateX(5px);
    }
    .welcome-banner {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        border-radius: 20px;
        padding: 25px;
        color: white;
        margin-bottom: 30px;
    }
    .product-list {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 10px 15px;
        margin-top: 10px;
    }
    .product-item {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px solid #eee;
    }
    .product-item:last-child {
        border-bottom: none;
    }
    .badge-pendiente {
        background: #f39c12;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
    }
</style>

<div class="container mt-4">
    <div class="welcome-banner">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2><i class="fas fa-clock"></i> Cobros Pendientes</h2>
                <p class="mb-0">Ventas que esperan ser cobradas por el cajero</p>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-light text-dark" style="font-size: 24px; padding: 10px 20px;">
                    <?= count($sales) ?> pendientes
                </span>
            </div>
        </div>
    </div>
    
    <?php if (empty($sales)): ?>
        <div class="alert alert-success text-center">
            <i class="fas fa-check-circle fa-3x d-block mb-2"></i>
            <h4>¡No hay ventas pendientes!</h4>
            <p>Todas las ventas han sido cobradas.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($sales as $s): ?>
                <div class="col-md-6 mb-3">
                    <div class="card pending-card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div style="flex: 1;">
                                    <h5 class="card-title">
                                        <i class="fas fa-file-invoice"></i> <?= htmlspecialchars($s['invoice_number']) ?>
                                        <span class="badge-pendiente">⏳ Pendiente</span>
                                    </h5>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <p class="card-text mb-1">
                                                <strong><i class="fas fa-user"></i> Cliente:</strong> 
                                                <?= htmlspecialchars($s['client_name'] ?? 'Cliente Genérico') ?>
                                            </p>
                                            <p class="card-text mb-1">
                                                <strong><i class="fas fa-user-tie"></i> Vendedor:</strong> 
                                                <?= htmlspecialchars($s['user_name']) ?>
                                            </p>
                                            <p class="card-text mb-1">
                                                <strong><i class="fas fa-calendar"></i> Fecha:</strong> 
                                                <?= date('d/m/Y H:i', strtotime($s['sale_date'])) ?>
                                            </p>
                                        </div>
                                        <div class="col-6 text-end">
                                            <h4 class="text-success">L <?= number_format($s['total'], 2) ?></h4>
                                            <small class="text-muted">Total a cobrar</small>
                                        </div>
                                    </div>
                                    
                                    <!-- DETALLE DE PRODUCTOS -->
                                    <div class="product-list">
                                        <small class="text-muted"><i class="fas fa-box"></i> Productos:</small>
                                        <?php foreach ($s['products'] as $product): ?>
                                            <div class="product-item">
                                                <span>
                                                    <strong><?= htmlspecialchars($product['product_name']) ?></strong>
                                                    <span class="badge bg-secondary">x<?= $product['quantity'] ?></span>
                                                </span>
                                                <span class="text-success">L <?= number_format($product['subtotal'], 2) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <a href="invoice_detail.php?id=<?= $s['id'] ?>&action=cobrar" class="btn btn-success w-100">
                                    <i class="fas fa-money-bill-wave"></i> Cobrar Venta
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>