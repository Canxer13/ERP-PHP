<?php 
require_once 'includes/header.php'; 
require_once 'config/db.php';

// --- HELPER GROWTH ---
function getGrowth($pdo, $table, $col_sum, $col_date) {
    // Logic hitung persen (Sama seperti sebelumnya)
    $sqlCur = $col_sum 
        ? "SELECT SUM($col_sum) FROM $table WHERE MONTH($col_date) = MONTH(CURRENT_DATE()) AND YEAR($col_date) = YEAR(CURRENT_DATE())"
        : "SELECT COUNT(*) FROM $table WHERE MONTH($col_date) = MONTH(CURRENT_DATE()) AND YEAR($col_date) = YEAR(CURRENT_DATE())";
    $cur = $pdo->query($sqlCur)->fetchColumn() ?: 0;

    $sqlLast = $col_sum
        ? "SELECT SUM($col_sum) FROM $table WHERE MONTH($col_date) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR($col_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)"
        : "SELECT COUNT(*) FROM $table WHERE MONTH($col_date) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR($col_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)";
    $last = $pdo->query($sqlLast)->fetchColumn() ?: 0;

    $pct = ($last == 0) ? ($cur > 0 ? 100 : 0) : (($cur - $last) / $last) * 100;
    return ['val' => $cur, 'pct' => round($pct), 'up' => $pct >= 0];
}

// Hitung Data
$sales = getGrowth($pdo, 'sales_orders', 'total_amount', 'order_date');
$active_orders = getGrowth($pdo, 'sales_orders', null, 'order_date');
$bills = getGrowth($pdo, 'vendor_bills', null, 'bill_date');
$items = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// --- PERBAIKAN QUERY HISTORY (RECENT ACTIVITY) ---
// Masalahnya ada di sini sebelumnya. Kita harus pastikan kolom tanggalnya benar.
// Sales Order pakai 'order_date', Bill pakai 'bill_date', Product pakai 'created_at'
$sqlHistory = "
    (SELECT 'Sales Order' as type, order_number as ref, customer_name as descr, order_date as date, 'primary' as color FROM sales_orders)
    UNION ALL
    (SELECT 'Vendor Bill' as type, bill_number as ref, 'Tagihan Baru' as descr, bill_date as date, 'warning' as color FROM vendor_bills)
    UNION ALL
    (SELECT 'New Product' as type, internal_ref as ref, name as descr, created_at as date, 'success' as color FROM products)
    ORDER BY date DESC 
    LIMIT 5
";
$history = $pdo->query($sqlHistory)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 fw-bold text-dark">Dashboard</h2>
    <button onclick="window.location.reload()" class="btn btn-sm btn-outline-dark">Refresh</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-erp p-3 border-start border-4 border-primary">
            <div class="small text-muted fw-bold">TOTAL SALES</div>
            <h3 class="fw-bold text-dark">Rp <?= number_format($sales['val'], 0, ',', '.') ?></h3>
            <small class="<?= $sales['up'] ? 'text-success' : 'text-danger' ?>">
                <?= $sales['up'] ? '▲' : '▼' ?> <?= abs($sales['pct']) ?>% vs last month
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-erp p-3 border-start border-4 border-success">
            <div class="small text-muted fw-bold">ACTIVE ORDERS</div>
            <h3 class="fw-bold text-dark"><?= $active_orders['val'] ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-erp p-3 border-start border-4 border-warning">
            <div class="small text-muted fw-bold">NEW BILLS</div>
            <h3 class="fw-bold text-dark"><?= $bills['val'] ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-erp p-3 border-start border-4 border-info">
            <div class="small text-muted fw-bold">PRODUCTS</div>
            <h3 class="fw-bold text-dark"><?= $items ?></h3>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold m-0">Recent Activity</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Type</th>
                    <th>Reference</th>
                    <th>Description</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)): ?>
                    <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada history. Coba buat Sales Order baru.</td></tr>
                <?php else: ?>
                    <?php foreach ($history as $h): ?>
                    <tr>
                        <td><span class="badge bg-<?= $h['color'] ?>"><?= $h['type'] ?></span></td>
                        <td class="fw-bold"><?= $h['ref'] ?></td>
                        <td><?= $h['descr'] ?></td>
                        <td><?= date('d M Y', strtotime($h['date'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>