<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    // Validate ID
    $stmt = $pdo->prepare("SELECT id, status FROM bookings WHERE id = ?");
    $stmt->execute([$id]);
    $booking = $stmt->fetch();
    
    if ($booking) {
        if ($action == 'approve') {
            $pdo->prepare("UPDATE bookings SET status = 'completed' WHERE id = ?")->execute([$id]);
            $msg = "Pembayaran untuk Booking #$id berhasil diverifikasi (Lunas).";
        } else if ($action == 'reject') {
            // Kita kembalikan statusnya ke pending agar user bisa bayar ulang, atau batalkan.
            // Di sini kita ubah ke pending, tapi hapus bukti pembayarannya.
            $pdo->prepare("UPDATE bookings SET status = 'pending' WHERE id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM payments WHERE booking_id = ?")->execute([$id]);
            $msg = "Pembayaran untuk Booking #$id ditolak. Status kembali ke Pending.";
        }
    }
}

// Get payments data joined with bookings, users, and fields
$query = "
    SELECT p.*, b.status, b.total_price, b.booking_date, b.start_time, b.end_time, 
           u.name as user_name, f.name as field_name
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    JOIN users u ON b.user_id = u.id
    JOIN fields f ON b.field_id = f.id
    ORDER BY p.payment_date DESC
";
$stmt = $pdo->query($query);
$payments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100">
    <!-- Navbar -->
    <nav class="bg-slate-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="font-bold text-xl text-emerald-400">Admin Panel</span>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="manage_schedule.php" class="text-slate-300 hover:text-white px-3 py-2 rounded-md font-medium text-sm transition">Kelola Jadwal</a>
                    <a href="manage_payment.php" class="bg-slate-800 px-3 py-2 rounded-md font-medium text-sm">Kelola Pembayaran</a>
                    <a href="manage_users.php" class="text-slate-300 hover:text-white px-3 py-2 rounded-md font-medium text-sm transition">Kelola User</a>
                    <a href="../auth/logout.php" class="text-red-400 hover:text-red-300 px-3 py-2 rounded-md font-medium text-sm transition ml-4 border-l border-slate-700 pl-4">Logout</a>
                </div>
                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500">
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div class="hidden md:hidden bg-slate-900 border-t border-slate-800" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="manage_schedule.php" class="text-slate-300 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Kelola Jadwal</a>
                <a href="manage_payment.php" class="bg-slate-800 text-white block px-3 py-2 rounded-md text-base font-medium">Kelola Pembayaran</a>
                <a href="manage_users.php" class="text-slate-300 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Kelola User</a>
                <a href="../auth/logout.php" class="text-red-400 hover:text-red-300 block px-3 py-2 rounded-md text-base font-medium">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Verifikasi Pembayaran</h2>
                <p class="text-slate-500 mt-1">Cek dan verifikasi bukti transfer yang diunggah oleh pelanggan.</p>
            </div>
        </div>

        <?php if(isset($msg)): ?>
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded shadow-sm">
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Booking ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Tagihan</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Bukti Transfer</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu Upload</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php if (count($payments) > 0): ?>
                            <?php foreach ($payments as $p): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">
                                        #<?= str_pad($p['booking_id'], 4, '0', STR_PAD_LEFT) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-900"><?= htmlspecialchars($p['user_name']) ?></div>
                                        <div class="text-xs text-slate-500"><?= htmlspecialchars($p['field_name']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                        Rp <?= number_format($p['total_price'], 0, ',', '.') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="../assets/uploads/<?= htmlspecialchars($p['proof_image']) ?>" target="_blank" class="inline-block relative group">
                                            <img src="../assets/uploads/<?= htmlspecialchars($p['proof_image']) ?>" alt="Bukti" class="h-12 w-12 object-cover rounded shadow-sm border border-slate-200">
                                            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition rounded">
                                                <span class="text-white text-xs font-bold">Lihat</span>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        <?= date('d M Y, H:i', strtotime($p['payment_date'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                            $status_class = 'bg-yellow-100 text-yellow-800'; 
                                            if ($p['status'] == 'paid') $status_class = 'bg-blue-100 text-blue-800';
                                            if ($p['status'] == 'completed') $status_class = 'bg-emerald-100 text-emerald-800';
                                            if ($p['status'] == 'cancelled') $status_class = 'bg-red-100 text-red-800';
                                        ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full <?= $status_class ?>">
                                            <?= ucfirst($p['status'] == 'paid' ? 'Perlu Verifikasi' : $p['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <?php if ($p['status'] == 'paid' || $p['status'] == 'pending'): ?>
                                            <a href="?action=approve&id=<?= $p['booking_id'] ?>" class="text-white bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 rounded shadow-sm transition" onclick="return confirm('Verifikasi pembayaran ini dan tandai Lunas?')">Terima</a>
                                            <a href="?action=reject&id=<?= $p['booking_id'] ?>" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded shadow-sm transition" onclick="return confirm('Tolak bukti pembayaran ini?')">Tolak</a>
                                        <?php else: ?>
                                            <span class="text-slate-400 italic text-xs">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    Belum ada data pembayaran yang diunggah.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
