<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$role = $_SESSION['role'] ?? 'seller';
if (!in_array($role, ['cashier', 'admin', 'superadmin'])) {
    echo json_encode(['success' => false, 'error' => 'No tienes permisos para cobrar']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$sale_id = $data['sale_id'] ?? 0;
$payment_method = $data['payment_method'] ?? 'Efectivo';
$monto_recibido = $data['monto_recibido'] ?? 0;
$total = $data['total'] ?? 0;

if (!$sale_id) {
    echo json_encode(['success' => false, 'error' => 'ID de venta no proporcionado']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, total, status, subtotal, tax FROM sales WHERE id = ?");
    $stmt->execute([$sale_id]);
    $sale = $stmt->fetch();
    
    if (!$sale) {
        throw new Exception('Venta no encontrada');
    }
    
    if ($sale['status'] != 'pendiente') {
        throw new Exception('Esta venta ya fue ' . $sale['status']);
    }
    
    $total = $sale['total'];
    $change_amount = 0;
    
    // Validar pago en efectivo
    if ($payment_method === 'Efectivo') {
        if ($monto_recibido < $total) {
            throw new Exception('El monto recibido (L ' . number_format($monto_recibido, 2) . ') es menor al total (L ' . number_format($total, 2) . ')');
        }
        $change_amount = $monto_recibido - $total;
    }
    
    // Actualizar la venta como pagada
    $stmt = $pdo->prepare("
        UPDATE sales 
        SET status = 'pagado', 
            payment_method = ?, 
            payment_date = NOW(),
            cashier_id = ?,
            cashier_name = ?,
            change_amount = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $payment_method,
        $_SESSION['user_id'],
        $_SESSION['fullname'] ?? $_SESSION['username'],
        $change_amount,
        $sale_id
    ]);
    
    $mensaje = 'Pago registrado correctamente.';
    if ($payment_method === 'Efectivo' && $change_amount > 0) {
        $mensaje .= ' Cambio: L ' . number_format($change_amount, 2);
    } elseif ($payment_method === 'Efectivo' && $change_amount == 0) {
        $mensaje .= ' Pago exacto.';
    }
    
    echo json_encode([
        'success' => true,
        'message' => $mensaje,
        'change_amount' => $change_amount
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>