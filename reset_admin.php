<?php
require_once 'config/db.php';

// Password yang diinginkan
$password_baru = 'staff123';
// Enkripsi password
$password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

try {
    // 1. Cek apakah user admin sudah ada?
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'barista'");
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        // Jika ada, UPDATE passwordnya
        $sql = "UPDATE users SET password = ?, role = 'staff' WHERE username = 'barista'";
        $pdo->prepare($sql)->execute([$password_hash]);
        echo "<h1>BERHASIL! ✅</h1>";
        echo "<p>Password untuk user <b>admin</b> berhasil di-reset.</p>";
    } else {
        // Jika belum ada, BUAT user baru
        $sql = "INSERT INTO users (username, password, full_name, role, department_id) VALUES (?, ?, ?, ?, ?)";
        // Asumsi ID Departemen 1 ada. Jika error, set NULL
        $pdo->prepare($sql)->execute(['barista', $password_hash, 'Budi Barista', 'staff', 1]);
        echo "<h1>BERHASIL DIBUAT! ✅</h1>";
        echo "<p>User <b>admin</b> baru berhasil ditambahkan.</p>";
    }

    echo "<p>Silakan login dengan:</p>";
    echo "<ul><li>Username: <b>admin</b></li><li>Password: <b>admin123</b></li></ul>";
    echo "<br><a href='auth/login.php'>Ke Halaman Login >></a>";

} catch (Exception $e) {
    echo "<h1>GAGAL ❌</h1>";
    echo "Error: " . $e->getMessage();
}
?>