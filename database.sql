-- Buat database baru dengan nama 'akademik_sederhana'
-- CREATE DATABASE IF NOT EXISTS akademik_sederhana DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
-- USE akademik_sederhana;

-- --------------------------------------------------------

-- Struktur tabel untuk `dosen`
CREATE TABLE `dosen` (
  `nidn` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `bidang_keahlian` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
); -- ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Memasukkan data contoh untuk tabel `dosen`
INSERT INTO `dosen` (`nidn`, `nama`, `bidang_keahlian`, `email`) VALUES
('0401019001', 'Agus Setiawan, Ph.D.', 'Sistem Basis Data', 'agus.s@univ.ac.id'),
('0410118001', 'Dr. Indah Kurnia, M.Kom.', 'Kecerdasan Buatan', 'indah.k@univ.ac.id'),
('0415078803', 'Rina Wulandari, S.Kom., M.Cs.', 'Rekayasa Perangkat Lunak', 'rina.w@univ.ac.id'),
('0425057502', 'Prof. Dr. Bambang Hartono, M.T.', 'Jaringan Komputer', 'bambang.h@univ.ac.id');

-- --------------------------------------------------------

-- Struktur tabel untuk `mahasiswa`
CREATE TABLE `mahasiswa` (
  `nim` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `prodi` varchar(50) DEFAULT NULL,
  `angkatan` int(4) DEFAULT NULL
); -- ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Memasukkan data contoh untuk tabel `mahasiswa`
INSERT INTO `mahasiswa` (`nim`, `nama`, `prodi`, `angkatan`) VALUES
('11210010', 'Eko Prasetyo', 'Teknik Informatika', 2021),
('11220001', 'Budi Santoso', 'Teknik Informatika', 2022),
('11220002', 'Citra Lestari', 'Teknik Informatika', 2022),
('12220005', 'Dewi Anggraini', 'Sistem Informasi', 2022),
('13210015', 'Fitriani', 'Manajemen Informatika', 2021);

-- --------------------------------------------------------

-- Struktur tabel untuk `mata_kuliah`
CREATE TABLE `mata_kuliah` (
  `kode_mk` varchar(10) NOT NULL,
  `nama_mk` varchar(100) NOT NULL,
  `sks` int(1) NOT NULL,
  `semester` int(1) NOT NULL
); --ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Memasukkan data contoh untuk tabel `mata_kuliah`
INSERT INTO `mata_kuliah` (`kode_mk`, `nama_mk`, `sks`, `semester`) VALUES
('IF101', 'Algoritma & Pemrograman', 3, 1),
('IF102', 'Struktur Data', 3, 2),
('IF301', 'Kecerdasan Buatan', 3, 4),
('SI201', 'Basis Data', 3, 2),
('UM101', 'Pendidikan Pancasila', 2, 1);

-- --------------------------------------------------------

-- Struktur tabel untuk `nilai`
CREATE TABLE `nilai` (
  `id_nilai` int(11) PRIMARY KEY AUTOINCREMENT,
  `nim` varchar(10) NOT NULL,
  `kode_mk` varchar(10) NOT NULL,
  `nilai` varchar(2) NOT NULL
); --ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Memasukkan data contoh untuk tabel `nilai`
INSERT INTO `nilai` (`id_nilai`, `nim`, `kode_mk`, `nilai`) VALUES
(1, '11220001', 'IF101', 'A'),
(2, '11220001', 'UM101', 'A-'),
(3, '11220002', 'IF101', 'B+'),
(4, '12220005', 'SI201', 'A');

--
-- Indexes for dumped tables
--

-- Indexes for table `dosen`
-- ALTER TABLE `dosen`
--   ADD PRIMARY KEY (`nidn`);

-- -- Indexes for table `mahasiswa`
-- ALTER TABLE `mahasiswa`
--   ADD PRIMARY KEY (`nim`);

-- -- Indexes for table `mata_kuliah`
-- ALTER TABLE `mata_kuliah`
--   ADD PRIMARY KEY (`kode_mk`);

-- -- Indexes for table `nilai`
-- ALTER TABLE `nilai`
--   ADD PRIMARY KEY (`id_nilai`),
--   ADD KEY `nim` (`nim`),
--   ADD KEY `kode_mk` (`kode_mk`);

-- -- AUTO_INCREMENT for dumped tables
-- --
-- ALTER TABLE `nilai`
--   MODIFY `id_nilai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- -- Constraints for dumped tables
-- --
-- ALTER TABLE `nilai`
--   ADD CONSTRAINT `nilai_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`),
--   ADD CONSTRAINT `nilai_ibfk_2` FOREIGN KEY (`kode_mk`) REFERENCES `mata_kuliah` (`kode_mk`);
-- COMMIT;

