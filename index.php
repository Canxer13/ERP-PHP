<?php require_once 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 fw-bold text-dark">Dashboard Overview</h2>
    <button class="btn btn-sm btn-outline-secondary">Refresh Data</button>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card-erp p-3 h-100 border-start border-4 border-primary">
            <div class="text-uppercase text-muted small fw-bold">Total Sales</div>
            <div class="fs-2 fw-bold text-dark">Rp 150.000.000</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-erp p-3 h-100 border-start border-4 border-success">
            <div class="text-uppercase text-muted small fw-bold">Active Orders</div>
            <div class="fs-2 fw-bold text-dark">25</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-erp p-3 h-100 border-start border-4 border-warning">
            <div class="text-uppercase text-muted small fw-bold">Pending Bills</div>
            <div class="fs-2 fw-bold text-dark">5</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-erp p-3 h-100 border-start border-4 border-info">
            <div class="text-uppercase text-muted small fw-bold">Inventory Items</div>
            <div class="fs-2 fw-bold text-dark">1,204</div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>