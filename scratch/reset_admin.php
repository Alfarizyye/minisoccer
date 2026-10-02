<?php
require __DIR__ . '/../config/db.php';

$email = 'admin@minisoccer.com';
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$name = 'Administrator';

try {
    // Cek apakah user admin dengan email ini sudah ada
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Jika ada, update password dan pastikan rolenya admin
        $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin' WHERE email = ?");
        $stmt->execute([$hashed_password, $email]);
        echo "Akun admin diperbarui!<br>";
    } else {
        // Jika tidak ada, buat baru
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$name, $email, $hashed_password]);
        echo "Akun admin baru berhasil dibuat!<br>";
    }
    
    echo "<b>Email:</b> $email<br>";
    echo "<b>Password:</b> $password<br>";
    echo "Silakan coba login kembali.";

} catch (PDOException $e) {
    echo "Kesalahan: " . $e->getMessage();
}
?>

