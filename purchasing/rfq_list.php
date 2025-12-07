<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

$sql = "SELECT r.*, v.name as vendor_name 
        FROM rfqs r 
        JOIN vendors v ON r.vendor_id = v.id 
        ORDER BY r.id DESC";
// Gunakan try catch agar tidak error jika tabel belum ada
try {
    $rfqs = $pdo->query($sql)->fetchAll();
} catch (Exception $e) {
    $rfqs = []; // Array kosong jika error
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold m-0">Requests for Quotation</h2>
    <a href="rfq.php" class="btn btn-primary-erp px-4">
        <i class="bi bi-plus-lg me-1"></i> New
    </a>
</div>

<div class="card shadow-sm border-0">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Reference</th>
                <th>Vendor</th>
                <th>Order Date</th>
                <th>Status</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($rfqs)): ?>
                <tr><td colspan="5" class="text-center py-4 text-muted">No RFQs found</td></tr>
            <?php else: ?>
                <?php foreach($rfqs as $r): ?>
                <tr style="cursor: pointer;" onclick="window.location='rfq.php?id=<?= $r['id'] ?>'">
                    <td class="fw-bold text-primary"><?= $r['rfq_number'] ?></td>
                    <td><?= $r['vendor_name'] ?></td>
                    <td><?= $r['order_date'] ?></td>
                    <td><span class="badge bg-info text-dark bg-opacity-25"><?= $r['status'] ?></span></td>
                    <td class="text-end">Rp 0</td> </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>