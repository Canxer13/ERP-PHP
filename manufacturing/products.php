<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

// Logic Hapus Data
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    echo "<script>window.location='products.php';</script>";
}

// Ambil Data Produk
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold m-0">Products</h2>
    <a href="product_form.php" class="btn btn-primary-erp px-4">
        <i class="bi bi-plus-lg me-1"></i> New
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Internal Ref</th>
                    <th>Sales Price</th>
                    <th>Cost</th>
                    <th>On Hand</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td class="fw-bold"><?= htmlspecialchars($p['name']) ?></td>
                    <td class="text-muted"><?= htmlspecialchars($p['internal_ref']) ?></td>
                    <td><?= formatRupiah($p['sales_price']) ?></td>
                    <td><?= formatRupiah($p['cost_price']) ?></td>
                    <td><span class="badge bg-info text-dark bg-opacity-25"><?= $p['qty_on_hand'] ?> <?= $p['uom'] ?></span></td>
                    <td class="text-end">
                        <a href="product_form.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <a href="?delete_id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk ini?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>