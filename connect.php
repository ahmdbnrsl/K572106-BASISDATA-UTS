<?php
// Nama file database SQLite
$db_name = 'akademik.sqlite';

// Membuat koneksi ke database SQLite
$koneksi = new SQLite3($db_name);

// Cek apakah koneksi berhasil (opsional, karena SQLite3 otomatis bikin file baru kalau belum ada)
if (!$koneksi) {
    die("Koneksi ke database SQLite gagal!");
}

// Mengatur pragma agar mendukung UTF-8 (setara dengan set_charset di MySQL)
$koneksi->exec("PRAGMA encoding = 'UTF-8';");

// Opsional: tes koneksi
//echo "Koneksi ke database SQLite berhasil!";
?>