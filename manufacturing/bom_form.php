<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

// 1. AMBIL DATA PRODUK (LENGKAP DENGAN COST & UOM RATIO)
// Kita butuh rasio UoM asli produk untuk menghitung konversi harga
$sqlProd = "SELECT p.*, u.ratio as uom_ratio, u.name as uom_name 
            FROM products p 
            LEFT JOIN uoms u ON p.uom_id = u.id 
            ORDER BY p.name ASC";
$products = $pdo->query($sqlProd)->fetchAll();

// 2. AMBIL DATA UOM (LENGKAP DENGAN RATIO)
$uoms = $pdo->query("SELECT u.*, c.name as cat_name 
                     FROM uoms u 
                     JOIN uom_categories c ON u.category_id = c.id 
                     ORDER BY c.name, u.ratio DESC")->fetchAll();

$id = $_GET['id'] ?? null;
$bom = ['product_id'=>'', 'reference'=>'', 'qty'=>1, 'type'=>'manufacturing'];
$lines = [];

// 3. JIKA EDIT, AMBIL DATA LAMA
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM boms WHERE id=?");
    $stmt->execute([$id]);
    $bom = $stmt->fetch();

    // Join ke produk & uom untuk mendapatkan data real-time saat edit
    $sqlLines = "SELECT bl.*, p.cost_price as base_cost, p_uom.ratio as base_ratio 
                 FROM bom_lines bl
                 JOIN products p ON bl.component_id = p.id
                 LEFT JOIN uoms p_uom ON p.uom_id = p_uom.id
                 WHERE bl.bom_id = ?";
    $stmtLine = $pdo->prepare($sqlLines);
    $stmtLine->execute([$id]);
    $lines = $stmtLine->fetchAll();
}

// 4. LOGIC SIMPAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        if ($id) {
            $sql = "UPDATE boms SET product_id=?, reference=?, qty=?, type=? WHERE id=?";
            $pdo->prepare($sql)->execute([$_POST['product_id'], $_POST['reference'], $_POST['qty'], $_POST['type'], $id]);
            $pdo->prepare("DELETE FROM bom_lines WHERE bom_id=?")->execute([$id]);
            $bom_id = $id;
        } else {
            $sql = "INSERT INTO boms (product_id, reference, qty, type) VALUES (?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$_POST['product_id'], $_POST['reference'], $_POST['qty'], $_POST['type']]);
            $bom_id = $pdo->lastInsertId();
        }

        if (isset($_POST['comp_id'])) {
            $stmtInsert = $pdo->prepare("INSERT INTO bom_lines (bom_id, component_id, qty, uom_id) VALUES (?, ?, ?, ?)");
            for ($i = 0; $i < count($_POST['comp_id']); $i++) {
                $comp = $_POST['comp_id'][$i];
                $c_qty = $_POST['comp_qty'][$i];
                $uom_id = $_POST['uom_id'][$i];
                if($comp) {
                    $stmtInsert->execute([$bom_id, $comp, $c_qty, $uom_id]);
                }
            }
        }

        $pdo->commit();
        echo "<script>window.location='bom.php';</script>";

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold m-0"><?= $id ? 'Edit BOM' : 'Create Bill of Materials' ?></h2>
    <div>
        <a href="bom.php" class="btn btn-outline-secondary me-2">Discard</a>
        <button onclick="document.getElementById('bomForm').submit()" class="btn btn-primary-erp px-4">Save</button>
    </div>
</div>

