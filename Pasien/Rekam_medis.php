<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Rekam Medis</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/rekam_medis.css">
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
                        <a class="nav-link" href="#">Dashboard Konsultasi</a>
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
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm mb-5">
                    <div class="card-body p-4">
                        <!-- Header dengan judul dan tombol kembali -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="card-title text-success mb-0">Riwayat Rekam Medis</h3>
                            <a href="Dashboard_konsultasi.html" class="btn btn-custom-orange">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>

                        <!-- Tabel Rekam Medis -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Konsultasi</th>
                                        <th>Data Konsultasi</th>
                                        <th>Nama Dokter</th>
                                        <th>Diagnosis</th>
                                        <th>Medikamentosa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>KS-2024030001</td>
                                        <td>
                                            <button class="btn btn-sm btn-custom-green">
                                                Detail Data
                                            </button>
                                        </td>
                                        <td>dr. Sarah Johnson, Sp.PD</td>
                                        <td>Demam Tifoid</td>
                                        <td>
                                            <ul class="list-unstyled mb-0">
                                                <li>Cefixime 200mg</li>
                                                <li>Paracetamol 500mg</li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>KS-2024020015</td>
                                        <td>
                                            <button class="btn btn-sm btn-custom-green">
                                                Detail Data
                                            </button>
                                        </td>
                                        <td>dr. Michael Smith</td>
                                        <td>ISPA</td>
                                        <td>
                                            <ul class="list-unstyled mb-0">
                                                <li>Amoxicillin 500mg</li>
                                                <li>Ibuprofen 400mg</li>
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Informasi Akun & Data Pribadi -->
                        <div class="border-top pt-4">
                            <h5 class="text-success mb-3">Informasi Akun & Data Pribadi</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Nama Pengguna:</strong> johndoe</p>
                                    <p class="mb-1"><strong>Nama Lengkap:</strong> John Doe</p>
                                    <p class="mb-1"><strong>Jenis Kelamin:</strong> Laki-laki</p>
                                    <p class="mb-1"><strong>Umur:</strong> 28 Tahun</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Alamat:</strong> Jl. Contoh No. 123, Kota Malang</p>
                                    <p class="mb-1"><strong>No. HP:</strong> 081234567890</p>
                                    <p class="mb-1"><strong>Email:</strong> johndoe@example.com</p>
                                </div>
                            </div>
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
