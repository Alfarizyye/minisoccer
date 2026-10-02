<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Soccer Booking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <!-- Penanda Update Responsif -->
    <div class="h-1 bg-emerald-600 w-full fixed top-0 z-[60]"></div>

    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="flex-shrink-0 flex items-center">
                        <span class="font-bold text-xl sm:text-2xl text-emerald-600">⚽ MiniSoccer</span>
                    </a>
                </div>
                <!-- Desktop Menu -->
                <div class="hidden sm:flex items-center">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="user/dashboard.php" class="text-slate-600 hover:text-emerald-600 px-3 py-2 rounded-md font-medium transition">Dashboard</a>
                        <a href="auth/logout.php" class="ml-4 bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg font-medium transition">Logout</a>
                    <?php else: ?>
                        <a href="auth/login.php" class="text-slate-600 hover:text-emerald-600 px-3 py-2 rounded-md font-medium transition">Masuk</a>
                        <a href="auth/register.php" class="ml-4 bg-emerald-600 text-white hover:bg-emerald-700 px-5 py-2.5 rounded-lg font-medium transition shadow-sm hover:shadow-md">Daftar</a>
                    <?php endif; ?>
                </div>
                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500">
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div class="hidden sm:hidden bg-white border-t border-slate-100 shadow-lg" id="mobile-menu">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="user/dashboard.php" class="text-slate-600 hover:bg-slate-50 block px-3 py-3 rounded-md text-base font-semibold border-b border-slate-50">Dashboard</a>
                    <a href="auth/logout.php" class="text-red-600 hover:bg-red-50 block px-3 py-3 rounded-md text-base font-semibold">Logout</a>
                <?php else: ?>
                    <a href="auth/login.php" class="text-slate-600 hover:bg-slate-50 block px-3 py-3 rounded-md text-base font-semibold border-b border-slate-50">Masuk</a>
                    <div class="pt-2">
                        <a href="auth/register.php" class="bg-emerald-600 text-white text-center block px-3 py-3 rounded-xl text-base font-bold shadow-md">Daftar Sekarang</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 bg-white lg:max-w-2xl lg:w-full">
                <main class="mt-8 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="text-center lg:text-left">
                        <h1 class="text-3xl tracking-tight font-extrabold text-slate-900 sm:text-5xl md:text-6xl">
                            <span class="block">Sewa Lapangan</span>
                            <span class="block text-emerald-600 mt-1">Mini Soccer Impianmu</span>
                        </h1>
                        <p class="mt-3 text-base text-slate-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0 leading-relaxed">
                            Cek jadwal ketersediaan lapangan secara real-time dan pesan langsung dari perangkat Anda. Nikmati fasilitas terbaik untuk permainan terbaik Anda.
                        </p>
                        <div class="mt-8 sm:mt-10 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-xl shadow-lg">
                                <a href="auth/login.php" class="w-full flex items-center justify-center px-10 py-4 border border-transparent text-lg font-bold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 transition duration-300 ease-in-out transform hover:scale-105">
                                    Pesan Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 mt-10 lg:mt-0">
            <img class="h-64 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full shadow-inner" src="assets/images/hero.png" alt="Mini Soccer Field">
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-emerald-600 font-bold tracking-widest uppercase text-sm">Fasilitas Kami</h2>
                <p class="mt-3 text-3xl leading-tight font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                    Kenapa Memilih Kami?
                </p>
            </div>

            <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-10 text-center hover:shadow-xl transition duration-500 group">
                    <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-8 text-4xl group-hover:bg-emerald-600 group-hover:text-white transition duration-500">🌱</div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Rumput Sintetis</h3>
                    <p class="text-slate-500 leading-relaxed">Kualitas standar FIFA yang tebal dan empuk untuk kenyamanan maksimal saat bermain.</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-10 text-center hover:shadow-xl transition duration-500 group">
                    <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-8 text-4xl group-hover:bg-emerald-600 group-hover:text-white transition duration-500">💡</div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Lampu LED Terang</h3>
                    <p class="text-slate-500 leading-relaxed">Pencahayaan berstandar tinggi yang merata untuk pengalaman bermain malam hari yang optimal.</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-10 text-center hover:shadow-xl transition duration-500 group">
                    <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-8 text-4xl group-hover:bg-emerald-600 group-hover:text-white transition duration-500">🚿</div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Fasilitas Lengkap</h3>
                    <p class="text-slate-500 leading-relaxed">Ruang ganti bersih, shower area, kantin, dan area parkir luas yang aman.</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Footer -->
    <footer class="bg-slate-900 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-slate-400">&copy; 2026 MiniSoccer Booking System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