<form id="bomForm" method="POST">
    <div class="card shadow-sm border-0 p-4 mb-4">
        <h5 class="fw-bold mb-3 border-bottom pb-2">BOM Information</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Product</label>
                    <select name="product_id" class="form-select">
                        <option value="">Select Product...</option>
                        <?php foreach($products as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $bom['product_id'] == $p['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Quantity Produced</label>
                    <input type="number" step="0.01" name="qty" class="form-control" value="<?= $bom['qty'] ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Reference</label>
                    <input type="text" name="reference" class="form-control" value="<?= $bom['reference'] ?>" placeholder="e.g. BOM/KOPI/001">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">BOM Type</label>
                    <div class="mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" value="manufacturing" <?= $bom['type']=='manufacturing'?'checked':'' ?>>
                            <label class="form-check-label">Manufacturing Order</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" value="kit" <?= $bom['type']=='kit'?'checked':'' ?>>
                            <label class="form-check-label">Kit / Phantom</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 p-4">
        <h5 class="fw-bold mb-3">Components</h5>
        <table class="table table-bordered align-middle" id="compTable">
            <thead class="table-light">
                <tr>
                    <th width="30%">Component</th>
                    <th width="15%">Quantity</th>
                    <th width="15%">UoM</th>
                    <th width="15%" class="text-end">Component Cost</th>
                    <th width="15%" class="text-end">Subtotal</th>
                    <th width="5%" class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($id && count($lines) > 0): ?>
                    <?php foreach($lines as $line): ?>
                        <tr class="item-row">
                            <td>
                                <select name="comp_id[]" class="form-select comp-select" onchange="calculateRow(this)">
                                    <?php foreach($products as $p): ?>
                                        <option value="<?= $p['id'] ?>" 
                                            data-cost="<?= $p['cost_price'] ?>" 
                                            data-base-ratio="<?= $p['uom_ratio'] ?>"
                                            <?= $line['component_id'] == $p['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.001" name="comp_qty[]" class="form-control qty-input" value="<?= $line['qty'] ?>" oninput="calculateRow(this)">
                            </td>
                            <td>
                                <select name="uom_id[]" class="form-select form-select-sm uom-select" onchange="calculateRow(this)">
                                    <?php foreach($uoms as $u): ?>
                                        <option value="<?= $u['id'] ?>" 
                                            data-ratio="<?= $u['ratio'] ?>"
                                            <?= ($line['uom_id'] == $u['id']) ? 'selected' : '' ?>>
                                            <?= $u['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="text-end">
                                <input type="text" class="form-control-plaintext text-end unit-cost-display" readonly value="0">
                            </td>
                            <td class="text-end">
                                <input type="text" class="form-control-plaintext text-end subtotal-display fw-bold" readonly value="0">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm text-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="table-light fw-bold">
                    <td colspan="4" class="text-end">Total BOM Cost:</td>
                    <td class="text-end" id="grandTotal">Rp 0</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        
        <button type="button" class="btn btn-sm text-primary fw-bold" onclick="addRow()">
            <i class="bi bi-plus-circle"></i> Add a line
        </button>
    </div>
</form>

<script>
// Data UOM dalam bentuk String HTML untuk baris baru
const uomOptions = `<?php 
    $current_cat = '';
    foreach($uoms as $u) {
        if ($current_cat != $u['cat_name']) {
            if ($current_cat != '') echo '</optgroup>';
            echo '<optgroup label="'.$u['cat_name'].'">';
            $current_cat = $u['cat_name'];
        }
        echo '<option value="'.$u['id'].'" data-ratio="'.$u['ratio'].'">'.$u['name'].'</option>';
    }
    echo '</optgroup>';
?>`;

const productOptions = `<?php 
    echo '<option value="">Select Component...</option>';
    foreach($products as $p) {
        echo '<option value="'.$p['id'].'" data-cost="'.$p['cost_price'].'" data-base-ratio="'.$p['uom_ratio'].'">'.htmlspecialchars($p['name']).'</option>';
    }
?>`;

// 1. Fungsi Tambah Baris
function addRow() {
    let table = document.getElementById('compTable').querySelector('tbody');
    let tr = document.createElement('tr');
    tr.className = 'item-row';
    
    tr.innerHTML = `
        <td><select name="comp_id[]" class="form-select comp-select" onchange="calculateRow(this)">${productOptions}</select></td>
        <td><input type="number" step="0.001" name="comp_qty[]" class="form-control qty-input" value="1" oninput="calculateRow(this)"></td>
        <td><select name="uom_id[]" class="form-select form-select-sm uom-select" onchange="calculateRow(this)">${uomOptions}</select></td>
        <td class="text-end"><input type="text" class="form-control-plaintext text-end unit-cost-display" readonly value="0"></td>
        <td class="text-end"><input type="text" class="form-control-plaintext text-end subtotal-display fw-bold" readonly value="0"></td>
        <td class="text-center"><button type="button" class="btn btn-sm text-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
    `;
    
    table.appendChild(tr);
}

// 2. Fungsi Hapus Baris
function removeRow(btn) {
    btn.closest('tr').remove();
    calculateGrandTotal();
}

// 3. FUNGSI UTAMA: MENGHITUNG BIAYA (CORE LOGIC ODOO)
function calculateRow(element) {
    let row = element.closest('tr');
    
    // Ambil Elemen
    let productSelect = row.querySelector('.comp-select');
    let qtyInput = row.querySelector('.qty-input');
    let uomSelect = row.querySelector('.uom-select');
    let costDisplay = row.querySelector('.unit-cost-display');
    let subtotalDisplay = row.querySelector('.subtotal-display');

    // Ambil Data dari Atribut Data (Dataset)
    let selectedProd = productSelect.options[productSelect.selectedIndex];
    let baseCost = parseFloat(selectedProd.dataset.cost) || 0;
    let baseRatio = parseFloat(selectedProd.dataset.baseRatio) || 1; // Ratio asli produk (misal kg = 1)
    
    let selectedUom = uomSelect.options[uomSelect.selectedIndex];
    let lineRatio = parseFloat(selectedUom.dataset.ratio) || 1; // Ratio di baris ini (misal gram = 0.001)
    
    let qty = parseFloat(qtyInput.value) || 0;

    // RUMUS KONVERSI HARGA:
    // Harga Baris = (Harga Dasar / Ratio Dasar) * Ratio Baris
    // Contoh: Gula 20.000/kg. Baris pakai Gram.
    // (20.000 / 1) * 0.001 = Rp 20 (per gram)
    let lineUnitCost = (baseCost / baseRatio) * lineRatio;
    
    // Hitung Subtotal
    let subtotal = lineUnitCost * qty;

    // Tampilkan (Format Rupiah)
    costDisplay.value = formatRupiah(lineUnitCost);
    subtotalDisplay.value = formatRupiah(subtotal);
    
    // Simpan nilai asli di atribut agar mudah dijumlahkan
    subtotalDisplay.dataset.value = subtotal;

    calculateGrandTotal();
}

// 4. Hitung Total Keseluruhan
function calculateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal-display').forEach(input => {
        total += parseFloat(input.dataset.value) || 0;
    });
    document.getElementById('grandTotal').innerText = formatRupiah(total);
}

// Helper: Format Rupiah JS
function formatRupiah(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 2});
}

