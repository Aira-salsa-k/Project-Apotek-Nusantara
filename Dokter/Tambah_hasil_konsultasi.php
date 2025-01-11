<?php
session_start();
require_once('../Koneksi/Config.php');

// Check if user is logged in as doctor
if (!isset($_SESSION['id_dokter'])) {
    header("Location: ../login.php");
    exit();
}

// Get consultation ID from URL
$id_konsultasi = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id_konsultasi) {
    die("ID Konsultasi tidak ditemukan");
}

// Get consultation data
$stmt = $conn->prepare("SELECT k.*, p.nama as nama_pasien, p.id_pasien FROM konsultasi k 
                       JOIN pasien p ON k.id_pasien = p.id_pasien 
                       WHERE k.id_konsultasi = ?");
$stmt->bind_param("i", $id_konsultasi);
$stmt->execute();
$result = $stmt->get_result();
$konsultasi = $result->fetch_assoc();

// Get medicine list
$query_obat = "SELECT * FROM obat ORDER BY nama_obat";
$result_obat = $conn->query($query_obat);
$obat_list = [];
while ($row = $result_obat->fetch_assoc()) {
    $obat_list[] = $row;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->begin_transaction();

        // Get form data
        $hasil_konsultasi = $_POST['hasil_konsultasi'];
        $catatan_dokter = $_POST['catatan_dokter'];
        $status_konsultasi = $_POST['status_konsultasi'];
        $obat_list = json_decode($_POST['obat'], true);

        // Validate required fields
        if (empty($hasil_konsultasi) || empty($catatan_dokter) || empty($status_konsultasi)) {
            throw new Exception("Semua field harus diisi");
        }

        // Update konsultasi status
        $stmt = $conn->prepare("UPDATE konsultasi SET status_konsultasi = ? WHERE id_konsultasi = ?");
        $stmt->bind_param("si", $status_konsultasi, $id_konsultasi);
        if (!$stmt->execute()) {
            throw new Exception("Gagal mengupdate status konsultasi");
        }

        // Insert into rekam_medis
        $stmt = $conn->prepare("INSERT INTO rekam_medis (id_dokter, id_konsultasi, catatan_dokter, diagnosis, id_pasien) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iissi", $_SESSION['id_dokter'], $id_konsultasi, $catatan_dokter, $hasil_konsultasi, $konsultasi['id_pasien']);
        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan rekam medis");
        }

        // Create resep if there are medicines
        if (!empty($obat_list)) {
            // Insert resep
            $tanggal_resep = date('Y-m-d');
            $stmt = $conn->prepare("INSERT INTO resep (id_pasien, id_dokter, dosis, tanggal_resep) 
                                  VALUES (?, ?, ?, ?)");
            $dosis = "Sesuai aturan pakai"; // Default dosis
            $stmt->bind_param("iiss", $konsultasi['id_pasien'], $_SESSION['id_dokter'], $dosis, $tanggal_resep);
            if (!$stmt->execute()) {
                throw new Exception("Gagal membuat resep");
            }
            $id_resep = $conn->insert_id;

            // Insert resep_obat
            $stmt = $conn->prepare("INSERT INTO resep_obat (id_resep, id_obat, instruksi_pengguna, jumlah_obat) 
                                  VALUES (?, ?, ?, ?)");
            foreach ($obat_list as $obat) {
                // Jika obat baru, tambahkan ke tabel obat terlebih dahulu
                if (isset($obat['is_new']) && $obat['is_new']) {
                    $stmt_obat = $conn->prepare("INSERT INTO obat (nama_obat, deskripsi, jenis_obat, tanggal_kadaluarsa, harga) 
                                               VALUES (?, ?, 'Umum', CURRENT_DATE + INTERVAL 1 YEAR, 0)");
                    $stmt_obat->bind_param("ss", $obat['nama'], $obat['deskripsi']);
                    if (!$stmt_obat->execute()) {
                        throw new Exception("Gagal menambahkan obat baru");
                    }
                    $obat['id_obat'] = $conn->insert_id;
                }

                // Pastikan id_obat ada
                if (!isset($obat['id_obat'])) {
                    throw new Exception("ID obat tidak valid");
                }

                $stmt->bind_param("iiss", $id_resep, $obat['id_obat'], $obat['instruksi'], $obat['jumlah']);
                if (!$stmt->execute()) {
                    throw new Exception("Gagal menyimpan detail obat: " . $conn->error);
                }
            }
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Tambah Data Hasil Konsultasi</title>
		<!-- Bootstrap CSS -->
		<link
			href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
			rel="stylesheet"
		/>
		<link
			rel="stylesheet"
			href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
		/>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link
			href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap"
			rel="stylesheet"
		/>
		
		<link rel="stylesheet" href="../CSS/navbar.css" />
		<link rel="stylesheet" href="../CSS/tambah_hasil_konsultasi.css" />
		<link rel="stylesheet" href="../CSS/daftar_konsultasi.css" />
		    <style>
        body, button {
            font-family: 'Poppins', sans-serif; /* Mengatur font Poppins untuk seluruh halaman */
        }
        .form-group {
            margin-bottom: 1rem; /* Mengatur jarak antar elemen form */
        }
        thead th {
            font-weight: 450; /* Mengurangi ketebalan teks pada header tabel */
        }
                table td {
            font-size: 0.9rem;
        }
    </style>
	</head>
	
	<body>
		<!-- Navbar -->
		<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
			<div class="container">
				<a class="navbar-brand" href="#"
					>Apotek
					<span class="brand-orange"
						>Nusant<span class="strikethrough-a">a</span>ra</span
					>.</a
				>
				<button
					class="navbar-toggler"
					type="button"
					data-bs-toggle="collapse"
					data-bs-target="#navbarNav"
					aria-controls="navbarNav"
					aria-expanded="false"
					aria-label="Toggle navigation"
				>
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
			<button class="btn btn-custom-orange">
						<a href="Dashboard_dokter.php" class="fas fa-arrow-left me-2" style = "  font-family: 'Poppins', sans-serif; font-weight: 500; text-decoration: none; color : white;">Kembali</a>
			</button>
			<div class="row justify-content-center">
				<div class="col-lg-12">
					<!-- Card untuk Hasil Konsultasi dan Catatan Dokter -->
					<div class="card shadow-sm mb-4">
						<div class="card-body p-4">

							<h4 class="card-title text-success mb-4">Tambah Data Hasil Konsultasi</h4>
							<?php if (isset($error)): ?>
								<div class="alert alert-danger"><?php echo $error; ?></div>
							<?php endif; ?>
							<form id="hasilKonsultasiForm" method="POST">
								<div class="row">
									<div class="col-md-6 mb-3">
										<label class="form-label">Hasil Konsultasi</label>
										<textarea class="form-control" rows="3" name="hasil_konsultasi" required></textarea>
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label">Catatan Dokter</label>
										<textarea class="form-control" rows="3" name="catatan_dokter" required></textarea>
									</div>
								</div>
							</form>
									</div>
								</div>

					<!-- Card untuk Obat -->
					<div class="card shadow-sm mb-4">
						<div class="card-body p-4">
							<h4 class="card-title text-success mb-4">Data Obat</h4>
							<form id="obatForm">
									<div class="row mb-3">
										<div class="col-md-6">
											<label class="form-label">Pilih Metode Input Obat</label>
											<select class="form-select" id="metodeInput" required>
												<option value="pilih">Pilih dari Daftar Obat</option>
												<option value="baru">Tambah Obat Baru</option>
											</select>
										</div>
									</div>

									<!-- Form untuk memilih obat yang sudah ada -->
									<div id="pilihObatForm" class="row">
										<div class="col-md-6 mb-3">
											<label class="form-label">Pilih Obat</label>
											<select class="form-select" id="nama_obat">
												<option value="">Pilih Obat</option>
												<?php foreach ($obat_list as $obat): ?>
													<option value="<?php echo $obat['id_obat']; ?>" 
														data-nama="<?php echo $obat['nama_obat']; ?>"
														data-deskripsi="<?php echo $obat['deskripsi']; ?>">
														<?php echo $obat['nama_obat']; ?>
													</option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-6 mb-3">
											<label class="form-label">Deskripsi Obat</label>
											<textarea class="form-control" rows="2" id="deskripsi_obat" readonly></textarea>
										</div>
									</div>

									<!-- Form untuk menambah obat baru -->
									<div id="obatBaruForm" class="row" style="display: none;">
										<div class="col-md-6 mb-3">
											<label class="form-label">Nama Obat Baru</label>
											<input type="text" class="form-control" id="nama_obat_baru">
										</div>
										<div class="col-md-6 mb-3">
											<label class="form-label">Deskripsi Obat</label>
											<textarea class="form-control" rows="2" id="deskripsi_obat_baru"></textarea>
										</div>
									</div>

									<!-- Form untuk informasi penggunaan obat -->
									<div class="row">
										<div class="col-md-6 mb-3">
											<label class="form-label">Dosis Obat</label>
											<input type="text" class="form-control" id="dosis_obat" required>
										</div>
										<div class="col-md-6 mb-3">
											<label class="form-label">Instruksi Penggunaan</label>
											<textarea class="form-control" rows="1" id="instruksi_penggunaan" required></textarea>
										</div>
										<div class="col-md-3 mb-3">
											<label class="form-label">Jumlah</label>
											<input type="number" class="form-control" id="jumlah_obat" required>
										</div>
										<div class="col-md-3 mb-3">
											<label class="form-label">Satuan</label>
											<select class="form-select" id="satuan_obat" required>
												<option value="">Pilih Satuan</option>
												<option value="Tablet">Tablet</option>
												<option value="Kaplet">Kaplet</option>
												<option value="Botol">Botol</option>
												<option value="Kapsul">Kapsul</option>
												<option value="Strip">Strip</option>
												<option value="Sachet">Sachet</option>
												<option value="Ampul">Ampul</option>
											</select>
										</div>
										<div class="col-md-6 mb-3 text-end">
											<button type="submit" class="btn btn-success btn-sm">
												<i class="fas fa-plus"></i> Tambah Obat
											</button>
										</div>
									</div>
							</form>
						</div>
								</div>

					<!-- Tabel Preview Data Obat -->
					<div class="card shadow-sm mb-4">
						<div class="card-body p-4">
							<h4 class="card-title text-success mb-4">Preview Data Obat</h4>
							<div class="table-responsive">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th>Nama Obat</th>
												<th>Deskripsi</th>
												<th>Instruksi</th>
												<th>Jumlah</th>
												<th>Aksi</th>
											</tr>
										</thead>
										<tbody id="obatTableBody">
										</tbody>
									</table>
								</div>

								<!-- Tombol Simpan -->
								
							</form>
						</div>
					</div>

					<!-- Status Konsultasi -->
					<div class="card shadow-sm mb-4">
						<div class="card-body p-4">
							<h4 class="card-title text-success mb-4">Status Konsultasi</h4>
							<div class="row">
								<div class="col-md-6 mb-3">
									<label class="form-label">Status Konsultasi</label>
									<select class="form-select" name="status_konsultasi" id="status_konsultasi" required>
										<option value="" disabled selected>Pilih Status</option>
										<option value="Selesai">Selesai</option>
										<option value="Batal">Batal</option>
									</select>
								</div>
								<div class="col-md-6 text-end">
									<button type="button" id="simpanKonsultasi" class="btn btn-primary">
										<i class="fas fa-save"></i> Simpan Hasil Konsultasi
									</button>
								</div>
							</div>
						</div>
					</div>
					
					
				</div>
			</div>
		</div>

		<!-- Bootstrap Bundle JS -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
		<script>
document.addEventListener('DOMContentLoaded', function() {
    const obatForm = document.getElementById('obatForm');
    const obatTable = document.getElementById('obatTableBody');
    const metodeInput = document.getElementById('metodeInput');
    const pilihObatForm = document.getElementById('pilihObatForm');
    const obatBaruForm = document.getElementById('obatBaruForm');
    const obatList = [];

    // Toggle form berdasarkan metode input
    metodeInput.addEventListener('change', function() {
        if (this.value === 'pilih') {
            pilihObatForm.style.display = 'flex';
            obatBaruForm.style.display = 'none';
            document.getElementById('nama_obat').required = true;
            document.getElementById('nama_obat_baru').required = false;
        } else {
            pilihObatForm.style.display = 'none';
            obatBaruForm.style.display = 'flex';
            document.getElementById('nama_obat').required = false;
            document.getElementById('nama_obat_baru').required = true;
        }
    });

    // Auto-fill deskripsi untuk obat yang sudah ada
    document.getElementById('nama_obat').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('deskripsi_obat').value = selectedOption.dataset.deskripsi || '';
        } else {
            document.getElementById('deskripsi_obat').value = '';
        }
    });

    obatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let obatData;
        if (metodeInput.value === 'pilih') {
            const selectedOption = document.getElementById('nama_obat').options[document.getElementById('nama_obat').selectedIndex];
            if (!selectedOption.value) {
                alert('Silakan pilih obat terlebih dahulu!');
                return;
            }
            obatData = {
                id_obat: selectedOption.value,
                nama: selectedOption.text,
                deskripsi: document.getElementById('deskripsi_obat').value,
                instruksi: document.getElementById('instruksi_penggunaan').value,
                jumlah: document.getElementById('jumlah_obat').value,
                is_new: false
            };
        } else {
            const namaObatBaru = document.getElementById('nama_obat_baru').value;
            const deskripsiObatBaru = document.getElementById('deskripsi_obat_baru').value;
            if (!namaObatBaru || !deskripsiObatBaru) {
                alert('Silakan lengkapi data obat baru!');
                return;
            }
            obatData = {
                nama: namaObatBaru,
                deskripsi: deskripsiObatBaru,
                instruksi: document.getElementById('instruksi_penggunaan').value,
                jumlah: document.getElementById('jumlah_obat').value,
                is_new: true
            };
        }

        // Validasi instruksi dan jumlah
        if (!obatData.instruksi || !obatData.jumlah) {
            alert('Silakan lengkapi instruksi penggunaan dan jumlah obat!');
            return;
        }

        // Tambahkan ke list
        obatList.push(obatData);
        updateObatTable();
        this.reset();
    });

    function updateObatTable() {
        obatTable.innerHTML = '';
        obatList.forEach((obat, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${obat.nama}</td>
                <td>${obat.deskripsi}</td>
                <td>${obat.instruksi}</td>
                <td>${obat.jumlah}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" onclick="editObat(${index})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteObat(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            obatTable.appendChild(row);
        });
    }

    window.editObat = function(index) {
        const obat = obatList[index];
        if (obat.is_new) {
            metodeInput.value = 'baru';
            document.getElementById('nama_obat_baru').value = obat.nama;
            document.getElementById('deskripsi_obat_baru').value = obat.deskripsi;
        } else {
            metodeInput.value = 'pilih';
            document.getElementById('nama_obat').value = obat.id_obat;
            document.getElementById('deskripsi_obat').value = obat.deskripsi;
        }
        metodeInput.dispatchEvent(new Event('change'));
        
        document.getElementById('dosis_obat').value = obat.dosis;
        document.getElementById('instruksi_penggunaan').value = obat.instruksi;
        document.getElementById('jumlah_obat').value = obat.jumlah;
        
        obatList.splice(index, 1);
        updateObatTable();
    };

    window.deleteObat = function(index) {
        if (confirm('Apakah Anda yakin ingin menghapus obat ini?')) {
            obatList.splice(index, 1);
            updateObatTable();
        }
    };

    // Tambahkan event listener untuk tombol Simpan Hasil Konsultasi
    document.getElementById('simpanKonsultasi').addEventListener('click', function() {
        const hasilKonsultasi = document.querySelector('textarea[name="hasil_konsultasi"]').value;
        const catatanDokter = document.querySelector('textarea[name="catatan_dokter"]').value;
        const statusKonsultasi = document.getElementById('status_konsultasi').value;

        if (!hasilKonsultasi || !catatanDokter || !statusKonsultasi) {
            alert('Silakan lengkapi semua data konsultasi!');
            return;
        }

        if (obatList.length === 0) {
            alert('Silakan tambahkan minimal satu obat!');
            return;
        }

        const formData = new FormData();
        formData.append('hasil_konsultasi', hasilKonsultasi);
        formData.append('catatan_dokter', catatanDokter);
        formData.append('status_konsultasi', statusKonsultasi);
        formData.append('obat', JSON.stringify(obatList));

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Data berhasil disimpan!');
                window.location.href = 'Dashboard_dokter.php';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan data');
        });
    });
});
</script>
	</body>
</html>
