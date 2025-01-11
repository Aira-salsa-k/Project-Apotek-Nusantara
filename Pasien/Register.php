<?php
include '../Koneksi/Config.php';

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama_lengkap'];
    $umur = $_POST['umur'];
    $no_telepon = $_POST['no_telepon'];
    $alamat = $_POST['alamat'];
    $jenis_kelamin = $_POST['gender'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // Validasi input
    $errors = [];

    // Cek password
    if ($password !== $konfirmasi_password) {
        $errors[] = "Konfirmasi password tidak sesuai.";
    }

    // Cek apakah username sudah ada
    $cek_username = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($cek_username);
    if ($result->num_rows > 0) {
        $errors[] = "Username sudah digunakan.";
    }

    // Jika tidak ada error, lakukan registrasi
    if (empty($errors)) {
        // Mulai transaksi
        $conn->begin_transaction();

        try {
             // 1. Tambahkan user ke tabel users
            $sql_user = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'Pasien')";
            $conn->query($sql_user);
            $id_user = $conn->insert_id;

            // 2. Tambahkan data pasien ke tabel pasien
            // Gunakan username sebagai nama jika nama tidak diisi
            $nama = $nama ?: $username;
            $sql_pasien = "INSERT INTO pasien (nama, umur, nomor_telepon, alamat, jenis_kelamin, id_user, id_dokter) 
                           VALUES ('$nama', $umur, '$no_telepon', '$alamat', '$jenis_kelamin', $id_user, NULL)";
            $conn->query($sql_pasien);

            // Commit transaksi
            $conn->commit();

            // Redirect ke halaman login atau dashboard
            header("Location: ../Login.php");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            $errors[] = "Gagal melakukan registrasi: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Akun Pasien</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Import Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/register.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
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
</nav>
<!-- End navbar -->

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="card-title text-success mb-4">Register Akun Pasien</h4>
                    
                    <!-- Tampilkan pesan error jika ada -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo $error; ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row">
                            <!-- Data Pribadi -->
                            <h5 class="mb-4">Data Pribadi</h5>
                            <div class="col-md-6 pe-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Umur</label>
                                    <input type="number" name="umur" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="tel" name="no_telepon" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="3" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" value="Laki-laki" id="male" required>
                                            <label class="form-check-label" for="male">
                                                Laki-laki
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" value="Perempuan" id="female">
                                            <label class="form-check-label" for="female">
                                                Perempuan
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Akun -->
                            <div class="col-md-6 ps-md-4 border-start">
                                <h5 class="mb-4">Data Akun</h5>

                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" name="konfirmasi_password" class="form-control" required>
                                </div>

                                <div class="mt-5">
                                    <button type="submit" class="btn btn-custom-green w-100">
                                        Daftar Akun
                                    </button>
                                    <p class="text-center mt-3">
                                        Sudah punya akun? <a href="../Login.php" class="text-success text-decoration-none">Login di sini</a>
                                    </p>
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