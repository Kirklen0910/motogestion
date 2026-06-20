<?php
session_start();
require_once '../../config/database.php';

// Si ya está logueado
if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard/index.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Validación de espacios - trim y sanitización
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // ✅ Validar que no estén vacíos después de trim
    if (empty($username) || empty($password)) {
        $error = '⚠️ Usuario y contraseña son obligatorios';
    } else {
        // ✅ Buscar usuario (case-sensitive en username)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE BINARY username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        $passwordValid = false;
        
        if ($user) {
            // Verificar si la contraseña está hasheada
            if (strpos($user['password'], '$2y$') === 0) {
                $passwordValid = password_verify($password, $user['password']);
            } else {
                // Validación exacta (case-sensitive)
                $passwordValid = ($password === $user['password']);
                if ($passwordValid) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $updateStmt->execute([$newHash, $user['id']]);
                }
            }
        }
        
        if ($passwordValid) {
            // Guardar datos en sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            
            // Registrar login exitoso
            registerLog($pdo, 'LOGIN_EXITOSO', 'users', $user['id'], "Usuario {$user['username']} inició sesión");
            
            header('Location: ../dashboard/index.php');
            exit;
        } else {
            // Registrar intento fallido
            if ($user) {
                registerLog($pdo, 'LOGIN_FALLIDO', 'users', $user['id'] ?? null, "Intento fallido para usuario: $username");
            } else {
                registerLog($pdo, 'LOGIN_FALLIDO', null, null, "Intento fallido - Usuario no existe: $username");
            }
            $error = '❌ Usuario o contraseña incorrectos';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoGestión Web - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ===== ESTILOS EXCLUSIVOS PARA LOGIN ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        /* ===== FONDO CON FIGURAS DECORATIVAS ===== */
        body::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: floatBubble 8s ease-in-out infinite;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            animation: floatBubble 10s ease-in-out infinite reverse;
        }
        
        @keyframes floatBubble {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -30px) scale(1.1); }
        }
        
        /* ===== FIGURAS DECORATIVAS ADICIONALES ===== */
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            background: white;
        }
        .shape-1 {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 5%;
            animation: floatBubble 12s ease-in-out infinite;
        }
        .shape-2 {
            width: 150px;
            height: 150px;
            bottom: 15%;
            right: 8%;
            animation: floatBubble 14s ease-in-out infinite reverse;
        }
        .shape-3 {
            width: 100px;
            height: 100px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: pulseGlow 4s ease-in-out infinite;
        }
        
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.03; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.08; transform: translate(-50%, -50%) scale(1.3); }
        }
        
        /* ===== TARJETA DE LOGIN ===== */
        .login-card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px 36px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.25), 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            max-width: 420px;
            width: 100%;
            animation: fadeInUp 0.8s ease forwards;
            transition: transform 0.3s ease;
        }
        .login-card:hover {
            transform: translateY(-4px);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        /* ===== LOGO ===== */
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo .icon {
            display: inline-block;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 24px;
            line-height: 80px;
            text-align: center;
            font-size: 40px;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .login-logo .icon:hover {
            transform: scale(1.05) rotate(-5deg);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }
        .login-logo h1 {
            font-size: 28px;
            font-weight: 700;
            margin-top: 16px;
            margin-bottom: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
        .login-logo p {
            color: #718096;
            font-size: 14px;
            font-weight: 400;
            margin: 0;
            -webkit-text-fill-color: #718096;
        }
        
        /* ===== CAMPOS DE FORMULARIO ===== */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        .form-group .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 18px;
            transition: color 0.3s ease;
            z-index: 3;
        }
        .form-group .form-control {
            padding: 14px 16px 14px 48px;
            border-radius: 14px;
            border: 2px solid #e2e8f0;
            background: rgba(255, 255, 255, 0.9);
            font-size: 15px;
            transition: all 0.3s ease;
            height: 54px;
            color: #2d3748;
            font-weight: 500;
        }
        .form-group .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.12);
            background: white;
            outline: none;
        }
        .form-group .form-control::placeholder {
            color: #a0aec0;
            font-weight: 400;
        }
        .form-group .form-control:focus + .input-icon,
        .form-group .form-control:focus ~ .input-icon {
            color: #667eea;
        }
        
        /* ===== BOTÓN DE LOGIN ===== */
        .btn-login {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 17px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            height: 54px;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            margin-top: 6px;
        }
        .btn-login::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.6s ease;
            transform: translate(-50%, -50%);
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }
        .btn-login:hover::after {
            width: 400px;
            height: 400px;
        }
        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .btn-login i {
            margin-right: 10px;
        }
        
        /* ===== ALERTAS ===== */
        .alert-custom {
            border-radius: 14px;
            padding: 14px 18px;
            border: none;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shakeError 0.5s ease;
            margin-bottom: 20px;
        }
        .alert-custom.alert-danger {
            background: rgba(252, 129, 129, 0.12);
            color: #e53e3e;
            border-left: 4px solid #fc8181;
        }
        .alert-custom.alert-success {
            background: rgba(72, 187, 120, 0.12);
            color: #276749;
            border-left: 4px solid #48bb78;
        }
        .alert-custom i {
            font-size: 20px;
        }
        
        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 480px) {
            .login-card {
                padding: 28px 20px;
                margin: 16px;
                border-radius: 20px;
            }
            .login-logo .icon {
                width: 64px;
                height: 64px;
                line-height: 64px;
                font-size: 30px;
                border-radius: 20px;
            }
            .login-logo h1 {
                font-size: 24px;
            }
            .form-group .form-control {
                padding: 12px 14px 12px 44px;
                height: 48px;
                font-size: 14px;
            }
            .btn-login {
                height: 48px;
                font-size: 15px;
            }
            .shape-1, .shape-2 {
                display: none;
            }
        }
        
        /* ===== FOOTER DE LOGIN ===== */
        .login-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }
        .login-footer p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
            margin: 0;
            letter-spacing: 1px;
        }
        .login-footer p i {
            color: #fc8181;
            margin: 0 4px;
        }
        .login-footer .version {
            color: rgba(255, 255, 255, 0.3);
            font-size: 11px;
        }
    </style>
</head>
<body>

<!-- Figuras decorativas -->
<div class="shape shape-1"></div>
<div class="shape shape-2"></div>
<div class="shape shape-3"></div>

<div class="login-card">
    
    <!-- Logo -->
    <div class="login-logo">
        <div class="icon">
            <i class="fas fa-motorcycle"></i>
        </div>
        <h1>MotoGestión</h1>
        <p>Inicia sesión para continuar</p>
    </div>
    
    <!-- Mensajes de error/éxito -->
    <?php if ($error): ?>
        <div class="alert-custom alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <!-- Formulario -->
    <form method="POST" autocomplete="off">
        <div class="form-group">
            <input type="text" 
                   name="username" 
                   class="form-control" 
                   placeholder="Nombre de usuario"
                   value="<?= htmlspecialchars($username) ?>"
                   required 
                   autofocus>
            <i class="fas fa-user input-icon"></i>
        </div>
        
        <div class="form-group">
            <input type="password" 
                   name="password" 
                   class="form-control" 
                   placeholder="Contraseña"
                   required>
            <i class="fas fa-lock input-icon"></i>
        </div>
        
        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Ingresar
        </button>
    </form>
    
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>