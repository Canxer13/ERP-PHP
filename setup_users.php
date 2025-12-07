<?php
require_once 'config/db.php';

echo "<h2>⚙️ Setup User & Departemen Otomatis</h2><hr>";

try {
    // 1. PASTIKAN DEPARTEMEN ADA DULU (Supaya tidak error Foreign Key)
    // Kita buat dummy departemen jika tabel kosong
    $cekDept = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
    if ($cekDept == 0) {
        $pdo->exec("INSERT INTO departments (id, name, description) VALUES 
            (1, 'IT / Management', 'Pengelola Sistem'),
            (2, 'Kitchen', 'Dapur Utama'),
            (3, 'Bar', 'Minuman'),
            (4, 'Cashier', 'Kasir Depan')
        ");
        echo "<p>✅ Data Departemen berhasil dibuat.</p>";
    } else {
        echo "<p>ℹ️ Data Departemen sudah ada. Lewati.</p>";
    }

    // 2. DAFTAR USER YANG AKAN DI-RESET / DIBUAT
    $users = [
        [
            'username' => 'admin',
            'password' => 'admin123',
            'full_name' => 'Super Administrator',
            'role' => 'super_admin',
            'dept_id' => 1
        ],
        [
            'username' => 'staff',
            'password' => 'staff123',
            'full_name' => 'Budi Staff',
            'role' => 'staff',
            'dept_id' => 2
        ]
    ];

    // 3. PROSES RESET USER
    $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmtUpdate = $pdo->prepare("UPDATE users SET password = ?, full_name = ?, role = ?, department_id = ? WHERE username = ?");
    $stmtInsert = $pdo->prepare("INSERT INTO users (username, password, full_name, role, department_id) VALUES (?, ?, ?, ?, ?)");

    foreach ($users as $u) {
        // Enkripsi Password (WAJIB!)
        $hash = password_hash($u['password'], PASSWORD_DEFAULT);

        // Cek apakah user sudah ada?
        $stmtCheck->execute([$u['username']]);
        $exists = $stmtCheck->fetch();

        if ($exists) {
            // Update user lama
            $stmtUpdate->execute([$hash, $u['full_name'], $u['role'], $u['dept_id'], $u['username']]);
            echo "<p>✅ User <b>{$u['username']}</b> berhasil di-reset (Update).</p>";
        } else {
            // Buat user baru
            $stmtInsert->execute([$u['username'], $hash, $u['full_name'], $u['role'], $u['dept_id']]);
            echo "<p>✅ User <b>{$u['username']}</b> berhasil dibuat baru (Insert).</p>";
        }
    }

    echo "<hr><h3>🎉 SELESAI! Silakan Login:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>
            <tr style='background:#eee'><th>Role</th><th>Username</th><th>Password</th></tr>
            <tr><td>👑 Super Admin</td><td><b>admin</b></td><td><b>admin123</b></td></tr>
            <tr><td>👤 Staff</td><td><b>staff</b></td><td><b>staff123</b></td></tr>
          </table>";
    
    echo "<br><br><a href='auth/login.php' style='background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Ke Halaman Login >></a>";

} catch (Exception $e) {
    echo "<h3 style='color:red'>❌ GAGAL</h3>";
    echo "Error: " . $e->getMessage();
}
?>