<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
// Default values
$product = [
    'name' => '', 
    'internal_ref' => '', 
    'sales_price' => 0, 
    'cost_price' => 0, 
    'qty_on_hand' => 0, 
    'uom_id' => 1 // Default ke Unit (ID 1)
];

// 1. Ambil Data UoM (Satuan) untuk Dropdown
// Kita join dengan kategori biar rapi (Weight: kg, gram | Length: m, cm)
$uoms = $pdo->query("SELECT u.*, c.name as cat_name 
                     FROM uoms u 
                     JOIN uom_categories c ON u.category_id = c.id 
                     ORDER BY c.name, u.ratio DESC")->fetchAll();

// 2. Jika Mode Edit, Ambil Data Produk Lama
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}

// 3. Logic Simpan (Insert / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $ref = $_POST['internal_ref'];
    $price = $_POST['sales_price'];
    $cost = $_POST['cost_price'];
    $qty = $_POST['qty_on_hand'];
    $uom_id = $_POST['uom_id']; // Ambil ID UoM dari dropdown

    try {
        if ($id) {
            // Update
            $sql = "UPDATE products SET name=?, internal_ref=?, sales_price=?, cost_price=?, qty_on_hand=?, uom_id=? WHERE id=?";
            $pdo->prepare($sql)->execute([$name, $ref, $price, $cost, $qty, $uom_id, $id]);
        } else {
            // Insert
            $sql = "INSERT INTO products (name, internal_ref, sales_price, cost_price, qty_on_hand, uom_id) VALUES (?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$name, $ref, $price, $cost, $qty, $uom_id]);
        }
        echo "<script>window.location='products.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Gagal menyimpan: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold m-0"><?= $id ? 'Edit Product' : 'New Product' ?></h2>
    <div>
        <a href="products.php" class="btn btn-outline-secondary me-2">Cancel</a>
        <button onclick="document.getElementById('formProduct').submit()" class="btn btn-primary-erp px-4">Save</button>
    </div>
</div>

<div class="card shadow-sm border-0 p-4">
    <form id="formProduct" method="POST">
        <div class="row mb-3">
            <div class="col-md-8">
                <label class="form-label fw-bold">Product Name</label>
                <input type="text" name="name" class="form-control form-control-lg" value="<?= htmlspecialchars($product['name']) ?>" placeholder="e.g. Biji Kopi Arabica" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Internal Reference</label>
                <input type="text" name="internal_ref" class="form-control form-control-lg" value="<?= htmlspecialchars($product['internal_ref']) ?>" placeholder="e.g. MAT-KOPI-001">
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label text-muted fw-bold small">Sales Price</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="sales_price" class="form-control" value="<?= $product['sales_price'] ?>">
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label text-muted fw-bold small">Cost</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="cost_price" class="form-control" value="<?= $product['cost_price'] ?>">
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label text-muted fw-bold small">Quantity On Hand</label>
                <input type="number" name="qty_on_hand" class="form-control" value="<?= $product['qty_on_hand'] ?>">
            </div>
            
            <div class="col-md-3 mb-3">
                <label class="form-label text-muted fw-bold small">Unit of Measure</label>
                <select name="uom_id" class="form-select">
                    <?php 
                    $current_cat = '';
                    foreach($uoms as $u): 
                        // Logic untuk membuat Group (Weight, Length, Unit)
                        if ($current_cat != $u['cat_name']) {
                            if ($current_cat != '') echo '</optgroup>';
                            echo '<optgroup label="'.$u['cat_name'].'">';
                            $current_cat = $u['cat_name'];
                        }
                    ?>
                        <option value="<?= $u['id'] ?>" <?= ($product['uom_id'] == $u['id']) ? 'selected' : '' ?>>
                            <?= $u['name'] ?> (<?= $u['ratio'] ?>)
                        </option>
                    <?php endforeach; ?>
                    </optgroup> </select>
            </div>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>