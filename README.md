# Sistem Informasi Parkir (Zona Parkir) 🚗

Aplikasi pemantauan slot parkir berbasis web secara *real-time* yang terintegrasi dengan perangkat IoT (Sensor ESP32). Dibangun menggunakan framework **CodeIgniter 3**, mendukung database SQLite untuk kemudahan *development*, serta dilengkapi dengan *Docker environment*.

## Fitur Utama ✨
* **Live Monitoring IoT**: Memantau ketersediaan slot parkir secara *real-time*.
* **User Dashboard**: Antarmuka responsif dan dinamis untuk pengguna memantau slot yang kosong.
* **Admin Dashboard**: Sistem manajemen data log parkir dan manajemen admin.
* **Database Dinamis**: Dukungan bawaan menggunakan **SQLite** (tanpa perlu install server database eksternal) atau MySQL/MariaDB.
* **Docker Ready**: Tersedia `Dockerfile` dan `docker-compose.yml` untuk *deployment* super cepat.

---

## Prasyarat 🛠️
Pilih salah satu metode instalasi/menjalankan aplikasi di bawah ini:
* **Metode Lokal:** PHP 8.0+ dan ekstensi `sqlite3` aktif di `php.ini`.
* **Metode Docker:** Docker Engine & Docker Compose.

---

## Cara Menjalankan Aplikasi 🚀

### Cara 1: Menggunakan PHP Built-in Server (Lokal)
1. Buka Terminal/Command Prompt di dalam folder project ini.
2. Jalankan perintah:
   ```bash
   php -S localhost:8000
   ```
3. Buka browser dan akses URL: `http://localhost:8000`

### Cara 2: Menggunakan Docker
Jika kamu sudah memiliki Docker terinstal di komputermu, cukup jalankan:
1. Buka Terminal di folder project.
2. Jalankan docker compose:
   ```bash
   docker-compose up -d
   ```
3. Buka browser dan akses: `http://localhost:8000`

### Cara 3: Menggunakan XAMPP/Laragon
1. Pindahkan folder project ke dalam `htdocs` (XAMPP) atau `www` (Laragon).
2. Akses melalui browser: `http://localhost/namaproj`

---

## Setup & Seeder Database 💾

Aplikasi ini mendukung dua jenis database: **SQLite** (bawaan development) dan **MySQL/MariaDB** (untuk production/Docker).

### A. Menggunakan MySQL (Lokal atau Docker)
Jika Anda menggunakan MySQL, ikuti langkah berikut untuk menginisialisasi database dan tabel:

#### 1. Inisialisasi Database & Migrasi
* **Jika berjalan di Lokal:**
  Pastikan kredensial MySQL di `application/config/database.php` sudah benar, lalu jalankan:
  ```bash
  php setup_mysql.php
  ```
* **Jika berjalan di Docker:**
  Jalankan perintah ini untuk melakukan migrasi langsung ke database di dalam container:
  ```bash
  docker exec -it infoparkir_web php setup_mysql.php
  ```

#### 2. Menjalankan Seeder (Membuat Akun Admin & Petugas)
* **Jika berjalan di Lokal:**
  ```bash
  php index.php seeder user
  ```
* **Jika berjalan di Docker:**
  ```bash
  docker exec -it infoparkir_web php index.php seeder user
  ```

### B. Menggunakan SQLite (Default Development)
Secara *default*, file database SQLite sudah tersedia di `application/database/infoparkir.sqlite`. Anda hanya perlu menjalankan seeder jika diperlukan:
```bash
php index.php seeder user
```

**Daftar Akun yang Dihasilkan oleh Seeder:**
| Username | Password | Keterangan |
| :--- | :--- | :--- |
| `admin` | `admin123` | Administrator Utama |
| `admin2` | `admin123` | Administrator Dua |
| `petugas1` | `petugas123` | Petugas Parkir Pagi |
| `petugas2` | `petugas123` | Petugas Parkir Malam |

---

## Struktur Folder Penting 📁
* `application/controllers/Seeder.php` : Logika untuk me-*generate* data user admin palsu.
* `application/database/` : Tempat penyimpanan file database SQLite (`infoparkir.sqlite`).
* `application/views/` : Kumpulan antarmuka UI Dashboard Admin & User.
* `setup_sqlite.php` : Skrip utilitas (*one-time run*) yang sebelumnya dipakai untuk inisiasi DB SQLite.
* `database_migration.sql` : Skrip _raw_ SQL cadangan jika kamu ingin migrasi ke MySQL.

---
**Powered by ESP32 IoT • Developed by Zona Parkir**
