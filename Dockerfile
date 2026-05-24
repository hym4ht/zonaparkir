FROM php:8.2-apache

# Mengaktifkan Apache mod_rewrite agar URL CodeIgniter bekerja dengan baik (tanpa index.php)
RUN a2enmod rewrite

# Install ekstensi yang dibutuhkan untuk SQLite dan MySQL
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Pindah ke direktori web Apache
WORKDIR /var/www/html

# Copy semua file ke dalam container
COPY . /var/www/html/

# Atur permission yang sesuai untuk folder web (termasuk folder database SQLite agar bisa di-write)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/application/database \
    && chmod -R 775 /var/www/html/application/logs \
    && chmod -R 775 /var/www/html/application/cache
