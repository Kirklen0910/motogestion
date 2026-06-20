<?php
session_start();
require_once '../../config/database.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) { 
    header('Location: /modules/auth/login.php'); 
    exit; 
}

$user_id = $_SESSION['user_id'];
$mensaje = '';

// Obtener datos del usuario
$stmt = $pdo->prepare("SELECT username, fullname, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($fullname) || empty($email)) {
        $mensaje = '<div class="alert alert-danger">⚠️ Todos los campos son obligatorios</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = '<div class="alert alert-danger">⚠️ Correo electrónico inválido</div>';
    } else {
        // Actualizar datos básicos
        $stmt = $pdo->prepare("UPDATE users SET fullname = ?, email = ? WHERE id = ?");
        $stmt->execute([$fullname, $email, $user_id]);
        
        // Actualizar contraseña si se proporcionó
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $mensaje = '<div class="alert alert-danger">⚠️ La contraseña debe tener al menos 6 caracteres</div>';
            } elseif ($password !== $confirm_password) {
                $mensaje = '<div class="alert alert-danger">⚠️ Las contraseñas no coinciden</div>';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed, $user_id]);
                $_SESSION['fullname'] = $fullname;
                $mensaje = '<div class="alert alert-success">✅ Perfil actualizado correctamente</div>';
            }
        } else {
            $_SESSION['fullname'] = $fullname;
            $mensaje = '<div class="alert alert-success">✅ Perfil actualizado correctamente</div>';
        }
    }
}

include '../../includes/header.php';
?>

<style>
    .config-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.10);
        transition: all 0.3s ease;
        background: white;
    }
    .config-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.20);
    }
    .config-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px 20px 0 0 !important;
        padding: 16px 24px;
    }
    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        color: white;
    }
</style>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="config-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-cog"></i> Mi Perfil</h5>
                </div>
                <div class="card-body p-4">
                    <?= $mensaje ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre de Usuario</label>
                            <input type="text" class="form-control" 
                                   value="<?= htmlspecialchars($user['username']) ?>" disabled>
                            <small class="text-muted">El nombre de usuario no se puede modificar</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" name="fullname" class="form-control" 
                                   value="<?= htmlspecialchars($user['fullname']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        
                        <hr>
                        <h6 class="fw-bold">Cambiar Contraseña</h6>
                        <small class="text-muted">Dejar en blanco si no deseas cambiar la contraseña</small>
                        
                        <div class="mb-3 mt-2">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="password" class="form-control" minlength="6">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" name="confirm_password" class="form-control" minlength="6">
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="/modules/dashboard/index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>