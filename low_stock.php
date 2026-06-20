<?php
require_once '../../config/database.php';
if (!isset($_SESSION['user_id'])) { header('Location: /modules/auth/login.php'); exit; }

$products = $pdo->query("SELECT * FROM products WHERE stock <= min_stock ORDER BY stock ASC")->fetchAll();

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>
<div class="container mt-4">
    <h2>Productos con Stock Bajo</h2>
    <?php if (count($products) == 0): ?>
        <div class="alert alert-success">No hay productos con stock bajo.</div>
    <?php else: ?>
        <table class="table table-bordered mt-3">
            <thead><tr><th>Código</th><th>Nombre</th><th>Stock Actual</th><th>Stock Mínimo</th><th>Acción</th></tr></thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr class="table-danger">
                    <td><?= htmlspecialchars($p['code']) ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><strong><?= $p['stock'] ?></strong></td>
                    <td><?= $p['min_stock'] ?></td>
                    <td><a href="/modules/products/edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Reabastecer</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php include '../../includes/footer.php'; ?>