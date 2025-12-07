<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM vendors WHERE id=?")->execute([$_GET['del']]);
    echo "<script>window.location='vendors.php';</script>";
}

$vendors = $pdo->query("SELECT * FROM vendors")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold m-0">Vendors</h2>
    <a href="vendor_form.php" class="btn btn-primary-erp px-4">Create</a>
</div>

<div class="card shadow-sm border-0">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr><th>Name</th><th>Phone</th><th>Email</th><th>Address</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php foreach($vendors as $v): ?>
            <tr>
                <td class="fw-bold"><?= $v['name'] ?></td>
                <td><?= $v['phone'] ?></td>
                <td><?= $v['email'] ?></td>
                <td><?= $v['address'] ?></td>
                <td>
                    <a href="vendor_form.php?id=<?= $v['id'] ?>" class="btn btn-sm text-primary"><i class="bi bi-pencil"></i></a>
                    <a href="?del=<?= $v['id'] ?>" class="btn btn-sm text-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../includes/footer.php'; ?>