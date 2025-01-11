<?php
include '../Koneksi/Config.php';
session_start();

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan hanya pasien yang sudah login yang bisa mengakses
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Pasien') {
    header("Location: ../Login.php");
    exit();
}

// Ambil data pasien yang sedang login
$username = $conn->real_escape_string($_SESSION['username']);

// Query untuk mendapatkan ID Pasien berdasarkan username
$sql_pasien = "SELECT p.* FROM pasien p 
               JOIN users u ON p.id_user = u.id_user 
               WHERE u.username = '$username'";
$result_pasien = $conn->query($sql_pasien);

// Tambahkan pengecekan error
if (!$result_pasien) {
    die("Error query pasien: " . $conn->error);
}

// Periksa apakah ada hasil
if ($result_pasien->num_rows == 0) {
    // Jika tidak ditemukan, tambahkan data pasien secara otomatis
    $sql_tambah_pasien = "INSERT INTO pasien (nama, umur, id_dokter, nomor_telepon, alamat, jenis_kelamin) 
                          VALUES ('$username', 25, NULL, '08xxxxxxxx', 'Alamat Tidak Diketahui', 'Laki-laki')";
    
    if ($conn->query($sql_tambah_pasien)) {
        // Jalankan ulang query pasien
        $result_pasien = $conn->query($sql_pasien);
    } else {
        die("Gagal menambahkan data pasien: " . $conn->error);
    }
}

$pasien = $result_pasien->fetch_assoc();
$id_pasien = $pasien['id_pasien'];

// Check for expired consultations and update their status
$update_expired = "UPDATE konsultasi 
                  SET status_konsultasi = 'Dibatalkan', 
                      nomor_antrian = NULL 
                  WHERE DATE(tanggal_konsultasi) < CURDATE() 
                  AND status_konsultasi IN ('Menunggu', 'On-Progress')";
$conn->query($update_expired);

// Ambil data konsultasi pasien
$sql_konsultasi = "SELECT k.*, d.nama as nama_dokter, COALESCE(s.nama_spesialis, 'Umum') as nama_spesialis 
                   FROM konsultasi k
                   LEFT JOIN dokter d ON k.id_dokter = d.id_dokter
                   LEFT JOIN spesialis s ON d.id_dokter = s.id_dokter
                   WHERE k.id_pasien = $id_pasien
                   ORDER BY k.tanggal_konsultasi DESC";

$result_konsultasi = $conn->query($sql_konsultasi);

// Periksa error query konsultasi
if (!$result_konsultasi) {
    die("Error query konsultasi: " . $conn->error);
}

// Ambil nomor antrian pasien yang sedang menunggu
$sql_antrian = "SELECT nomor_antrian 
                FROM konsultasi 
                WHERE id_pasien = $id_pasien 
                AND status_konsultasi IN ('Menunggu', 'On-Progress')
                AND DATE(tanggal_konsultasi) = CURDATE()
                ORDER BY tanggal_konsultasi DESC 
                LIMIT 1";
$result_antrian = $conn->query($sql_antrian);

// Ambil nomor antrian pertama yang masih On-Progress
$sql_current = "SELECT k.nomor_antrian, k.status_konsultasi
                FROM konsultasi k
                WHERE DATE(k.tanggal_konsultasi) = CURDATE()
                AND k.status_konsultasi = 'On-Progress'
                AND k.nomor_antrian IS NOT NULL
                ORDER BY 
                    CASE 
                        WHEN k.nomor_antrian LIKE 'P-%' THEN 1
                        WHEN k.nomor_antrian LIKE 'S-%' THEN 2
                        WHEN k.nomor_antrian LIKE 'M-%' THEN 3
                    END,
                    CAST(SUBSTRING(k.nomor_antrian, 3) AS UNSIGNED)
                LIMIT 1";
$result_current = $conn->query($sql_current);

// Periksa error query
if (!$result_antrian || !$result_current) {
    die("Error query antrian: " . $conn->error);
}

$antrian = $result_antrian->fetch_assoc();
$current = $result_current->fetch_assoc();

// Jika tidak ada konsultasi, atur nilai default
$konsultasi_data = [];
if ($result_konsultasi->num_rows > 0) {
    while ($row = $result_konsultasi->fetch_assoc()) {
        $konsultasi_data[] = $row;
    }
} else {
    $konsultasi_data = null;
}

// Ambil data dokter
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
  <title>Dashboard Konsultasi</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/dashboard_konsultasi.css">
