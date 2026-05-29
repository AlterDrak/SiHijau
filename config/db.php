<?php
// config/db.php
$host = 'localhost';
$db   = 'lazpersis_db';
$user = 'root';
$pass = ''; // Sesuaikan jika ada password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    // Pastikan $pdo bisa diakses global
    if (!isset($GLOBALS['pdo'])) {
        $GLOBALS['pdo'] = $pdo;
    }
} catch (PDOException $e) {
    // Untuk debugging, matikan komentar baris bawah saat development
    // die("Koneksi gagal: " . $e->getMessage());
    
    // Untuk produksi, gunakan ini:
    error_log("DB Error: " . $e->getMessage());
    exit("Koneksi database gagal.");
}
?>