# Deploy ke Vercel - PLUNG!

> **Baca dulu bagian [Keterbatasan](#keterbatasan) sebelum pindah.**
>
> Vercel BUKAN hosting Laravel biasa. Aplikasi ini perlu dua perubahan
> besar supaya bisa jalan di sana, dan satu layanan eksternal yang tetap
> wajib.

---

## TL;DR

| Kebutuhan | Solusi |
|---|---|
| Code aplikasi | Sudah diubah (disk foto jadi agnostic) |
| PHP runtime | `@vercel/php` (komunitas, sudah dikonfigurasi) |
| Foto profil dan bukti setoran | **WAJIB** object storage (R2 / S3) |
| Database MySQL | **WAJIB** MySQL dari luar Vercel |
| Session | Cookie (bukan database) |
| Antrean | `sync` (tidak ada worker di Vercel) |

---

## Keterbatasan

**1. Vercel tidak punya MySQL.**
Aplikasi ini butuh MySQL. Pilih salah satu: TiDB Serverless (free tier,
MySQL-compatible), Aiven, PlanetScale, atau Railway **hanya** untuk
MySQL-nya.

**2. Filesystem read-only.**
`->store('setoran', ...)` ke disk lokal akan error. Sudah diatasi dengan
memakai object storage.

**3. Batas ukuran request 4.5 MB.**
Kode menerima foto maksimal 3 MB (`max:3072`), jadi masih muat. Tapi
jalur base64 dari HP menambah sekitar 33 persen, jadi 3 MB jadi 4 MB,
masih aman.

**4. Execution time 10 detik** (Hobby, bisa dinaikkan sampai 60).
Butuh sekitar 500 ms per request. Ekspor PDF (`/laporan/export`) paling
berisiko, karena dompdf cukup berat.

**5. `@vercel/php` adalah runtime komunitas,** bukan bawaan Vercel.
Perilaku bisa saja berubah sewaktu-waktu.

---

## 1. Siapkan object storage

Bucket harus bisa **dibaca publik**, karena file ditambahkan dari server
lalu dibaca langsung oleh browser.

**Cloudflare R2** (free tier 10 GB, tanpa biaya egress):

1. Dashboard R2 -> **Create bucket**, misalnya `plung-foto`
2. **Settings** -> **Public access** -> **Connect custom domain**
3. Masukkan domain, misal `cdn.plunganda.com` (free)
4. **Manage R2 API Tokens** -> buat token dengan izin Object Read & Write
5. Catat: **Access Key ID**, **Secret Access Key**, dan **S3 endpoint**
   `https://<account-id>.r2.cloudflarestorage.com`

---

## 2. Deploy ke Vercel

1. <https://vercel.com> -> **Add New** -> **Project** -> import repo
   `uptechdong/plung-app`
2. Framework presets: pilih **Other**
3. **Biarkan** Build Command `npm run build` dan Output Directory kosong
4. **Install Command**:
   ```
   composer install --no-dev --optimize-autoloader && npm ci && npm run build
   ```
5. Deploy, lalu cek **Settings** -> **Domains** untuk custom domain

File `vercel.json` dan `api/index.php` sudah disiapkan di repo, jadi Vercel
akan membaca build config-nya sendiri.

---

## 3. Environment variables

Tambahkan di Vercel: project -> **Settings** -> **Environment Variables**.

### Wajib - identitas

```
APP_NAME=PLUNG!
APP_ENV=production
APP_DEBUG=false
APP_KEY=<hasil php artisan key:generate>
APP_URL=https://domain-anda.vercel.app
APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID
LOG_CHANNEL=stderr
LOG_LEVEL=warning
```

### Wajib - database (dari provider langkah 1)

```
DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

> `config/database.php` proyek ini membaca `DB_URL`, bukan `DATABASE_URL`
> seperti bawaan Laravel. Kalau provider memberi `DATABASE_URL`, salin
> nilainya ke `DB_URL`.

### Wajib - path yang harus bisa ditulis

```
VIEW_COMPILED_PATH=/tmp/views
APP_PACKAGES_CACHE=/tmp/packages.php
APP_SERVICES_CACHE=/tmp/services.php
APP_EVENTS_CACHE=/tmp/events.php
SESSION_DRIVER=cookie
CACHE_STORE=database
QUEUE_CONNECTION=sync
```

### Wajib - object storage

```
FOTO_DRIVER=s3
FOTO_KEY=<access key id>
FOTO_SECRET=<secret access key>
FOTO_BUCKET=plung-foto
FOTO_ENDPOINT=https://<account-id>.r2.cloudflarestorage.com
FOTO_URL=https://cdn.plunganda.com
FOTO_PATH_STYLE=true
FOTO_REGION=auto
```

---

## 4. Seed data awal (WAJIB)

Vercel tidak punya shell, jadi jalankan dari **komputer Anda**:

```powershell
# Arahkan ke database Vercel lalu isi data
$env:DB_HOST="<host mysql>"; $env:DB_PORT="3306"
$env:DB_DATABASE="<database>"; $env:DB_USERNAME="<user>"; $env:DB_PASSWORD="<password>"
php artisan migrate --force
php artisan db:seed --force
```

`db:seed` hanya **sekali**. `DatabaseSeeder` memakai `::create()`, jadi
mengulanginya akan error duplicate key.

---

## Troubleshooting

### 500 dengan `MissingAppKeyException`

`VIEW_COMPILED_PATH` atau `APP_PACKAGES_CACHE` belum diisi, sehingga
Laravel gagal menulis ke folder yang read-only. Isi blok path di atas.

### `bootstrap/cache directory must be present and writable`

Tambahkan `APP_PACKAGES_CACHE` dan `APP_SERVICES_CACHE` ke `/tmp`.

### Foto tidak muncul (gambar rusak)

`FOTO_URL` salah, atau bucket belum dibuat public. Cek buka langsung
URL dari `Storage::disk('foto')->url(...)` di browser.

### `Class "Aws\S3\..." not found`

`composer install` jalan tanpa paket AWS. Pastikan Install Command
di langkah 2 sudah benar, atau tambahkan `aws/aws-sdk-php` ke
`composer.json`.

### Halaman tampil tanpa styling

`public/build/manifest.json` tidak ikut ter-deploy. Pastikan Build
Command benar-benar `npm run build`.

### Login sukses tapi refresh kesal

Session disimpan di cookie. Pastikan `SESSION_DRIVER=cookie` (bukan
`database`) dan `SESSION_ENCRYPT=false`.

---

## Kembali ke hosting biasa

Semua perubahan kode bersifat backwards-compatible. Kalau nanti pindah
ke hosting dengan disk (shared hosting, Railway, VPS), cukup kembalikan:

```
FOTO_DRIVER=local
FOTO_URL=<APP_URL>/storage
```

Tidak ada perubahan kode yang perlu dibatalkan.