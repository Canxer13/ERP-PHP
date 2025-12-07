<?php include '../includes/header.php'; ?>

<div class="top-bar d-flex justify-content-between align-items-center">
    <div>
        <div class="breadcrumb-custom">Sales / Quotation / New</div>
        <h2>New Quotation</h2>
    </div>
    <div>
        <button class="btn btn-secondary">Discard</button>
        <button class="btn btn-primary-custom">Confirm</button>
    </div>
</div>

<div class="form-section">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Customer</label>
                <input type="text" class="form-control" placeholder="Select Customer...">
            </div>
            <div class="mb-3">
                <label class="form-label">Invoice Address</label>
                <textarea class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Delivery Address</label>
                <textarea class="form-control" rows="2"></textarea>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Quotation Date</label>
                <input type="date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Expiration</label>
                <input type="date" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Payment Terms</label>
                <select class="form-select">
                    <option>Immediate Payment</option>
                    <option>15 Days</option>
                    <option>30 Days</option>
                </select>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#order-lines">Order Lines</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#optional">Optional Products</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#other-info">Other Info</button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="order-lines">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Taxes</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" class="form-control form-control-sm" placeholder="Add a product"></td>
                        <td><input type="text" class="form-control form-control-sm"></td>
                        <td><input type="number" class="form-control form-control-sm" value="1"></td>
                        <td><input type="text" class="form-control form-control-sm" value="0.00"></td>
                        <td><input type="text" class="form-control form-control-sm"></td>
                        <td class="text-end">Rp 0</td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-sm btn-link">Add a product</button>
        </div>
        
        <div class="tab-pane fade" id="other-info">
            <div class="row">
                <div class="col-md-6">
                    <h5>Sales</h5>
                    <div class="mb-2">
                        <label class="form-label">Sales Person</label>
                        <input type="text" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Invoicing</h5>
                    <div class="mb-2">
                        <label class="form-label">Bank Account</label>
                        <input type="text" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>