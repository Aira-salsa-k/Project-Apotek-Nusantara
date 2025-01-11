<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Hasil Konsultasi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/edit_hasil_konsultasi.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif; /* Mengatur font Poppins untuk seluruh halaman */
        }
        .form-group {
            margin-bottom: 1rem; /* Mengatur jarak antar elemen form */
        }
        thead th {
            font-weight: 400; /* Mengurangi ketebalan teks pada header tabel */
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
                        <a class="nav-link" href="#">Dashboard</a>
                    </li>
                    <div class="d-flex ms-4"></div>
                    <button class="btn btn-custom-green">
                        <i class="fas fa-sign-out-alt me-2"></i>LOGOUT
                    </button>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Card untuk Edit Hasil Konsultasi dan Catatan Dokter -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="card-title text-success mb-4">Edit Data Hasil Konsultasi</h4>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hasil Konsultasi</label>
                                    <textarea class="form-control" rows="3" required>Hasil konsultasi yang sudah ada</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Catatan Dokter</label>
                                    <textarea class="form-control" rows="3" required>Catatan dokter yang sudah ada</textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status Konsultasi</label>
                                    <select class="form-select" required>
                                        <option value="" disabled>Pilih Status</option>
                                        <option value="selesai" selected>Selesai</option>
                                        <option value="batal">Batal</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Card untuk Obat -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="card-title text-success mb-4">Data Obat</h4>
                        <form>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Nama Obat</label>
                                    <input type="text" class="form-control" value="Contoh Obat 1" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Deskripsi Obat</label>
                                    <textarea class="form-control" rows="3" required>Deskripsi Obat 1</textarea>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Dosis Obat</label>
                                    <input type="text" class="form-control" value="1 Tablet" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Instruksi Penggunaan</label>
                                    <textarea class="form-control" rows="3" required>Setelah Makan</textarea>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Jumlah Obat</label>
                                    <input type="number" class="form-control" value="10" required>
                                </div>
                                <div class="col-md-6 mb-3 text-end">
                                    <button type="button" class="btn btn-custom-green btn-sm">Update Obat</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabel Preview Data Obat -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="card-title text-success mb-4">Preview Data Obat</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Deskripsi Obat</th>
                                    <th>Dosis Obat</th>
                                    <th>Instruksi Penggunaan</th>
                                    <th>Jumlah Obat</th>
                                    <th>Aksi</th> <!-- Kolom Aksi -->
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Contoh Obat 1</td>
                                    <td>Deskripsi Obat 1</td>
                                    <td>1 Tablet</td>
                                    <td>Setelah Makan</td>
                                    <td>10</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm">Edit</button>
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Contoh Obat 2</td>
                                    <td>Deskripsi Obat 2</td>
                                    <td>2 Tablet</td>
                                    <td>Sebelum Makan</td>
                                    <td>5</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm">Edit</button>
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </td>
                                </tr>
                                <!-- Tambahkan baris obat lainnya sesuai kebutuhan -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card untuk Input Status Konsultasi -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title text-success mb-4">Input Status Konsultasi</h4>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status Konsultasi</label>
                                    <select class="form-select" required>
                                        <option value="" disabled>Pilih Status</option>
                                        <option value="selesai" selected>Selesai</option>
                                        <option value="batal">Batal</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tombol Simpan Perubahan -->
                <div class="col-md-12 text-end mt-3">
                    <button type="submit" class="btn btn-custom-green btn-sm">Simpan Perubahan</button>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 