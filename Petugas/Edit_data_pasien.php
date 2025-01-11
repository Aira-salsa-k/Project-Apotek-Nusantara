<?php
include '../Koneksi/Config.php';
session_start();

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan hanya pasien atau petugas yang sudah login yang bisa mengakses
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'Pasien' && $_SESSION['role'] !== 'Petugas')) {
    header("Location: ../Login.php");
    exit();
}

// Ambil ID konsultasi dari URL
$id_konsultasi = $_GET['id_konsultasi'];

// Query untuk mengambil data konsultasi berdasarkan id_konsultasi
$sql_konsultasi = "SELECT * FROM konsultasi WHERE id_konsultasi = $id_konsultasi";
$result_konsultasi = $conn->query($sql_konsultasi);

// Tambahkan pengecekan error
if (!$result_konsultasi) {
    die("Error query konsultasi: " . $conn->error);
}

// Periksa apakah ada hasil
if ($result_konsultasi->num_rows == 0) {
    die("Konsultasi tidak ditemukan.");
}

// Ambil data konsultasi
$konsultasi = $result_konsultasi->fetch_assoc();

// Query untuk mengambil dokter dengan spesialis
$sql_dokter = "SELECT d.id_dokter, d.nama, COALESCE(s.nama_spesialis, 'Umum') as nama_spesialis 
               FROM dokter d
               LEFT JOIN spesialis s ON d.id_dokter = s.id_dokter";
$result_dokter = $conn->query($sql_dokter);

// Proses pengeditan data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_konsultasi = $_POST['tanggal_konsultasi'];
    $waktu_konsultasi = $_POST['waktu_konsultasi'];
    $id_dokter = $_POST['id_dokter'];
    $keluhan = $_POST['keluhan'];
    $riwayat_penyakit = $_POST['riwayat_penyakit'];

    // Update data konsultasi
    $sql_update = "UPDATE konsultasi SET 
        tanggal_konsultasi = '$tanggal_konsultasi', 
        waktu_konsultasi = '$waktu_konsultasi', 
        id_dokter = $id_dokter, 
        keluhan = '$keluhan', 
        riwayat_penyakit = '$riwayat_penyakit' 
        WHERE id_konsultasi = $id_konsultasi";

    if ($conn->query($sql_update) === TRUE) {
        echo "<script>alert('Data konsultasi berhasil diperbarui!'); window.location.href='Dashboard_konsultasi.php';</script>";
    } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
    }
    
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit data Konsultasi</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="../CSS/navbar.css">
   <link rel="stylesheet" href="../CSS/daftar_konsultasi.css">
    <!-- Import Noto Sans dengan berbagai weight -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- ... kode lainnya ... -->
</head>
<body>
<!-- navbar -->
   <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Apotek <span class="brand-orange">Nusant<span class="strikethrough-a">a</span>ra</span>.</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="#">Daftar Konsultasi</a>
                    </li> 
                <div class="d-flex ms-4"></div>
                <button class="btn btn-custom-green">
                    <i class="fas fa-sign-out-alt me-2"></i>LOGOUT
                </button>
            </div>
                </ul>
            </div>
        </div>
    </nav>
<!-- End navbar -->

<!-- section daftar konsultasi -->

<div class="container mt-5"></div>
<?php
// Menentukan URL kembali berdasarkan role
$backUrl = ($_SESSION['role'] === 'Pasien') ? 'Dashboard_konsultasi.php' : '../Dashboard_petugas.php';
?>
<a href="<?php echo $backUrl; ?>" class="btn btn-custom-orange">
    <i class="fas fa-arrow-left me-2"></i>Kembali
</a>
<div class="row justify-content-center">
    <div class="col-lg-10"> <!-- Memperlebar container -->
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="card-title mb-4">Form Pendaftaran Konsultasi</h3>
                
                <form method="POST">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6"> <!-- Tambahkan padding end untuk gap -->
                            <!-- Tanggal Konsultasi -->
                            <div class="mb-4">
                                <label class="form-label">Tanggal Konsultasi</label>
                                <input type="date" name="tanggal_konsultasi" class="form-control" value="<?php echo $konsultasi['tanggal_konsultasi']; ?>" required>
                            </div>

                            <!-- Waktu Konsultasi -->
                           <!-- Waktu Konsultasi -->
                            <div class="mb-4">
                                <label class="form-label">Waktu Konsultasi</label>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="waktu_konsultasi" id="pagi" value="08:00 - 10:00" <?php echo ($konsultasi['waktu_konsultasi'] == '08:00:00') ? 'checked' : ''; ?> required>
                                            <label class="form-check-label" for="pagi">
                                                08:00 - 10:00 WIT
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="waktu_konsultasi" id="siang" value="13:00 - 16:00" <?php echo ($konsultasi['waktu_konsultasi'] == '13:00:00') ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="siang">
                                                13:00 - 16:00 WIT
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="waktu_konsultasi" id="sore" value="19:00 - 21:00" <?php echo ($konsultasi['waktu_konsultasi'] == '19:00:00') ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="sore">
                                                19:00 - 21:00 WIT
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pilih Dokter -->
                            <div class="mb-4">
                                <label class="form-label">Pilih Dokter</label>
                                <select name="id_dokter" class="form-select" required>
                                    <option value="" selected disabled>Pilih dokter...</option>
                                    <?php while($dokter = $result_dokter->fetch_assoc()): ?>
                                        <option value="<?php echo $dokter['id_dokter']; ?>" <?php echo ($konsultasi['id_dokter'] == $dokter['id_dokter']) ? 'selected' : ''; ?>>
                                            dr. <?php echo $dokter['nama'] . " (" . $dokter['nama_spesialis'] . ")"; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                                <div class="col-md-6">
                                    <!-- Keluhan -->
                                    <div class="mb-4">
                                        <label class="form-label">Keluhan</label>
                                        <textarea name="keluhan" class="form-control" rows="5" placeholder="Deskripsikan keluhan Anda secara detail..." required><?php echo htmlspecialchars($konsultasi['keluhan']); ?></textarea>
                                    </div>

                                    <!-- Riwayat Penyakit -->
                                    <div class="mb-4">
                                        <label class="form-label">Riwayat Penyakit</label>
                                        <textarea name="riwayat_penyakit" class="form-control" rows="5" placeholder="Tuliskan riwayat penyakit yang pernah/sedang Anda alami..."><?php echo htmlspecialchars($konsultasi['riwayat_penyakit']); ?></textarea>
                                    </div>
                                </div>
                                
                            <!-- Button Submit - Full Width -->
                            <div class="col-12 text-end mt-3">
                                <button type="submit" class="btn btn-custom-green px-5">
                                    <i class="fas fa-calendar-check me-2"></i>Edit Konsultasi
                                </button>
                            </div>
                     </div>       
                </div>
                </form>
                
            </div>
        </div>
    </div>
</div>
</div>
<!-- End section daftar konsultasi -->
<!-- End section daftar konsultasi -->

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
