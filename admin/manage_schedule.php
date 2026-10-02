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
    
    if ($action == 'approve') {
        $pdo->prepare("UPDATE bookings SET status = 'completed' WHERE id = ?")->execute([$id]);
        $msg = "Jadwal #$id ditandai selesai.";
    } else if ($action == 'cancel') {
        $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?")->execute([$id]);
        $msg = "Jadwal #$id dibatalkan.";
    } else if ($action == 'delete') {
        $pdo->prepare("DELETE FROM bookings WHERE id = ?")->execute([$id]);
        $msg = "Jadwal #$id dihapus permanen.";
    }
    // Prevent resubmission on refresh
    header("Location: manage_schedule.php" . (isset($msg) ? "?msg=".urlencode($msg) : ""));
    exit();
}

// Handle Manual Booking
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_manual'])) {
    $user_id = $_POST['user_id'];
    $field_id = $_POST['field_id'];
    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $status = $_POST['status']; // e.g., 'paid' or 'completed' for manual

    if (strtotime($start_time) >= strtotime($end_time)) {
        $error = "Waktu selesai harus setelah waktu mulai.";
    } else {
        // Check availability
        $check = $pdo->prepare("SELECT id FROM bookings WHERE field_id = ? AND booking_date = ? AND status != 'cancelled' AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?))");
        $check->execute([$field_id, $booking_date, $end_time, $start_time, $end_time, $start_time]);
        
        if ($check->rowCount() > 0) {
            $error = "Gagal! Lapangan sudah dipesan pada rentang waktu tersebut.";
        } else {
            // Get field price
            $stmt = $pdo->prepare("SELECT price_per_hour FROM fields WHERE id = ?");
            $stmt->execute([$field_id]);
            $field = $stmt->fetch();
            
            $start = strtotime($start_time);
            $end = strtotime($end_time);
            $hours = ($end - $start) / 3600;
            $total_price = $hours * $field['price_per_hour'];

            // Insert booking
            $insert = $pdo->prepare("INSERT INTO bookings (user_id, field_id, booking_date, start_time, end_time, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($insert->execute([$user_id, $field_id, $booking_date, $start_time, $end_time, $total_price, $status])) {
                header("Location: manage_schedule.php?msg=" . urlencode("Jadwal manual berhasil ditambahkan!"));
                exit();
            } else {
                $error = "Terjadi kesalahan sistem saat menyimpan data.";
            }
        }
    }
}

// Fetch all bookings
$stmt = $pdo->query("SELECT b.*, f.name as field_name, u.name as user_name FROM bookings b JOIN fields f ON b.field_id = f.id JOIN users u ON b.user_id = u.id ORDER BY b.booking_date DESC, b.start_time DESC");
$bookings = $stmt->fetchAll();

