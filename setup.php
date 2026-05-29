<?php
require 'config/db.php';
$pass = 'admin123';
$hash = password_hash($pass, PASSWORD_DEFAULT);
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) UNIQUE NOT NULL, password_hash VARCHAR(255) NOT NULL, role ENUM('super_admin','admin','user') DEFAULT 'user', is_active TINYINT(1) DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'superadmin'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES ('superadmin', ?, 'super_admin')")->execute([$hash]);
        echo "✅ Super Admin dibuat. Username: superadmin | Password: admin123<br>";
    } else { echo "✅ Akun sudah ada.<br>"; }
    echo "<a href='user/login.php'>➡️ Login Sekarang</a><br><br><em style='color:red'>⚠️ HAPUS file ini!</em>";
} catch (PDOException $e) { echo " " . $e->getMessage(); }
?>