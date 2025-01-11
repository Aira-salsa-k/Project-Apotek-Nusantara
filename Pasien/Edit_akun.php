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

// Ambil ID pengguna dari sesi
$id_user = $_SESSION['id_user'];

// Query untuk mengambil data pasien berdasarkan id_user
$sql_pasien = "SELECT p.* FROM pasien p WHERE p.id_user = $id_user";
$result_pasien = $conn->query($sql_pasien);

// Tambahkan pengecekan error
if (!$result_pasien) {
    die("Error query pasien: " . $conn->error);
}

// Periksa apakah ada hasil
if ($result_pasien->num_rows == 0) {
    // Jika tidak ditemukan, tambahkan data pasien secara otomatis
    $sql_tambah_pasien = "INSERT INTO pasien (nama, umur, id_dokter, nomor_telepon, alamat, jenis_kelamin, id_user) 
                          VALUES ('$username', 25, 1, '08xxxxxxxx', 'Alamat Tidak Diketahui', 'Laki-laki', $id_user)";
    
    if ($conn->query($sql_tambah_pasien)) {
        // Jalankan ulang query pasien
        $result_pasien = $conn->query($sql_pasien);
    } else {
        die("Gagal menambahkan data pasien: " . $conn->error);
    }
}

// Ambil data pasien
$pasien = $result_pasien->fetch_assoc();
$id_pasien = $pasien['id_pasien'];

// Query untuk mengambil username dari tabel users
$sql_user = "SELECT username FROM users WHERE id_user = $id_user";
$result_user = $conn->query($sql_user);

// Query untuk mengambil username dari tabel users
$sql_user = "SELECT username FROM users WHERE id_user = $id_user";
$result_user = $conn->query($sql_user);

// Ambil username
$username = '';
if ($result_user->num_rows > 0) {
    $user_data = $result_user->fetch_assoc();
    $username = $user_data['username'];
} else {
    die("Username tidak ditemukan.");
}

// Proses pengeditan data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $umur = $_POST['umur'];
    $nomor_telepon = $_POST['nomor_telepon'];
    $alamat = $_POST['alamat'];
    $jenis_kelamin = $_POST['gender'];

    // Update data pasien
    $sql_update = "UPDATE pasien SET 
        nama = '$nama', 
        umur = $umur, 
        nomor_telepon = '$nomor_telepon', 
        alamat = '$alamat', 
        jenis_kelamin = '$jenis_kelamin' 
        WHERE id_pasien = $id_pasien";

    if ($conn->query($sql_update) === TRUE) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='Dashboard_konsultasi.php';</script>";
    } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
    }

    // Proses pengeditan data
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];


    // Query untuk memeriksa password saat ini
    $sql_check_password = "SELECT password FROM users WHERE id_user = $id_user";
    $result_check = $conn->query($sql_check_password);

    if ($result_check && $result_check->num_rows > 0) {
        $user = $result_check->fetch_assoc();

        // Verifikasi password saat ini
        if ($user['password'] === $current_password) {
            // Cek apakah password baru dan konfirmasi password baru cocok
            if ($new_password === $confirm_new_password) {
                // Update password di database
                $sql_update_password = "UPDATE users SET password = '$new_password' WHERE id_user = $id_user";
                if ($conn->query($sql_update_password) === TRUE) {
                    echo "<script>alert('Password berhasil diperbarui!');</script>";
                } else {
                    echo "Error: " . $conn->error;
                }
            } else {
                echo "<script>alert('Password baru dan konfirmasi tidak cocok.');</script>";
            }
        } else {
            echo "<script>alert('Password saat ini salah.');</script>";
        }
    } else {
        echo "Error: " . $conn->error;
    }
       // Proses update username
    if (isset($_POST['username']) && $_POST['username'] !== $username) {
        $new_username = $_POST['username'];
        $sql_update_username = "UPDATE users SET username = '$new_username' WHERE id_user = $id_user";
        if ($conn->query($sql_update_username) === TRUE) {
            echo "<script>alert('Username berhasil diperbarui!');</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
    
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Akun Pasien</title>
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
        </div>
    </nav>
<!-- End navbar -->

    <div class="container mt-5 mb-5">
                         <a href="Dashboard_konsultasi.php" class="btn btn-custom-orange">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title text-success mb-4">Edit Data Akun Pasien</h4>
                        <form method="POST">
                            <div class="row">
                                <!-- Data Pribadi -->
                                    <h5 class="mb-4">Data Pribadi</h5>
                                <div class="col-md-6 pe-md-4">

                                    <div class="mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" name="nama" value="<?php echo $pasien['nama']; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Umur</label>
                                        <input type="number" class="form-control" name="umur" value="<?php echo $pasien['umur']; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">No. Telepon</label>
                                        <input type="tel" class="form-control" name="nomor_telepon" value="<?php echo $pasien['nomor_telepon']; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Alamat</label>
                                        <textarea class="form-control" name="alamat" rows="3" required><?php echo $pasien['alamat']; ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Jenis Kelamin</label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gender" id="male" value="Laki-laki" <?php echo ($pasien['jenis_kelamin'] == 'Laki-laki') ? 'checked' : ''; ?> required>
                                                <label class="form-check-label" for="male">
                                                    Laki-laki
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gender" id="female" value="Perempuan" <?php echo ($pasien['jenis_kelamin'] == 'Perempuan') ? 'checked' : ''; ?>>
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
                                        <input type="text" class="form-control" name="username" value="<?php echo $username; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Konfirmasi Password</label>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>

                                    <div class="mt-5">
                                        <button type="submit" class="btn btn-custom-green w-100">
                                            Edit data Akun
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
</body>
</html>