<?php
require_once '../../config/database.php';
if (!isset($_SESSION['user_id'])) { die('No autorizado'); }

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

$sales = $pdo->prepare("
    SELECT s.*, c.name as client_name, u.fullname as user_name 
    FROM sales s 
    LEFT JOIN clients c ON s.client_id = c.id 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE DATE(s.sale_date) BETWEEN ? AND ?
    ORDER BY s.sale_date DESC
");
$sales->execute([$start_date, $end_date]);
$sales = $sales->fetchAll();

$total = array_sum(array_column($sales, 'total'));

// Configurar cabeceras para descargar HTML como PDF (simulado por ahora)
header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><title>Reporte de Ventas</title>';
echo '<style>';
echo 'body { font-family: Arial, sans-serif; margin: 20px; }';
echo 'table { width: 100%; border-collapse: collapse; }';
echo 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
echo 'th { background-color: #f2f2f2; }';
echo '.total { margin-top: 20px; font-size: 18px; font-weight: bold; }';
echo '</style></head><body>';
echo '<h2>Motomanía - Reporte de Ventas</h2>';
echo '<p>Período: ' . $start_date . ' al ' . $end_date . '</p>';
echo '<table>';
echo '<tr><th>Factura</th><th>Cliente</th><th>Fecha</th><th>Vendedor</th><th>Total</th></tr>';
foreach ($sales as $s) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($s['invoice_number']) . '</td>';
    echo '<td>' . htmlspecialchars($s['client_name'] ?? 'Ocasional') . '</td>';
    echo '<td>' . $s['sale_date'] . '</td>';
    echo '<td>' . htmlspecialchars($s['user_name']) . '</td>';
    echo '<td>L ' . number_format($s['total'],2) . '</td>';
    echo '</tr>';
}
echo '</table>';
echo '<p class="total">Total General: L ' . number_format($total,2) . '</p>';
echo '<p>Generado el: ' . date('d/m/Y H:i:s') . '</p>';
echo '</body></html>';
?>