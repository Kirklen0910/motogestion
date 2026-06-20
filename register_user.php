<?php
require_once '../../config/database.php';
if (!isset($_SESSION['user_id'])) { 
    header('Location: login.php'); 
    exit; 
}

$role = $_SESSION['role'] ?? 'seller';

// Solo admin y superadmin pueden acceder
if (!in_array($role, ['admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

// Obtener lista de usuarios
$users = $pdo->query("SELECT id, username, fullname, email, role, created_at FROM users ORDER BY id DESC")->fetchAll();

include '../../includes/header.php';
?>

<style>
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
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-superadmin { background: #e74c3c; color: white; }
    .status-admin { background: #3498db; color: white; }
    .status-seller { background: #2ecc71; color: white; }
    .status-cashier { background: #f39c12; color: white; }
    .status-inventory { background: #9b59b6; color: white; }
    .status-finance { background: #1abc9c; color: white; }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-users-cog"></i> Gestión de Usuarios</h2>
        <a href="register.php" class="btn btn-success">
            <i class="fas fa-user-plus"></i> Nuevo Usuario
        </a>
    </div>
    
    <div class="card modern-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): 
                            $isSuperadmin = ($u['role'] == 'superadmin');
                            $isSelf = ($u['id'] == $_SESSION['user_id']);
                            
                            // Admin no puede modificar superadmin
                            $canModify = ($role == 'superadmin' || ($role == 'admin' && !$isSuperadmin));
                            
                            // Solo superadmin puede eliminar (y no puede eliminarse a sí mismo)
                            $canDelete = ($role == 'superadmin' && !$isSelf);
                        ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                            <td><?= htmlspecialchars($u['fullname']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <span class="status-badge status-<?= $u['role'] ?>">
                                    <?php 
                                    $roleNames = [
                                        'superadmin' => 'Super Admin',
                                        'admin' => 'Administrador',
                                        'finance' => 'Finanzas',
                                        'inventory' => 'Inventario',
                                        'seller' => 'Vendedor',
                                        'cashier' => 'Cajero'
                                    ];
                                    echo $roleNames[$u['role']] ?? $u['role'];
                                    ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <?php if ($canModify): ?>
                                    <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($canDelete): ?>
                                    <a href="delete_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!$canModify && !$canDelete): ?>
                                    <span class="text-muted"><i class="fas fa-lock"></i> Sin permisos</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No hay usuarios registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>