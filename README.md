# ⚽ Mini Soccer Booking System

Sistem manajemen pemesanan lapangan mini soccer berbasis web yang dirancang untuk memudahkan pengguna dalam mengecek ketersediaan jadwal, melakukan reservasi secara real-time, dan mengelola pembayaran. Dilengkapi dengan fitur komunitas untuk mencari teman bermain (Mabar).

## 🚀 Fitur Utama

- **Sistem Reservasi Real-time**: Pengguna dapat melihat jadwal yang tersedia dan memesan langsung tanpa harus menghubungi admin secara manual.
- **Validasi Bentrok Jadwal**: Mencegah terjadinya double-booking pada waktu dan lapangan yang sama.
- **Manajemen Pembayaran**: Fitur unggah bukti transfer untuk verifikasi pembayaran oleh admin.
- **Fitur Mabar (Main Bareng)**: Komunitas kecil untuk pengguna yang kekurangan pemain dan ingin mengajak orang lain bergabung.
- **Dashboard Admin**: Panel kontrol untuk mengelola lapangan, memverifikasi pembayaran, dan memantau seluruh reservasi.
- **Desain Responsif**: Antarmuka modern yang optimal di perangkat mobile maupun desktop menggunakan Tailwind CSS.

## 🛠️ Teknologi yang Digunakan

- **Frontend**: HTML5, Tailwind CSS, JavaScript
- **Backend**: Native PHP 8.x
- **Database**: MySQL (MariaDB)
- **Library**: 
  - [Tailwind CSS](https://tailwindcss.com/) (Styling)
  - [Google Fonts](https://fonts.google.com/) (Typography)
  - [PDO](https://www.php.net/manual/en/book.pdo.php) (Database Security)

## 📦 Instalasi

1. **Clone repositori**:
   ```bash
   git clone https://github.com/username/booking-minisoccer.git
   ```
2. **Konfigurasi Database**:
   - Buat database baru bernama `minisoccer_db` di phpMyAdmin atau MySQL.
   - Import file `database.sql` yang tersedia di root folder.
3. **Konfigurasi Koneksi**:
   - Buka file `config/db.php`.
   - Sesuaikan `host`, `user`, dan `pass` dengan konfigurasi server lokal Anda (default Laragon/XAMPP: user=`root`, pass=``).
4. **Jalankan Aplikasi**:
   - Pindahkan folder project ke direktori `www` (Laragon) atau `htdocs` (XAMPP).
   - Akses melalui browser: `http://localhost/booking-minisoccer`.
5. **Reset Password**:
   - Jika user lupa password, arahkan ke admin untuk meminta password sementara.
   - Admin login lalu buka menu **Kelola User**, pilih akun, buat password sementara minimal 8 karakter, lalu sampaikan secara pribadi kepada user.

### 🔐 Akun Akses Default
Untuk keperluan pengujian, gunakan akun berikut:

| Role | Email | Password |
|:---|:---|:---|
| **Admin** | `admin@gmail.com` | `admin123` |
| **User** | *(Silakan daftar melalui menu Registrasi)* | - |

---

## 🧪 Pengujian Kualitas Aplikasi

Berikut adalah hasil pengujian aplikasi berdasarkan berbagai aspek kualitas perangkat lunak:

| No | Aspek Kualitas | Skenario Pengujian | Hasil yang Diharapkan | Status |
|:---|:---|:---|:---|:---:|
| **1** | **Fungsionalitas** | Registrasi & Login pengguna baru | Pengguna berhasil mendaftar dan masuk ke dashboard | ✅ Sukses |
| | | Pemesanan lapangan pada jam yang sama | Sistem menolak reservasi jika jadwal sudah terisi (Bentrok) | ✅ Sukses |
| | | Unggah bukti pembayaran | Admin menerima notifikasi/data bukti pembayaran untuk verifikasi | ✅ Sukses |
| | | Fitur Cari Teman (Mabar) | Pengguna dapat membuat postingan ajakan main bersama | ✅ Sukses |
| **2** | **Usability** | Responsivitas Mobile | Tampilan menyesuaikan (Hamburger menu muncul, grid menjadi 1 kolom) | ✅ Sukses |
| | | Navigasi Antarmuka | Pengguna dapat berpindah menu tanpa hambatan dan link berfungsi | ✅ Sukses |
| | | Feedback Validasi | Muncul pesan error jika form diisi tidak lengkap atau salah | ✅ Sukses |
| **3** | **Security** | SQL Injection Prevention | Penggunaan PDO Prepared Statements pada seluruh query database | ✅ Sukses |
| | | Session Management | Halaman user/admin tidak bisa diakses tanpa login (Redirect) | ✅ Sukses |
| | | XSS Prevention | Data yang ditampilkan menggunakan `htmlspecialchars()` | ✅ Sukses |
| **4** | **Performance** | Page Load Time | Halaman utama termuat di bawah 2 detik pada koneksi standar | ✅ Sukses |
| | | Database Efficiency | Query menggunakan index primary key untuk kecepatan akses data | ✅ Sukses |
| **5** | **Maintainability**| Struktur Folder | Kode terpisah secara modular (auth, config, user, admin) | ✅ Sukses |

---

## 👥 Kontribusi

Kontribusi selalu terbuka! Silakan lakukan *fork* pada repositori ini dan kirimkan *pull request* untuk fitur-fitur baru atau perbaikan bug.

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
