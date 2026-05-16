-- Adminer 5.4.2 MySQL 8.0.30 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `armada_truk`;
CREATE TABLE `armada_truk` (
  `id_truk` int NOT NULL AUTO_INCREMENT,
  `nomor_polisi` varchar(20) NOT NULL,
  `kapasitas` varchar(50) NOT NULL,
  `status` enum('Sedang diproses','Dalam pengiriman','Selesai') NOT NULL DEFAULT 'Selesai',
  PRIMARY KEY (`id_truk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `armada_truk` (`id_truk`, `nomor_polisi`, `kapasitas`, `status`) VALUES
(1,	'123',	'120kg',	'Dalam pengiriman'),
(2,	'231',	'125',	'Dalam pengiriman');

DROP TABLE IF EXISTS `detail_pesanan_online`;
CREATE TABLE `detail_pesanan_online` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `id_produk` int NOT NULL,
  `jumlah` int NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_pesanan` (`id_pesanan`),
  KEY `id_produk` (`id_produk`),
  CONSTRAINT `detail_pesanan_online_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`),
  CONSTRAINT `detail_pesanan_online_ibfk_3` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan_online` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `detail_pesanan_online` (`id_detail`, `id_pesanan`, `id_produk`, `jumlah`, `harga`, `subtotal`) VALUES
(14,	1,	7,	1,	90000.00,	90000.00),
(15,	1,	6,	1,	15000.00,	15000.00),
(16,	1,	4,	1,	85000.00,	85000.00),
(17,	2,	11,	1,	520000.00,	520000.00),
(18,	3,	17,	1,	75000.00,	75000.00),
(19,	4,	12,	1,	70000.00,	70000.00),
(20,	4,	10,	1,	65000.00,	65000.00),
(21,	5,	16,	1,	60000.00,	60000.00),
(22,	5,	18,	1,	160000.00,	160000.00);

DROP TABLE IF EXISTS `detail_transaksi`;
CREATE TABLE `detail_transaksi` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `id_transaksi` int NOT NULL,
  `id_produk` int NOT NULL,
  `jumlah` int NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_transaksi` (`id_transaksi`),
  KEY `id_produk` (`id_produk`),
  CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `detail_transaksi` (`id_detail`, `id_transaksi`, `id_produk`, `jumlah`, `harga`, `subtotal`) VALUES
(1,	1,	2,	1,	150000.00,	150000.00),
(2,	3,	17,	1,	75000.00,	75000.00);

DROP TABLE IF EXISTS `hutang_piutang`;
CREATE TABLE `hutang_piutang` (
  `id_hp` int NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int NOT NULL,
  `jenis` enum('Hutang','Piutang') NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `tanggal` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'belum_lunas',
  PRIMARY KEY (`id_hp`),
  KEY `id_pelanggan` (`id_pelanggan`),
  CONSTRAINT `hutang_piutang_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


DROP TABLE IF EXISTS `karyawan`;
CREATE TABLE `karyawan` (
  `id_karyawan` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(50) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `gaji` decimal(12,2) NOT NULL DEFAULT '0.00',
  `id_role` int NOT NULL,
  PRIMARY KEY (`id_karyawan`),
  KEY `id_role` (`id_role`),
  CONSTRAINT `karyawan_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `karyawan` (`id_karyawan`, `nama`, `jabatan`, `no_hp`, `gaji`, `id_role`) VALUES
(1,	'Pemilik Toko',	'Owner',	'081234560001',	0.00,	1),
(2,	'Budi Santoso',	'Admin',	'081234560002',	4500000.00,	2),
(3,	'Sari Dewi',	'Kasir',	'081234560003',	3000000.00,	3),
(4,	'Roni Wijaya',	'Gudang',	'081234560004',	3000000.00,	4);

DROP TABLE IF EXISTS `keuangan`;
CREATE TABLE `keuangan` (
  `id_keuangan` int NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `jenis` enum('Pemasukan','Pengeluaran') NOT NULL,
  `sumber` varchar(50) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `keterangan` text NOT NULL,
  PRIMARY KEY (`id_keuangan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `keuangan` (`id_keuangan`, `tanggal`, `jenis`, `sumber`, `jumlah`, `keterangan`) VALUES
(1,	'2026-05-15',	'Pemasukan',	'-',	100000.00,	'-');

DROP TABLE IF EXISTS `pelanggan`;
CREATE TABLE `pelanggan` (
  `id_pelanggan` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id_pelanggan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `pelanggan` (`id_pelanggan`, `nama`, `no_hp`, `alamat`, `email`) VALUES
(1,	'Nur Munifa',	'089023090201',	'Jl G Obos',	'nifa@gmail.com'),
(2,	'Dea Halida',	'089087678908',	'JL G Obos',	'dea@gmail.com'),
(3,	'Rhea Alfia',	'089076574834',	'JL Tjilik Riwut',	'rhea@gmail.com'),
(4,	'Amrik Juniadi',	'087020906890',	'JL Badak Induk',	'amrik@gmail.com'),
(5,	'Dania Aprianti',	'085056478934',	'JL Hiu Putih NO.20',	'dania@gmail.com');

DROP TABLE IF EXISTS `penggajian`;
CREATE TABLE `penggajian` (
  `id_penggajian` int NOT NULL AUTO_INCREMENT,
  `id_karyawan` int NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_penggajian`),
  KEY `id_karyawan` (`id_karyawan`),
  CONSTRAINT `penggajian_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `penggajian` (`id_penggajian`, `id_karyawan`, `tanggal`, `jumlah`) VALUES
(1,	3,	'2026-05-10',	1000000.00);

DROP TABLE IF EXISTS `pengiriman`;
CREATE TABLE `pengiriman` (
  `id_pengiriman` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `id_truk` int NOT NULL,
  `tanggal_kirim` date NOT NULL,
  `status` enum('Sedang diproses','Dalam pengiriman','Selesai') NOT NULL DEFAULT 'Sedang diproses',
  PRIMARY KEY (`id_pengiriman`),
  KEY `id_pesanan` (`id_pesanan`),
  KEY `id_truk` (`id_truk`),
  CONSTRAINT `pengiriman_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan_online` (`id_pesanan`),
  CONSTRAINT `pengiriman_ibfk_2` FOREIGN KEY (`id_truk`) REFERENCES `armada_truk` (`id_truk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `pengiriman` (`id_pengiriman`, `id_pesanan`, `alamat`, `id_truk`, `tanggal_kirim`, `status`) VALUES
(1,	2,	'JL G Obos',	1,	'2026-05-14',	'Sedang diproses'),
(2,	4,	'JL Badak Induk',	1,	'2026-05-14',	'Sedang diproses'),
(3,	1,	'Jl G Obos',	2,	'2026-05-14',	'Sedang diproses'),
(4,	3,	'JL Tjilik Riwut',	2,	'2026-05-14',	'Sedang diproses'),
(5,	5,	'JL Hiu Putih NO.20',	1,	'2026-05-14',	'Sedang diproses');

DROP TABLE IF EXISTS `pesanan_online`;
CREATE TABLE `pesanan_online` (
  `id_pesanan` int NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int NOT NULL,
  `tanggal` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(12,2) NOT NULL,
  `status` enum('pending','diproses','dikirim','selesai','dibatalkan') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id_pesanan`),
  KEY `id_pelanggan` (`id_pelanggan`),
  CONSTRAINT `pesanan_online_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `pesanan_online` (`id_pesanan`, `id_pelanggan`, `tanggal`, `total`, `status`) VALUES
(1,	1,	'2026-05-08 15:17:20',	190000.00,	'dikirim'),
(2,	2,	'2026-05-13 15:23:59',	520000.00,	'dikirim'),
(3,	3,	'2026-05-13 15:58:53',	75000.00,	'dikirim'),
(4,	4,	'2026-05-14 04:52:49',	135000.00,	'dikirim'),
(5,	5,	'2026-05-14 08:16:33',	220000.00,	'dikirim');

DROP TABLE IF EXISTS `produk`;
CREATE TABLE `produk` (
  `id_produk` int NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `harga_beli` decimal(12,2) NOT NULL,
  `harga_jual` decimal(12,2) NOT NULL,
  `barcode` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `foto` varchar(255) DEFAULT NULL COMMENT 'Foto Produk',
  PRIMARY KEY (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `produk` (`id_produk`, `nama_produk`, `kategori`, `satuan`, `harga_beli`, `harga_jual`, `barcode`, `created_at`, `foto`) VALUES
(1,	'Seng Warna',	'Seng',	'Pcs',	50000.00,	75000.00,	'101010',	'2026-05-06 08:39:31',	'seng-warna.jpg'),
(2,	'Paku Payung',	'Paku',	'Kg',	5000.00,	10000.00,	'202020',	'2026-05-06 08:40:04',	'paku-payung.jpg'),
(4,	'Semen PCC',	'Semen',	'Sak',	50000.00,	85000.00,	'102030',	'2026-05-07 16:25:17',	'1778171117_semen.png'),
(5,	'Seng Alumunium',	'Seng',	'Pcs',	25000.00,	55000.00,	'203040',	'2026-05-07 16:28:39',	'1778171319_seng.jpg'),
(6,	'Paku Kayu',	'Paku',	'Kg',	10000.00,	15000.00,	'304050',	'2026-05-07 16:30:45',	'1778171445_paku.jpeg'),
(7,	'Cat Tembok',	'Cat',	'Liter',	55000.00,	90000.00,	'405060',	'2026-05-07 16:34:20',	'1778171660_cat.jpg'),
(10,	'Obeng Set 6 pcs',	'Alat',	'Set',	45000.00,	65000.00,	'456500',	'2026-05-08 16:27:07',	'1778257627_obeng-set6.jpg'),
(11,	'Bor Listrik',	'Mesin',	'Unit',	450000.00,	520000.00,	'519900',	'2026-05-08 16:30:36',	'1778257836_bor.jpg'),
(12,	'Keramik 40x40 Motif',	'Keramik',	'Dus',	50000.00,	70000.00,	'007050',	'2026-05-08 16:37:04',	'1778258224_keramik.jpg'),
(13,	'Selang Air 20m',	'Taman',	'Roll',	85000.00,	110000.00,	'850099',	'2026-05-08 16:47:22',	'1778258842_selang.jpg'),
(14,	'Helm Proyek',	'Safety',	'Buah',	35000.00,	50000.00,	'350049',	'2026-05-08 16:53:26',	'1778259206_helmproyek.jpg'),
(16,	'Sekop Pasir',	'Alat',	'Buah',	45000.00,	60000.00,	'999900',	'2026-05-08 17:00:47',	'1778259647_sekopbb.jpg'),
(17,	'Cangkul Garuk Rumput',	'Alat',	'Buah',	55000.00,	75000.00,	'000055',	'2026-05-08 17:07:02',	'1778260022_cangkul.jpeg'),
(18,	'Closet Flush Tank',	'Sanitary',	'Unit',	120000.00,	160000.00,	'161200',	'2026-05-08 17:12:00',	'1778260320_closet.jpg'),
(19,	'Saklar Panasonic',	'Saklar',	'Buah',	18000.00,	25000.00,	'180025',	'2026-05-08 17:15:28',	'1778260528_saklar7.jpg'),
(20,	'Stop Kontak Panasonic',	'Stop Kontak',	'Buah',	18000.00,	28000.00,	'180028',	'2026-05-08 17:21:18',	'1778260878_fb22bb19022d4495e180811271a7745a.jpg');

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id_role` int NOT NULL,
  `nama_role` varchar(50) NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `role` (`id_role`, `nama_role`) VALUES
(1,	'Owner'),
(2,	'Admin'),
(3,	'Kasir'),
(4,	'Gudang');

DROP TABLE IF EXISTS `stok_mutasi`;
CREATE TABLE `stok_mutasi` (
  `id_mutasi` int NOT NULL AUTO_INCREMENT,
  `id_produk` int NOT NULL,
  `tanggal` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `jenis` varchar(20) NOT NULL,
  `jumlah` int NOT NULL,
  `referensi` varchar(100) NOT NULL,
  `keterangan` text NOT NULL,
  PRIMARY KEY (`id_mutasi`),
  KEY `id_produk` (`id_produk`),
  CONSTRAINT `stok_mutasi_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `stok_mutasi` (`id_mutasi`, `id_produk`, `tanggal`, `jenis`, `jumlah`, `referensi`, `keterangan`) VALUES
(1,	1,	'2026-05-06 08:58:11',	'seng',	15,	'-',	'amfmamda'),
(2,	2,	'2026-05-06 08:58:35',	'Paku',	149,	'-',	'jqkhqkq'),
(3,	2,	'2026-05-07 04:08:26',	'masuk',	10,	'PO-2026-07-05',	''),
(4,	1,	'2026-05-07 04:08:57',	'masuk',	200,	'PO-2026-08',	''),
(5,	1,	'2026-05-07 12:15:51',	'keluar',	3,	'PO-6',	'Pesanan Online #6'),
(6,	1,	'2026-05-07 12:15:55',	'keluar',	3,	'PO-7',	'Pesanan Online #7'),
(7,	1,	'2026-05-07 12:18:03',	'keluar',	3,	'PO-8',	'Pesanan Online #8'),
(8,	1,	'2026-05-07 12:20:20',	'keluar',	3,	'PO-9',	'Pesanan Online #9'),
(9,	1,	'2026-05-07 12:20:20',	'keluar',	3,	'PO-9',	'Pesanan Online #9'),
(10,	1,	'2026-05-07 13:43:03',	'keluar',	3,	'PO-10',	'Pesanan Online #10'),
(11,	1,	'2026-05-07 13:43:03',	'keluar',	3,	'PO-10',	'Pesanan Online #10'),
(12,	2,	'2026-05-07 13:43:03',	'keluar',	1,	'PO-10',	'Pesanan Online #10'),
(13,	2,	'2026-05-07 13:43:03',	'keluar',	1,	'PO-10',	'Pesanan Online #10'),
(14,	2,	'2026-05-07 13:49:51',	'keluar',	1,	'PO-11',	'Pesanan Online #11'),
(15,	2,	'2026-05-07 13:49:51',	'keluar',	1,	'PO-11',	'Pesanan Online #11'),
(16,	2,	'2026-05-07 13:53:26',	'keluar',	2,	'PO-12',	'Pesanan Online #12'),
(17,	2,	'2026-05-07 13:53:26',	'keluar',	2,	'PO-12',	'Pesanan Online #12'),
(18,	1,	'2026-05-07 13:58:51',	'keluar',	3,	'PO-13',	'Pesanan Online #13'),
(19,	1,	'2026-05-07 13:58:51',	'keluar',	3,	'PO-13',	'Pesanan Online #13'),
(20,	1,	'2026-05-07 15:53:53',	'keluar',	2,	'PO-14',	'Pesanan Online #14'),
(21,	1,	'2026-05-07 15:53:53',	'keluar',	2,	'PO-14',	'Pesanan Online #14'),
(22,	2,	'2026-05-07 15:53:53',	'keluar',	1,	'PO-14',	'Pesanan Online #14'),
(23,	2,	'2026-05-07 15:53:53',	'keluar',	1,	'PO-14',	'Pesanan Online #14'),
(24,	7,	'2026-05-07 16:36:31',	'masuk',	99,	'PM-01',	''),
(25,	2,	'2026-05-07 16:37:06',	'masuk',	150,	'PM-02',	''),
(26,	4,	'2026-05-07 16:37:36',	'masuk',	198,	'PM-03',	''),
(27,	5,	'2026-05-07 16:39:09',	'masuk',	250,	'PM-04',	''),
(28,	6,	'2026-05-07 16:39:43',	'masuk',	500,	'PM-05',	''),
(29,	7,	'2026-05-08 03:04:54',	'keluar',	1,	'PO-15',	'Pesanan Online #15'),
(30,	7,	'2026-05-08 03:04:54',	'keluar',	1,	'PO-15',	'Pesanan Online #15'),
(31,	1,	'2026-05-08 03:04:54',	'keluar',	1,	'PO-15',	'Pesanan Online #15'),
(32,	1,	'2026-05-08 03:04:54',	'keluar',	1,	'PO-15',	'Pesanan Online #15'),
(33,	6,	'2026-05-08 04:23:49',	'keluar',	1,	'PO-16',	'Pesanan Online #16'),
(34,	6,	'2026-05-08 04:23:49',	'keluar',	1,	'PO-16',	'Pesanan Online #16'),
(35,	6,	'2026-05-08 07:32:11',	'keluar',	1,	'PO-17',	'Pesanan Online #17'),
(36,	7,	'2026-05-08 12:29:15',	'keluar',	1,	'PO-18',	'Pesanan Online #18'),
(37,	6,	'2026-05-08 12:29:15',	'keluar',	1,	'PO-18',	'Pesanan Online #18'),
(38,	2,	'2026-05-08 12:29:15',	'keluar',	1,	'PO-18',	'Pesanan Online #18'),
(39,	2,	'2026-05-08 12:30:03',	'keluar',	1,	'PO-19',	'Pesanan Online #19'),
(40,	4,	'2026-05-08 12:30:03',	'keluar',	1,	'PO-19',	'Pesanan Online #19'),
(41,	5,	'2026-05-08 12:30:03',	'keluar',	1,	'PO-19',	'Pesanan Online #19'),
(42,	1,	'2026-05-08 12:30:03',	'keluar',	1,	'PO-19',	'Pesanan Online #19'),
(43,	7,	'2026-05-08 14:41:27',	'keluar',	1,	'PO-20',	'Pesanan Online #20'),
(44,	7,	'2026-05-08 14:41:27',	'keluar',	1,	'PO-20',	'Pesanan Online #20'),
(45,	2,	'2026-05-08 14:41:27',	'keluar',	1,	'PO-20',	'Pesanan Online #20'),
(46,	2,	'2026-05-08 14:41:27',	'keluar',	1,	'PO-20',	'Pesanan Online #20'),
(47,	6,	'2026-05-08 14:42:06',	'keluar',	1,	'PO-21',	'Pesanan Online #21'),
(48,	6,	'2026-05-08 14:42:06',	'keluar',	1,	'PO-21',	'Pesanan Online #21'),
(49,	4,	'2026-05-08 14:42:06',	'keluar',	1,	'PO-21',	'Pesanan Online #21'),
(50,	4,	'2026-05-08 14:42:06',	'keluar',	1,	'PO-21',	'Pesanan Online #21'),
(51,	1,	'2026-05-08 14:42:06',	'keluar',	1,	'PO-21',	'Pesanan Online #21'),
(52,	1,	'2026-05-08 14:42:06',	'keluar',	1,	'PO-21',	'Pesanan Online #21'),
(53,	6,	'2026-05-08 14:42:49',	'keluar',	1,	'PO-22',	'Pesanan Online #22'),
(54,	6,	'2026-05-08 14:42:49',	'keluar',	1,	'PO-22',	'Pesanan Online #22'),
(55,	1,	'2026-05-08 14:42:49',	'keluar',	3,	'PO-22',	'Pesanan Online #22'),
(56,	1,	'2026-05-08 14:42:49',	'keluar',	3,	'PO-22',	'Pesanan Online #22'),
(57,	7,	'2026-05-08 14:47:25',	'keluar',	3,	'PO-23',	'Pesanan Online #23'),
(58,	7,	'2026-05-08 14:47:25',	'keluar',	3,	'PO-23',	'Pesanan Online #23'),
(59,	6,	'2026-05-08 14:47:25',	'keluar',	1,	'PO-23',	'Pesanan Online #23'),
(60,	6,	'2026-05-08 14:47:25',	'keluar',	1,	'PO-23',	'Pesanan Online #23'),
(61,	4,	'2026-05-08 14:48:24',	'keluar',	1,	'PO-24',	'Pesanan Online #24'),
(62,	4,	'2026-05-08 14:48:24',	'keluar',	1,	'PO-24',	'Pesanan Online #24'),
(63,	7,	'2026-05-08 14:48:24',	'keluar',	1,	'PO-24',	'Pesanan Online #24'),
(64,	7,	'2026-05-08 14:48:24',	'keluar',	1,	'PO-24',	'Pesanan Online #24'),
(65,	6,	'2026-05-08 14:48:24',	'keluar',	1,	'PO-24',	'Pesanan Online #24'),
(66,	6,	'2026-05-08 14:48:24',	'keluar',	1,	'PO-24',	'Pesanan Online #24'),
(67,	2,	'2026-05-08 14:48:24',	'keluar',	1,	'PO-24',	'Pesanan Online #24'),
(68,	2,	'2026-05-08 14:48:24',	'keluar',	1,	'PO-24',	'Pesanan Online #24'),
(69,	1,	'2026-05-08 14:48:24',	'keluar',	1,	'PO-24',	'Pesanan Online #24'),
(70,	1,	'2026-05-08 14:48:24',	'keluar',	1,	'PO-24',	'Pesanan Online #24'),
(71,	5,	'2026-05-08 14:49:15',	'keluar',	4,	'PO-25',	'Pesanan Online #25'),
(72,	5,	'2026-05-08 14:49:15',	'keluar',	4,	'PO-25',	'Pesanan Online #25'),
(73,	4,	'2026-05-08 14:49:15',	'keluar',	1,	'PO-25',	'Pesanan Online #25'),
(74,	4,	'2026-05-08 14:49:15',	'keluar',	1,	'PO-25',	'Pesanan Online #25'),
(75,	7,	'2026-05-08 15:11:30',	'keluar',	3,	'PO-1',	'Pesanan Online #1'),
(76,	7,	'2026-05-08 15:11:30',	'keluar',	3,	'PO-1',	'Pesanan Online #1'),
(77,	7,	'2026-05-08 15:17:20',	'keluar',	1,	'PO-1',	'Pesanan Online #1'),
(78,	6,	'2026-05-08 15:17:20',	'keluar',	1,	'PO-1',	'Pesanan Online #1'),
(79,	4,	'2026-05-08 15:17:20',	'keluar',	1,	'PO-1',	'Pesanan Online #1'),
(80,	19,	'2026-05-08 17:29:18',	'masuk',	60,	'PM-9',	''),
(81,	11,	'2026-05-08 17:30:30',	'masuk',	55,	'PM-10',	''),
(82,	11,	'2026-05-08 17:31:06',	'masuk',	43,	'PM-12',	''),
(83,	10,	'2026-05-08 17:31:40',	'masuk',	38,	'PM-13',	''),
(84,	12,	'2026-05-08 17:32:15',	'masuk',	48,	'PM-15',	''),
(85,	13,	'2026-05-08 17:32:47',	'masuk',	70,	'PM-17',	''),
(86,	14,	'2026-05-08 17:33:25',	'masuk',	98,	'PM-19',	''),
(87,	16,	'2026-05-08 17:34:28',	'masuk',	120,	'PM-11',	''),
(88,	17,	'2026-05-08 17:35:03',	'masuk',	149,	'PM-14',	''),
(89,	20,	'2026-05-08 17:35:45',	'masuk',	89,	'PM-18',	''),
(90,	19,	'2026-05-08 17:36:14',	'masuk',	97,	'PM-20',	''),
(91,	18,	'2026-05-08 17:36:57',	'masuk',	48,	'PM-21',	''),
(92,	11,	'2026-05-13 15:23:59',	'keluar',	1,	'PO-2',	'Pesanan Online #2'),
(93,	17,	'2026-05-13 15:33:01',	'keluar',	1,	'TRX-3',	'Penjualan kasir'),
(94,	17,	'2026-05-13 15:58:53',	'keluar',	1,	'PO-3',	'Pesanan Online #3'),
(95,	12,	'2026-05-14 04:52:49',	'keluar',	1,	'PO-4',	'Pesanan Online #4'),
(96,	10,	'2026-05-14 04:52:49',	'keluar',	1,	'PO-4',	'Pesanan Online #4'),
(97,	16,	'2026-05-14 08:16:33',	'keluar',	1,	'PO-5',	'Pesanan Online #5'),
(98,	18,	'2026-05-14 08:16:33',	'keluar',	1,	'PO-5',	'Pesanan Online #5');

DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL AUTO_INCREMENT,
  `tanggal` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(12,2) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `id_kasir` int NOT NULL,
  PRIMARY KEY (`id_transaksi`),
  KEY `id_kasir` (`id_kasir`),
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_kasir`) REFERENCES `karyawan` (`id_karyawan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `transaksi` (`id_transaksi`, `tanggal`, `total`, `metode_pembayaran`, `id_kasir`) VALUES
(1,	'2026-05-06 08:44:15',	1500000.00,	'cash',	1),
(2,	'2026-05-06 08:44:37',	100000.00,	'transfer',	2),
(3,	'2026-05-13 15:33:01',	75000.00,	'QRIS',	3);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_karyawan` int NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  KEY `id_karyawan` (`id_karyawan`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `users` (`id_user`, `username`, `password`, `id_karyawan`) VALUES
(1,	'owner',	'5be057accb25758101fa5eadbbd79503',	1),
(2,	'admin',	'0192023a7bbd73250516f069df18b500',	2),
(3,	'kasir',	'de28f8f7998f23ab4194b51a6029416f',	3),
(4,	'gudang',	'cbb7449d78314665f9e7c7dd0a18a68a',	4);

DROP VIEW IF EXISTS `view_detail_penjualan`;
CREATE TABLE `view_detail_penjualan` (`id_transaksi` int, `tanggal` timestamp, `nama_produk` varchar(100), `jumlah` int, `harga` decimal(12,2), `subtotal` decimal(12,2));


DROP VIEW IF EXISTS `view_hutang_piutang_aktif`;
CREATE TABLE `view_hutang_piutang_aktif` (`nama` varchar(100), `jenis` enum('Hutang','Piutang'), `jumlah` decimal(12,2), `status` varchar(50));


DROP VIEW IF EXISTS `view_keuangan`;
CREATE TABLE `view_keuangan` (`tanggal` date, `jenis` enum('Pemasukan','Pengeluaran'), `sumber` varchar(50), `jumlah` decimal(12,2), `keterangan` text);


DROP VIEW IF EXISTS `view_laba_rugi`;
CREATE TABLE `view_laba_rugi` (`total_pemasukan` decimal(34,2), `total_pengeluaran` decimal(34,2), `laba_bersih` decimal(35,2));


DROP VIEW IF EXISTS `view_laporan_penjualan`;
CREATE TABLE `view_laporan_penjualan` (`id_transaksi` int, `tanggal` timestamp, `kasir` varchar(100), `total_penjualan` decimal(34,2));


DROP VIEW IF EXISTS `view_pengiriman`;
CREATE TABLE `view_pengiriman` (`id_pengiriman` int, `pelanggan` varchar(100), `nomor_polisi` varchar(20), `tanggal_kirim` date, `status` enum('Sedang diproses','Dalam pengiriman','Selesai'));


DROP VIEW IF EXISTS `view_penjualan_harian`;
CREATE TABLE `view_penjualan_harian` (`tanggal` date, `total_harian` decimal(34,2));


DROP VIEW IF EXISTS `view_pesanan_online`;
CREATE TABLE `view_pesanan_online` (`id_pesanan` int, `pelanggan` varchar(100), `tanggal` timestamp, `total` decimal(12,2), `status` enum('pending','diproses','dikirim','selesai','dibatalkan'));


DROP VIEW IF EXISTS `view_produk_terlaris`;
CREATE TABLE `view_produk_terlaris` (`nama_produk` varchar(100), `total_terjual` decimal(32,0));


DROP VIEW IF EXISTS `view_stok_produk`;
CREATE TABLE `view_stok_produk` (`id_produk` int, `nama_produk` varchar(100), `kategori` varchar(50), `satuan` varchar(20), `stok_akkhir` decimal(32,0));


DROP VIEW IF EXISTS `view_user_role`;
CREATE TABLE `view_user_role` (`username` varchar(50), `nama` varchar(100), `nama_role` varchar(50));


DROP TABLE IF EXISTS `view_detail_penjualan`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_detail_penjualan` AS select `t`.`id_transaksi` AS `id_transaksi`,`t`.`tanggal` AS `tanggal`,`p`.`nama_produk` AS `nama_produk`,`d`.`jumlah` AS `jumlah`,`d`.`harga` AS `harga`,`d`.`subtotal` AS `subtotal` from ((`transaksi` `t` join `detail_transaksi` `d` on((`t`.`id_transaksi` = `d`.`id_transaksi`))) join `produk` `p` on((`d`.`id_produk` = `p`.`id_produk`)));

DROP TABLE IF EXISTS `view_hutang_piutang_aktif`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_hutang_piutang_aktif` AS select `p`.`nama` AS `nama`,`hp`.`jenis` AS `jenis`,`hp`.`jumlah` AS `jumlah`,`hp`.`status` AS `status` from (`hutang_piutang` `hp` join `pelanggan` `p` on((`hp`.`id_pelanggan` = `p`.`id_pelanggan`))) where (`hp`.`status` = 'belum_lunas');

DROP TABLE IF EXISTS `view_keuangan`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_keuangan` AS select `keuangan`.`tanggal` AS `tanggal`,`keuangan`.`jenis` AS `jenis`,`keuangan`.`sumber` AS `sumber`,`keuangan`.`jumlah` AS `jumlah`,`keuangan`.`keterangan` AS `keterangan` from `keuangan`;

DROP TABLE IF EXISTS `view_laba_rugi`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_laba_rugi` AS select sum((case when (`keuangan`.`jenis` = 'Pemasukan') then `keuangan`.`jumlah` else 0 end)) AS `total_pemasukan`,sum((case when (`keuangan`.`jenis` = 'Pengeluaran') then `keuangan`.`jumlah` else 0 end)) AS `total_pengeluaran`,(sum((case when (`keuangan`.`jenis` = 'Pemasukan') then `keuangan`.`jumlah` else 0 end)) - sum((case when (`keuangan`.`jenis` = 'Pengeluaran') then `keuangan`.`jumlah` else 0 end))) AS `laba_bersih` from `keuangan`;

DROP TABLE IF EXISTS `view_laporan_penjualan`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_laporan_penjualan` AS select `t`.`id_transaksi` AS `id_transaksi`,`t`.`tanggal` AS `tanggal`,`k`.`nama` AS `kasir`,sum(`d`.`subtotal`) AS `total_penjualan` from ((`transaksi` `t` join `detail_transaksi` `d` on((`t`.`id_transaksi` = `d`.`id_transaksi`))) join `karyawan` `k` on((`t`.`id_kasir` = `k`.`id_karyawan`))) group by `t`.`id_transaksi`;

DROP TABLE IF EXISTS `view_pengiriman`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_pengiriman` AS select `pg`.`id_pengiriman` AS `id_pengiriman`,`pl`.`nama` AS `pelanggan`,`at`.`nomor_polisi` AS `nomor_polisi`,`pg`.`tanggal_kirim` AS `tanggal_kirim`,`pg`.`status` AS `status` from (((`pengiriman` `pg` join `pesanan_online` `po` on((`pg`.`id_pesanan` = `po`.`id_pesanan`))) join `pelanggan` `pl` on((`po`.`id_pelanggan` = `pl`.`id_pelanggan`))) join `armada_truk` `at` on((`pg`.`id_truk` = `at`.`id_truk`)));

DROP TABLE IF EXISTS `view_penjualan_harian`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_penjualan_harian` AS select cast(`t`.`tanggal` as date) AS `tanggal`,sum(`d`.`subtotal`) AS `total_harian` from (`transaksi` `t` join `detail_transaksi` `d` on((`t`.`id_transaksi` = `d`.`id_transaksi`))) group by cast(`t`.`tanggal` as date);

DROP TABLE IF EXISTS `view_pesanan_online`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_pesanan_online` AS select `po`.`id_pesanan` AS `id_pesanan`,`pl`.`nama` AS `pelanggan`,`po`.`tanggal` AS `tanggal`,`po`.`total` AS `total`,`po`.`status` AS `status` from (`pesanan_online` `po` join `pelanggan` `pl` on((`po`.`id_pelanggan` = `pl`.`id_pelanggan`)));

DROP TABLE IF EXISTS `view_produk_terlaris`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_produk_terlaris` AS select `p`.`nama_produk` AS `nama_produk`,sum(`d`.`jumlah`) AS `total_terjual` from (`detail_transaksi` `d` join `produk` `p` on((`d`.`id_produk` = `p`.`id_produk`))) group by `d`.`id_produk` order by sum(`d`.`jumlah`) desc;

DROP TABLE IF EXISTS `view_stok_produk`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_stok_produk` AS select `p`.`id_produk` AS `id_produk`,`p`.`nama_produk` AS `nama_produk`,`p`.`kategori` AS `kategori`,`p`.`satuan` AS `satuan`,coalesce(sum((case when (`sm`.`jenis` = 'masuk') then `sm`.`jumlah` when (`sm`.`jenis` = 'keluar') then -(`sm`.`jumlah`) end)),0) AS `stok_akkhir` from (`produk` `p` left join `stok_mutasi` `sm` on((`p`.`id_produk` = `sm`.`id_produk`))) group by `p`.`id_produk`;

DROP TABLE IF EXISTS `view_user_role`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_user_role` AS select `u`.`username` AS `username`,`k`.`nama` AS `nama`,`r`.`nama_role` AS `nama_role` from ((`users` `u` join `karyawan` `k` on((`u`.`id_karyawan` = `k`.`id_karyawan`))) join `role` `r` on((`k`.`id_role` = `r`.`id_role`)));

-- 2026-05-14 08:20:11 UTC
