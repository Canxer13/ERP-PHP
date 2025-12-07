<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

// Logic Hapus
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM customers WHERE id=?")->execute([$_GET['del']]);
    echo "<script>window.location='customer_list.php';</script>";
}

$customers = $pdo->query("SELECT * FROM customers ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold m-0">Customers</h2>
    <a href="customers.php" class="btn btn-primary-erp px-4">
        <i class="bi bi-plus-lg me-1"></i> New
    </a>
</div>

<div class="card shadow-sm border-0">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Phone</th>
                <th>Email</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($customers as $c): ?>
            <tr>
                <td class="fw-bold"><?= htmlspecialchars($c['name']) ?></td>
                <td><span class="badge bg-light text-dark border"><?= $c['type'] ?></span></td>
                <td><?= $c['phone'] ?></td>
                <td><?= $c['email'] ?></td>
                <td class="text-end">
                    <a href="customers.php?id=<?= $c['id'] ?>" class="btn btn-sm text-primary"><i class="bi bi-pencil"></i></a>
                    <a href="?del=<?= $c['id'] ?>" class="btn btn-sm text-danger" onclick="return confirm('Hapus customer ini?')"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>