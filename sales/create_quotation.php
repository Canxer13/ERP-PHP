<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

// Ambil Data Produk untuk Dropdown
$products = $pdo->query("SELECT * FROM products")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. Simpan Header
        $so_number = "SO/" . date('Y') . "/" . rand(1000, 9999);
        $sql = "INSERT INTO sales_orders (order_number, customer_name, order_date, total_amount, status) VALUES (?, ?, ?, ?, 'Draft')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$so_number, $_POST['customer'], date('Y-m-d'), $_POST['grand_total']]);
        $order_id = $pdo->lastInsertId();

        // 2. Simpan Lines (Looping)
        if (isset($_POST['product_id'])) {
            $stmtLine = $pdo->prepare("INSERT INTO sales_order_lines (order_id, product_id, qty, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
            
            for ($i = 0; $i < count($_POST['product_id']); $i++) {
                $pid = $_POST['product_id'][$i];
                $qty = $_POST['qty'][$i];
                $price = $_POST['price'][$i];
                $sub = $qty * $price;
                $stmtLine->execute([$order_id, $pid, $qty, $price, $sub]);
            }
        }

        $pdo->commit();
        echo "<script>alert('Order Berhasil Disimpan!'); window.location='orders.php';</script>";

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold">New Quotation</h2>
    <button onclick="document.getElementById('soForm').submit()" class="btn btn-primary-erp">Confirm</button>
</div>

<form id="soForm" method="POST">
    <div class="card shadow-sm border-0 p-4 mb-4">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label fw-bold">Customer Name</label>
                <input type="text" name="customer" class="form-control" placeholder="Putra Siregar" required>
            </div>
            <div class="col-md-6 text-end">
                <h3 class="fw-bold text-muted">New</h3>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 p-4">
        <table class="table table-bordered" id="linesTable">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th width="15%">Qty</th>
                    <th width="20%">Unit Price</th>
                    <th width="5%">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="product_id[]" class="form-select" onchange="updatePrice(this)">
                            <option value="">Select Product...</option>
                            <?php foreach($products as $p): ?>
                                <option value="<?= $p['id'] ?>" data-price="<?= $p['sales_price'] ?>">
                                    <?= $p['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="number" name="qty[]" class="form-control" value="1"></td>
                    <td><input type="number" name="price[]" class="form-control" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">X</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-link" onclick="addLine()">Add Product</button>
        
        <input type="hidden" name="grand_total" value="0"> 
    </div>
</form>

<script>
// Simple JS untuk update harga otomatis
function updatePrice(select) {
    let price = select.options[select.selectedIndex].getAttribute('data-price');
    let row = select.closest('tr');
    row.querySelector('input[name="price[]"]').value = price;
}

function addLine() {
    let table = document.getElementById('linesTable').querySelector('tbody');
    let newRow = table.rows[0].cloneNode(true);
    newRow.querySelector('input[name="qty[]"]').value = 1;
    newRow.querySelector('input[name="price[]"]').value = '';
    table.appendChild(newRow);
}
</script>

<?php require_once '../includes/footer.php'; ?>