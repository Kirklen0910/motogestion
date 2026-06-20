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
        'factura_prefijo',
        'factura_inicial',
        'factura_pie'
    ];
    
    foreach ($campos as $campo) {
        if (isset($_POST[$campo])) {
            updateConfig($pdo, $campo, trim($_POST[$campo]));
        }
    }
    
    $mensaje = '<div class="alert alert-success">✅ Configuración guardada correctamente</div>';
}

// Obtener valores actuales
$factura_prefijo = getConfig($pdo, 'factura_prefijo', 'INV-');
$factura_inicial = getConfig($pdo, 'factura_inicial', '1001');
$factura_pie = getConfig($pdo, 'factura_pie', '¡Gracias por su compra!');

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
                    <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Configuración de Facturación</h5>
                </div>
                <div class="card-body p-4">
                    <?= $mensaje ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Prefijo de Factura</label>
                            <input type="text" name="factura_prefijo" class="form-control" 
                                   value="<?= htmlspecialchars($factura_prefijo) ?>" required>
                            <small class="text-muted">Ejemplo: INV-, FAC-, BOL-</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Número de Factura Inicial</label>
                            <input type="number" name="factura_inicial" class="form-control" 
                                   value="<?= htmlspecialchars($factura_inicial) ?>" required min="1">
                            <small class="text-muted">Número desde el cual comenzará la numeración</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pie de Página</label>
                            <input type="text" name="factura_pie" class="form-control" 
                                   value="<?= htmlspecialchars($factura_pie) ?>">
                            <small class="text-muted">Texto que aparecerá al final de cada factura</small>
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