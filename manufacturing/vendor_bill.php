<?php 
// Menggunakan __DIR__ untuk path absolut yang aman dari error
require_once dirname(__DIR__) . '/includes/header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small text-muted">
                <li class="breadcrumb-item">Accounting</li>
                <li class="breadcrumb-item">Vendors</li>
                <li class="breadcrumb-item">Bills</li>
                <li class="breadcrumb-item active text-dark" aria-current="page">Draft</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold text-dark m-0">Draft Bill <span class="badge bg-secondary fs-6 align-middle ms-2">DRAFT</span></h2>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-light border text-muted px-3">Discard</button>
        <button class="btn btn-primary px-4" style="background-color: #714B67; border-color: #714B67;">Confirm</button>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">
        
        <div class="row g-4 mb-4">
            <div class="col-md-6 border-end-md">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary small">Vendor</label>
                    <input type="text" class="form-control bg-light" placeholder="Select Vendor...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary small">Bill Reference</label>
                    <input type="text" class="form-control" placeholder="e.g. INV/2023/001">
                </div>
            </div>
            <div class="col-md-6 ps-md-4">
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold text-secondary small">Accounting Date</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold text-secondary small">Due Date</label>
                        <input type="date" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active text-dark fw-bold" id="lines-tab" data-bs-toggle="tab" data-bs-target="#lines" type="button">Invoice Lines</button>
            </li>
            <li class="nav-item">
                <button class="nav-link text-secondary" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">Journal Items</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="lines" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%;">Product</th>
                                <th>Label</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-center">Tax</th>
                                <th class="text-end">Subtotal</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Gula Pasir</td>
                                <td class="text-muted">PO001</td>
                                <td class="text-center"><input type="number" class="form-control form-control-sm text-center" value="10" style="width: 60px; display:inline;"></td>
                                <td class="text-end">20.000</td>
                                <td class="text-center"><span class="badge bg-light text-dark border">11%</span></td>
                                <td class="text-end fw-bold">200.000</td>
                                <td class="text-center"><button class="btn btn-sm text-danger"><i class="bi bi-trash"></i></button></td>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <a href="#" class="text-decoration-none fw-bold small" style="color: #714B67;">
                                        <i class="bi bi-plus-circle me-1"></i> Add a line
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end mt-4">
                    <div class="col-md-4">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-end text-muted fw-bold">Untaxed Amount:</td>
                                <td class="text-end">200.000</td>
                            </tr>
                            <tr>
                                <td class="text-end text-muted fw-bold">Tax:</td>
                                <td class="text-end">22.000</td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-end fs-5 fw-bold text-dark">Total:</td>
                                <td class="text-end fs-5 fw-bold text-dark">Rp 222.000</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="info" role="tabpanel">
                <p class="text-muted p-3">Journal items will be generated upon confirmation.</p>
            </div>
        </div>

    </div>
</div>

<?php 
require_once dirname(__DIR__) . '/includes/footer.php'; 
?>