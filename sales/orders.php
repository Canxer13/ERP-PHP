<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

$orders = $pdo->query("SELECT * FROM sales_orders ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold">Quotations</h2>
    <a href="create_quotation.php" class="btn btn-primary-erp">New</a>
</div>

<div class="card shadow-sm border-0">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr><th>Order Number</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach($orders as $o): ?>
            <tr>
                <td class="fw-bold text-primary"><?= $o['order_number'] ?></td>
                <td><?= $o['customer_name'] ?></td>
                <td><?= $o['order_date'] ?></td>
                <td class="fw-bold"><?= formatRupiah($o['total_amount']) ?></td>
                <td><span class="badge bg-secondary"><?= $o['status'] ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../includes/footer.php'; ?>