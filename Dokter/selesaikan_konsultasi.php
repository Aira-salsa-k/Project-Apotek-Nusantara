<?php
include '../Koneksi/Config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Dokter') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_konsultasi'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit();
}

try {
    $id_konsultasi = intval($_POST['id_konsultasi']);
    
    // Mulai transaksi
    $conn->begin_transaction();
    
    // Update status konsultasi menjadi Selesai
    $sql_update = "UPDATE konsultasi 
                   SET status_konsultasi = 'Selesai'
                   WHERE id_konsultasi = ? 
                   AND status_konsultasi = 'On-Progress'";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("i", $id_konsultasi);
    
    if (!$stmt->execute()) {
        throw new Exception("Gagal mengupdate status konsultasi");
    }
    
    if ($stmt->affected_rows === 0) {
        throw new Exception("Konsultasi tidak ditemukan atau sudah selesai");
    }
    
    // Commit transaksi
    $conn->commit();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Konsultasi berhasil diselesaikan'
    ]);
    
} catch (Exception $e) {
    // Rollback jika terjadi error
    $conn->rollback();
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
