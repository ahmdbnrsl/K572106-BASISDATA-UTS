<?php
// Mengatur header agar output berupa JSON
header('Content-Type: application/json');

// Memasukkan file koneksi database
require 'koneksi.php';

// Menyiapkan array untuk menampung hasil
$response = [];
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // --- Logika untuk mengambil data (READ) ---
    if (isset($_GET['get'])) {
        $get = $_GET['get'];
        $result = null;

        // Menentukan query berdasarkan parameter 'get'
        switch ($get) {
            case 'mahasiswa':
                $sql = "SELECT * FROM mahasiswa ORDER BY nama ASC";
                $result = $koneksi->query($sql);
                break;
            case 'dosen':
                $sql = "SELECT * FROM dosen ORDER BY nama ASC";
                $result = $koneksi->query($sql);
                break;
            case 'matakuliah':
                $sql = "SELECT * FROM mata_kuliah ORDER BY semester ASC, nama_mk ASC";
                $result = $koneksi->query($sql);
                break;
            case 'nilai':
                $sql = "SELECT n.id_nilai, m.nim, m.nama AS nama_mahasiswa, mk.kode_mk, mk.nama_mk, n.nilai 
                        FROM nilai n
                        JOIN mahasiswa m ON n.nim = m.nim
                        JOIN mata_kuliah mk ON n.kode_mk = mk.kode_mk
                        ORDER BY m.nama ASC, mk.nama_mk ASC";
                $result = $koneksi->query($sql);
                break;
            default:
                $response = ['error' => 'Parameter tidak valid'];
                break;
        }

        if ($result) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $response = $data;
        } elseif (!isset($response['error'])) {
            $response = ['error' => 'Gagal mengambil data: ' . $koneksi->error];
        }
    } else {
        $response = ['error' => 'Parameter "get" tidak ditemukan'];
    }

} elseif ($method == 'POST') {
    // --- Logika untuk (CREATE, UPDATE, DELETE) ---
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    $table = $data['table'] ?? '';
    $stmt = null;

    if ($action && $table) {
        switch ($action) {
            case 'create':
                if ($table == 'mahasiswa') {
                    $stmt = $koneksi->prepare("INSERT INTO mahasiswa (nim, nama, prodi, angkatan) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("sssi", $data['nim'], $data['nama'], $data['prodi'], $data['angkatan']);
                } elseif ($table == 'dosen') {
                    $stmt = $koneksi->prepare("INSERT INTO dosen (nidn, nama, bidang_keahlian, email) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $data['nidn'], $data['nama'], $data['bidang_keahlian'], $data['email']);
                } elseif ($table == 'matakuliah') {
                    $stmt = $koneksi->prepare("INSERT INTO mata_kuliah (kode_mk, nama_mk, sks, semester) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssii", $data['kode_mk'], $data['nama_mk'], $data['sks'], $data['semester']);
                } elseif ($table == 'nilai') {
                    $stmt = $koneksi->prepare("INSERT INTO nilai (nim, kode_mk, nilai) VALUES (?, ?, ?)");
                    $stmt->bind_param("ssd", $data['nim'], $data['kode_mk'], $data['nilai']);
                }
                break;
            
            case 'update':
                if ($table == 'mahasiswa') {
                    $stmt = $koneksi->prepare("UPDATE mahasiswa SET nama = ?, prodi = ?, angkatan = ? WHERE nim = ?");
                    $stmt->bind_param("ssis", $data['nama'], $data['prodi'], $data['angkatan'], $data['nim']);
                } elseif ($table == 'dosen') {
                    $stmt = $koneksi->prepare("UPDATE dosen SET nama = ?, bidang_keahlian = ?, email = ? WHERE nidn = ?");
                    $stmt->bind_param("ssss", $data['nama'], $data['bidang_keahlian'], $data['email'], $data['nidn']);
                } elseif ($table == 'matakuliah') {
                    $stmt = $koneksi->prepare("UPDATE mata_kuliah SET nama_mk = ?, sks = ?, semester = ? WHERE kode_mk = ?");
                    $stmt->bind_param("siis", $data['nama_mk'], $data['sks'], $data['semester'], $data['kode_mk']);
                } elseif ($table == 'nilai') {
                    $stmt = $koneksi->prepare("UPDATE nilai SET nim = ?, kode_mk = ?, nilai = ? WHERE id_nilai = ?");
                    $stmt->bind_param("ssdi", $data['nim'], $data['kode_mk'], $data['nilai'], $data['id_nilai']);
                }
                break;

            case 'delete':
                 if ($table == 'mahasiswa') {
                    $stmt = $koneksi->prepare("DELETE FROM mahasiswa WHERE nim = ?");
                    $stmt->bind_param("s", $data['key']);
                } elseif ($table == 'dosen') {
                    $stmt = $koneksi->prepare("DELETE FROM dosen WHERE nidn = ?");
                    $stmt->bind_param("s", $data['key']);
                } elseif ($table == 'matakuliah') {
                    $stmt = $koneksi->prepare("DELETE FROM mata_kuliah WHERE kode_mk = ?");
                    $stmt->bind_param("s", $data['key']);
                } elseif ($table == 'nilai') {
                    $stmt = $koneksi->prepare("DELETE FROM nilai WHERE id_nilai = ?");
                    $stmt->bind_param("i", $data['key']);
                }
                break;
        }

        if ($stmt && $stmt->execute()) {
            $response = ['success' => true, 'message' => 'Operasi berhasil.'];
        } else {
            $error_message = $stmt ? $stmt->error : $koneksi->error;
            $response = ['success' => false, 'message' => 'Operasi gagal: ' . $error_message];
        }
        if($stmt) $stmt->close();

    } else {
        $response = ['success' => false, 'message' => 'Aksi atau tabel tidak spesifik.'];
    }
} else {
    $response = ['error' => 'Metode request tidak didukung'];
}

// Menutup koneksi database
$koneksi->close();

// Mencetak hasil dalam format JSON
echo json_encode($response);
?>

