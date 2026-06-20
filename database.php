<?php
$host = 'sql103.infinityfree.com';
$dbname = 'if0_42154355_motomania';
$username = 'if0_42154355';
$password = 'ingsof2026';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función para verificar permisos según rol
function hasPermission($requiredRole) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        return false;
    }
    
    $role = $_SESSION['role'];
    $roleLevel = [
        'seller' => 1,
        'cashier' => 1,
        'inventory' => 2,
        'finance' => 3,
        'admin' => 4,
        'superadmin' => 5
    ];
    
    $requiredLevel = $roleLevel[$requiredRole] ?? 0;
    $userLevel = $roleLevel[$role] ?? 0;
    
    return $userLevel >= $requiredLevel;
}

// Función para verificar si puede modificar a otro usuario
function canModifyUser($targetUserId, $targetUserRole, $pdo) {
    $currentRole = $_SESSION['role'];
    $currentUserId = $_SESSION['user_id'];
    
    // Superadmin puede modificar a cualquiera
    if ($currentRole === 'superadmin') {
        return true;
    }
    
    // No puede modificarse a sí mismo (excepto superadmin)
    if ($currentUserId == $targetUserId) {
        return false;
    }
    
    // Admin puede modificar: seller, cashier, inventory, finance
    if ($currentRole === 'admin') {
        return in_array($targetUserRole, ['seller', 'cashier', 'inventory', 'finance']);
    }
    
    // Otros roles no pueden modificar usuarios
    return false;
}

// Función para obtener el nivel del rol
function getRoleLevel($role) {
    $levels = [
        'seller' => 1,
        'cashier' => 1,
        'inventory' => 2,
        'finance' => 3,
        'admin' => 4,
        'superadmin' => 5
    ];
    return $levels[$role] ?? 0;
}

// Función para registrar logs del sistema
function registerLog($pdo, $action, $tableName = null, $recordId = null, $details = null) {
    try {
        $userId = $_SESSION['user_id'] ?? null;
        $username = $_SESSION['username'] ?? 'Sistema';
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        // Convertir detalles a JSON si es array
        if (is_array($details)) {
            $details = json_encode($details, JSON_UNESCAPED_UNICODE);
        }
        
        $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, username, action, table_name, record_id, details, ip_address, user_agent) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $username, $action, $tableName, $recordId, $details, $ip, $userAgent]);
        
        return true;
    } catch(PDOException $e) {
        error_log("Error al registrar log: " . $e->getMessage());
        return false;
    }
}

// ========== FUNCIÓN PARA REDIMENSIONAR IMÁGENES ==========
function resizeImage($source_path, $target_path, $max_width = 800, $max_height = 800, $quality = 85) {
    // Verificar que la función GD esté instalada
    if (!extension_loaded('gd')) {
        // Si no hay GD, solo copiar la imagen original
        return copy($source_path, $target_path);
    }
    
    // Obtener información de la imagen original
    $image_info = getimagesize($source_path);
    if (!$image_info) {
        return false;
    }
    
    list($orig_width, $orig_height, $image_type) = $image_info;
    
    // Calcular nuevas dimensiones manteniendo la proporción
    if ($orig_width > $orig_height) {
        $new_width = $max_width;
        $new_height = intval($orig_height * $max_width / $orig_width);
    } else {
        $new_height = $max_height;
        $new_width = intval($orig_width * $max_height / $orig_height);
    }
    
    // Crear imagen de destino
    $destination = imagecreatetruecolor($new_width, $new_height);
    
    // Cargar imagen original según el tipo
    switch ($image_type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($source_path);
            // Preservar transparencia para PNG
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $new_width, $new_height, $transparent);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($source_path);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($source_path);
            break;
        default:
            return false;
    }
    
    // Redimensionar
    imagecopyresampled($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);
    
    // Guardar imagen redimensionada según el tipo original
    $result = false;
    switch ($image_type) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($destination, $target_path, $quality);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($destination, $target_path, 9);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($destination, $target_path);
            break;
        case IMAGETYPE_WEBP:
            $result = imagewebp($destination, $target_path, $quality);
            break;
    }
    
    // Liberar memoria
    imagedestroy($source);
    imagedestroy($destination);
    
    return $result;
}
?>