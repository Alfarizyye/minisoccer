<?php
// Cek apakah sedang berjalan di localhost atau server (InfinityFree)
$is_localhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || $_SERVER['HTTP_HOST'] == 'localhost';

if ($is_localhost) {
    // Konfigurasi Local (Laragon/XAMPP)
    $host = 'localhost';
    $dbname = 'minisoccer_db';
    $user = 'root';
    $pass = '';
} else {
    // Konfigurasi InfinityFree
    $host = 'sql200.infinityfree.com'; 
    $dbname = 'if0_41792898_minisoccer_db'; 
    $user = 'if0_41792898'; 
    $pass = 'MASUKKAN_PASSWORD_ANDA_DISINI'; 
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Jangan tampilkan detail error di produksi untuk keamanan
    if ($is_localhost) {
        die("Koneksi Database Gagal: " . $e->getMessage());
    } else {
        die("Koneksi Database Gagal. Silakan periksa konfigurasi database Anda.");
    }
}
?>

