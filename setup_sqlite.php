<?php

$dir = __DIR__ . '/application/database';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$db_file = $dir . '/infoparkir.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = <<<'EOD'
    CREATE TABLE IF NOT EXISTS `slot` (
        `id_slot`    INTEGER PRIMARY KEY AUTOINCREMENT,
        `kode_slot`  VARCHAR(5) NOT NULL UNIQUE,
        `status`     VARCHAR(10) DEFAULT 'kosong',
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    INSERT OR IGNORE INTO `slot` (`id_slot`, `kode_slot`, `status`) VALUES
        (1, 'A', 'kosong'),
        (2, 'B', 'kosong'),
        (3, 'C', 'kosong'),
        (4, 'D', 'kosong');

    CREATE TABLE IF NOT EXISTS `log_akses` (
        `id`    INTEGER PRIMARY KEY AUTOINCREMENT,
        `jenis` VARCHAR(10) NOT NULL,
        `waktu` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_waktu ON log_akses(waktu);
    CREATE INDEX IF NOT EXISTS idx_jenis ON log_akses(jenis);

    CREATE TABLE IF NOT EXISTS `parkir` (
        `id_parkir`    INTEGER PRIMARY KEY AUTOINCREMENT,
        `kode_slot`    VARCHAR(5),
        `waktu_masuk`  DATETIME,
        `waktu_keluar` DATETIME,
        `status`       VARCHAR(10) DEFAULT 'parkir'
    );
    CREATE INDEX IF NOT EXISTS idx_status ON parkir(status);
    CREATE INDEX IF NOT EXISTS idx_waktu_keluar ON parkir(waktu_keluar);

    CREATE TABLE IF NOT EXISTS `admin` (
        `id_admin`     INTEGER PRIMARY KEY AUTOINCREMENT,
        `username`     VARCHAR(50) NOT NULL UNIQUE,
        `password`     VARCHAR(255) NOT NULL,
        `nama_lengkap` VARCHAR(100),
        `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    INSERT OR IGNORE INTO `admin` (`id_admin`, `username`, `password`, `nama_lengkap`) VALUES
        (1, 'admin', '$2y$10$S8Zc1QYIe5bB11Lw/46xO.e5f9gYJ5G5qR9n5tQh7n1qX8wQe/W2O', 'Administrator InfoParkir');
EOD;

    $pdo->exec($sql);
    echo "Database SQLite berhasil dibuat di: " . $db_file . "\n";
    echo "Tabel dan data awal berhasil di-generate!\n";

} catch (PDOException $e) {
    echo "Gagal membuat database: " . $e->getMessage() . "\n";
}
