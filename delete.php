<?php
require_once '../../config/database.php';
if (!isset($_SESSION['user_id'])) { header('Location: /modules/auth/login.php'); exit; }

$id = $_GET['id'] ?? 0;

// Obtener datos del producto antes de eliminarlo
$productData = $pdo->prepare("SELECT name, code, image FROM products WHERE id = ?");
$productData->execute([$id]);
$product = $productData->fetch();

if ($product) {
    // Eliminar la imagen si existe
    if (!empty($product['image']) && file_exists('../../' . $product['image'])) {
        unlink('../../' . $product['image']);
    }
    
    // 📝 Registrar log de eliminación de producto
    registerLog($pdo, 'DELETE', 'products', $id, "Producto eliminado: {$product['name']} (Código: {$product['code']})");
}

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);
header('Location: index.php');
exit;
?>