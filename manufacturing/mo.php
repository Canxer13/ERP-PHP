<?php require_once '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small text-muted">
                <li class="breadcrumb-item">Manufacturing</li>
                <li class="breadcrumb-item active">Manufacturing Orders</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold m-0">MO/2025/001</h2>
    </div>
    <div>
        <button class="btn btn-primary-erp">Mark as Done</button>
        <button class="btn btn-outline-secondary">Cancel</button>
    </div>
</div>

<div class="card shadow-sm border-0 p-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="mb-3 row">
                <label class="col-sm-4 form-label text-muted fw-bold">Product</label>
                <div class="col-sm-8"><input type="text" class="form-control fw-bold" value="Es Kopi Butterscotch"></div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-4 form-label text-muted fw-bold">Quantity</label>
                <div class="col-sm-8"><input type="number" class="form-control" value="50"></div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-4 form-label text-muted fw-bold">BOM Reference</label>
                <div class="col-sm-8"><input type="text" class="form-control" value="BOM/KOPI/001"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3 row">
                <label class="col-sm-4 form-label text-muted fw-bold">Scheduled Date</label>
                <div class="col-sm-8"><input type="date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-4 form-label text-muted fw-bold">Responsible</label>
                <div class="col-sm-8"><input type="text" class="form-control" value="Mitchell Admin"></div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-3">Components</h5>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Required Qty</th>
                    <th>Available Qty</th>
                    <th>Unit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Biji Kopi Arabica</td>
                    <td>1.5</td>
                    <td class="text-danger fw-bold">0.5</td>
                    <td>Kg</td>
                    <td><span class="badge bg-danger">Shortage</span></td>
                </tr>
                <tr>
                    <td>Susu UHT</td>
                    <td>10</td>
                    <td class="text-success fw-bold">50</td>
                    <td>Liter</td>
                    <td><span class="badge bg-success">Available</span></td>
                </tr>
                <tr>
                    <td>Syrup Butterscotch</td>
                    <td>2</td>
                    <td class="text-success fw-bold">5</td>
                    <td>Liter</td>
                    <td><span class="badge bg-success">Available</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>