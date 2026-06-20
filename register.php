<?php
session_start();
require_once '../../config/database.php';

// Verificar permisos (solo admin o superadmin pueden acceder)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$currentRole = $_SESSION['role'];
$currentUserId = $_SESSION['user_id'];
$error = '';
$success = '';
$editMode = false;
$editId = null;

// Obtener usuario para editar
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT id, username, fullname, role FROM users WHERE id = ?");
    $stmt->execute([$editId]);
    $editUser = $stmt->fetch();
    
    if ($editUser) {
        // Verificar si puede editar a este usuario
        if ($currentRole == 'admin' && $editUser['role'] == 'superadmin') {
            $error = 'No tienes permiso para editar un Super Administrador';
            $editMode = false;
        } else {
            $editMode = true;
        }
    }
}

// Eliminar usuario
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    
    // No permitir eliminarse a sí mismo
    if ($deleteId == $currentUserId) {
        $error = 'No puedes eliminarte a ti mismo';
    } else {
        $stmt = $pdo->prepare("SELECT role, username FROM users WHERE id = ?");
        $stmt->execute([$deleteId]);
        $targetUser = $stmt->fetch();
        
        if ($targetUser) {
            // Admin no puede eliminar superadmin
            if ($currentRole == 'admin' && $targetUser['role'] == 'superadmin') {
                $error = 'No tienes permiso para eliminar un Super Administrador';
            } else {
                // 📝 Registrar eliminación antes de borrar
                registerLog($pdo, 'DELETE', 'users', $deleteId, "Usuario eliminado: {$targetUser['username']} (Rol: {$targetUser['role']})");
                
                $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $deleteStmt->execute([$deleteId]);
                $success = 'Usuario eliminado correctamente';
            }
        }
    }
}

// Procesar formulario (crear o editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['delete'])) {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'seller';
    $isEdit = isset($_POST['edit_id']) && !empty($_POST['edit_id']);
    
    // Validaciones
    if (empty($username) || empty($fullname)) {
        $error = 'Nombre de usuario y nombre completo son obligatorios';
    } elseif (!$isEdit && empty($password)) {
        $error = 'La contraseña es obligatoria para nuevos usuarios';
    } elseif (!$isEdit && strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } elseif (!$isEdit && $password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } else {
        
        if ($isEdit) {
            // MODO EDICIÓN
            $editId = (int)$_POST['edit_id'];
            
            // Verificar si puede editar este usuario
            $checkStmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
            $checkStmt->execute([$editId]);
            $targetUser = $checkStmt->fetch();
            
            if ($currentRole == 'admin' && $targetUser['role'] == 'superadmin') {
                $error = 'No tienes permiso para editar un Super Administrador';
            } else {
                // Construir query según si cambia contraseña
                if (!empty($password)) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $updateStmt = $pdo->prepare("UPDATE users SET username = ?, fullname = ?, password = ?, role = ? WHERE id = ?");
                    $result = $updateStmt->execute([$username, $fullname, $hashedPassword, $role, $editId]);
                } else {
                    $updateStmt = $pdo->prepare("UPDATE users SET username = ?, fullname = ?, role = ? WHERE id = ?");
                    $result = $updateStmt->execute([$username, $fullname, $role, $editId]);
                }
                
                if ($result) {
                    // 📝 Registrar edición
                    registerLog($pdo, 'UPDATE', 'users', $editId, "Usuario editado: $username - Nuevo rol: $role");
                    $success = 'Usuario actualizado exitosamente';
                    header('Location: register.php');
                    exit;
                } else {
                    $error = 'Error al actualizar el usuario';
                }
            }
        } else {
            // MODO CREACIÓN
            // Validar que el rol sea permitido según quien crea
            if ($currentRole == 'admin' && !in_array($role, ['seller', 'cashier', 'inventory', 'finance'])) {
                $error = 'Un administrador solo puede crear usuarios con roles: Vendedor, Cajero, Inventario o Finanzas';
            } else {
                // Verificar si el usuario ya existe
                $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                $checkStmt->execute([$username]);
                
                if ($checkStmt->fetch()) {
                    $error = 'El nombre de usuario ya existe';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $insertStmt = $pdo->prepare("INSERT INTO users (username, fullname, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
                    
                    if ($insertStmt->execute([$username, $fullname, $hashedPassword, $role])) {
                        $newId = $pdo->lastInsertId();
                        // 📝 Registrar creación
                        registerLog($pdo, 'CREATE', 'users', $newId, "Usuario creado: $username - Rol: $role");
                        $success = 'Usuario creado exitosamente';
                        $_POST = [];
                    } else {
                        $error = 'Error al crear el usuario';
                    }
                }
            }
        }
    }
}

