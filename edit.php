<?php
require_once '../../config/database.php';
if (!isset($_SESSION['user_id'])) { header('Location: /modules/auth/login.php'); exit; }

$id = $_GET['id'] ?? 0;
$product = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$product->execute([$id]);
$product = $product->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}

$suppliers = $pdo->query("SELECT * FROM suppliers")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $cost = $_POST['cost'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $min_stock = $_POST['min_stock'];
    $supplier_id = $_POST['supplier_id'] ?: null;
    $image = $product['image'];
    $stock_anterior = $product['stock'];
    
    // Validar que precio sea mayor que costo
    if ($price <= $cost) {
        $error = 'El precio de venta debe ser mayor que el costo del producto';
    }
    
    // Procesar nueva imagen
    if (empty($error) && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $file_size = $_FILES['image']['size'];
        
        if (!in_array($file_ext, $allowed)) {
            $error = 'Formato no permitido. Use: JPG, PNG, GIF o WEBP';
        } elseif ($file_size > 5242880) {
            $error = 'La imagen no debe superar los 5MB';
        } else {
            $new_filename = uniqid() . '.' . $file_ext;
            $upload_path = '../../uploads/products/';
            $temp_path = $upload_path . 'temp_' . $new_filename;
            $final_path = $upload_path . $new_filename;
            
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $temp_path)) {
                if (resizeImage($temp_path, $final_path, 800, 800, 85)) {
                    if ($image && file_exists('../../' . $image)) {
                        unlink('../../' . $image);
                    }
                    unlink($temp_path);
                    $image = 'uploads/products/' . $new_filename;
                } else {
                    $error = 'Error al procesar la imagen';
                    if (file_exists($temp_path)) unlink($temp_path);
                }
            } else {
                $error = 'Error al subir la imagen';
            }
        }
    }
    
    // Eliminar imagen si se solicitó
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        if ($image && file_exists('../../' . $image)) {
            unlink('../../' . $image);
        }
        $image = '';
    }
    
    if (empty($error)) {
        $stmt = $pdo->prepare("UPDATE products SET code=?, name=?, image=?, cost=?, price=?, stock=?, min_stock=?, supplier_id=? WHERE id=?");
        $stmt->execute([$code, $name, $image, $cost, $price, $stock, $min_stock, $supplier_id, $id]);
        
        // Registrar movimiento si el stock cambió (CORREGIDO)
        if ($stock != $stock_anterior) {
            $diferencia = $stock - $stock_anterior;
            $tipo = ($diferencia > 0) ? 'IN' : 'OUT';
            $cantidad = abs($diferencia);
            
            $stmt_movement = $pdo->prepare("INSERT INTO inventory_movements (product_id, type, quantity, reference, user_id) VALUES (?, ?, ?, 'EDICION_PRODUCTO', ?)");
            $stmt_movement->execute([$id, $tipo, $cantidad, $_SESSION['user_id']]);
        }
        
        $ganancia = $price - $cost;
        registerLog($pdo, 'UPDATE', 'products', $id, "Producto actualizado: $name - Costo: L $cost - Precio: L $price - Ganancia: L $ganancia");
        
        header('Location: index.php');
        exit;
    }
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h4><i class="fas fa-edit"></i> Editar Producto</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Código *</label>
                                    <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($product['code']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Nombre *</label>
                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Costo (Lps) *</label>
                                    <input type="number" step="0.01" name="cost" class="form-control" value="<?= $product['cost'] ?>" required id="cost" onchange="calcularGanancia()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Precio Venta (Lps) *</label>
                                    <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required id="price" onchange="calcularGanancia()">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" id="gananciaPreview">
                                    <?php 
                                    $gananciaActual = $product['price'] - $product['cost'];
                                    $margenActual = $product['price'] > 0 ? ($gananciaActual / $product['price']) * 100 : 0;
                                    if ($gananciaActual > 0) {
                                        echo '<i class="fas fa-chart-line"></i> Ganancia por unidad: <strong>L ' . number_format($gananciaActual, 2) . '</strong> | Margen: <strong>' . number_format($margenActual, 1) . '%</strong>';
                                    } elseif ($gananciaActual < 0) {
                                        echo '<i class="fas fa-exclamation-triangle"></i> <strong>Pérdida por unidad: L ' . number_format(abs($gananciaActual), 2) . '</strong>';
                                    } else {
                                        echo '<i class="fas fa-chart-line"></i> Ganancia por unidad: <strong>L 0.00</strong>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Stock *</label>
                                    <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Stock mínimo</label>
                                    <input type="number" name="min_stock" class="form-control" value="<?= $product['min_stock'] ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label>Proveedor</label>
                                    <select name="supplier_id" class="form-control">
                                        <option value="">-- Ninguno --</option>
                                        <?php foreach($suppliers as $s): ?>
                                            <option value="<?= $s['id'] ?>" <?= $s['id'] == $product['supplier_id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label>Imagen Actual</label>
                            <?php if ($product['image'] && file_exists('../../' . $product['image'])): ?>
                                <div class="border rounded p-2 text-center">
                                    <img src="/<?= $product['image'] ?>" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                    <div class="mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
                                            <label class="form-check-label text-danger" for="remove_image">
                                                <i class="fas fa-trash"></i> Eliminar imagen actual
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-secondary">No hay imagen para este producto</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label>Cambiar Imagen</label>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImage(this)">
                            <small class="text-muted">
                                Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB. La imagen se redimensionará automáticamente a 800x800 px.
                            </small>
                            <div class="mt-2" id="imagePreview" style="display: none;">
                                <img id="preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Producto
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calcularGanancia() {
    var cost = parseFloat(document.getElementById('cost').value) || 0;
    var price = parseFloat(document.getElementById('price').value) || 0;
    var ganancia = price - cost;
    var margen = price > 0 ? (ganancia / price) * 100 : 0;
    
    var alertDiv = document.getElementById('gananciaPreview');
    if (ganancia > 0) {
        alertDiv.className = 'alert alert-success';
        alertDiv.innerHTML = '<i class="fas fa-chart-line"></i> Ganancia por unidad: <strong>L ' + ganancia.toFixed(2) + '</strong> | Margen: <strong>' + margen.toFixed(1) + '%</strong>';
    } else if (ganancia < 0) {
        alertDiv.className = 'alert alert-danger';
        alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <strong>Pérdida por unidad: L ' + Math.abs(ganancia).toFixed(2) + '</strong> - El precio de venta es menor que el costo';
    } else {
        alertDiv.className = 'alert alert-warning';
        alertDiv.innerHTML = '<i class="fas fa-chart-line"></i> Ganancia por unidad: <strong>L 0.00</strong> - Sin margen de ganancia';
    }
}

function previewImage(input) {
    var preview = document.getElementById('imagePreview');
    var img = document.getElementById('preview');
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include '../../includes/footer.php'; ?>