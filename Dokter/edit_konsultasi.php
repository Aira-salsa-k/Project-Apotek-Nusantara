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
$error_message = '';
$success_message = '';

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal_konsultasi'];
    $waktu = $_POST['waktu_konsultasi'];
    $keluhan = $_POST['keluhan'];
    $riwayat = $_POST['riwayat_penyakit'];
    
    $query = "UPDATE konsultasi 
             SET tanggal_konsultasi = ?, 
                 waktu_konsultasi = ?,
                 keluhan = ?,
                 riwayat_penyakit = ?
             WHERE id_konsultasi = ? AND id_dokter = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssssii", $tanggal, $waktu, $keluhan, $riwayat, $id_konsultasi, $_SESSION['id_dokter']);
        
        if ($stmt->execute()) {
            $success_message = "Data konsultasi berhasil diperbarui!";
        } else {
            $error_message = "Gagal memperbarui data: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_message = "Error dalam menyiapkan query: " . $conn->error;
    }
}

// Ambil data konsultasi
$query = "SELECT k.*, p.nama AS nama_pasien
          FROM konsultasi k
          JOIN pasien p ON k.id_pasien = p.id_pasien
          WHERE k.id_konsultasi = ? AND k.id_dokter = ?";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("ii", $id_konsultasi, $_SESSION['id_dokter']);
    $stmt->execute();
    $result = $stmt->get_result();
    $konsultasi = $result->fetch_assoc();
    $stmt->close();

    if (!$konsultasi) {
        header("Location: Dashboard_dokter.php");
        exit();
    }
} else {
    header("Location: Dashboard_dokter.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Konsultasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Edit Konsultasi</h4>
            </div>
            <div class="card-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Pasien</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($konsultasi['nama_pasien']); ?>" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Konsultasi</label>
                            <input type="date" name="tanggal_konsultasi" class="form-control" 
                                   value="<?php echo $konsultasi['tanggal_konsultasi']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu Konsultasi</label>
                            <input type="time" name="waktu_konsultasi" class="form-control" 
                                   value="<?php echo $konsultasi['waktu_konsultasi']; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keluhan</label>
                        <textarea name="keluhan" class="form-control" rows="3" required><?php echo htmlspecialchars($konsultasi['keluhan']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Riwayat Penyakit</label>
                        <textarea name="riwayat_penyakit" class="form-control" rows="3" required><?php echo htmlspecialchars($konsultasi['riwayat_penyakit']); ?></textarea>
                    </div>

                    <div class="mt-4">
                        <a href="Dashboard_dokter.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
