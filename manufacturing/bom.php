<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

// Hapus Data
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM boms WHERE id=?")->execute([$_GET['del']]);
    echo "<script>window.location='bom.php';</script>";
}

// Ambil Data BOM + Nama Produk
$sql = "SELECT b.*, p.name as product_name 
        FROM boms b 
        JOIN products p ON b.product_id = p.id 
        ORDER BY b.id DESC";
$boms = $pdo->query($sql)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small text-muted">
                <li class="breadcrumb-item">Manufacturing</li>
                <li class="breadcrumb-item active text-dark">Bills of Materials</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold m-0">Bills of Materials</h2>
    </div>
    <a href="bom_form.php" class="btn btn-primary-erp px-4">
        <i class="bi bi-plus-lg me-1"></i> New
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Reference</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>BoM Type</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($boms)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data BOM</td></tr>
                <?php else: ?>
                    <?php foreach($boms as $b): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= $b['reference'] ?: '-' ?></td>
                        <td class="fw-bold"><?= $b['product_name'] ?></td>
                        <td><?= $b['qty'] ?> Unit</td>
                        <td>
                            <?php if($b['type'] == 'manufacturing'): ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary">Manufacturing</span>
                            <?php else: ?>
                                <span class="badge bg-info bg-opacity-10 text-info">Kit / Phantom</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="bom_form.php?id=<?= $b['id'] ?>" class="btn btn-sm text-primary"><i class="bi bi-pencil"></i></a>
                            <a href="?del=<?= $b['id'] ?>" class="btn btn-sm text-danger" onclick="return confirm('Hapus Resep ini?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>