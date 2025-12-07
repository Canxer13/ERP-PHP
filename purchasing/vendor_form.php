<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
$v = ['name'=>'', 'phone'=>'', 'email'=>'', 'address'=>'', 'tax_id'=>''];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM vendors WHERE id=?");
    $stmt->execute([$id]);
    $v = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($id) {
        $sql = "UPDATE vendors SET name=?, phone=?, email=?, address=?, tax_id=? WHERE id=?";
        $pdo->prepare($sql)->execute([$_POST['name'], $_POST['phone'], $_POST['email'], $_POST['address'], $_POST['tax_id'], $id]);
    } else {
        $sql = "INSERT INTO vendors (name, phone, email, address, tax_id) VALUES (?,?,?,?,?)";
        $pdo->prepare($sql)->execute([$_POST['name'], $_POST['phone'], $_POST['email'], $_POST['address'], $_POST['tax_id']]);
    }
    echo "<script>window.location='vendors.php';</script>";
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold"><?= $id ? 'Edit Vendor' : 'New Vendor' ?></h2>
    <button onclick="document.getElementById('vForm').submit()" class="btn btn-primary-erp">Save</button>
</div>

<div class="card shadow-sm border-0 p-4">
    <form id="vForm" method="POST">
        <div class="mb-3">
            <label class="form-label fw-bold">Name</label>
            <input type="text" name="name" class="form-control" value="<?= $v['name'] ?>" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= $v['phone'] ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= $v['email'] ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control"><?= $v['address'] ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Tax ID (NPWP)</label>
            <input type="text" name="tax_id" class="form-control" value="<?= $v['tax_id'] ?>">
        </div>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>