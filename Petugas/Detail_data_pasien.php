<?php
include '../Koneksi/Config.php';
session_start();

// Pastikan pasien sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../Login.php");
    exit();
}

// Ambil ID konsultasi dari URL
$id_konsultasi = isset($_GET['id_konsultasi']) ? intval($_GET['id_konsultasi']) : 0;

// Query untuk mengambil detail konsultasi
$sql_detail = "SELECT k.*, p.nama as nama_pasien, p.umur, p.nomor_telepon, p.alamat, p.jenis_kelamin, 
                      d.nama as nama_dokter, COALESCE(s.nama_spesialis, 'Umum') as nama_spesialis, 
                      s.nomor_lisensi 
               FROM konsultasi k
               JOIN pasien p ON k.id_pasien = p.id_pasien
               JOIN dokter d ON k.id_dokter = d.id_dokter
               LEFT JOIN spesialis s ON d.id_dokter = s.id_dokter
               WHERE k.id_konsultasi = $id_konsultasi";

$result_detail = $conn->query($sql_detail);


// Periksa apakah data ditemukan
if ($result_detail->num_rows == 0) {
    die("Data tidak ditemukan.");
}

$detail = $result_detail->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Hasil Konsultasi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/Detail_data_konsultasi.css">
    <!-- Import Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
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

<!-- section detail hasil konsultasi -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Card Pertama - Identitas & Dokter -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0" style="color: #4CAF50;">Detail Hasil Konsultasi</h4>
                        <button class="btn btn-custom-orange">
                            
                            <a href="Dashboard_konsultasi.php" class="fas fa-arrow-left me-2" style = " text-decoration: none; color : white;">Kembali</a>
                        </button>
                    </div>
                    
                    <div class="row">
                        <!-- Kolom Kiri - Identitas Pribadi -->
                        <div class="col-md-6 pe-md-4">
                            <h5 class="mb-4">Identitas Pribadi</h5>
                            
                            <div class="mb-3">
                                <label class="mb-1">Nama Lengkap</label>
                                <p class="fw-medium"><?php echo $detail['nama_pasien']; ?></p>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Jenis Kelamin</label>
                                <p class="fw-medium"><?php echo $detail['jenis_kelamin']; ?></p>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Umur</label>
                                <p class="fw-medium"><?php echo $detail['umur']; ?> Tahun</p>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Alamat</label>
                                <p class="fw-medium"><?php echo $detail['alamat']; ?></p>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">No. HP</label>
                                <p class="fw-medium"><?php echo $detail['nomor_telepon']; ?></p>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Keluhan</label>
                                <p class="fw-medium"><?php echo $detail['keluhan']; ?></p>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Riwayat Penyakit</label>
                                <p class="fw-medium"><?php echo $detail['riwayat_penyakit']; ?></p>
                            </div>
                        </div>

                        <!-- Kolom Kanan - Informasi Dokter -->
                        <div class="col-md-6 ps-md-4 border-start">
                            <h5 class="mb-4">Dokter Pemeriksa</h5>

                            <div class="mb-3">
                                <label class="mb-1">Nama Dokter</label>
                                <p class="fw-medium"><?php echo $detail['nama_dokter']; ?></p>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Spesialisasi</label>
                                <p class="fw-medium"><?php echo $detail['nama_spesialis']; ?></p>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">No. Lisensi Dokter</label>
                                <p class="fw-medium"><?php echo $detail['nomor_lisensi']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Kedua - Hasil Pemeriksaan & Resep -->
            <div class="card_second shadow-sm">
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Kolom Kiri - Hasil Pemeriksaan -->
                        <div class="col-md-6 pe-md-4">
                            <h4 class="text-success mb-4">Hasil Pemeriksaan</h4>
                            
                            <div class="mb-4">
                                <label class="mb-2 fw-medium">Diagnosis</label>
                                <p class="fw-medium">Demam Tifoid dengan komplikasi ringan</p>
                            </div>

                            <div class="mb-4">
                                <label class="mb-2">Catatan Dokter</label>
                                <p class="fw-medium">
                                    Pasien menunjukkan gejala demam tifoid dengan fever >38°C. 
                                    Perlu istirahat total dan menjaga asupan cairan. 
                                    Kontrol kembali dalam 3 hari jika tidak ada perbaikan.
                                </p>
                            </div>
                        </div>

                        <!-- Kolom Kanan - Resep Obat -->
                        <div class="col-md-6 ps-md-4 border-start">
                            <h4 class="text-success mb-4">Resep Obat</h4>

                            <div class="mb-3">
                                <label class="mb-1">ID Resep</label>
                                <p class="fw-medium">RSP-2024030001</p>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Tanggal</label>
                                <p class="fw-medium">15 Maret 2024</p>
                            </div>

                            <!-- Daftar Obat -->
                            <div class="mb-3">
                                <label class="mb-2">Daftar Obat</label>
                                
                                <!-- Obat 1 -->
                                <div class="card bg-light mb-3">
                                    <div class="card-body p-3">
                                        <h6 class="fw-medium mb-2">Cefixime 200mg</h6>
                                        <p class="small mb-2"><span>Deskripsi:</span> Antibiotik sefalosporin generasi ketiga</p>
                                        <p class="small mb-2"><span>Dosis:</span> 2x sehari</p>
                                        <p class="small mb-2"><span>Jumlah:</span> 10 tablet</p>
                                        <p class="small mb-2"><span>Instruksi:</span> Diminum setelah makan pagi dan malam</p>
                                        <p class="small mb-0"><span>Harga:</span> Rp 15.000/tablet</p>
                                    </div>
                                </div>

                                <!-- Obat 2 -->
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <h6 class="fw-medium mb-2">Paracetamol 500mg</h6>
                                        <p class="small mb-2"><span>Deskripsi:</span> Analgesik dan antipiretik</p>
                                        <p class="small mb-2"><span>Dosis:</span> 3x sehari</p>
                                        <p class="small mb-2"><span>Jumlah:</span> 15 tablet</p>
                                        <p class="small mb-2"><span>Instruksi:</span> Diminum setiap 8 jam jika demam</p>
                                        <p class="small mb-0"><span>Harga:</span> Rp 5.000/tablet</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Harga -->
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Total Harga</h6>
                                    <h6 class="fw-medium mb-0">Rp 225.000</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- End section detail hasil konsultasi -->
</body>
</html>