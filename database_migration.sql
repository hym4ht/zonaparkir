-- ============================================================
-- MIGRATION: Sistem Informasi Parkir - ESP32 Integration
-- Jalankan script ini di phpMyAdmin atau MySQL CLI
-- Database: infoparkir
-- ============================================================

USE infoparkir;

-- ============================================================
-- 1. Pastikan tabel slot sudah ada (buat jika belum)
-- ============================================================
CREATE TABLE IF NOT EXISTS `slot` (
    `id_slot`    INT AUTO_INCREMENT PRIMARY KEY,
    `kode_slot`  VARCHAR(5) NOT NULL UNIQUE,
    `status`     ENUM('kosong','terisi') DEFAULT 'kosong',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- 2. Tambah kolom updated_at ke tabel slot (jika sudah ada tapi belum punya kolom ini)
-- ============================================================
ALTER TABLE `slot`
    ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- ============================================================
-- 3. Isi data awal slot A, B, C, D (jika belum ada)
-- ============================================================
INSERT IGNORE INTO `slot` (`kode_slot`, `status`) VALUES
    ('A', 'kosong'),
    ('B', 'kosong'),
    ('C', 'kosong'),
    ('D', 'kosong');

-- ============================================================
-- 4. Tabel log_akses (BARU) — log setiap kendaraan masuk/keluar gate
-- ============================================================
CREATE TABLE IF NOT EXISTS `log_akses` (
    `id`    INT AUTO_INCREMENT PRIMARY KEY,
    `jenis` ENUM('masuk','keluar') NOT NULL,
    `waktu` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_waktu (waktu),
    INDEX idx_jenis (jenis)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- 5. Tabel parkir (pastikan ada — jika sudah ada, skip)
-- ============================================================
CREATE TABLE IF NOT EXISTS `parkir` (
    `id_parkir`    INT AUTO_INCREMENT PRIMARY KEY,
    `kode_slot`    VARCHAR(5),
    `waktu_masuk`  DATETIME,
    `waktu_keluar` DATETIME,
    `status`       ENUM('parkir','keluar') DEFAULT 'parkir',
    INDEX idx_status (status),
    INDEX idx_waktu_keluar (waktu_keluar)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- 6. Tabel admin (BARU) — untuk autentikasi dinamis
-- ============================================================
CREATE TABLE IF NOT EXISTS `admin` (
    `id_admin`     INT AUTO_INCREMENT PRIMARY KEY,
    `username`     VARCHAR(50) NOT NULL UNIQUE,
    `password`     VARCHAR(255) NOT NULL,
    `nama_lengkap` VARCHAR(100),
    `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Isi data awal admin (username: admin, password: admin123)
-- Hash bcrypt dari 'admin123' adalah: $2y$10$S8Zc1QYIe5bB11Lw/46xO.e5f9gYJ5G5qR9n5tQh7n1qX8wQe/W2O
INSERT IGNORE INTO `admin` (`id_admin`, `username`, `password`, `nama_lengkap`) VALUES
    (1, 'admin', '$2y$10$S8Zc1QYIe5bB11Lw/46xO.e5f9gYJ5G5qR9n5tQh7n1qX8wQe/W2O', 'Administrator InfoParkir');

-- ============================================================
-- Selesai! Tabel yang diperlukan sudah siap.
-- ============================================================
