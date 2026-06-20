<?php
session_start();
require_once '../../config/database.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) { 
    header('Location: /modules/auth/login.php'); 
    exit; 
}

$role = $_SESSION['role'] ?? 'seller';
$user_id = $_SESSION['user_id'];
$mensaje = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tema = $_POST['tema'] ?? 'claro';
    $fuente = $_POST['fuente'] ?? 'normal';
    
    updateUserPreference($pdo, $user_id, 'tema', $tema);
    updateUserPreference($pdo, $user_id, 'fuente', $fuente);
    
    $mensaje = '<div class="alert alert-success">✅ Preferencias guardadas correctamente</div>';
}

// Obtener preferencias actuales
$tema = getUserPreference($pdo, $user_id, 'tema', 'claro');
$fuente = getUserPreference($pdo, $user_id, 'fuente', 'normal');

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
                    <h5 class="mb-0"><i class="fas fa-sliders-h"></i> Mis Preferencias</h5>
                </div>
                <div class="card-body p-4">
                    <?= $mensaje ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tema</label>
                            <select name="tema" class="form-control">
                                <option value="claro" <?= $tema == 'claro' ? 'selected' : '' ?>>☀️ Claro</option>
                                <option value="oscuro" <?= $tema == 'oscuro' ? 'selected' : '' ?>>🌙 Oscuro</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tamaño de Fuente</label>
                            <select name="fuente" class="form-control">
                                <option value="pequena" <?= $fuente == 'pequena' ? 'selected' : '' ?>>Pequeña</option>
                                <option value="normal" <?= $fuente == 'normal' ? 'selected' : '' ?>>Normal</option>
                                <option value="grande" <?= $fuente == 'grande' ? 'selected' : '' ?>>Grande</option>
                            </select>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Guardar Preferencias
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