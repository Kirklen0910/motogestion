<?php
session_start();
require_once '../../config/database.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) { 
    header('Location: /modules/auth/login.php'); 
    exit; 
}

$role = $_SESSION['role'] ?? 'seller';

// Solo admin, superadmin y finance pueden ver esto
if (!in_array($role, ['admin', 'superadmin', 'finance'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$mensaje = '';
$solo_lectura = ($role == 'finance');

// Procesar formulario (solo admin/superadmin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$solo_lectura) {
    if (isset($_POST['tasa_impuesto'])) {
        updateConfig($pdo, 'tasa_impuesto', trim($_POST['tasa_impuesto']));
    }
    if (isset($_POST['aplicar_impuesto'])) {
        updateConfig($pdo, 'aplicar_impuesto', $_POST['aplicar_impuesto']);
    }
    
    $mensaje = '<div class="alert alert-success">✅ Configuración guardada correctamente</div>';
}

// Obtener valores actuales
$tasa_impuesto = getConfig($pdo, 'tasa_impuesto', '15.00');
$aplicar_impuesto = getConfig($pdo, 'aplicar_impuesto', '1');

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
                    <h5 class="mb-0"><i class="fas fa-percent"></i> Configuración de Impuestos</h5>
                </div>
                <div class="card-body p-4">
                    <?= $mensaje ?>
                    
                    <?php if ($solo_lectura): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Nota:</strong> Como usuario de Finanzas, solo puedes visualizar la configuración de impuestos.
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tasa de Impuesto (ISV) %</label>
                            <input type="number" name="tasa_impuesto" class="form-control" 
                                   value="<?= htmlspecialchars($tasa_impuesto) ?>" 
                                   step="0.01" min="0" max="100"
                                   <?= $solo_lectura ? 'readonly' : '' ?>>
                            <small class="text-muted">Tasa de impuesto aplicada en Honduras (ISV 15%)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Aplicar Impuesto</label>
                            <select name="aplicar_impuesto" class="form-control" <?= $solo_lectura ? 'disabled' : '' ?>>
                                <option value="1" <?= $aplicar_impuesto == '1' ? 'selected' : '' ?>>✅ Sí, aplicar impuesto</option>
                                <option value="0" <?= $aplicar_impuesto == '0' ? 'selected' : '' ?>>❌ No aplicar impuesto</option>
                            </select>
                        </div>
                        
                        <?php if (!$solo_lectura): ?>
                            <div class="text-end">
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                <a href="/modules/dashboard/index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-end">
                                <a href="/modules/dashboard/index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>