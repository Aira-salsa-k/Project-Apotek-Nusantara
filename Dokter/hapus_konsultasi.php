<?php
session_start();
require_once('../Koneksi/Config.php');

// Pastikan hanya dokter yang sudah login yang bisa mengakses
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Dokter') {
    header("Location: ../Login.php");
    exit();
}

// Pastikan ada ID konsultasi yang diberikan
if (!isset($_GET['id'])) {
    header("Location: Dashboard_dokter.php");
    exit();
}

$id_konsultasi = $_GET['id'];
$id_dokter = $_SESSION['id_dokter'];

// Periksa apakah konsultasi milik dokter yang sedang login
$check_query = "SELECT id_konsultasi FROM konsultasi WHERE id_konsultasi = ? AND id_dokter = ?";
if ($stmt = $conn->prepare($check_query)) {
    $stmt->bind_param("ii", $id_konsultasi, $id_dokter);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Jika konsultasi tidak ditemukan atau bukan milik dokter ini
        header("Location: Dashboard_dokter.php");
        exit();
    }
    $stmt->close();
}

// Hapus konsultasi
$delete_query = "DELETE FROM konsultasi WHERE id_konsultasi = ? AND id_dokter = ?";
if ($stmt = $conn->prepare($delete_query)) {
    $stmt->bind_param("ii", $id_konsultasi, $id_dokter);
    
    if ($stmt->execute()) {
        // Berhasil menghapus
        header("Location: Dashboard_dokter.php?message=delete_success");
    } else {
        // Gagal menghapus
        header("Location: Dashboard_dokter.php?message=delete_error");
    }
    $stmt->close();
} else {
    // Error dalam query
    header("Location: Dashboard_dokter.php?message=query_error");
}
exit();
?>
