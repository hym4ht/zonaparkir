<?php
/**
 * InfoParkir - Database Setup & Migration Script for MySQL
 * 
 * Script ini digunakan untuk membuat database MySQL dan mengimpor file schema
 * 'database_migration.sql'. Bekerja baik di local host maupun di dalam Docker container.
 */

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'infoparkir';

echo "=== InfoParkir MySQL Database Setup ===\n";
echo "Connecting to MySQL server at '$host' as '$user'...\n";

// Hubungkan ke server MySQL tanpa memilih database terlebih dahulu
$conn = @new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n\nSilakan periksa konfigurasi hostname, username, dan password database Anda.\n");
}

echo "Connected successfully to MySQL server.\n";

// Membuat database jika belum ada
$sql_db = "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8 COLLATE utf8_general_ci";
if ($conn->query($sql_db) === TRUE) {
    echo "Database `$db` created successfully or already exists.\n";
} else {
    die("Error creating database: " . $conn->error . "\n");
}

// Tutup koneksi sementara dan sambungkan langsung ke database target
$conn->close();

$conn = @new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection to database `$db` failed: " . $conn->connect_error . "\n");
}

// Membaca file migrasi SQL
$migration_file = __DIR__ . '/database_migration.sql';
if (!file_exists($migration_file)) {
    die("Migration file not found at: $migration_file\n");
}

$sql_content = file_get_contents($migration_file);

// Bersihkan komentar SQL dan pecah menjadi query individu
$sql_content = preg_replace('!/\*.*?\*/!s', '', $sql_content);
$lines = explode("\n", $sql_content);
$queries = [];
$current_query = "";

foreach ($lines as $line) {
    $trimmed = trim($line);
    if (empty($trimmed) || str_is_comment($trimmed)) {
        continue;
    }
    
    $current_query .= $line . "\n";
    
    if (substr($trimmed, -1) === ';') {
        $queries[] = trim($current_query);
        $current_query = "";
    }
}

if (!empty(trim($current_query))) {
    $queries[] = trim($current_query);
}

function str_is_comment($str) {
    return (substr($str, 0, 2) === '--') || (substr($str, 0, 1) === '#');
}

echo "Running " . count($queries) . " migration queries...\n";
$success_count = 0;
$fail_count = 0;

foreach ($queries as $i => $query) {
    if (empty($query)) continue;
    
    $snippet = str_replace("\n", " ", substr($query, 0, 60));
    if (strlen($query) > 60) {
        $snippet .= "...";
    }
    
    try {
        if ($conn->query($query)) {
            echo "Query " . ($i + 1) . " success: $snippet\n";
            $success_count++;
        } else {
            echo "Query " . ($i + 1) . " failed: $snippet\n";
            echo "Error: " . $conn->error . "\n";
            $fail_count++;
        }
    } catch (Throwable $e) {
        // Jika ALTER TABLE ADD COLUMN IF NOT EXISTS gagal karena error sintaks / kolom sudah ada, lewati dengan aman
        if (stripos($query, 'ADD COLUMN') !== false && stripos($query, 'updated_at') !== false) {
            echo "Query " . ($i + 1) . " skipped/ignored (updated_at alteration): " . $e->getMessage() . "\n";
            $success_count++;
        } else {
            echo "Query " . ($i + 1) . " failed with exception: $snippet\n";
            echo "Error: " . $e->getMessage() . "\n";
            $fail_count++;
        }
    }
}

echo "\nDatabase migration completed. Success: $success_count, Failed: $fail_count.\n";
echo "========================================\n";

$conn->close();
