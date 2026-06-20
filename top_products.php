<?php
session_start();
require_once '../../config/database.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) { 
    header('Location: /modules/auth/login.php'); 
    exit; 
}

$role = $_SESSION['role'] ?? 'seller';

// Roles permitidos
if (!in_array($role, ['inventory', 'admin', 'superadmin', 'seller', 'cashier'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Consulta corregida
$stmt = $pdo->prepare("
    SELECT 
        p.id, 
        p.name, 
        p.code, 
        SUM(sd.quantity) as total_quantity,
        SUM(sd.subtotal) as total_revenue,
        SUM(sd.subtotal_cost) as total_cost,
        (SUM(sd.subtotal) - SUM(sd.subtotal_cost)) as total_profit
    FROM sale_details sd
    JOIN products p ON sd.product_id = p.id
    JOIN sales s ON sd.sale_id = s.id
    WHERE DATE(s.sale_date) BETWEEN ? AND ?
    GROUP BY p.id
    ORDER BY total_quantity DESC
    LIMIT 20
");
$stmt->execute([$start_date, $end_date]);
$products = $stmt->fetchAll();

include '../../includes/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .modern-card {
        border: none;
        border-radius: 20px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }
    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .progress-bar-custom {
        border-radius: 10px;
        background: linear-gradient(90deg, #e67e22, #f39c12);
    }
    .welcome-banner {
        background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%);
        border-radius: 20px;
        padding: 25px;
        color: white;
        margin-bottom: 30px;
    }
</style>

<div class="container-fluid mt-4">

<div class="welcome-banner">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2><i class="fas fa-trophy"></i> Productos Más Vendidos</h2>
            <p class="mb-0">Ranking de productos con mayor rotación</p>
        </div>
        <div class="col-md-4 text-end">
            <i class="fas fa-chart-line" style="font-size: 60px; opacity: 0.5;"></i>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card modern-card shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-4">
        <h5><i class="fas fa-filter text-primary"></i> Filtrar por Fecha</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Fecha Inicio</label>
                <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
            </div>
            <div class="col-md-5">
                <label class="form-label">Fecha Fin</label>
                <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Resultados -->
<div class="card modern-card shadow-sm">
    <div class="card-header bg-white border-0 pt-4">
        <h5><i class="fas fa-chart-bar text-warning"></i> Top Productos</h5>
        <small class="text-muted"><?= date('d/m/Y', strtotime($start_date)) ?> al <?= date('d/m/Y', strtotime($end_date)) ?></small>
    </div>
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                No hay ventas en el período seleccionado.
            </div>
        <?php else: ?>
            <canvas id="topProductsChart" style="max-height: 400px; margin-bottom: 30px;"></canvas>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Ingresos</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalQuantity = array_sum(array_column($products, 'total_quantity'));
                        $rank = 1;
                        foreach ($products as $p): 
                            $percentage = $totalQuantity > 0 ? ($p['total_quantity'] / $totalQuantity) * 100 : 0;
                        ?>
                        <tr>
                            <td><strong><?= $rank++ ?></strong></td>
                            <td><code><?= htmlspecialchars($p['code']) ?></code></td>
                            <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                            <td class="text-primary"><strong><?= number_format($p['total_quantity']) ?></strong></td>
                            <td class="text-success">L <?= number_format($p['total_revenue'], 2) ?></td>
                            <td style="width: 200px;">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar progress-bar-custom" style="width: <?= $percentage ?>%">
                                        <?= number_format($percentage, 1) ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">TOTALES:</td>
                            <td><?= number_format($totalQuantity) ?></td>
                            <td>L <?= number_format(array_sum(array_column($products, 'total_revenue')), 2) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

</div>

<script>
<?php if (!empty($products)): ?>
const ctx = document.getElementById('topProductsChart');
if(ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($products, 'name')) ?>,
            datasets: [{
                label: 'Unidades Vendidas',
                data: <?= json_encode(array_column($products, 'total_quantity')) ?>,
                backgroundColor: 'rgba(230, 126, 34, 0.7)',
                borderColor: 'rgba(230, 126, 34, 1)',
                borderWidth: 2,
                borderRadius: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                x: { beginAtZero: true, title: { display: true, text: 'Unidades Vendidas' } }
            }
        }
    });
}
<?php endif; ?>
</script>

<?php include '../../includes/footer.php'; ?>