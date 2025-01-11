<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif; /* Mengatur font Poppins untuk seluruh halaman */
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            background-color: #f8f9fa;
            padding: 30px 20px; /* Menambahkan padding di sidebar */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar a {
            font-weight: 500;
            color: #333;
            margin-bottom: 10px; /* Menambahkan margin bawah pada link */
        }
        .sidebar a:hover {
            color: #007bff;
        }
        .content {
            margin-left: 350px; /* Mengatur margin untuk konten agar tidak tertutup sidebar */
            padding: 20px;
            margin-top: 20px; /* Menambahkan jarak antara sidebar dan konten */
        }
        .card {
            margin-bottom: 20px; /* Mengatur jarak antar card */
            border-radius: 10px; /* Membuat sudut card lebih bulat */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Menambahkan bayangan pada card */
        }
        .card-title {
            font-weight: 600; /* Menebalkan judul card */
        }
        .card-text {
            font-size: 1.5rem; /* Mengatur ukuran font untuk teks jumlah */
            font-weight: 700; /* Menebalkan teks jumlah */
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#">Dashboard Utama</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Data User</a>
            </li>
            <li class="nav-item">
                <button class="btn btn-danger mt-3">Logout</button>
            </li>
        </ul>
    </div>
    <!-- End Sidebar -->

    <div class="content">
        <div class="container mt-5 mb-5">
            <h1 class="mb-4">Selamat Datang di Dashboard Admin</h1>
            <div class="row">
                <div class="col-lg-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Jumlah Dokter</h5>
                            <p class="card-text" id="jumlahDokter">10</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Jumlah Petugas</h5>
                            <p class="card-text" id="jumlahPetugas">5</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Jumlah Pasien</h5>
                            <p class="card-text" id="jumlahPasien">20</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
 