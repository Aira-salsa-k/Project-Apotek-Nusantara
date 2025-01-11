<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Data Konsultasi Pasien</title>
		<!-- Bootstrap CSS -->
		<link
			href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
			rel="stylesheet"
		/>
		<link
			rel="stylesheet"
			href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
		/>
		<link
			href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Poppins:wght@400;500&display=swap"
			rel="stylesheet"
		/>
		<link rel="stylesheet" href="../CSS/navbar.css" />
		<link rel="stylesheet" href="../CSS/data_konsultasi.css" />
		
		<style>
        /* Styling untuk header tabel */
        .table thead th {
            background-color: #f0f1f1;
            color: rgb(32, 32, 32);
            font-weight: 550;
            font-size: 1rem;
            padding-bottom: 1.1rem;
            padding-top: 1.1rem;
        }
        .card-title {
            font-family: 'Inter', sans-serif;
        }
        .table {
            font-family: 'Poppins', sans-serif;
        }
    </style>
	</head>
	<body>
		<?php
		session_start();
		require_once('../Koneksi/Config.php');

		// Check for expired consultations and update their status
		$update_expired = "UPDATE konsultasi 
                  SET status_konsultasi = 'Dibatalkan', 
                      nomor_antrian = NULL 
                  WHERE DATE(tanggal_konsultasi) < CURDATE() 
                  AND status_konsultasi IN ('Menunggu', 'On-Progress')";
		mysqli_query($conn, $update_expired);

		// Pastikan hanya dokter yang sudah login yang bisa mengakses
		if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Dokter') {
			header("Location: ../Login.php");
			exit();
		}

		// Get doctor ID from users and dokter tables
		if (!isset($_SESSION['id_dokter'])) {
			$username = $_SESSION['username'];
			$check_query = "SELECT d.id_dokter 
							FROM dokter d 
							JOIN users u ON d.id_user = u.id_user 
							WHERE u.username = ?";
			
			if ($stmt = $conn->prepare($check_query)) {
				$stmt->bind_param("s", $username);
				$stmt->execute();
				$result = $stmt->get_result();
				
				if ($row = $result->fetch_assoc()) {
					$_SESSION['id_dokter'] = $row['id_dokter'];
					$id_dokter = $row['id_dokter'];
				} else {
					session_destroy();
					header("Location: ../Login.php");
					exit();
				}
				$stmt->close();
			} else {
				die("Error in preparing statement: " . $conn->error);
			}
		} else {
			$id_dokter = $_SESSION['id_dokter'];
		}
		?>

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
						<a href="../Logout.php" class="btn btn-custom-green">
							<i class="fas fa-sign-out-alt me-2"></i>LOGOUT
						</a>
					</ul>
				</div>
			</div>
		</nav>
		<!-- End Navbar -->

		<div class="container mt-5">
			<div class="row justify-content-center">
				<div class="col-lg-12">
					<div class="card shadow-sm mb-5">
						<div class="card-body p-4">
							<h4 class="card-title text-success mb-4">
								Data Konsultasi Pasien
							</h4>
							<div class="table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Waktu Konsultasi </th>
											<th>Nama Pasien</th>
											<th>Nama Dokter</th>
											<th>Spesialis</th>
											<th>No Antrian</th>
											<th>Status Konsultasi</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php
										// Fetch consultations for today
										$query = "SELECT k.*, p.nama AS nama_pasien, d.nama AS nama_dokter, 
												COALESCE(s.nama_spesialis, 'Umum') AS spesialis
												FROM konsultasi k 
												JOIN pasien p ON k.id_pasien = p.id_pasien 
												JOIN dokter d ON k.id_dokter = d.id_dokter 
												LEFT JOIN spesialis s ON d.id_dokter = s.id_dokter
												WHERE k.id_dokter = ?
												ORDER BY k.tanggal_konsultasi DESC, k.waktu_konsultasi ASC";

										if ($stmt = $conn->prepare($query)) {
											$stmt->bind_param("i", $id_dokter);
											$stmt->execute();
											$result = $stmt->get_result();

											if ($result->num_rows > 0) {
												while ($row = $result->fetch_assoc()) {
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
													<tr>
														<td><?php echo date('H:i', strtotime($row['waktu_konsultasi'])) . ' WIT'; ?></td>
														<td><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
														<td><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
														<td><?php echo htmlspecialchars($row['spesialis']); ?></td>
														<td><?php echo $row['nomor_antrian'] ? htmlspecialchars($row['nomor_antrian']) : '-'; ?></td>
														<td><span class="badge <?php echo $status_class; ?>"><?php echo $row['status_konsultasi']; ?></span></td>
														<td>
															<div class="btn-group" role="group">
																<!-- View Button -->
																<button class="btn btn-sm btn-info" 
																		onclick="window.location.href='Detail_data_pasien.php?id_konsultasi=<?php echo $row['id_konsultasi']; ?>'"
																		title="Lihat Detail">
																	<i class="fas fa-eye"></i>
																</button>
																
																<!-- Edit Button -->
																<button class="btn btn-sm btn-warning" 
																		onclick="window.location.href='edit_konsultasi.php?id=<?php echo $row['id_konsultasi']; ?>'"
																		title="Edit Konsultasi">
																	<i class="fas fa-edit"></i>
																</button>
																
																<!-- Delete Button -->
																<button class="btn btn-sm btn-danger" 
																		onclick="confirmDelete(<?php echo $row['id_konsultasi']; ?>)"
																		title="Hapus Konsultasi">
																	<i class="fas fa-trash"></i>
																</button>

																<!-- Add Consultation Result Button -->
																<button class="btn btn-sm btn-success" 
																		onclick="window.location.href='Tambah_hasil_konsultasi.php?id=<?php echo $row['id_konsultasi']; ?>'"
																		title="Isi Data Konsultasi"
																		<?php echo $row['status_konsultasi'] != 'On-Progress' ? 'disabled' : ''; ?>>
																	<i class="fas fa-notes-medical"></i>
																</button>
															</div>
														</td>
													</tr>
												<?php }
											} else {
												echo "<tr><td colspan='7' class='text-center'>Tidak ada konsultasi yang tersedia</td></tr>";
											}
											$stmt->close();
										} else {
											echo "<tr><td colspan='7' class='text-center text-danger'>Error: " . $conn->error . "</td></tr>";
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Bootstrap Bundle JS -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script>
		function confirmDelete(id) {
			if (confirm('Apakah Anda yakin ingin menghapus konsultasi ini?')) {
				window.location.href = 'hapus_konsultasi.php?id=' + id;
			}
		}
		</script>
	</body>
</html>
