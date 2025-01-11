<?php
include '../Koneksi/Config.php';
session_start();

// Pastikan hanya pasien yang sudah login yang bisa mengakses
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Pasien') {
    header("Location: ../Login.php");
    exit();
}

// Ambil data pasien yang sedang login
$username = $conn->real_escape_string($_SESSION['username']);

// Query untuk mendapatkan ID Pasien
$sql_pasien = "SELECT p.id_pasien, p.nama 
               FROM pasien p
               JOIN users u ON p.id_user = u.id_user 
               WHERE u.username = '$username'";
$result_pasien = $conn->query($sql_pasien);

if (!$result_pasien || $result_pasien->num_rows == 0) {
    die("Tidak dapat menemukan data pasien.");
}

$pasien = $result_pasien->fetch_assoc();
$id_pasien = $pasien['id_pasien'];

// Proses form konsultasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $tanggal_konsultasi = $_POST['tanggal_konsultasi'];
    $waktu_konsultasi = $_POST['waktuKonsultasi'];
    $id_dokter = $_POST['dokter'];
    $keluhan = $conn->real_escape_string($_POST['keluhan']);
    $riwayat_penyakit = $conn->real_escape_string($_POST['riwayat_penyakit'] ?? '');

    // Query untuk menyimpan konsultasi
    $sql_konsultasi = "INSERT INTO konsultasi (
        id_pasien, 
        tanggal_konsultasi, 
        id_dokter, 
        waktu_konsultasi, 
        nomor_antrian, 
        keluhan, 
        riwayat_penyakit,
        status_nomor_antrian,
        status_konsultasi
    ) VALUES (
        $id_pasien, 
        '$tanggal_konsultasi', 
        $id_dokter, 
        '$waktu_konsultasi', 
        0,  
        '$keluhan', 
        '$riwayat_penyakit',
        0,
        'Menunggu'
    )";

    if ($conn->query($sql_konsultasi)) {
        // Redirect ke halaman dashboard konsultasi dengan pesan sukses
        header("Location: Dashboard_konsultasi.php?success=1");
        exit();
    } else {
        $error_message = "Gagal mendaftar konsultasi: " . $conn->error;
    }
}

// Query untuk mengambil dokter dengan spesialis
$sql_dokter = "SELECT d.id_dokter, d.nama, COALESCE(s.nama_spesialis, 'Umum') as nama_spesialis 
               FROM dokter d
               LEFT JOIN spesialis s ON d.id_dokter = s.id_dokter";
$result_dokter = $conn->query($sql_dokter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Konsultasi - Apotek Uniyap</title>
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../CSS/daftar_konsultasi">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <style>
        body, input, select, textarea, button {
            font-family: 'Poppins', sans-serif;
        }
    </style>
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
                    <a class="nav-link" href="Daftar_konsultasi.php">Daftar Konsultasi</a>
                </li> 
                <div class="d-flex ms-4"></div>
                <button class="btn btn-custom-green">
                    <i class="fas fa-sign-out-alt me-2"></i>LOGOUT
                </button>
            </ul>
        </div>
    </div>
</nav>
<!-- End navbar -->

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-12">
            <a href="Dashboard_konsultasi.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard Konsultasi
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3 class="card-title mb-4">Form Pendaftaran Konsultasi</h3>
                    
                    <?php 
                    // Tampilkan pesan error jika ada
                    if (isset($error_message)): 
                    ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <!-- Tanggal Konsultasi -->
                                    <div class="mb-4">
                                        <label class="form-label">Tanggal Konsultasi</label>
                                        <input type="date" name="tanggal_konsultasi" class="form-control" required>
                                    </div>

                                    <!-- Waktu Konsultasi -->
                                    <div class="mb-4">
                                        <label class="form-label">Waktu Konsultasi</label>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="waktuKonsultasi" value="08:00" id="pagi" required>
                                                    <label class="form-check-label" for="pagi">
                                                        08:00 - 10:00 WIT
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="waktuKonsultasi" value="13:00" id="siang">
                                                    <label class="form-check-label" for="siang">
                                                        13:00 - 16:00 WIT
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="waktuKonsultasi" value="19:00" id="sore">
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
                                        <select name="dokter" class="form-select" required>
                                            <option value="" selected disabled>Pilih dokter...</option>
                                            <?php while($dokter = $result_dokter->fetch_assoc()): ?>
                                                <option value="<?php echo $dokter['id_dokter']; ?>">
                                                    dr. <?php echo $dokter['nama'] . " (" . $dokter['nama_spesialis'] . ")"; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <!-- Keluhan -->
                                    <div class="mb-4">
                                        <label class="form-label">Keluhan</label>
                                        <textarea name="keluhan" class="form-control" rows="5" placeholder="Deskripsikan keluhan Anda secara detail..." required></textarea>
                                    </div>

                                    <!-- Riwayat Penyakit -->
                                    <div class="mb-4">
                                        <label class="form-label">Riwayat Penyakit</label>
                                        <textarea name="riwayat_penyakit" class="form-control" rows="5" placeholder="Tuliskan riwayat penyakit yang pernah/sedang Anda alami..."></textarea>
                                    </div>
                                </div>

                                <!-- Button Submit -->
                                <div class="col-12 text-end mt-3">
                                    <button type="submit" class="btn btn-custom-green px-5">
                                        <i class="fas fa-calendar-check me-2"></i>Daftar Konsultasi
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

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>