// Hitung ulang semua baris saat halaman dimuat (untuk Mode Edit)
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.item-row').forEach(row => {
        // Trigger perhitungan pada salah satu elemen di baris
        calculateRow(row.querySelector('.comp-select'));
    });
});
</script>

<?php require_once '../includes/footer.php'; ?><?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

// 1. AMBIL DATA PRODUK (LENGKAP DENGAN COST & UOM RATIO)
// Kita butuh rasio UoM asli produk untuk menghitung konversi harga
$sqlProd = "SELECT p.*, u.ratio as uom_ratio, u.name as uom_name 
            FROM products p 
            LEFT JOIN uoms u ON p.uom_id = u.id 
            ORDER BY p.name ASC";
$products = $pdo->query($sqlProd)->fetchAll();

// 2. AMBIL DATA UOM (LENGKAP DENGAN RATIO)
$uoms = $pdo->query("SELECT u.*, c.name as cat_name 
                     FROM uoms u 
                     JOIN uom_categories c ON u.category_id = c.id 
                     ORDER BY c.name, u.ratio DESC")->fetchAll();

$id = $_GET['id'] ?? null;
$bom = ['product_id'=>'', 'reference'=>'', 'qty'=>1, 'type'=>'manufacturing'];
$lines = [];

// 3. JIKA EDIT, AMBIL DATA LAMA
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM boms WHERE id=?");
    $stmt->execute([$id]);
    $bom = $stmt->fetch();

    // Join ke produk & uom untuk mendapatkan data real-time saat edit
    $sqlLines = "SELECT bl.*, p.cost_price as base_cost, p_uom.ratio as base_ratio 
                 FROM bom_lines bl
                 JOIN products p ON bl.component_id = p.id
                 LEFT JOIN uoms p_uom ON p.uom_id = p_uom.id
                 WHERE bl.bom_id = ?";
    $stmtLine = $pdo->prepare($sqlLines);
    $stmtLine->execute([$id]);
    $lines = $stmtLine->fetchAll();
}

