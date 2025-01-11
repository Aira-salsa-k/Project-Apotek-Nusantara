<?php
session_start();
require_once('../Koneksi/Config.php'); // Menghubungkan ke database

// Check for expired consultations and update their status
$update_expired = "UPDATE konsultasi 
                  SET status_konsultasi = 'Dibatalkan', 
                      nomor_antrian = NULL 
                  WHERE DATE(tanggal_konsultasi) < CURDATE() 
                  AND status_konsultasi IN ('Menunggu', 'On-Progress')";
mysqli_query($conn, $update_expired);

// Pastikan hanya petugas yang sudah login yang bisa mengakses
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Petugas') {
    header("Location: ../Login.php");
    exit();
}

// Query untuk mengambil data pasien dan konsultasi
$query = "SELECT p.nama AS nama_pasien, p.umur, p.nomor_telepon, p.alamat, 
               k.*, 
               d.nama AS nama_dokter, s.nama_spesialis 
        FROM pasien p 
        JOIN konsultasi k ON p.id_pasien = k.id_pasien 
        JOIN dokter d ON k.id_dokter = d.id_dokter 
        LEFT JOIN spesialis s ON d.id_dokter = s.id_dokter 
        WHERE DATE(k.tanggal_konsultasi) >= CURDATE() 
        ORDER BY k.tanggal_konsultasi ASC, k.waktu_konsultasi ASC";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Pasien</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/Dashboard_Petugas.css">
    <style>
       .btn-custom-confirm {
        background-color: #4CAF50; /* Warna hijau untuk konfirmasi */
        color: white; /* Warna teks putih */
    }
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
    .action-button {
        font-size: 0.8em;
        border: none; /* Menghilangkan border */
        border-radius: 5px; /* Sudut membulat */
        cursor: pointer; /* Pointer saat hover */
    }
    .nomor-antrian {
        font-weight: bold;
        color: #4CAF50;
    }
  </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Apotek <span class="brand-orange">Nusant<span class="strikethrough-a">a</span>ra</span>.</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Dashboard Konsultasi</a>
                    </li> 
                    <div class="d-flex ms-4"></div>
                    <a href="../Logout.php" class="btn btn-custom-green"> <i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <div class="container-fluid mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                        <h4 class="card-title text-success mb-4">Data Pasien</h4>
                <div class="card shadow-sm mb-5">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal Konsultasi</th>
                                        <th>Waktu Konsultasi</th>
                                        <th>No. Antrian</th>
                                        <th>Nama Pasien</th>
                                        <th>Dokter Pemeriksa</th>
                                        <th>Spesialis</th>
                                        <th>Status Konsultasi</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="patientTableBody">
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                        <?php 
                                            $status_class = '';
                                            switch($row['status_konsultasi']) {
                                                case 'Menunggu':
                                                    $status_class = 'bg-warning';
                                                    break;
                                                case 'On-Progress':
                                                    $status_class = 'bg-primary';
                                                    break;
                                                case 'Selesai':
                                                    $status_class = 'bg-success';
                                                    break;
                                                case 'Dibatalkan':
                                                    $status_class = 'bg-danger';
                                                    break;
                                            }
                                        ?>
                                        <tr id="row-<?php echo $row['id_konsultasi']; ?>">
                                            <td><?php echo date('Y-m-d', strtotime($row['tanggal_konsultasi'])); ?></td>
                                            <td>
                                             <?php 
                                                    // Mengambil waktu dari database
                                                    $dateTime = new DateTime($row['waktu_konsultasi']);
                                                    // Memformat waktu menjadi 'H:i WIT'
                                                    echo $dateTime->format('H:i') . ' WIT'; 
                                                ?>
                                            
                                            </td>
                                            <td class="nomor-antrian"><?php echo !empty($row['nomor_antrian']) ? $row['nomor_antrian'] : '-'; ?></td>
                                            <td><?php echo $row['nama_pasien']; ?></td>
                                            <td>Dr. <?php echo $row['nama_dokter']; ?></td>
                                            <td><?php echo isset($row['nama_spesialis']) ? $row['nama_spesialis'] : 'Umum'; ?></td>
                                            
                                            <td><span class="badge <?php echo $status_class; ?>"><?php echo $row['status_konsultasi']; ?></span></td>
                                            <td>
                                                <button class="action-button btn-custom-confirm" 
                                                        data-id="<?php echo $row['id_konsultasi']; ?>" 
                                                        title="Konfirmasi"
                                                        <?php echo $row['status_konsultasi'] != 'Menunggu' || date('Y-m-d') > date('Y-m-d', strtotime($row['tanggal_konsultasi'])) ? 'disabled' : ''; ?>>
                                                    Confirm
                                                </button>
                                                <button class="action-button btn-custom-view" title="Lihat Data" onclick="window.location.href='Data_detail_pasien.php?id_konsultasi=<?php echo $row['id_konsultasi']; ?>'">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-button btn-custom-edit" title="Edit Data" onclick="window.location.href='../Pasien/Edit_daftar_konsul.php?id_konsultasi=<?php echo $row['id_konsultasi']; ?>'">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-button btn-custom-delete" title="Delete Data" onclick="confirmDelete(<?php echo $row['id_konsultasi']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data pasien.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                window.location.href = 'Delete_data_konsultasi.php?id_konsultasi=' + id; 
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.btn-custom-confirm').click(function() {
                const button = $(this);
                const id_konsultasi = button.data('id');
                const row = $(`#row-${id_konsultasi}`);
                
                if (confirm('Apakah Anda yakin ingin mengkonfirmasi konsultasi ini?')) {
                    $.ajax({
                        url: '/Apotek_Uniyap/Petugas/konfirmasi_konsultasi.php',
                        type: 'POST',
                        data: {
                            id_konsultasi: id_konsultasi
                        },
                        success: function(response) {
                            console.log('Response:', response);
                            if (response.status === 'success') {
                                // Update nomor antrian
                                row.find('.nomor-antrian').text(response.nomor_antrian);
                                
                                // Update status
                                row.find('.badge')
                                   .removeClass('bg-warning')
                                   .addClass('bg-info')
                                   .text('On-Progress');
                                
                                // Disable tombol
                                button.prop('disabled', true);
                                
                                alert('Konsultasi berhasil dikonfirmasi. Nomor antrian: ' + response.nomor_antrian);
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat mengkonfirmasi konsultasi');
                        }
                    });
                }
            });
        });
    </script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>