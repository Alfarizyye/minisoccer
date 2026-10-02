<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    $passwordConfirmation = isset($_POST['password_confirmation']) && is_string($_POST['password_confirmation'])
        ? $_POST['password_confirmation']
        : '';
    $csrfToken = isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])
        ? $_POST['csrf_token']
        : '';

    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $error = 'Permintaan tidak valid. Silakan muat ulang halaman.';
    } elseif (!$userId) {
        $error = 'Pilih akun user yang akan direset.';
    } elseif (strlen($password) < 8) {
        $error = 'Password sementara harus minimal 8 karakter.';
    } elseif ($password !== $passwordConfirmation) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'user'");
        $stmt->execute([$userId]);

        if (!$stmt->fetch()) {
            $error = 'Akun user tidak ditemukan.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ? AND role = 'user'");
            $update->execute([$passwordHash, $userId]);
            $success = 'Password sementara berhasil diatur. Sampaikan password sementara kepada user secara pribadi.';
        }
    }
}

$users = $pdo->query("SELECT id, name, email FROM users WHERE role = 'user' ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen">
    <nav class="bg-slate-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <span class="font-bold text-xl text-emerald-400">Admin Panel</span>
                <div class="flex items-center gap-4">
                    <a href="manage_schedule.php" class="text-slate-300 hover:text-white text-sm">Kelola Jadwal</a>
                    <a href="manage_payment.php" class="text-slate-300 hover:text-white text-sm">Kelola Pembayaran</a>
                    <a href="manage_users.php" class="text-white text-sm font-semibold">Kelola User</a>
                    <a href="../auth/logout.php" class="text-red-400 hover:text-red-300 text-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-xl mx-auto px-4 py-10">
        <section class="bg-white rounded-xl shadow-sm p-6">
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Reset Password User</h1>
            <p class="text-slate-600 mb-6">Buat password sementara, lalu sampaikan langsung kepada user. Jangan kirim melalui tempat publik.</p>

            <?php if ($error !== null): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-5 rounded" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if ($success !== null): ?>
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-5 rounded" role="status">
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if (!$users): ?>
                <p class="text-slate-600">Belum ada akun user terdaftar.</p>
            <?php else: ?>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                    <label for="user_id" class="block text-sm font-semibold text-slate-700 mb-2">Akun user</label>
                    <select id="user_id" name="user_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 mb-4">
                        <option value="">Pilih user</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= (int) $user['id'] ?>" <?= (string) ($_POST['user_id'] ?? '') === (string) $user['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['name'] . ' (' . $user['email'] . ')', ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password sementara</label>
                    <input id="password" type="password" name="password" minlength="8" required class="w-full rounded-lg border border-slate-300 px-3 py-2 mb-4">

                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Ulangi password sementara</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" minlength="8" required class="w-full rounded-lg border border-slate-300 px-3 py-2 mb-5">

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg px-4 py-3">
                        Atur Password Sementara
                    </button>
                </form>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
