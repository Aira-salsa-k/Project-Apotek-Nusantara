<?php
session_start();
session_destroy(); // Menghancurkan session
header("Location:Konten.php"); // Arahkan kembali ke halaman landing page
exit();
?>
