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

// Ambil detail konsultasi
$query = "SELECT k.*, p.nama AS nama_pasien, p.umur, p.jenis_kelamin, p.alamat, p.nomor_telepon,
          d.nama AS nama_dokter, COALESCE(s.nama_spesialis, 'Umum') AS spesialis
          FROM konsultasi k
          JOIN pasien p ON k.id_pasien = p.id_pasien
          JOIN dokter d ON k.id_dokter = d.id_dokter
          LEFT JOIN spesialis s ON d.id_dokter = s.id_dokter
          WHERE k.id_konsultasi = ?";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $id_konsultasi);
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
    <title>Detail Konsultasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Detail Konsultasi</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="text-primary">Data Pasien</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td width="150">Nama</td>
                                <td>: <?php echo htmlspecialchars($konsultasi['nama_pasien']); ?></td>
                            </tr>
                            <tr>
                                <td>Umur</td>
                                <td>: <?php echo htmlspecialchars($konsultasi['umur']); ?> tahun</td>
                            </tr>
                            <tr>
                                <td>Jenis Kelamin</td>
                                <td>: <?php echo htmlspecialchars($konsultasi['jenis_kelamin']); ?></td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>: <?php echo htmlspecialchars($konsultasi['alamat']); ?></td>
                            </tr>
                            <tr>
                                <td>No. Telepon</td>
                                <td>: <?php echo htmlspecialchars($konsultasi['nomor_telepon']); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-primary">Data Konsultasi</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td width="150">Tanggal</td>
                                <td>: <?php echo date('d-m-Y', strtotime($konsultasi['tanggal_konsultasi'])); ?></td>
                            </tr>
                            <tr>
                                <td>Waktu</td>
                                <td>: <?php echo date('H:i', strtotime($konsultasi['waktu_konsultasi'])); ?> WIT</td>
                            </tr>
                            <tr>
                                <td>Dokter</td>
                                <td>: <?php echo htmlspecialchars($konsultasi['nama_dokter']); ?></td>
                            </tr>
                            <tr>
                                <td>Spesialis</td>
                                <td>: <?php echo htmlspecialchars($konsultasi['spesialis']); ?></td>
                            </tr>
                            <tr>
                                <td>No. Antrian</td>
                                <td>: <?php echo $konsultasi['nomor_antrian'] ? htmlspecialchars($konsultasi['nomor_antrian']) : '-'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h5 class="text-primary">Keluhan dan Riwayat</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Keluhan</th>
                                <td><?php echo nl2br(htmlspecialchars($konsultasi['keluhan'])); ?></td>
                            </tr>
                            <tr>
                                <th>Riwayat Penyakit</th>
                                <td><?php echo nl2br(htmlspecialchars($konsultasi['riwayat_penyakit'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="Dashboard_dokter.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <?php if ($konsultasi['status_konsultasi'] == 'On-Progress'): ?>
                    <a href="Tambah_hasil_konsultasi.php?id=<?php echo $id_konsultasi; ?>" class="btn btn-primary">
                        <i class="fas fa-notes-medical"></i> Isi Hasil Konsultasi
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
