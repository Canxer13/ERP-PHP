<?php require_once '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold m-0">Customer: Putra Siregar</h2>
    <div>
        <button class="btn btn-outline-secondary me-2">Cancel</button>
        <button class="btn btn-primary-erp">Save</button>
    </div>
</div>

<div class="card shadow-sm border-0 p-4">
    <div class="row mb-4">
        <div class="col-md-2 text-center">
            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 100px; width: 100px; margin: auto;">
                <i class="bi bi-camera fs-1 text-muted"></i>
            </div>
        </div>
        <div class="col-md-10">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Name</label>
                <input type="text" class="form-control fs-3 fw-bold border-0 border-bottom rounded-0 px-0" value="Putra Siregar">
            </div>
            <div class="d-flex gap-4">
                <div>
                    <label class="form-check-label me-2">Type:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="type" checked> Individual
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="type"> Company
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <label class="me-2">NPWP:</label>
                    <input type="text" class="form-control form-control-sm" placeholder="00.000.000.0-000.000">
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4" id="custTabs">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#contacts">Contacts & Addresses</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#salespur">Sales & Purchase</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#invoicing">Invoicing</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="contacts">
            <button class="btn btn-outline-primary btn-sm mb-3">Add Contact</button>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100 p-3 bg-light border">
                        <div class="fw-bold">Gudang Cikarang</div>
                        <div class="small text-muted">Jl. Industri No 5, Cikarang<br>Mobile: 08123456789</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="salespur">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2">SALES</h6>
                    <div class="mb-3"><label class="form-label small">Salesperson</label><select class="form-select"><option>Mitchell Admin</option></select></div>
                    <div class="mb-3"><label class="form-label small">Payment Terms</label><select class="form-select"><option>Immediate Payment</option></select></div>
                </div>
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2">PURCHASE</h6>
                    <div class="mb-3"><label class="form-label small">Payment Terms</label><select class="form-select"><option>30 Days</option></select></div>
                    <div class="mb-3"><label class="form-label small">Supplier Currency</label><select class="form-select"><option>IDR</option></select></div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="invoicing">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2">BANK ACCOUNTS</h6>
                    <table class="table table-sm">
                        <thead><tr><th>Bank</th><th>Account Number</th></tr></thead>
                        <tbody><tr><td>BCA</td><td>123-456-7890</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>