</head>
<style>
  
    .btn-custom-view {
        background-color: #ACE2F8; /* Warna biru untuk lihat data */
        color: black; /* Warna teks putih */
    }
    .btn-custom-edit {
        background-color: #FBE7E9; /* Warna kuning untuk edit */
        color: black; /* Warna teks hitam */
    }
    .btn-custom-delete {
        background-color: #dc3545; /* Warna merah untuk delete */
        color: white; /* Warna teks putih */
    }
    .btn:hover, .btn-custom-delete:hover, .btn-custom-edit:hover{
        background-color: #F28D33;
        color : white;
    }
    .action-button {
        font-size: 0.8em;
        border: none; /* Menghilangkan border */
        border-radius: 5px; /* Sudut membulat */
        cursor: pointer; /* Pointer saat hover */
    }
  </style>
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
                <a href="../Logout.php" class="btn btn-custom-green"> <i class="fas fa-sign-out-alt me-2"></i>Logout</a>
            </div>
                </ul>
            </div>
        </div>
    </nav>
<!-- End navbar -->

    <div class="container mt-4">
        <div class="row">
            <!-- Alert Verifikasi -->
            <div class="col-12 mb-2">
            <?php
                if (isset($_GET['message'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            ' . htmlspecialchars($_GET['message']) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                }

                // Cek status nomor antrian
                if ($antrian && $antrian['nomor_antrian']) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Selamat!</strong> Nomor antrian Anda adalah ' . htmlspecialchars($antrian['nomor_antrian']) . '. Silakan menunggu giliran Anda.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                } else {
                    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Perhatian!</strong> Silakan menunggu hingga nomor antrian Anda diverifikasi oleh petugas.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                }
                ?>
            </div>

            <!-- Nomor Antrian Card - Kolom Kiri -->
            <div class="col-md-3">
                <div class="card bg-white shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="text-success text-center mb-4">Nomor Antrian Anda</h5>
                        <div class="text-center mb-4">
                            <div class="display-1 fw-bold text-success mb-2">
                                <?php 
                                if ($antrian && $antrian['nomor_antrian']) {
                                    echo htmlspecialchars($antrian['nomor_antrian']);
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="text-center border-top pt-4">
                            <p class="small text-muted mb-0">Nomor Antrian Saat Ini</p>
                            <div class="h3 fw-bold text mb-2">
                                <?php 
                                if ($current && $current['nomor_antrian']) {
                                    echo htmlspecialchars($current['nomor_antrian']);
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Konsultasi - Kolom Kanan -->
            <div class="col-md-9">
                <div class="card shadow-sm mb-5">
                    <div class="card-body p-4">
                        <h5 class="card-title text-success mb-4">Data Konsultasi</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Nama Pasien</th>
                                        <th>Nama Dokter</th>
                                        <th>Spesialis</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($konsultasi_data !== null): ?>
                                        <?php foreach ($konsultasi_data as $konsultasi): ?>
                                        <tr>
                                            <td><?php echo $konsultasi['tanggal_konsultasi']; ?></td>
                                            <td>
                                                <?php 
                                                    // Mengambil waktu dari database
                                                    $waktu_konsultasi = $konsultasi['waktu_konsultasi']; 
                                                    // Membuat objek DateTime
                                                    $dateTime = new DateTime($waktu_konsultasi);
                                                    // Memformat waktu menjadi 'H:i WIT'
                                                    echo $dateTime->format('H:i') . ' WIT'; 
                                                ?>
                                            </td>
                                            <td><?php echo $pasien['nama']; ?></td>
                                            <td><?php echo $konsultasi['nama_dokter']; ?></td>
                                            <td><?php echo $konsultasi['nama_spesialis']; ?></td>
                                            <td>
                                            <span class="badge <?php 
                                                echo $konsultasi['status_konsultasi'] == 'Menunggu' 
                                                    ? 'bg-warning' 
                                                    : ($konsultasi['status_konsultasi'] == 'On-Progress' 
                                                        ? 'bg-info' 
                                                        : ($konsultasi['status_konsultasi'] == 'Selesai' 
                                                            ? 'bg-success' 
                                                            : 'bg-danger'));
                                            ?>">
                                                <?php echo $konsultasi['status_konsultasi']; ?>
                                            </span>

                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-custom-view" onclick="window.location.href='detail_hasil_konsultasi.php?id_konsultasi=<?php echo $konsultasi['id_konsultasi']; ?>'">
                                                    Detail
                                                </button>
                                                <button class="action-button btn-custom-edit" onclick="window.location.href='Edit_daftar_konsul.php?id_konsultasi=<?php echo $konsultasi['id_konsultasi']; ?>'">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-button btn-custom-delete" title="Delete Data" onclick="confirmDelete(<?php echo $konsultasi['id_konsultasi']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada konsultasi.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Button Rekam Medis -->
                        <div class="d-flex justify-content-between align-items-center mt-4"></div>
                        <a href="Edit_akun.php" class="text-success text-decoration-underline fw-bold">
                            Informasi Akun & Data Pribadi
                        </a>
                        <div class="text-end mt-4">
                            <button class="btn btn-custom-green">
                                Lihat Rekam Medis
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function confirmDelete(id) {
        if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
            window.location.href = 'Delete_konsultasi.php?id_konsultasi=' + id; 
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>