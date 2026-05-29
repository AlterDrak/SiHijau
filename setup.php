<?php
// File: setup.php
// Akses: http://localhost/Lazpersis/setup.php

require 'config/db.php';

echo "<h1>Setup Database Lazpersis</h1>";

try {
    // Buat tabel users jika belum ada
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('super_admin', 'staff', 'user') DEFAULT 'user',
        division ENUM('keuangan', 'penghimpunan', 'pendayagunaan', 'media', 'all') DEFAULT 'all',
        is_active TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Buat tabel user_balances
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_balances (
        user_id INT PRIMARY KEY,
        balance DECIMAL(12,2) DEFAULT 0.00,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Buat tabel transactions
    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        type ENUM('topup', 'zakat', 'infaq', 'shodaqoh') NOT NULL,
        category VARCHAR(50) NULL,
        amount DECIMAL(12,2) NOT NULL,
        status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
        qris_ref VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Buat tabel campaigns
    $pdo->exec("CREATE TABLE IF NOT EXISTS campaigns (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100),
        status ENUM('aktif', 'selesai') DEFAULT 'aktif'
    )");

    // Password hash untuk 'admin123'
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Cek apakah superadmin sudah ada
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'superadmin'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Insert Super Admin
        $pdo->prepare("INSERT INTO users (username, password_hash, role, division, is_active) VALUES (?, ?, 'super_admin', 'all', 1)")
            ->execute(['superadmin', $password]);
        echo "<p style='color:green'>✅ Super Admin berhasil dibuat</p>";
        
        // Insert Staff
        $pdo->prepare("INSERT INTO users (username, password_hash, role, division, is_active) VALUES (?, ?, 'staff', 'keuangan', 1)")
            ->execute(['staff_keuangan', $password]);
        echo "<p style='color:green'>✅ Staff Keuangan berhasil dibuat</p>";
        
        $pdo->prepare("INSERT INTO users (username, password_hash, role, division, is_active) VALUES (?, ?, 'staff', 'penghimpunan', 1)")
            ->execute(['staff_penghimpunan', $password]);
        echo "<p style='color:green'>✅ Staff Penghimpunan berhasil dibuat</p>";
        
        $pdo->prepare("INSERT INTO users (username, password_hash, role, division, is_active) VALUES (?, ?, 'staff', 'pendayagunaan', 1)")
            ->execute(['staff_pendayagunaan', $password]);
        echo "<p style='color:green'>✅ Staff Pendayagunaan berhasil dibuat</p>";
        
        $pdo->prepare("INSERT INTO users (username, password_hash, role, division, is_active) VALUES (?, ?, 'staff', 'media', 1)")
            ->execute(['staff_media', $password]);
        echo "<p style='color:green'>✅ Staff Media berhasil dibuat</p>";
        
        echo "<hr>";
        echo "<h3>✅ Setup Selesai!</h3>";
        echo "<p><strong>Username:</strong> superadmin<br>";
        echo "<strong>Password:</strong> admin123</p>";
        echo "<p><a href='user/login.php' style='background:#0b5345;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;'>→ Login Sekarang</a></p>";
        echo "<p style='color:red'><strong>⚠️ PENTING: Hapus file setup.php setelah selesai!</strong></p>";
    } else {
        echo "<p style='color:blue'>ℹ️ Database sudah ada. User sudah tersedia.</p>";
        echo "<p><a href='user/login.php'>→ Ke Halaman Login</a></p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
?>