<?php
// File ini akan generate akun dengan hash yang 100% cocok dengan PHP server Anda
require 'config/db.php';

echo "<h1>🔑 Generate Akun Fresh</h1>";

// Password yang mau dipakai
$password = 'admin123';

// Generate hash FRESH di server ini (PASTI COCOK)
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<p><strong>Password:</strong> $password</p>";
echo "<p><strong>Hash Generated:</strong> <code>$hash</code></p>";
echo "<hr>";

try {
    // Hapus user lama
    $pdo->exec("DELETE FROM users WHERE username IN ('superadmin', 'staff_keuangan', 'staff_penghimpunan', 'staff_pendayagunaan', 'staff_media')");
    
    // Insert user baru dengan hash yang FRESH
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role, division, is_active) VALUES (?, ?, ?, ?, 1)");
    
    $users = [
        ['superadmin', 'super_admin', 'all'],
        ['staff_keuangan', 'staff', 'keuangan'],
        ['staff_penghimpunan', 'staff', 'penghimpunan'],
        ['staff_pendayagunaan', 'staff', 'pendayagunaan'],
        ['staff_media', 'staff', 'media'],
    ];
    
    foreach ($users as $u) {
        $stmt->execute([$u[0], $hash, $u[1], $u[2]]);
        echo "✅ Created: <strong>{$u[0]}</strong><br>";
    }
    
    echo "<hr>";
    echo "<h3 style='color:green'>✅ SEMUA AKUN BERHASIL DIBUAT!</h3>";
    echo "<p><strong>Username:</strong> superadmin<br>";
    echo "<strong>Password:</strong> admin123</p>";
    
    // TEST LANGSUNG
    echo "<hr>";
    echo "<h3>🧪 Testing Password Verify:</h3>";
    
    $testStmt = $pdo->query("SELECT password_hash FROM users WHERE username='superadmin' LIMIT 1");
    $testHash = $testStmt->fetchColumn();
    
    $verify = password_verify('admin123', $testHash);
    
    if ($verify) {
        echo "<p style='color:green; font-size:1.2em; font-weight:bold;'>✅ PASSWORD_VERIFY BERHASIL! Login pasti tembus!</p>";
        echo "<p><a href='user/login.php' style='background:#0b5345;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;'>→ LOGIN SEKARANG</a></p>";
    } else {
        echo "<p style='color:red'>❌ Password verify gagal. Ini aneh banget.</p>";
    }
    
    echo "<p style='color:red'><strong>⚠️ HAPUS FILE INI SETELAH SELESAI!</strong></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
?>