<?php
session_start();
require_once '../../config/database.php';

// 📝 Registrar cierre de sesión
if (isset($_SESSION['user_id'])) {
    registerLog($pdo, 'LOGOUT', 'users', $_SESSION['user_id'], "Usuario {$_SESSION['username']} cerró sesión");
}

session_destroy();
header('Location: login.php');
exit;
?>