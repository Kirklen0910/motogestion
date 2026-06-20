<?php
require_once '../../config/database.php';
if (!isset($_SESSION['user_id'])) { 
    header('Location: /modules/auth/login.php'); 
    exit; 
}

$id = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? 'ver';

// Obtener información de la venta
$sale = $pdo->prepare("
    SELECT s.*, c.name as client_name, c.address, c.phone, c.email, 
           u.fullname as user_name, u2.fullname as cashier_name
    FROM sales s 
    LEFT JOIN clients c ON s.client_id = c.id 
    LEFT JOIN users u ON s.user_id = u.id 
    LEFT JOIN users u2 ON s.cashier_id = u2.id
    WHERE s.id = ?
");
$sale->execute([$id]);
$sale = $sale->fetch();

if (!$sale) { 
    header('Location: invoices.php'); 
    exit; 
}

// Obtener detalles de la venta
$details = $pdo->prepare("
    SELECT 
        sd.quantity,
        sd.unit_price,
        sd.subtotal,
        p.name as product_name,
        p.code as product_code
    FROM sale_details sd 
    INNER JOIN products p ON sd.product_id = p.id 
    WHERE sd.sale_id = ?
    ORDER BY sd.id ASC
");
$details->execute([$id]);
$details = $details->fetchAll();

$role = $_SESSION['role'] ?? 'seller';
$status = $sale['status'] ?? 'pendiente';
$isPending = $status == 'pendiente';
$canCash = $isPending && in_array($role, ['cashier', 'admin', 'superadmin']);

// Calcular impuestos si no están en la base de datos
$subtotal = $sale['subtotal'] ?? $sale['total'];
$tax = $sale['tax'] ?? 0;
$tax_rate = $sale['tax_rate'] ?? 15;
$total = $sale['total'];

// Si no hay impuesto calculado, calcularlo
if ($tax == 0 && $subtotal > 0) {
    $tax = $subtotal * ($tax_rate / 100);
    $total = $subtotal + $tax;
}

include '../../includes/header.php';
?>

<style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
        color: #555;
        background: white;
    }
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
        border-collapse: collapse;
    }
    .invoice-box table td {
        padding: 8px 5px;
        vertical-align: top;
    }
    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }
    .invoice-box table tr.item td {
        border-bottom: 1px solid #eee;
    }
    .invoice-box table tr.total td {
        border-top: 2px solid #eee;
        font-weight: bold;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        .invoice-box {
            box-shadow: none;
            padding: 0;
        }
        body {
            background: white;
            padding: 0;
            margin: 0;
        }
    }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
        display: inline-block;
    }
    .status-pendiente { background: #f39c12; color: white; }
    .status-pagado { background: #27ae60; color: white; }
    .status-cancelado { background: #e74c3c; color: white; }
    .alert-cambio {
        font-size: 18px;
        font-weight: bold;
        padding: 10px 15px;
        border-radius: 10px;
    }
    .alert-cambio.exacto { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-cambio.falta { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .alert-cambio.cambio { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
</style>

<div class="container mt-4">
    <div class="text-center mb-3 no-print">
        <?php if ($canCash): ?>
            <button onclick="abrirModalCobro()" class="btn btn-success btn-lg">
                <i class="fas fa-money-bill-wave"></i> COBRAR VENTA
            </button>
        <?php endif; ?>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimir Factura
        </button>
        <a href="invoices.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr class="top">
                <td colspan="2">
                    <table width="100%">
                        <tr>
                            <td class="title">
                                <h2>🏍️ MotoGestión Web</h2>
                                <h4>Motomanía</h4>
                            </td>
                            <td class="text-right">
                                <strong>FACTURA</strong><br>
                                N°: <?= htmlspecialchars($sale['invoice_number']) ?><br>
                                Fecha: <?= date('d/m/Y H:i:s', strtotime($sale['sale_date'])) ?><br>
                                Vendedor: <?= htmlspecialchars($sale['user_name']) ?><br>
                                <span class="status-badge status-<?= $status ?>">
                                    <?= $status == 'pendiente' ? '⏳ PENDIENTE' : ($status == 'pagado' ? '✅ PAGADO' : '❌ CANCELADO') ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="2">
                    <table width="100%">
                        <tr>
                            <td>
                                <strong>CLIENTE</strong><br>
                                <?php if ($sale['client_id']): ?>
                                    <?= htmlspecialchars($sale['client_name']) ?><br>
                                    <?= htmlspecialchars($sale['address'] ?? '') ?>
                                <?php else: ?>
                                    <strong>Cliente Genérico</strong><br>
                                    Venta sin identificación
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <strong>EMPRESA</strong><br>
                                MotoGestión Web<br>
                                Tel: +504 0000-0000<br>
                                RTN: 0000-0000-00000
                                <?php if ($status == 'pagado'): ?>
                                    <br><br>
                                    <strong>PAGADO</strong><br>
                                    Método: <?= $sale['payment_method'] ?? 'N/A' ?><br>
                                    Fecha: <?= $sale['payment_date'] ? date('d/m/Y H:i', strtotime($sale['payment_date'])) : 'N/A' ?><br>
                                    Cajero: <?= $sale['cashier_name'] ?? 'N/A' ?>
                                    <?php if ($sale['change_amount'] > 0): ?>
                                        <br><strong>Cambio: L <?= number_format($sale['change_amount'], 2) ?></strong>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td style="width: 10%; text-align: center;">Cant.</td>
                <td style="width: 90%;">Producto</td>
            </tr>
            
            <?php foreach ($details as $d): ?>
                <tr class="item">
                    <td class="text-center"><strong><?= $d['quantity'] ?></strong></td>
                    <td>
                        <strong><?= htmlspecialchars($d['product_name']) ?></strong><br>
                        <small>Código: <?= htmlspecialchars($d['product_code']) ?> | Precio: L <?= number_format($d['unit_price'], 2) ?></small>
                    </td>
                </tr>
            <?php endforeach; ?>
            
            <tr class="total">
                <td colspan="2" class="text-right">
                    <strong>SUBTOTAL: L <?= number_format($subtotal, 2) ?></strong><br>
                    <strong>ISV (<?= $tax_rate ?>%): L <?= number_format($tax, 2) ?></strong><br>
                    <strong>TOTAL: L <?= number_format($total, 2) ?></strong>
                </td>
            </tr>
        </table>
        
        <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #999;">
            <p>¡Gracias por su compra!</p>
        </div>
    </div>
</div>

<!-- MODAL DE COBRO MEJORADO -->
<?php if ($canCash): ?>
<div class="modal fade" id="modalCobro" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-money-bill-wave"></i> Cobrar Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Factura:</strong> <?= htmlspecialchars($sale['invoice_number']) ?></p>
                <p><strong>Cliente:</strong> <?= htmlspecialchars($sale['client_name'] ?? 'Cliente Genérico') ?></p>
                
                <hr>
                
                <div class="row">
                    <div class="col-6">
                        <strong>Subtotal:</strong> L <?= number_format($subtotal, 2) ?>
                    </div>
                    <div class="col-6">
                        <strong>ISV (15%):</strong> L <?= number_format($tax, 2) ?>
                    </div>
                </div>
                
                <div class="text-center my-3">
                    <h3 class="text-success">TOTAL: L <?= number_format($total, 2) ?></h3>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <label class="form-label"><strong>Método de Pago</strong></label>
                    <select id="payment_method" class="form-control" onchange="toggleMontoRecibido()">
                        <option value="Efectivo">💵 Efectivo</option>
                        <option value="Tarjeta Crédito">💳 Tarjeta Crédito</option>
                        <option value="Tarjeta Débito">💳 Tarjeta Débito</option>
                        <option value="Transferencia">🏦 Transferencia</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                
                <div class="mb-3" id="montoRecibidoContainer">
                    <label class="form-label">Monto Recibido <span class="text-danger">*</span></label>
                    <input type="number" id="monto_recibido" class="form-control" placeholder="0.00" step="0.01" min="0">
                    <div id="mensajeValidacion" class="mt-2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="procesarCobro(<?= $sale['id'] ?>)">
                    <i class="fas fa-check"></i> Confirmar Cobro
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMontoRecibido() {
    var method = document.getElementById('payment_method').value;
    var container = document.getElementById('montoRecibidoContainer');
    var input = document.getElementById('monto_recibido');
    var mensaje = document.getElementById('mensajeValidacion');
    
    if (method === 'Efectivo') {
        container.style.display = 'block';
        input.disabled = false;
        input.required = true;
        input.focus();
        validarMonto();
    } else {
        container.style.display = 'none';
        input.disabled = true;
        input.required = false;
        mensaje.innerHTML = '';
    }
}

function validarMonto() {
    var total = <?= $total ?>;
    var monto = parseFloat(document.getElementById('monto_recibido').value) || 0;
    var mensaje = document.getElementById('mensajeValidacion');
    var btn = document.querySelector('#modalCobro .btn-success');
    
    if (monto === 0) {
        mensaje.innerHTML = '';
        btn.disabled = true;
        return;
    }
    
    if (monto < total) {
        var falta = (total - monto).toFixed(2);
        mensaje.innerHTML = '<div class="alert alert-danger alert-cambio falta">❌ FALTAN L ' + falta + ' para completar el pago</div>';
        btn.disabled = true;
    } else if (monto === total) {
        mensaje.innerHTML = '<div class="alert alert-success alert-cambio exacto">✅ PAGO EXACTO - No requiere cambio</div>';
        btn.disabled = false;
    } else if (monto > total) {
        var cambio = (monto - total).toFixed(2);
        mensaje.innerHTML = '<div class="alert alert-info alert-cambio cambio">💰 CAMBIO: L ' + cambio + ' para el cliente</div>';
        btn.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('monto_recibido');
    if (input) {
        input.addEventListener('input', validarMonto);
        input.addEventListener('keyup', validarMonto);
    }
    toggleMontoRecibido();
});

function abrirModalCobro() {
    var modal = new bootstrap.Modal(document.getElementById('modalCobro'));
    modal.show();
    // Resetear campos
    document.getElementById('monto_recibido').value = '';
    document.getElementById('mensajeValidacion').innerHTML = '';
    document.querySelector('#modalCobro .btn-success').disabled = true;
    toggleMontoRecibido();
}

function procesarCobro(saleId) {
    var paymentMethod = document.getElementById('payment_method').value;
    var montoRecibido = parseFloat(document.getElementById('monto_recibido').value) || 0;
    var total = <?= $total ?>;
    
    // Validaciones
    if (paymentMethod === 'Efectivo') {
        if (montoRecibido === 0) {
            alert('⚠️ Ingrese el monto recibido en efectivo');
            return;
        }
        if (montoRecibido < total) {
            alert('❌ El monto recibido es menor al total a pagar');
            return;
        }
    }
    
    // Deshabilitar botón
    document.querySelector('#modalCobro .btn-success').disabled = true;
    document.querySelector('#modalCobro .btn-success').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    
    fetch('process_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            sale_id: saleId,
            payment_method: paymentMethod,
            monto_recibido: montoRecibido,
            total: total
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ Error: ' + data.error);
            document.querySelector('#modalCobro .btn-success').disabled = false;
            document.querySelector('#modalCobro .btn-success').innerHTML = '<i class="fas fa-check"></i> Confirmar Cobro';
        }
    })
    .catch(error => {
        alert('Error de conexión: ' + error);
        document.querySelector('#modalCobro .btn-success').disabled = false;
        document.querySelector('#modalCobro .btn-success').innerHTML = '<i class="fas fa-check"></i> Confirmar Cobro';
    });
}
</script>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>