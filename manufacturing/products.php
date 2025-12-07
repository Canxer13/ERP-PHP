<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

// ... (Delete logic remains the same) ...

// FIX: Join with UOMS table to get the Unit Name (e.g., 'kg', 'pcs')
$sql = "SELECT p.*, u.name as uom_name 
        FROM products p 
        LEFT JOIN uoms u ON p.uom_id = u.id 
        ORDER BY p.id DESC";
$products = $pdo->query($sql)->fetchAll();
?>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Internal Ref</th>
                    <th>Sales Price</th>
                    <th>Cost</th>
                    <th>On Hand</th> <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td class="fw-bold"><?= htmlspecialchars($p['name']) ?></td>
                    <td class="text-muted"><?= htmlspecialchars($p['internal_ref']) ?></td>
                    <td><?= formatRupiah($p['sales_price']) ?></td>
                    <td><?= formatRupiah($p['cost_price']) ?></td>
                    
                    <td>
                        <span class="badge bg-info text-dark bg-opacity-25">
                            <?= $p['qty_on_hand'] ?> <?= htmlspecialchars($p['uom_name'] ?? 'Unit') ?>
                        </span>
                    </td>
                    
                    <td class="text-end">
                        <a href="product_form.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <a href="?delete_id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete product?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>