// Fetch fields and users for manual form
$fields = $pdo->query("SELECT * FROM fields")->fetchAll();
$users = $pdo->query("SELECT id, name, email FROM users ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Jadwal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100">
    <nav class="bg-slate-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="font-bold text-xl text-emerald-400">Admin Panel</span>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="manage_schedule.php" class="bg-slate-800 px-3 py-2 rounded-md font-medium text-sm">Kelola Jadwal</a>
                    <a href="manage_payment.php" class="text-slate-300 hover:text-white px-3 py-2 rounded-md font-medium text-sm transition">Kelola Pembayaran</a>
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
                <a href="manage_schedule.php" class="bg-slate-800 text-white block px-3 py-2 rounded-md text-base font-medium">Kelola Jadwal</a>
                <a href="manage_payment.php" class="text-slate-300 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Kelola Pembayaran</a>
                <a href="manage_users.php" class="text-slate-300 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Kelola User</a>
                <a href="../auth/logout.php" class="text-red-400 hover:text-red-300 block px-3 py-2 rounded-md text-base font-medium">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <?php if(isset($_GET['msg'])): ?>
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded shadow-sm">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Form Tambah Manual -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-8">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
                <span class="mr-2">➕</span> Tambah Jadwal Manual (Offline)
            </h3>
            <form method="POST" action="" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="add_manual" value="1">
                
                <div class="sm:col-span-2 md:col-span-1">
                    <label class="block text-slate-700 text-xs font-bold mb-1">Pilih Pelanggan</label>
                    <select name="user_id" required class="w-full px-3 py-2 rounded bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 outline-none text-sm">
                        <?php foreach($users as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-slate-700 text-xs font-bold mb-1">Pilih Lapangan</label>
                    <select name="field_id" required class="w-full px-3 py-2 rounded bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 outline-none text-sm">
                        <?php foreach($fields as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-slate-700 text-xs font-bold mb-1">Tanggal</label>
                    <input type="date" name="booking_date" required class="w-full px-3 py-2 rounded bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 outline-none text-sm">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1">Mulai</label>
                        <select name="start_time" required class="w-full px-2 py-2 rounded bg-slate-50 border border-slate-200 outline-none text-sm">
                            <?php for($i=6; $i<=23; $i++) echo "<option value='".sprintf("%02d:00", $i)."'>".sprintf("%02d:00", $i)."</option>"; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1">Selesai</label>
                        <select name="end_time" required class="w-full px-2 py-2 rounded bg-slate-50 border border-slate-200 outline-none text-sm">
                            <?php for($i=7; $i<=24; $i++) echo "<option value='".sprintf("%02d:00", $i==24?0:$i)."'>".sprintf("%02d:00", $i==24?0:$i)."</option>"; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 text-xs font-bold mb-1">Status Pembayaran</label>
                    <select name="status" required class="w-full px-3 py-2 rounded bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 outline-none text-sm">
                        <option value="completed">Lunas (Selesai)</option>
                        <option value="pending">Belum Lunas (Pending)</option>
                    </select>
                </div>

                <div class="sm:col-span-2 md:col-span-3">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 px-6 rounded shadow-sm transition w-full md:w-auto">
                        Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>

        <h2 class="text-2xl font-bold text-slate-800 mb-4">Semua Jadwal</h2>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemesan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Lapangan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu Main</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php if(count($bookings) > 0): ?>
                            <?php foreach ($bookings as $b): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">#<?= $b['id'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-900"><?= htmlspecialchars($b['user_name']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"><?= htmlspecialchars($b['field_name']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        <?= date('d M Y', strtotime($b['booking_date'])) ?><br>
                                        <span class="font-medium text-slate-700"><?= date('H:i', strtotime($b['start_time'])) ?> - <?= date('H:i', strtotime($b['end_time'])) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                            $status_class = 'bg-yellow-100 text-yellow-800'; 
                                            if ($b['status'] == 'paid') $status_class = 'bg-blue-100 text-blue-800';
                                            if ($b['status'] == 'completed') $status_class = 'bg-emerald-100 text-emerald-800';
                                            if ($b['status'] == 'cancelled') $status_class = 'bg-red-100 text-red-800';
                                        ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full <?= $status_class ?>">
                                            <?= ucfirst($b['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <?php if ($b['status'] == 'paid'): ?>
                                            <a href="?action=approve&id=<?= $b['id'] ?>" class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 px-3 py-1 rounded-md transition" title="Tandai Selesai">Selesai</a>
                                        <?php endif; ?>
                                        <?php if ($b['status'] == 'pending' || $b['status'] == 'paid'): ?>
                                            <a href="?action=cancel&id=<?= $b['id'] ?>" class="text-orange-600 hover:text-orange-900 bg-orange-50 px-3 py-1 rounded-md transition" onclick="return confirm('Batalkan booking ini?')">Batal</a>
                                        <?php endif; ?>
                                        <a href="?action=delete&id=<?= $b['id'] ?>" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-md transition" onclick="return confirm('Hapus permanen jadwal ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">Tidak ada jadwal ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
