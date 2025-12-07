<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

// Join tabel MO dengan Products untuk mengambil nama produk
$sql = "SELECT mo.*, p.name as product_name 
        FROM manufacturing_orders mo 
        JOIN products p ON mo.product_id = p.id 
        ORDER BY mo.id DESC";
$orders = $pdo->query($sql)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold m-0">Manufacturing Orders</h2>
    <a href="mo.php" class="btn btn-primary-erp px-4">
        <i class="bi bi-plus-lg me-1"></i> New
    </a>
</div>

<div class="card shadow-sm border-0">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Reference</th>
                <th>Date</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Status</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $mo): 
                $badge = match($mo['status']) {
                    'Draft' => 'bg-secondary',
                    'Confirmed' => 'bg-primary',
                    'Done' => 'bg-success',
                    default => 'bg-warning'
                };
            ?>
            <tr style="cursor: pointer;" onclick="window.location='mo.php?id=<?= $mo['id'] ?>'">
                <td class="fw-bold text-primary"><?= $mo['mo_reference'] ?></td>
                <td><?= $mo['scheduled_date'] ?></td>
                <td class="fw-bold"><?= $mo['product_name'] ?></td>
                <td><?= $mo['qty'] ?></td>
                <td><span class="badge <?= $badge ?>"><?= $mo['status'] ?></span></td>
                <td class="text-end">
                    <i class="bi bi-chevron-right text-muted"></i>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>