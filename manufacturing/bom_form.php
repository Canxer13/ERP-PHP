<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

// 1. Ambil Data Referensi (Produk & UoM)
$products = $pdo->query("SELECT * FROM products ORDER BY name ASC")->fetchAll();
$uoms = $pdo->query("SELECT u.*, c.name as cat_name FROM uoms u JOIN uom_categories c ON u.category_id = c.id ORDER BY c.name, u.ratio DESC")->fetchAll();

$id = $_GET['id'] ?? null;
$bom = ['product_id'=>'', 'reference'=>'', 'qty'=>1, 'type'=>'manufacturing'];
$lines = [];

// 2. Jika Mode Edit, Ambil Data BOM Lama
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM boms WHERE id=?");
    $stmt->execute([$id]);
    $bom = $stmt->fetch();

    $stmtLine = $pdo->prepare("SELECT * FROM bom_lines WHERE bom_id=?");
    $stmtLine->execute([$id]);
    $lines = $stmtLine->fetchAll();
}

// 3. Logic Simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // A. Simpan Header BOM
        if ($id) {
            $sql = "UPDATE boms SET product_id=?, reference=?, qty=?, type=? WHERE id=?";
            $pdo->prepare($sql)->execute([$_POST['product_id'], $_POST['reference'], $_POST['qty'], $_POST['type'], $id]);
            
            // Hapus baris lama (Reset detail)
            $pdo->prepare("DELETE FROM bom_lines WHERE bom_id=?")->execute([$id]);
            $bom_id = $id;
        } else {
            $sql = "INSERT INTO boms (product_id, reference, qty, type) VALUES (?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$_POST['product_id'], $_POST['reference'], $_POST['qty'], $_POST['type']]);
            $bom_id = $pdo->lastInsertId();
        }

        // B. Simpan Komponen (Detail Lines)
        if (isset($_POST['comp_id'])) {
            $stmtInsert = $pdo->prepare("INSERT INTO bom_lines (bom_id, component_id, qty, uom_id) VALUES (?, ?, ?, ?)");
            
            for ($i = 0; $i < count($_POST['comp_id']); $i++) {
                $comp = $_POST['comp_id'][$i];
                $c_qty = $_POST['comp_qty'][$i];
                $uom_id = $_POST['uom_id'][$i]; // Ambil ID UoM dari dropdown
                
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
                                <?= $p['name'] ?>
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
                    <th>Component</th>
                    <th width="15%">Quantity</th>
                    <th width="20%">Unit of Measure</th>
                    <th width="5%" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($id && count($lines) > 0): ?>
                    <?php foreach($lines as $line): ?>
                        <tr>
                            <td>
                                <select name="comp_id[]" class="form-select">
                                    <?php foreach($products as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= $line['component_id'] == $p['id'] ? 'selected' : '' ?>><?= $p['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" step="0.001" name="comp_qty[]" class="form-control" value="<?= $line['qty'] ?>"></td>
                            <td>
                                <select name="uom_id[]" class="form-select form-select-sm">
                                    <?php foreach($uoms as $u): ?>
                                        <option value="<?= $u['id'] ?>" <?= ($line['uom_id'] == $u['id']) ? 'selected' : '' ?>><?= $u['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="text-center"><button type="button" class="btn btn-sm text-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    
                    <tr>
                        <td>
                            <select name="comp_id[]" class="form-select">
                                <option value="">Select Component...</option>
                                <?php foreach($products as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="number" step="0.001" name="comp_qty[]" class="form-control" value="1"></td>
                        <td>
                            <select name="uom_id[]" class="form-select form-select-sm">
                                <?php foreach($uoms as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= $u['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="text-center"><button type="button" class="btn btn-sm text-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button type="button" class="btn btn-sm text-primary fw-bold" onclick="addRow()"><i class="bi bi-plus-circle"></i> Add a line</button>
    </div>
</form>

<script>
function addRow() {
    let table = document.getElementById('compTable').querySelector('tbody');
    // Clone baris pertama (index 0)
    let newRow = table.rows[0].cloneNode(true);
    
    // Reset nilai input di baris baru agar kosong
    newRow.querySelector('select[name="comp_id[]"]').selectedIndex = 0;
    newRow.querySelector('input[name="comp_qty[]"]').value = 1;
    newRow.querySelector('select[name="uom_id[]"]').selectedIndex = 0;
    
    // Masukkan ke tabel
    table.appendChild(newRow);
}
</script>

<?php require_once '../includes/footer.php'; ?>