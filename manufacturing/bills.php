<?php require_once '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small text-muted">
                <li class="breadcrumb-item">Accounting</li>
                <li class="breadcrumb-item active text-dark">Vendor Bills</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold m-0">Vendor Bills</h2>
    </div>
    <a href="vendor_bill.php" class="btn btn-primary px-4" style="background-color: #714B67; border-color: #714B67;">
        <i class="bi bi-plus-lg me-1"></i> New
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Number</th>
                    <th>Vendor</th>
                    <th>Bill Date</th>
                    <th>Due Date</th>
                    <th>Total</th>
                    <th>Payment Status</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold">BILL/2023/001</td>
                    <td>CV. Biji Kopi Mantap</td>
                    <td>01/11/2023</td>
                    <td class="text-danger fw-bold">15/11/2023</td>
                    <td>Rp 5.500.000</td>
                    <td><span class="badge rounded-pill bg-danger">Not Paid</span></td>
                    <td>Posted</td>
                </tr>
                <tr>
                    <td class="fw-bold">BILL/2023/002</td>
                    <td>Toko Plastik Jaya</td>
                    <td>05/11/2023</td>
                    <td>20/11/2023</td>
                    <td>Rp 1.200.000</td>
                    <td><span class="badge rounded-pill bg-success">Paid</span></td>
                    <td>Posted</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>