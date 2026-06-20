<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['seller', 'admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$client_id = $_GET['client_id'] ?? 0;
$clients = $pdo->query("SELECT id, name FROM clients ORDER BY name")->fetchAll();

$client = null;
$sales = [];
$total = 0;

if ($client_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch();
    
    if ($client) {
        $sales = $pdo->prepare("
            SELECT s.*, u.fullname as user_name 
            FROM sales s
            LEFT JOIN users u ON s.user_id = u.id
            WHERE s.client_id = ?
            ORDER BY s.sale_date DESC
        ");
        $sales->execute([$client_id]);
        $sales = $sales->fetchAll();
        $total = array_sum(array_column($sales, 'total'));
    }
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <h2><i class="fas fa-history"></i> Historial por Cliente</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-search"></i> Seleccionar Cliente
        </div>
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-6">
                    <select name="client_id" class="form-control" required>
                        <option value="">-- Seleccione un cliente --</option>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $client_id == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if ($client): ?>
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h4>Información del Cliente</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3"><strong>Nombre:</strong> <?= htmlspecialchars($client['name']) ?></div>
                    <div class="col-md-3"><strong>Email:</strong> <?= htmlspecialchars($client['email'] ?? '-') ?></div>
                    <div class="col-md-3"><strong>Teléfono:</strong> <?= htmlspecialchars($client['phone'] ?? '-') ?></div>
                    <div class="col-md-3"><strong>Total Compras:</strong> L <?= number_format($total, 2) ?></div>
                </div>
                <div class="row mt-2">
                    <div class="col-12"><strong>Dirección:</strong> <?= htmlspecialchars($client['address'] ?? '-') ?></div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-success text-white">
                <i class="fas fa-shopping-cart"></i> Historial de Compras
            </div>
            <div class="card-body">
                <?php if (empty($sales)): ?>
                    <div class="alert alert-warning">Este cliente no tiene compras registradas.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Factura</th>
                                    <th>Fecha</th>
                                    <th>Vendedor</th>
                                    <th>Total</th>
                                    <th>Detalle</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales as $s): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($s['invoice_number']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($s['sale_date'])) ?></td>
                                        <td><?= htmlspecialchars($s['user_name']) ?></td>
                                        <td>L <?= number_format($s['total'], 2) ?></td>
                                        <td><a href="/modules/sales/invoice_detail.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-info">Ver</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-info">
                                <tr>
                                    <td colspan="3"><strong>TOTAL</strong></td>
                                    <td><strong>L <?= number_format($total, 2) ?></strong></td>
                                    <td></td>
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