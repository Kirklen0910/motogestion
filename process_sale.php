<?php
require_once '../../config/database.php';
session_start();
if (!isset($_SESSION['user_id'])) { 
    echo json_encode(['success'=>false,'error'=>'No autorizado']); 
    exit; 
}

$input = json_decode(file_get_contents('php://input'), true);
$client_id = $input['client_id'];
$cart = $input['cart'];
$user_id = $_SESSION['user_id'];

// Si es cliente genérico (ID 0), usar NULL en la base de datos
if ($client_id == 0) {
    $client_id = null;
}

// Tasa de impuesto (15% Honduras)
$tax_rate = 15.00;

try {
    $pdo->beginTransaction();
    
    $subtotal = 0;
    $total_cost = 0;
    
    // PASO 1: VERIFICAR STOCK DE TODOS LOS PRODUCTOS
    foreach ($cart as $index => $item) {
        $stmt = $pdo->prepare("SELECT id, stock, name, cost, price FROM products WHERE id = ? FOR UPDATE");
        $stmt->execute([$item['product_id']]);
        $product = $stmt->fetch();
        
        if (!$product) {
            throw new Exception("Producto no encontrado: {$item['name']}");
        }
        
        if ($product['stock'] < $item['qty']) {
            throw new Exception("Stock insuficiente para '{$product['name']}'. Disponible: {$product['stock']}, Solicitado: {$item['qty']}");
        }
        
        // Guardar datos del producto en el carrito
        $cart[$index]['cost'] = $product['cost'];
        $cart[$index]['price'] = $product['price'];
        $cart[$index]['subtotal'] = $item['qty'] * $product['price'];
        $cart[$index]['subtotal_cost'] = $item['qty'] * $product['cost'];
        
        $subtotal += $cart[$index]['subtotal'];
        $total_cost += $cart[$index]['subtotal_cost'];
    }
    
    // Calcular impuesto y total
    $tax = $subtotal * ($tax_rate / 100);
    $total = $subtotal + $tax;
    $total_profit = $subtotal - $total_cost;
    $invoice = 'INV-' . date('Ymd') . '-' . rand(100, 999);

    // PASO 2: INSERTAR VENTA CON IMPUESTOS
    $stmt = $pdo->prepare("INSERT INTO sales (invoice_number, client_id, subtotal, tax, tax_rate, total, total_cost, total_profit, user_id, status, sale_date) VALUES (?,?,?,?,?,?,?,?,?, 'pendiente', NOW())");
    $stmt->execute([$invoice, $client_id, $subtotal, $tax, $tax_rate, $total, $total_cost, $total_profit, $user_id]);
    $sale_id = $pdo->lastInsertId();

    // PASO 3: INSERTAR DETALLES Y ACTUALIZAR STOCK
    foreach ($cart as $item) {
        // Insertar detalle de venta
        $stmt = $pdo->prepare("INSERT INTO sale_details (sale_id, product_id, quantity, unit_price, unit_cost, subtotal, subtotal_cost) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([
            $sale_id, 
            $item['product_id'], 
            $item['qty'], 
            $item['price'], 
            $item['cost'], 
            $item['subtotal'],
            $item['subtotal_cost']
        ]);
        
        // ACTUALIZAR STOCK - RESTAR LA CANTIDAD VENDIDA
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $result = $stmt->execute([$item['qty'], $item['product_id']]);
        
        if (!$result) {
            throw new Exception("Error al actualizar stock del producto: {$item['name']}");
        }
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success'=>true, 
        'invoice'=>$invoice,
        'subtotal'=>$subtotal,
        'tax'=>$tax,
        'total'=>$total,
        'message'=>'Venta creada. Esperando pago del cajero.'
    ]);
    
} catch(Exception $e) {
    $pdo->rollBack();
    error_log("ERROR EN VENTA: " . $e->getMessage());
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
?>