<?php
session_start();
require_once '../config/db.php';

// Jika sudah login, lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Set Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        
        header("Location: ../index.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #2d2d2d; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { background-color: white; color: #333; padding: 40px; border-radius: 8px; width: 100%; max-width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        .btn-primary-erp { background-color: #714B67; border: none; color: white; width: 100%; padding: 10px; }
        .btn-primary-erp:hover { background-color: #5a3b52; }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center fw-bold mb-4" style="color: #714B67;">NOITIO ERP</h3>
        <?php if($error): ?>
            <div class="alert alert-danger text-center p-2 small"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary-erp fw-bold">LOGIN</button>
        </form>
        <div class="text-center mt-3 text-muted small">
            &copy; <?= date('Y') ?> Noitio System
        </div>
    </div>
</body>
</html>