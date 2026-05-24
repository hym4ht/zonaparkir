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
2. Pastikan port 8989 kosong, lalu jalankan docker compose:
   ```bash
   docker-compose up -d
   ```
3. Buka browser dan akses: `http://localhost:8989` (Atau IP VPS kamu dengan port 8989)

### Cara 3: Menggunakan XAMPP/Laragon
1. Pindahkan folder project ke dalam `htdocs` (XAMPP) atau `www` (Laragon).
2. Akses melalui browser: `http://localhost/namaproj`

---

## Setup & Seeder Database 💾

Secara *default*, aplikasi ini sudah dikonfigurasi untuk menggunakan **MySQL** (sesuai konfigurasi Docker terbaru). Jika dijalankan menggunakan Docker, aplikasi akan mencoba melakukan koneksi ke database MySQL Native di Host (VPS) via `host.docker.internal`.

### Menyiapkan MySQL
1. Buat database baru bernama `infoparkir` di VPS/MySQL kamu.
2. Impor file `database_migration.sql` ke dalam database tersebut.
3. (Opsional) Jika username/password database kamu bukan `root` dan kosong, kamu bisa menyesuaikan variabel `DB_USER` dan `DB_PASS` di dalam file `docker-compose.yml`.

### Menambahkan User Dummy (Seeder)
Untuk masuk ke sistem admin, kamu bisa men-generate data pengguna tambahan dengan menggunakan CLI CodeIgniter.
Jalankan perintah ini di Terminal (pastikan berada di *root* direktori project):

```bash
php index.php seeder user
```

**Daftar Akun yang Dihasilkan:**
| Username | Password | Keterangan |
| :--- | :--- | :--- |
| `admin` | `admin123` | Administrator Utama |
| `admin2` | `admin123` | Administrator Dua |
| `petugas1` | `petugas123` | Petugas Parkir Pagi |
| `petugas2` | `petugas123` | Petugas Parkir Malam |

*(Catatan: Jika kamu ingin menggunakan MySQL, silakan buat database baru di phpMyAdmin, impor file `database_migration.sql`, dan sesuaikan konfigurasi koneksinya di `application/config/database.php`)*.

---

## Struktur Folder Penting 📁
* `application/controllers/Seeder.php` : Logika untuk me-*generate* data user admin palsu.
* `application/database/` : Tempat penyimpanan file database SQLite (`infoparkir.sqlite`).
* `application/views/` : Kumpulan antarmuka UI Dashboard Admin & User.
* `setup_sqlite.php` : Skrip utilitas (*one-time run*) yang sebelumnya dipakai untuk inisiasi DB SQLite.
* `database_migration.sql` : Skrip _raw_ SQL cadangan jika kamu ingin migrasi ke MySQL.

---
**Powered by ESP32 IoT • Developed by Zona Parkir**
