<?php 
require_once '../includes/header.php'; 
require_once '../config/db.php';

// Proteksi
if ($_SESSION['role'] !== 'super_admin') {
    echo "<script>window.location='../index.php';</script>"; exit;
}

$depts = $pdo->query("SELECT * FROM departments")->fetchAll();

$id = $_GET['id'] ?? null;
$user = ['username'=>'', 'full_name'=>'', 'role'=>'staff', 'department_id'=>''];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    $dept_id = $_POST['department_id'];
    $password = $_POST['password'];

    try {
        if ($id) {
            // Update
            $sql = "UPDATE users SET username=?, full_name=?, role=?, department_id=? WHERE id=?";
            $params = [$username, $full_name, $role, $dept_id, $id];
            
            // Jika password diisi, update password juga
            if (!empty($password)) {
                $sql = "UPDATE users SET username=?, full_name=?, role=?, department_id=?, password=? WHERE id=?";
                $params = [$username, $full_name, $role, $dept_id, password_hash($password, PASSWORD_DEFAULT), $id];
            }
            $pdo->prepare($sql)->execute($params);
        } else {
            // Insert Baru (Password Wajib)
            if (empty($password)) { throw new Exception("Password wajib diisi untuk user baru"); }
            
            $sql = "INSERT INTO users (username, password, full_name, role, department_id) VALUES (?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$username, password_hash($password, PASSWORD_DEFAULT), $full_name, $role, $dept_id]);
        }
        echo "<script>window.location='staff_list.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold"><?= $id ? 'Edit User' : 'New User' ?></h2>
    <button onclick="document.getElementById('userForm').submit()" class="btn btn-primary-erp">Save User</button>
</div>

<div class="card shadow-sm border-0 p-4">
    <form id="userForm" method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?= $user['full_name'] ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?= $user['username'] ?>" required>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="staff" <?= $user['role']=='staff'?'selected':'' ?>>Staff</option>
                    <option value="super_admin" <?= $user['role']=='super_admin'?'selected':'' ?>>Super Admin</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <?php foreach($depts as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $user['department_id']==$d['id']?'selected':'' ?>>
                            <?= $d['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password <?= $id ? '<span class="text-muted small">(Biarkan kosong jika tidak ingin mengganti)</span>' : '' ?></label>
            <input type="password" name="password" class="form-control" <?= $id ? '' : 'required' ?>>
        </div>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>