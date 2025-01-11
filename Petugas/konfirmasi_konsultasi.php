<?php
include '../Koneksi/Config.php';
session_start();

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Set header JSON
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method tidak diizinkan');
    }
    
    if (!isset($_POST['id_konsultasi'])) {
        throw new Exception('ID Konsultasi tidak ditemukan');
    }
    
    $id_konsultasi = $_POST['id_konsultasi'];
    if (!is_numeric($id_konsultasi)) {
        throw new Exception('ID Konsultasi tidak valid');
    }
    
    // Debug
    error_log("Processing konsultasi ID: " . $id_konsultasi);
    
    // Cek koneksi database
    if ($conn->connect_error) {
        throw new Exception('Koneksi database gagal: ' . $conn->connect_error);
    }
    
    // Cek apakah konsultasi ada dan statusnya Menunggu
    $check_query = "SELECT status_konsultasi, tanggal_konsultasi, waktu_konsultasi, 
                   CASE 
                       WHEN TIME(waktu_konsultasi) BETWEEN '06:00:00' AND '11:59:59' THEN 'P'
                       WHEN TIME(waktu_konsultasi) BETWEEN '12:00:00' AND '17:59:59' THEN 'S'
                       WHEN TIME(waktu_konsultasi) BETWEEN '18:00:00' AND '23:59:59' 
                            OR TIME(waktu_konsultasi) BETWEEN '00:00:00' AND '05:59:59' THEN 'M'
                   END as sesi
                   FROM konsultasi WHERE id_konsultasi = ?";
    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        throw new Exception('Prepare statement error: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $id_konsultasi);
    if (!$stmt->execute()) {
        throw new Exception('Execute error: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('Konsultasi tidak ditemukan');
    }
    
    $konsultasi = $result->fetch_assoc();
    if ($konsultasi['status_konsultasi'] !== 'Menunggu') {
        throw new Exception('Konsultasi sudah dikonfirmasi sebelumnya');
    }
    
    $tanggal_konsultasi = $konsultasi['tanggal_konsultasi'];
    $waktu_konsultasi = $konsultasi['waktu_konsultasi'];
    $prefix = $konsultasi['sesi'];
    
    error_log("Tanggal konsultasi: " . $tanggal_konsultasi);
    error_log("Waktu konsultasi: " . $waktu_konsultasi);
    error_log("Prefix: " . $prefix);
    
    if (empty($prefix)) {
        throw new Exception('Tidak bisa menentukan sesi untuk waktu: ' . $waktu_konsultasi);
    }
    
    // Cek nomor antrian terakhir untuk tanggal dan sesi yang sama
    $query = "SELECT COALESCE(MAX(CAST(SUBSTRING(nomor_antrian, 3) AS UNSIGNED)), 0) as last_number 
              FROM konsultasi 
              WHERE tanggal_konsultasi = ? 
              AND nomor_antrian LIKE CONCAT(?, '-%')";
              
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare statement error: ' . $conn->error);
    }
    
    $stmt->bind_param("ss", $tanggal_konsultasi, $prefix);
    if (!$stmt->execute()) {
        throw new Exception('Execute error: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $next_number = $row['last_number'] + 1;
    
    error_log("Nomor terakhir: " . $row['last_number']);
    error_log("Nomor berikutnya: " . $next_number);
    
    // Format nomor antrian dengan prefix
    $formatted_number = $prefix . '-' . $next_number;
    
    error_log("Nomor antrian yang akan digunakan: " . $formatted_number);
    
    // Update status dan nomor antrian
    $update_query = "UPDATE konsultasi 
                    SET status_konsultasi = 'On-Progress', 
                        nomor_antrian = ?,
                        status_nomor_antrian = 1
                    WHERE id_konsultasi = ?";
    $stmt = $conn->prepare($update_query);
    if (!$stmt) {
        throw new Exception('Prepare statement error: ' . $conn->error);
    }
    
    $stmt->bind_param("si", $formatted_number, $id_konsultasi);
    if (!$stmt->execute()) {
        throw new Exception('Execute error: ' . $stmt->error);
    }
    
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Konsultasi berhasil dikonfirmasi',
            'nomor_antrian' => $formatted_number
        ]);
    } else {
        throw new Exception('Tidak ada data yang diupdate');
    }
    
} catch (Exception $e) {
    error_log("Error in konfirmasi_konsultasi.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
