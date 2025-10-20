<?php
// Pengaturan untuk koneksi database
$db_host = 'localhost';     // Biasanya 'localhost'
$db_user = 'root';          // User default XAMPP
$db_pass = '';              // Password default XAMPP kosong
$db_name = 'akademik_sederhana'; // Nama database yang dibuat

// Membuat koneksi ke database
$koneksi = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek jika koneksi gagal
if ($koneksi->connect_error) {
    // Hentikan eksekusi dan tampilkan pesan error
    die("Koneksi ke database gagal: " . $koneksi->connect_error);
}

// Mengatur character set menjadi utf8mb4 untuk mendukung berbagai karakter
$koneksi->set_charset("utf8mb4");
?>