// 4. LOGIC SIMPAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        if ($id) {
            $sql = "UPDATE boms SET product_id=?, reference=?, qty=?, type=? WHERE id=?";
            $pdo->prepare($sql)->execute([$_POST['product_id'], $_POST['reference'], $_POST['qty'], $_POST['type'], $id]);
            $pdo->prepare("DELETE FROM bom_lines WHERE bom_id=?")->execute([$id]);
            $bom_id = $id;
        } else {
            $sql = "INSERT INTO boms (product_id, reference, qty, type) VALUES (?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$_POST['product_id'], $_POST['reference'], $_POST['qty'], $_POST['type']]);
            $bom_id = $pdo->lastInsertId();
        }

        if (isset($_POST['comp_id'])) {
            $stmtInsert = $pdo->prepare("INSERT INTO bom_lines (bom_id, component_id, qty, uom_id) VALUES (?, ?, ?, ?)");
            for ($i = 0; $i < count($_POST['comp_id']); $i++) {
                $comp = $_POST['comp_id'][$i];
                $c_qty = $_POST['comp_qty'][$i];
                $uom_id = $_POST['uom_id'][$i];
                if($comp) {
                    $stmtInsert->execute([$bom_id, $comp, $c_qty, $uom_id]);
                }
            }
        }

        $pdo->commit();
        echo "<script>window.location='bom.php';</script>";

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold m-0"><?= $id ? 'Edit BOM' : 'Create Bill of Materials' ?></h2>
    <div>
        <a href="bom.php" class="btn btn-outline-secondary me-2">Discard</a>
        <button onclick="document.getElementById('bomForm').submit()" class="btn btn-primary-erp px-4">Save</button>
    </div>
</div>

