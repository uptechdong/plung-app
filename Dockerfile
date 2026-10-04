# ============================================================
#  DEPLOY RAILWAY - PLUNG! Sistem Pemilahan Sampah Mandiri
# ============================================================
#  Pakai: railway up  (atau hubungkan repo ini di dashboard Railway)
#  Detail langkahnya ada di RAILWAY.md
#
#  Kenapa Dockerfile, bukan Nixpacks bawaan Railway:
#  Aplikasi ini butuh ekstensi PHP yang tidak ada di image default,
#  yaitu pdo_mysql (konek MySQL) dan gd (dompdf merender gambar).
#  Dengan Dockerfile, isi container pasti sama dengan yang ditulis di sini.
#
#  WAJIB: pasang VOLUME di mount path /app/storage.
#  Tanpa volume, setiap foto profil dan bukti setoran yang terupload akan
#  hilang begitu container di-restart.
# ============================================================


# ------------------------------------------------------------
# Tahap 1 - Build aset frontend (Vite + Tailwind v4)
# ------------------------------------------------------------
# Tailwind v4 memindai sumber daya secara otomatis untuk menemukan
# nama kelas yang dipakai. Karena kelas di aplikasi ini sebagian besar
# ditulis di dalam file PHP (mis. warna badge di dalam controller),
# seluruh isi project ikut disalin ke tahap ini, bukan hanya resources/.
FROM node:22-slim AS frontend

WORKDIR /build

# Layer terpisah agar npm ci tidak diulang tiap ada perubahan kode.
COPY package.json package-lock.json ./
RUN npm ci

COPY . .

# Menghasilkan /build/public/build (berisi assets/ + manifest.json).
RUN npm run build


# ------------------------------------------------------------
# Tahap 2 - Aplikasi PHP
# ------------------------------------------------------------
FROM php:8.3-cli

# Pustaka sistem yang dibutuhkan ekstensi PHP di bawah.
# libonig-dev WAJIB ada: mbstring memakai mesin regex oniguruma, dan tanpa
# paket ini `docker-php-ext-install mbstring` gagal saat configure.
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libonig-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libicu-dev \
        zlib1g-dev \
        fonts-dejavu-core \
        fonts-liberation \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        gd \
        zip \
        bcmath \
        intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# --- Dependensi PHP (layer di-cache agar build berikutnya cepat) ---
# .env.example WAJIB ikut di-COPY sini, dipakai pada langkah di bawah.
COPY composer.json composer.lock .env.example ./
# .env.example disalin jadi .env sementara supaya `artisan package:discover`
# (yang dijalankan oleh script post-autoload-dump) bisa boot saat build.
# APP_KEY asli datang dari Railway, bukan dari file ini, dan .env ini
# dihapus lagi di bawah supaya tidak menimpa variabel Railway.
RUN cp .env.example .env \
    && composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --no-scripts \
        --no-progress

# --- Kode aplikasi ---
COPY . .

# --- Aset frontend hasil build tahap 1 ---
COPY --from=frontend /build/public/build ./public/build

# --- Finalisasi autoloader ---
# composer dump-autoload otomatis menjalankan script post-autoload-dump,
# yaitu `php artisan package:discover`, jadi tidak perlu dipanggil terpisah.
RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && rm -f .env

# --- Folder yang wajib ada dan bisa ditulis ---
# .dockerignore mengecualikan isi storage/framework, sehingga foldernya
# ikut hilang dari image. Akibatnya realpath() di config view mengembalikan
# false dan setiap render Blade gagal dengan:
#     "Please provide a valid cache path."
# Folder-folder ini dibuat di sini supaya ikut ter-copy ke volume Railway
# saat volume pertama kali dibuat, dan dibuat ulang lagi di CMD.
RUN mkdir -p \
        storage/framework/views \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/logs \
        storage/app/public \
        bootstrap/cache \
    && printf '*\n!.gitignore\n' > storage/framework/views/.gitignore \
    && printf '*\n!.gitignore\n' > storage/framework/cache/.gitignore \
    && printf '*\n!.gitignore\n' > storage/framework/sessions/.gitignore \
    && printf '*\n!.gitignore\n' > storage/logs/.gitignore

# ------------------------------------------------------------
# Konfigurasi runtime
# ------------------------------------------------------------
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_LEVEL=warning \
    PORT=8080

# `php artisan serve` memakai server bawaan PHP yang hanya melayani SATU
# permintaan pada satu waktu. Kalau petugas dan warga membuka aplikasi
# bersamaan, permintaan kedua akan ikut menunggu. Variabel ini membuat
# PHP menjalankan beberapa worker proses.
ENV PHP_CLI_SERVER_WORKERS=8

EXPOSE 8080

# storage:link -> symlink public/storage -> storage/app/public
# migrate     -> menyiapkan tabel di database Railway (aman diulang)
# serve       -> menjalankan aplikasi di 0.0.0.0:$PORT
#
# --no-reload WAJIB ada. Tanpa flag itu PHP mengabaikan
# PHP_CLI_SERVER_WORKERS, hanya menjalankan satu server, dan variabel
# lingkungan tidak terbaca sehingga aplikasi gagal dengan
# MissingAppKeyException.
#
# migrate --force aman diulang; TIDAK memakai migrate:fresh karena itu
# akan menghapus semua data setiap kali container start.
# db:seed sengaja tidak ada di sini. Proyek ini tidak menyertakan seeder,
# sehingga tidak idempoten, dan datanya sudah dibuat.
# Folder runtime selalu dipastikan ada. Volume Railway di /app/storage
# bisa ikut terisi ulang dari image, jadi jangan andalkan isi volume.
CMD ["sh", "-c", "mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs storage/app/public bootstrap/cache; php artisan storage:link --force || true; php artisan migrate --force --no-interaction; php artisan serve --host=0.0.0.0 --port=${PORT} --no-reload"]
