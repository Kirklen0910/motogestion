<?php
/**
 * Funciones auxiliares para el módulo de Configuración
 */

// Obtener valor de configuración por clave
function getConfig($pdo, $clave, $default = null) {
    $stmt = $pdo->prepare("SELECT valor FROM configuracion WHERE clave = ?");
    $stmt->execute([$clave]);
    $result = $stmt->fetchColumn();
    return $result !== false ? $result : $default;
}

// Actualizar valor de configuración
function updateConfig($pdo, $clave, $valor) {
    $stmt = $pdo->prepare("UPDATE configuracion SET valor = ? WHERE clave = ?");
    return $stmt->execute([$valor, $clave]);
}

// Obtener preferencia de usuario
function getUserPreference($pdo, $user_id, $clave, $default = null) {
    $stmt = $pdo->prepare("SELECT valor FROM preferencias_usuario WHERE user_id = ? AND clave = ?");
    $stmt->execute([$user_id, $clave]);
    $result = $stmt->fetchColumn();
    return $result !== false ? $result : $default;
}

// Actualizar preferencia de usuario
function updateUserPreference($pdo, $user_id, $clave, $valor) {
    $stmt = $pdo->prepare("INSERT INTO preferencias_usuario (user_id, clave, valor) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE valor = ?");
    return $stmt->execute([$user_id, $clave, $valor, $valor]);
}

// Verificar si el usuario tiene permiso para ver una configuración
function canAccessConfig($rol_usuario, $rol_permiso) {
    if ($rol_usuario == 'superadmin') return true;
    if ($rol_usuario == 'admin' && in_array($rol_permiso, ['admin', 'superadmin'])) return true;
    if ($rol_usuario == 'finance' && $rol_permiso == 'finance') return true;
    if ($rol_usuario == 'inventory' && $rol_permiso == 'inventory') return true;
    return $rol_usuario == $rol_permiso;
}

// Obtener todas las configuraciones de un grupo
function getConfigGroup($pdo, $grupo) {
    $stmt = $pdo->prepare("SELECT clave, valor, descripcion, tipo FROM configuracion WHERE grupo = ?");
    $stmt->execute([$grupo]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener configuraciones por rol
function getConfigByRole($pdo, $rol) {
    $stmt = $pdo->prepare("SELECT clave, valor, descripcion, tipo, grupo FROM configuracion WHERE rol_permiso = ? OR rol_permiso = 'admin' OR rol_permiso = 'superadmin'");
    $stmt->execute([$rol]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>