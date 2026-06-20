<?php
// Si no hay sesión, no mostrar nada
if (!isset($_SESSION['user_id'])) {
    return;
}

$role = $_SESSION['role'] ?? 'seller';
?>

<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="/modules/dashboard/index.php">🏍️ MotoGestión Web</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end bg-dark text-white" id="offcanvasNavbar">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">
                    Menú - <?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username']) ?> 
                    <span class="badge <?= $role == 'superadmin' ? 'bg-danger' : ($role == 'admin' ? 'bg-primary' : ($role == 'finance' ? 'bg-success' : ($role == 'inventory' ? 'bg-info' : 'bg-secondary'))) ?>">
                        <?php 
                        $roleNames = [
                            'superadmin' => 'Super Admin',
                            'admin' => 'Administrador',
                            'finance' => 'Finanzas',
                            'inventory' => 'Inventario',
                            'seller' => 'Vendedor',
                            'cashier' => 'Cajero'
                        ];
                        echo $roleNames[$role] ?? $role;
                        ?>
                    </span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    
                    <!-- ========== DASHBOARD - Todos los roles ========== -->
                    <li class="nav-item">
                        <a class="nav-link" href="/modules/dashboard/index.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    
                    <!-- ========== PRODUCTOS - SOLO admin, superadmin, inventory ========== -->
                    <?php if (in_array($role, ['admin', 'superadmin', 'inventory'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/modules/products/index.php">
                                <i class="fas fa-box"></i> Productos
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- ========== STOCK BAJO - inventory, admin, superadmin ========== -->
                    <?php if (in_array($role, ['inventory', 'admin', 'superadmin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/modules/inventory/low_stock.php">
                                <i class="fas fa-exclamation-triangle"></i> Stock Bajo
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- ========== PROVEEDORES - Solo admin y superadmin ========== -->
                    <?php if (in_array($role, ['admin', 'superadmin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/modules/suppliers/index.php">
                                <i class="fas fa-truck"></i> Proveedores
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- ========== CLIENTES - Todos excepto finance ========== -->
                    <?php if ($role != 'finance'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/modules/clients/index.php">
                                <i class="fas fa-users"></i> Clientes
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- ========== PUNTO DE VENTA - SOLO seller, admin, superadmin ========== -->
                    <?php if (in_array($role, ['seller', 'admin', 'superadmin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/modules/sales/index.php">
                                <i class="fas fa-shopping-cart"></i> Punto de Venta
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- ========== HISTORIAL VENTAS - seller, cashier, admin, superadmin ========== -->
                    <?php if (in_array($role, ['seller', 'cashier', 'admin', 'superadmin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/modules/sales/invoices.php">
                                <i class="fas fa-file-invoice"></i> Historial de Ventas
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- ========== COBROS PENDIENTES - Solo cashier, admin, superadmin ========== -->
                    <?php if (in_array($role, ['cashier', 'admin', 'superadmin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/modules/sales/pending.php">
                                <i class="fas fa-clock"></i> Cobros Pendientes
                                <?php 
                                try {
                                    $count = $pdo->query("SELECT COUNT(*) FROM sales WHERE status = 'pendiente'")->fetchColumn();
                                    if ($count > 0) {
                                        echo '<span class="badge bg-warning text-dark float-end">' . $count . '</span>';
                                    }
                                } catch(PDOException $e) {}
                                ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- ========== REPORTES - Todos los roles ========== -->
                    <?php if (in_array($role, ['seller', 'cashier', 'inventory', 'finance', 'admin', 'superadmin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/modules/reports/index.php">
                                <i class="fas fa-chart-line"></i> Panel de Reportes
                            </a>
                        </li>
                    <?php endif; ?>
                    
<!-- ========== GESTIÓN USUARIOS - Solo admin y superadmin ========== -->
<?php if (in_array($role, ['admin', 'superadmin'])): ?>
    <li class="nav-item">
        <a class="nav-link" href="/modules/auth/register.php">
            <i class="fas fa-user-plus"></i> Gestión de Usuarios
        </a>
    </li>
<?php endif; ?>
                    
                    <!-- ========== BITÁCORA Y LIMPIAR LOGS - Solo superadmin ========== -->
                    <?php if ($role == 'superadmin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/modules/admin/logs.php">
                                <i class="fas fa-history"></i> Bitácora del Sistema
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/modules/admin/clean_logs.php">
                                <i class="fas fa-trash-alt"></i> Limpiar Logs
                            </a>
                        </li>
                    <?php endif; ?>
                    
<!-- ========== CONFIGURACIÓN - Todos los roles según permiso ========== -->
<?php if (in_array($role, ['superadmin', 'admin', 'finance', 'inventory', 'seller', 'cashier'])): ?>
    <li class="nav-item">
        <hr class="dropdown-divider">
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/modules/config/index.php">
            <i class="fas fa-cog"></i> Configuración
        </a>
    </li>
<?php endif; ?>
                    
                    <li class="nav-item">
                        <hr class="dropdown-divider">
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="/modules/auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<div style="margin-top: 70px;"></div>