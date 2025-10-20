<?php
// Mengatur header agar output berupa JSON
header('Content-Type: application/json');

// Memasukkan file koneksi database
require 'connect.php'; // pastikan di connect.php kamu: $koneksi = new SQLite3('akademik.sqlite');

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
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $data[] = $row;
            }
            // finalize result jika perlu
            if ($result instanceof SQLite3Result) {
                $result->finalize();
            }
            $response = $data;
        } elseif (!isset($response['error'])) {
            $response = ['error' => 'Gagal mengambil data: ' . $koneksi->lastErrorMsg()];
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
    $ok = false;
    $error_message = '';

    if ($action && $table) {
        switch ($action) {
            case 'create':
                if ($table == 'mahasiswa') {
                    $stmt = $koneksi->prepare("INSERT INTO mahasiswa (nim, nama, prodi, angkatan) VALUES (?, ?, ?, ?)");
                    $stmt->bindValue(1, $data['nim'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(2, $data['nama'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(3, $data['prodi'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(4, isset($data['angkatan']) ? intval($data['angkatan']) : null, SQLITE3_INTEGER);
                } elseif ($table == 'dosen') {
                    $stmt = $koneksi->prepare("INSERT INTO dosen (nidn, nama, bidang_keahlian, email) VALUES (?, ?, ?, ?)");
                    $stmt->bindValue(1, $data['nidn'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(2, $data['nama'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(3, $data['bidang_keahlian'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(4, $data['email'] ?? '', SQLITE3_TEXT);
                } elseif ($table == 'matakuliah') {
                    $stmt = $koneksi->prepare("INSERT INTO mata_kuliah (kode_mk, nama_mk, sks, semester) VALUES (?, ?, ?, ?)");
                    $stmt->bindValue(1, $data['kode_mk'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(2, $data['nama_mk'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(3, isset($data['sks']) ? intval($data['sks']) : null, SQLITE3_INTEGER);
                    $stmt->bindValue(4, isset($data['semester']) ? intval($data['semester']) : null, SQLITE3_INTEGER);
                } elseif ($table == 'nilai') {
                    $stmt = $koneksi->prepare("INSERT INTO nilai (nim, kode_mk, nilai) VALUES (?, ?, ?)");
                    $stmt->bindValue(1, $data['nim'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(2, $data['kode_mk'] ?? '', SQLITE3_TEXT);
                    // gunakan float untuk nilai
                    $stmt->bindValue(3, isset($data['nilai']) ? floatval($data['nilai']) : null, SQLITE3_FLOAT);
                }
                break;
            
            case 'update':
                if ($table == 'mahasiswa') {
                    $stmt = $koneksi->prepare("UPDATE mahasiswa SET nama = ?, prodi = ?, angkatan = ? WHERE nim = ?");
                    $stmt->bindValue(1, $data['nama'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(2, $data['prodi'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(3, isset($data['angkatan']) ? intval($data['angkatan']) : null, SQLITE3_INTEGER);
                    $stmt->bindValue(4, $data['nim'] ?? '', SQLITE3_TEXT);
                } elseif ($table == 'dosen') {
                    $stmt = $koneksi->prepare("UPDATE dosen SET nama = ?, bidang_keahlian = ?, email = ? WHERE nidn = ?");
                    $stmt->bindValue(1, $data['nama'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(2, $data['bidang_keahlian'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(3, $data['email'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(4, $data['nidn'] ?? '', SQLITE3_TEXT);
                } elseif ($table == 'matakuliah') {
                    $stmt = $koneksi->prepare("UPDATE mata_kuliah SET nama_mk = ?, sks = ?, semester = ? WHERE kode_mk = ?");
                    $stmt->bindValue(1, $data['nama_mk'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(2, isset($data['sks']) ? intval($data['sks']) : null, SQLITE3_INTEGER);
                    $stmt->bindValue(3, isset($data['semester']) ? intval($data['semester']) : null, SQLITE3_INTEGER);
                    $stmt->bindValue(4, $data['kode_mk'] ?? '', SQLITE3_TEXT);
                } elseif ($table == 'nilai') {
                    $stmt = $koneksi->prepare("UPDATE nilai SET nim = ?, kode_mk = ?, nilai = ? WHERE id_nilai = ?");
                    $stmt->bindValue(1, $data['nim'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(2, $data['kode_mk'] ?? '', SQLITE3_TEXT);
                    $stmt->bindValue(3, isset($data['nilai']) ? floatval($data['nilai']) : null, SQLITE3_FLOAT);
                    $stmt->bindValue(4, isset($data['id_nilai']) ? intval($data['id_nilai']) : null, SQLITE3_INTEGER);
                }
                break;

            case 'delete':
                 if ($table == 'mahasiswa') {
                    $stmt = $koneksi->prepare("DELETE FROM mahasiswa WHERE nim = ?");
                    $stmt->bindValue(1, $data['key'] ?? '', SQLITE3_TEXT);
                } elseif ($table == 'dosen') {
                    $stmt = $koneksi->prepare("DELETE FROM dosen WHERE nidn = ?");
                    $stmt->bindValue(1, $data['key'] ?? '', SQLITE3_TEXT);
                } elseif ($table == 'matakuliah') {
                    $stmt = $koneksi->prepare("DELETE FROM mata_kuliah WHERE kode_mk = ?");
                    $stmt->bindValue(1, $data['key'] ?? '', SQLITE3_TEXT);
                } elseif ($table == 'nilai') {
                    $stmt = $koneksi->prepare("DELETE FROM nilai WHERE id_nilai = ?");
                    $stmt->bindValue(1, isset($data['key']) ? intval($data['key']) : null, SQLITE3_INTEGER);
                }
                break;
        }

        if ($stmt) {
            $execResult = $stmt->execute();
            // Jika execute mengembalikan SQLite3Result -> kemungkinan SELECT; for DML biasanya boolean-like object or true.
            if ($execResult === false) {
                $error_message = $koneksi->lastErrorMsg();
                $ok = false;
            } else {
                // untuk operasi yang mengembalikan result set (tidak diharapkan di create/update/delete) -> finalize
                if ($execResult instanceof SQLite3Result) {
                    $execResult->finalize();
                }
                // cek error code
                if ($koneksi->lastErrorCode() === 0) {
                    $ok = true;
                } else {
                    $ok = false;
                    $error_message = $koneksi->lastErrorMsg();
                }
            }
            // tutup statement
            $stmt->close();
        } else {
            $error_message = $koneksi->lastErrorMsg();
            $ok = false;
        }

        if ($ok) {
            $response = ['success' => true, 'message' => 'Operasi berhasil.'];
        } else {
            $response = ['success' => false, 'message' => 'Operasi gagal: ' . $error_message];
        }

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