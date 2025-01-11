<?php
session_start();
include 'Koneksi/Config.php'; // Menghubungkan ke database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: konten.php"); // Arahkan ke halaman landing page jika belum login
    exit();
}

// Ambil role pengguna dari session
$role = $_SESSION['role'];

// Routing berdasarkan role
switch ($role) {
    case 'Admin':
        header("Location: Admin/Dashboard_admin.php");
        break;
    case 'Dokter':
        header("Location: Dokter/Dashboard_dokter.php");
        break;
    case 'Pasien':
        header("Location: Pasien/Dashboard_konsultasi.php"); // Halaman untuk pasien
        break;
    case 'Petugas':
        header("Location: Petugas/Dashboard_petugas.php"); // Halaman untuk petugas
        break;
    default:
        echo "Role tidak dikenali.";
        break;
}
?>
