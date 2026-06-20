<?php
session_start();
require_once '../../config/database.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) { 
    header('Location: /modules/auth/login.php'); 
    exit; 
}

$role = $_SESSION['role'] ?? 'seller';

// Solo admin y superadmin pueden ver esto
if (!in_array($role, ['admin', 'superadmin'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$mensaje = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = [
        'empresa_nombre',
        'empresa_rtn',
        'empresa_telefono',
        'empresa_direccion',
        'empresa_email'
    ];
    
    foreach ($campos as $campo) {
        if (isset($_POST[$campo])) {
            updateConfig($pdo, $campo, trim($_POST[$campo]));
        }
    }
    
    $mensaje = '<div class="alert alert-success">✅ Configuración guardada correctamente</div>';
}

// Obtener valores actuales
$empresa_nombre = getConfig($pdo, 'empresa_nombre', 'MotoGestión Web');
$empresa_rtn = getConfig($pdo, 'empresa_rtn', '0000-0000-00000');
$empresa_telefono = getConfig($pdo, 'empresa_telefono', '+504 0000-0000');
$empresa_direccion = getConfig($pdo, 'empresa_direccion', 'Tegucigalpa, Honduras');
$empresa_email = getConfig($pdo, 'empresa_email', 'info@motogestion.com');

include '../../includes/header.php';
?>

<style>
    .config-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.10);
        transition: all 0.3s ease;
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
                    <h5 class="mb-0"><i class="fas fa-building"></i> Datos de la Empresa</h5>
                </div>
                <div class="card-body p-4">
                    <?= $mensaje ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre de la Empresa</label>
                            <input type="text" name="empresa_nombre" class="form-control" 
                                   value="<?= htmlspecialchars($empresa_nombre) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">RTN / NIT</label>
                            <input type="text" name="empresa_rtn" class="form-control" 
                                   value="<?= htmlspecialchars($empresa_rtn) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" name="empresa_telefono" class="form-control" 
                                   value="<?= htmlspecialchars($empresa_telefono) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Dirección</label>
                            <input type="text" name="empresa_direccion" class="form-control" 
                                   value="<?= htmlspecialchars($empresa_direccion) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" name="empresa_email" class="form-control" 
                                   value="<?= htmlspecialchars($empresa_email) ?>">
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