<form id="bomForm" method="POST">
    <div class="card shadow-sm border-0 p-4 mb-4">
        <h5 class="fw-bold mb-3 border-bottom pb-2">BOM Information</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Product</label>
                    <select name="product_id" class="form-select">
                        <option value="">Select Product...</option>
                        <?php foreach($products as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $bom['product_id'] == $p['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Quantity Produced</label>
                    <input type="number" step="0.01" name="qty" class="form-control" value="<?= $bom['qty'] ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Reference</label>
                    <input type="text" name="reference" class="form-control" value="<?= $bom['reference'] ?>" placeholder="e.g. BOM/KOPI/001">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">BOM Type</label>
                    <div class="mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" value="manufacturing" <?= $bom['type']=='manufacturing'?'checked':'' ?>>
                            <label class="form-check-label">Manufacturing Order</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" value="kit" <?= $bom['type']=='kit'?'checked':'' ?>>
                            <label class="form-check-label">Kit / Phantom</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 p-4">
        <h5 class="fw-bold mb-3">Components</h5>
        <table class="table table-bordered align-middle" id="compTable">
            <thead class="table-light">
                <tr>
                    <th width="30%">Component</th>
                    <th width="15%">Quantity</th>
                    <th width="15%">UoM</th>
                    <th width="15%" class="text-end">Component Cost</th>
                    <th width="15%" class="text-end">Subtotal</th>
                    <th width="5%" class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($id && count($lines) > 0): ?>
                    <?php foreach($lines as $line): ?>
                        <tr class="item-row">
                            <td>
                                <select name="comp_id[]" class="form-select comp-select" onchange="calculateRow(this)">
                                    <?php foreach($products as $p): ?>
                                        <option value="<?= $p['id'] ?>" 
                                            data-cost="<?= $p['cost_price'] ?>" 
                                            data-base-ratio="<?= $p['uom_ratio'] ?>"
                                            <?= $line['component_id'] == $p['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.001" name="comp_qty[]" class="form-control qty-input" value="<?= $line['qty'] ?>" oninput="calculateRow(this)">
                            </td>
                            <td>
                                <select name="uom_id[]" class="form-select form-select-sm uom-select" onchange="calculateRow(this)">
                                    <?php foreach($uoms as $u): ?>
                                        <option value="<?= $u['id'] ?>" 
                                            data-ratio="<?= $u['ratio'] ?>"
                                            <?= ($line['uom_id'] == $u['id']) ? 'selected' : '' ?>>
                                            <?= $u['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="text-end">
                                <input type="text" class="form-control-plaintext text-end unit-cost-display" readonly value="0">
                            </td>
                            <td class="text-end">
                                <input type="text" class="form-control-plaintext text-end subtotal-display fw-bold" readonly value="0">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm text-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="table-light fw-bold">
                    <td colspan="4" class="text-end">Total BOM Cost:</td>
                    <td class="text-end" id="grandTotal">Rp 0</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        
        <button type="button" class="btn btn-sm text-primary fw-bold" onclick="addRow()">
            <i class="bi bi-plus-circle"></i> Add a line
        </button>
    </div>
</form>

<script>
// Data UOM dalam bentuk String HTML untuk baris baru
const uomOptions = `<?php 
    $current_cat = '';
    foreach($uoms as $u) {
        if ($current_cat != $u['cat_name']) {
            if ($current_cat != '') echo '</optgroup>';
            echo '<optgroup label="'.$u['cat_name'].'">';
            $current_cat = $u['cat_name'];
        }
        echo '<option value="'.$u['id'].'" data-ratio="'.$u['ratio'].'">'.$u['name'].'</option>';
    }
    echo '</optgroup>';
?>`;

const productOptions = `<?php 
    echo '<option value="">Select Component...</option>';
    foreach($products as $p) {
        echo '<option value="'.$p['id'].'" data-cost="'.$p['cost_price'].'" data-base-ratio="'.$p['uom_ratio'].'">'.htmlspecialchars($p['name']).'</option>';
    }
?>`;

// 1. Fungsi Tambah Baris
function addRow() {
    let table = document.getElementById('compTable').querySelector('tbody');
    let tr = document.createElement('tr');
    tr.className = 'item-row';
    
    tr.innerHTML = `
        <td><select name="comp_id[]" class="form-select comp-select" onchange="calculateRow(this)">${productOptions}</select></td>
        <td><input type="number" step="0.001" name="comp_qty[]" class="form-control qty-input" value="1" oninput="calculateRow(this)"></td>
        <td><select name="uom_id[]" class="form-select form-select-sm uom-select" onchange="calculateRow(this)">${uomOptions}</select></td>
        <td class="text-end"><input type="text" class="form-control-plaintext text-end unit-cost-display" readonly value="0"></td>
        <td class="text-end"><input type="text" class="form-control-plaintext text-end subtotal-display fw-bold" readonly value="0"></td>
        <td class="text-center"><button type="button" class="btn btn-sm text-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
    `;
    
    table.appendChild(tr);
}

// 2. Fungsi Hapus Baris
function removeRow(btn) {
    btn.closest('tr').remove();
    calculateGrandTotal();
}

// 3. FUNGSI UTAMA: MENGHITUNG BIAYA (CORE LOGIC ODOO)
function calculateRow(element) {
    let row = element.closest('tr');
    
    // Ambil Elemen
    let productSelect = row.querySelector('.comp-select');
    let qtyInput = row.querySelector('.qty-input');
    let uomSelect = row.querySelector('.uom-select');
    let costDisplay = row.querySelector('.unit-cost-display');
    let subtotalDisplay = row.querySelector('.subtotal-display');

    // Ambil Data dari Atribut Data (Dataset)
    let selectedProd = productSelect.options[productSelect.selectedIndex];
    let baseCost = parseFloat(selectedProd.dataset.cost) || 0;
    let baseRatio = parseFloat(selectedProd.dataset.baseRatio) || 1; // Ratio asli produk (misal kg = 1)
    
    let selectedUom = uomSelect.options[uomSelect.selectedIndex];
    let lineRatio = parseFloat(selectedUom.dataset.ratio) || 1; // Ratio di baris ini (misal gram = 0.001)
    
    let qty = parseFloat(qtyInput.value) || 0;

    // RUMUS KONVERSI HARGA:
    // Harga Baris = (Harga Dasar / Ratio Dasar) * Ratio Baris
    // Contoh: Gula 20.000/kg. Baris pakai Gram.
    // (20.000 / 1) * 0.001 = Rp 20 (per gram)
    let lineUnitCost = (baseCost / baseRatio) * lineRatio;
    
    // Hitung Subtotal
    let subtotal = lineUnitCost * qty;

    // Tampilkan (Format Rupiah)
    costDisplay.value = formatRupiah(lineUnitCost);
    subtotalDisplay.value = formatRupiah(subtotal);
    
    // Simpan nilai asli di atribut agar mudah dijumlahkan
    subtotalDisplay.dataset.value = subtotal;

    calculateGrandTotal();
}

// 4. Hitung Total Keseluruhan
function calculateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal-display').forEach(input => {
        total += parseFloat(input.dataset.value) || 0;
    });
    document.getElementById('grandTotal').innerText = formatRupiah(total);
}

// Helper: Format Rupiah JS
function formatRupiah(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 2});
}

// Hitung ulang semua baris saat halaman dimuat (untuk Mode Edit)
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.item-row').forEach(row => {
        // Trigger perhitungan pada salah satu elemen di baris
        calculateRow(row.querySelector('.comp-select'));
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>