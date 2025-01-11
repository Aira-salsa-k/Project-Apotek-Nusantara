<?php
include '../Koneksi/Config.php';
session_start();

// Pastikan hanya pasien yang sudah login yang bisa mengakses
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Pasien') {
    header("Location: ../Login.php");
    exit();
}

// Ambil ID konsultasi dari URL
if (isset($_GET['id_konsultasi'])) {
    $id_konsultasi = intval($_GET['id_konsultasi']);

    // Query untuk menghapus data konsultasi
    $sql_delete = "DELETE FROM konsultasi WHERE id_konsultasi = $id_konsultasi";

    if ($conn->query($sql_delete) === TRUE) {
        // Redirect ke halaman dashboard setelah berhasil menghapus
        header("Location: Dashboard_konsultasi.php?message=Data berhasil dihapus");
        exit();
    } else {
        die("Error menghapus data: " . $conn->error);
    }
} else {
    die("ID konsultasi tidak ditemukan.");
}
?>