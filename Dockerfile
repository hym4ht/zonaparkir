FROM php:8.2-apache

# Aktifkan modul Apache yang diperlukan
RUN a2enmod rewrite headers

# Install ekstensi yang dibutuhkan untuk MySQL
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy konfigurasi VirtualHost kustom (AllowOverride All, header file statis)
COPY infoparkir.conf /etc/apache2/sites-available/000-default.conf

# Pindah ke direktori web Apache
WORKDIR /var/www/html

# Copy semua file ke dalam container
COPY . /var/www/html/

# Atur permission
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R 775 /var/www/html/application/logs \
    && chmod -R 775 /var/www/html/application/cache
