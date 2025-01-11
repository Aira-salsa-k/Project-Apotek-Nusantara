<?php
session_start();
include 'Koneksi/Config.php'; // Menghubungkan ke database

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // $username = $_POST['username'];
    // $password = $_POST['password'];
     $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    // Query untuk memeriksa pengguna
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Menyimpan data pengguna ke session
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        // $_SESSION['id_pasien'] = $user['id_pasien'];
         $_SESSION['id_user'] = $user['id_user']; 
        $_SESSION['role'] = $user['role'];

        // Arahkan ke halaman sesuai role
        switch ($user['role']) {
            case 'Admin':
                header("Location: Admin/Dashboard_admin.php");
                break;
            case 'Dokter':
                header("Location: Dokter/Dashboard_dokter.php");
                break;
            case 'Pasien':
                header("Location: Pasien/Dashboard_konsultasi.php");
                break;
            case 'Petugas':
                header("Location: Petugas/Dashboard_petugas.php");
                break;
        }
        exit();
    } else {
        echo "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="CSS/navbar.css">
   <link rel="stylesheet" href="CSS/dashboard_konsultasi.css">
   <style>
         
        .login-container {
            max-width: 400px; /* Lebar maksimum form */
            margin: 100px auto; /* Pusatkan form dengan margin atas */
            padding: 20px; /* Padding di dalam form */
            background-color: white; /* Warna latar belakang form */
            border-radius: 8px; /* Sudut membulat */
            box-shadow: none; /* Menghilangkan bayangan untuk tampilan flat */
        }
        .login-title {
            text-align: center; /* Pusatkan judul */
            margin-bottom: 20px; /* Jarak bawah judul */
        }
        .register-link {
            text-align: center; /* Pusatkan link register */
            margin-top: 15px; /* Jarak atas untuk link register */
         
        }
        .btn-primary {
            background-color: #4CAF50; /* Ubah warna tombol menjadi hijau */
            border: none; /* Menghilangkan border */
            height: 47px; /* Tinggi tombol */
        }
        .btn-primary:hover {
            background-color: #45a049; /* Warna saat hover */
        }
        a{
            color:  #FF6B00 ;;
        }
        a:hover{
            color: #45a049;
        }
         .required {
            color: red; /* Warna merah untuk tanda wajib */
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
            
        </div>
    </div>
</nav>
<!-- End navbar -->

<!-- login form -->
<div class="container">
    <div class="login-container">
        <h2 class="login-title">Login</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username<span class="required">*</span></label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password<span class="required">*</span></label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button> <!-- Tombol lebar penuh -->
        </form>
        <div class="register-link">
            <p>Belum punya akun? <a href="Pasien/Register.php">Daftar di sini</a></p>
        </div>
    </div>
</div>
<!-- end ogin form -->
    
</body>
</html>
