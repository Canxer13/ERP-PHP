<?php include '../includes/header.php'; ?>

<div class="top-bar d-flex justify-content-between align-items-center">
    <div>
        <div class="breadcrumb-custom">Manufacturing / Products / New</div>
        <h2>Tambah Produk Baru</h2>
    </div>
    <div>
        <button class="btn btn-primary-custom">Confirm</button>
    </div>
</div>

<div class="form-section">
    <div class="row">
        <div class="col-md-3 text-center">
            <div style="width: 100%; height: 200px; background-color: #eee; display: flex; align-items: center; justify-content: center; border: 2px dashed #ccc; margin-bottom: 10px;">
                <span class="text-muted"><i class="bi bi-camera"></i> Preview Foto</span>
            </div>
            <input type="file" class="form-control form-control-sm">
        </div>

        <div class="col-md-9">
            <div class="mb-3">
                <label class="form-label fs-4">Nama Produk</label>
                <input type="text" class="form-control form-control-lg" placeholder="e.g. Es Kopi Butterscotch" value="Es Kopi">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Kategori Produk</label>
                        <select class="form-select">
                            <option>Minuman</option>
                            <option>Makanan</option>
                            <option>Bahan Baku</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Jual</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" value="22000">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Modal / Cost</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" value="20000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" class="form-control" value="100">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi Produk</label>
                <textarea class="form-control" rows="3" placeholder="Deskripsi produk...">Terbuat dari biji kopi pilihan</textarea>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>