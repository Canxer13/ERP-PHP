<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

// Proteksi: Hanya Super Admin yang boleh akses
if ($_SESSION['role'] !== 'super_admin') {
    echo "<div class='alert alert-danger'>Akses Ditolak. Anda bukan Super Admin.</div>";
    require_once '../includes/footer.php';
    exit;
}

// Logic Hapus
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$_GET['del']]);
    echo "<script>window.location='staff_list.php';</script>";
}

// Ambil Data User + Nama Departemen
$sql = "SELECT u.*, d.name as dept_name 
        FROM users u 
        LEFT JOIN departments d ON u.department_id = d.id 
        ORDER BY u.id DESC";
$staffs = $pdo->query($sql)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold m-0">Staff Management</h2>
    <a href="staff_form.php" class="btn btn-primary-erp px-4"><i class="bi bi-person-plus"></i> New Staff</a>
</div>

<div class="card shadow-sm border-0">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Full Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Department</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($staffs as $s): ?>
            <tr>
                <td class="fw-bold"><?= $s['full_name'] ?></td>
                <td><?= $s['username'] ?></td>
                <td>
                    <?php if($s['role'] == 'super_admin'): ?>
                        <span class="badge bg-danger">Super Admin</span>
                    <?php else: ?>
                        <span class="badge bg-success">Staff</span>
                    <?php endif; ?>
                </td>
                <td><?= $s['dept_name'] ?? '-' ?></td>
                <td class="text-end">
                    <a href="staff_form.php?id=<?= $s['id'] ?>" class="btn btn-sm text-primary"><i class="bi bi-pencil"></i></a>
                    <?php if($s['id'] != $_SESSION['user_id']): // Jangan hapus diri sendiri ?>
                        <a href="?del=<?= $s['id'] ?>" class="btn btn-sm text-danger" onclick="return confirm('Hapus user ini?')"><i class="bi bi-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>