// Obtener lista de usuarios según el rol del usuario actual
if ($currentRole == 'superadmin') {
    // Superadmin ve TODOS los usuarios
    $users = $pdo->query("SELECT id, username, fullname, role, created_at FROM users ORDER BY 
                          FIELD(role, 'superadmin', 'admin', 'finance', 'inventory', 'seller', 'cashier'), username")->fetchAll();
} else {
    // Admin NO ve a los superadmin
    $stmt = $pdo->prepare("SELECT id, username, fullname, role, created_at FROM users 
                           WHERE role != 'superadmin' 
                           ORDER BY FIELD(role, 'admin', 'finance', 'inventory', 'seller', 'cashier'), username");
    $stmt->execute();
    $users = $stmt->fetchAll();
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><?= $editMode ? '✏️ Editar Usuario' : '📝 Registrar Nuevo Usuario' ?></h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <?php if ($editMode): ?>
                            <input type="hidden" name="edit_id" value="<?= $editUser['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label>Nombre de Usuario *</label>
                            <input type="text" name="username" class="form-control" 
                                   value="<?= htmlspecialchars($editMode ? $editUser['username'] : ($_POST['username'] ?? '')) ?>" 
                                   required <?= $editMode ? 'readonly' : '' ?>>
                            <?php if ($editMode): ?>
                                <small class="text-muted">El nombre de usuario no se puede modificar</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label>Nombre Completo *</label>
                            <input type="text" name="fullname" class="form-control" 
                                   value="<?= htmlspecialchars($editMode ? $editUser['fullname'] : ($_POST['fullname'] ?? '')) ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label><?= $editMode ? 'Nueva Contraseña (dejar vacío para no cambiar)' : 'Contraseña * (mínimo 6 caracteres)' ?></label>
                            <input type="password" name="password" class="form-control" <?= !$editMode ? 'required' : '' ?>>
                            <?php if ($editMode): ?>
                                <small class="text-muted">Solo llenar si deseas cambiar la contraseña</small>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!$editMode): ?>
                            <div class="mb-3">
                                <label>Confirmar Contraseña *</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label>Rol</label>
                            <select name="role" class="form-control" 
                                    <?= ($editMode && $editUser['role'] == 'superadmin') ? 'disabled' : '' ?>>
                                
                                <?php if ($currentRole == 'superadmin'): ?>
                                    <option value="superadmin" <?= ($editMode && $editUser['role'] == 'superadmin') ? 'selected' : '' ?>>Super Administrador (Acceso Total)</option>
                                    <option value="admin" <?= ($editMode && $editUser['role'] == 'admin') ? 'selected' : '' ?>>Administrador</option>
                                <?php endif; ?>
                                
                                <option value="finance" <?= ($editMode && $editUser['role'] == 'finance') ? 'selected' : '' ?>>Finanzas / Contabilidad</option>
                                <option value="inventory" <?= ($editMode && $editUser['role'] == 'inventory') ? 'selected' : '' ?>>Control de Inventario</option>
                                <option value="seller" <?= ($editMode && $editUser['role'] == 'seller') ? 'selected' : '' ?>>Vendedor</option>
                                <option value="cashier" <?= ($editMode && $editUser['role'] == 'cashier') ? 'selected' : '' ?>>Cajero</option>
                            </select>
                            
                            <?php if ($editMode && $editUser['role'] == 'superadmin'): ?>
                                <small class="text-danger">No se puede cambiar el rol del Super Administrador</small>
                            <?php endif; ?>
                            
                            <?php if ($currentRole == 'admin'): ?>
                                <small class="text-muted">Como administrador, solo puedes crear: Vendedor, Cajero, Inventario o Finanzas</small>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <?= $editMode ? 'Actualizar Usuario' : 'Registrar Usuario' ?>
                        </button>
                        
                        <?php if ($editMode): ?>
                            <a href="register.php" class="btn btn-secondary">Cancelar</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4>👥 Lista de Usuarios</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($users)): ?>
                        <div class="alert alert-info">No hay usuarios disponibles para mostrar</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Nombre Completo</th>
                                        <th>Rol</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <?php 
                                        // Determinar si el usuario actual puede editar/eliminar a este usuario
                                        $canModify = true;
                                        if ($currentRole == 'admin' && $user['role'] == 'superadmin') {
                                            $canModify = false;
                                        }
                                        if ($user['id'] == $currentUserId) {
                                            $canModify = false; // No puede eliminarse a sí mismo
                                        }
                                        ?>
                                        <tr class="<?= $user['id'] == $currentUserId ? 'table-warning' : '' ?>">
                                            <td><?= $user['id'] ?></td>
                                            <td><?= htmlspecialchars($user['username']) ?>
                                                <?= $user['id'] == $currentUserId ? ' <span class="badge bg-warning">Tú</span>' : '' ?>
                                            </td>
                                            <td><?= htmlspecialchars($user['fullname']) ?></td>
                                            <td>
                                                <?php
                                                $badgeClass = match($user['role']) {
                                                    'superadmin' => 'bg-danger',
                                                    'admin' => 'bg-primary',
                                                    'finance' => 'bg-success',
                                                    'inventory' => 'bg-info',
                                                    'seller' => 'bg-secondary',
                                                    'cashier' => 'bg-warning',
                                                    default => 'bg-dark'
                                                };
                                                $roleName = match($user['role']) {
                                                    'superadmin' => 'Super Admin',
                                                    'admin' => 'Administrador',
                                                    'finance' => 'Finanzas',
                                                    'inventory' => 'Inventario',
                                                    'seller' => 'Vendedor',
                                                    'cashier' => 'Cajero',
                                                    default => $user['role']
                                                };
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= $roleName ?></span>
                                            </td>
                                            <td>
                                                <?php if ($canModify): ?>
                                                    <a href="?edit=<?= $user['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                    <a href="?delete=<?= $user['id'] ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('¿Estás seguro de eliminar a <?= htmlspecialchars($user['username']) ?>?')">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">
                                                        <i class="fas fa-lock"></i> Sin permisos
                                                    </span>
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
    </div>
</div>

<?php include '../../includes/footer.php'; ?>