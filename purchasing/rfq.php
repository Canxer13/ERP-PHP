<?php require_once '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold m-0">Request for Quotation <span class="badge bg-secondary fs-6 align-middle">DRAFT</span></h2>
    <div>
        <button class="btn btn-primary-erp">Send by Email</button>
        <button class="btn btn-outline-secondary">Print</button>
    </div>
</div>

<div class="card shadow-sm border-0 p-4">
    <div class="row mb-4 bg-light p-3 rounded">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label small fw-bold">Vendor</label>
                <input type="text" class="form-control" placeholder="Select Vendor...">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Vendor Reference</label>
                <input type="text" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label small fw-bold">Order Deadline</label>
                <input type="datetime-local" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Currency</label>
                <select class="form-select"><option>IDR</option><option>USD</option></select>
            </div>
        </div>
    </div>

    <h5 class="fw-bold">Products</h5>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>UoM</th>
                <th>Unit Price</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input type="text" class="form-control border-0" placeholder="Add a product..."></td>
                <td><input type="number" class="form-control border-0" value="1"></td>
                <td>Unit</td>
                <td><input type="text" class="form-control border-0" placeholder="0"></td>
                <td class="text-end">Rp 0</td>
                <td class="text-center"><i class="bi bi-trash text-danger"></i></td>
            </tr>
        </tbody>
    </table>
    <button class="btn btn-sm text-primary fw-bold"><i class="bi bi-plus-circle"></i> Add a line</button>
</div>
<?php require_once '../includes/footer.php'; ?>