<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

if ($_SESSION['role'] !== 'super_admin') {
    echo "<script>window.location='../index.php';</script>"; exit;
}

// Handle Add New
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare("INSERT INTO departments (name, description) VALUES (?, ?)")
        ->execute([$_POST['name'], $_POST['desc']]);
    echo "<script>window.location='departments.php';</script>";
}

// Handle Delete
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM departments WHERE id=?")->execute([$_GET['del']]);
    echo "<script>window.location='departments.php';</script>";
}

$depts = $pdo->query("SELECT * FROM departments")->fetchAll();
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 p-4">
            <h5 class="fw-bold mb-3">Add Department</h5>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Kitchen" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="desc" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary-erp w-100">Add</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="m-0 fw-bold">Department List</h5>
            </div>
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Name</th><th>Description</th><th class="text-end">Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach($depts as $d): ?>
                    <tr>
                        <td class="fw-bold"><?= $d['name'] ?></td>
                        <td><?= $d['description'] ?></td>
                        <td class="text-end">
                            <a href="?del=<?= $d['id'] ?>" class="btn btn-sm text-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>