# Deploy ke Railway - PLUNG!

Panduan ini untuk simulasi sementara. Semua angka dan URL di bawah
adalah contoh, ganti dengan milik Anda.

---

##TL;DR

| Need | Isi |
|---|---|
| Build | `Dockerfile` (sudah dibuat, biarkan saja) |
| Database | Plugin **MySQL** |
| Penyimpanan foto | **Volume** di `/app/storage` |
| Domain | `https://NAMA-PROYEK.up.railway.app` (otomatis) |
| Biaya | Butuh **Pro $20/bulan** karena volume |

---

## 1. Push file deploy ke GitHub

Tiga file baru sudah dibuat di repo:

- `Dockerfile`
- `.dockerignore`
- `railway-env.example` (acuan variabel)

```bash
git add Dockerfile .dockerignore railway-env.example
git commit -m "tambahkan dockerfile & panduan deploy railway"
git push
```

Railway membaca `Dockerfile` otomatis, tidak perlu setting builder.

---

## 2. Buat project di Railway

1. Buka <https://railway.app> -> **New Project** -> **Deploy from GitHub repo**
2. Pilih repo `uptechdong/plung-app`
3. Railway otomatis membaca `Dockerfile` dan mulai build

Build pertama memakan waktu 3-6 menit (install ekstensi PHP + npm ci).

---

## 3. Tambah database MySQL

1. Di project Railway, klik **+ New** -> **Database** -> **MySQL**
2. Klik service MySQL itu -> tab **Variables** -> **Reference**

Catat nilai `DATABASE_URL`.

> **Penting:** salin isi `DATABASE_URL` ke variabel bernama **`DB_URL`**
> (bukan `DATABASE_URL`). `config/database.php:49` di proyek ini membaca
> `DB_URL`, bukan nama bawaan Laravel.

---

## 4. Pasang volume untuk foto

Tanpa langkah ini, **setiap foto profil dan bukti setoran yang
terupload akan hilang** begitu container di-restart. Volume bukan
pilihan tambahan melainkan keharusan, karena `SetoranController`
menulis foto lewat `->store('setoran', 'public')` ke disk lokal.

> ⚠️ Volume **hanya tersedia di plan Pro ($20/bulan)**. Kalau Anda
> mencoba di Hobby, aplikasi tetap jalan tapi foto tidak persisten.

1. Klik service aplikasi Anda -> tab **Volumes** -> **Add Volume**
2. Mount path: `/app/storage`

Variabel `FILESYSTEM_DISK=public` sudah mengarah ke sana, dan
`php artisan storage:link` dijalankan otomatis tiap start.

---

## 5. Isi environment variables

Klik service aplikasi -> tab **Variables** -> **New Variable**.
Salin dari `railway-env.example`.

Checklist minimum:

```
APP_NAME=PLUNG!
APP_ENV=production
APP_DEBUG=false
APP_KEY=<dari php artisan key:generate>
APP_URL=https://NAMA-PROYEK.up.railway.app
APP_LOCALE=id

DB_URL=<isi dari DATABASE_URL plugin>
DB_CONNECTION=mysql

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

LOG_LEVEL=warning
```

Buat `APP_KEY` di komputer Anda:

```bash
php artisan key:generate --show
```

---

## 6. Deploy pertama & cek log

Railway deploy otomatis setiap kali Anda push. Tab **Deployments** ->
pilih build terakhir -> **View Logs**.

Yang perlu terlihat:

```
INFO  Application running on http://0.0.0.0:8080
```

Kalau gagal, cek baris merah di log. Pesan yang paling sering muncul:

| Pesan | Cause | Solusi |
|---|---|---|
| `could not find driver` | `pdo_mysql` belum terinstall | `Dockerfile` sudah handles; rebuild |
| `Class not found` | autoloader belum optimised | `railway up --build` ulang |
| `SQLSTATE...Access denied` | `DB_URL` salah / belum diisi | Cek ulang langkah 3 |
| `No application encryption key` | `php artisan serve` jalan tanpa `--no-reload` sehingga variabel lingkungan tidak terbaca | `Dockerfile` sudah pakai `--no-reload`; rebuild |

---

## 7. Isi data awal

Migration hanya membuat tabel, isinya masih kosong. Proyek ini sengaja
tidak menyertakan seeder, jadi data awal diisi manual.

Data yang perlu ada agar aplikasi bisa dipakai:

| Tabel | Isi minimal |
|-------|-------------|
| `kategori_sampah` | 4 kategori beserta poin per unit dan satuannya |
| `rewards` | Katalog hadiah beserta harga poin dan stok |
| `users` | 1 akun pengurus + beberapa akun warga |

Gunakan **Shell di dashboard Railway** (project -> service aplikasi ->
tombol **Shell**) untuk mengisinya.

**Jangan pakai `railway shell` dari CLI.** Perintah itu berjalan di
komputer Anda dengan variabel Railway, bukan di dalam container, sehingga
domain internal tidak bisa dijangkau:

```
PDOException: getaddrinfo for mysql.railway.internal failed
```

### Nilai `status` yang valid

Kolom `setoran.status` bertipe enum dengan nilai:

```
diproses | diterima | ditolak
```

`pending` dan `disetujui` **sudah dihapus** oleh migration
`add_diproses_to_setoran_status_enum` dan `replace_disetujui_with_diterima`.

| Status | Arti |
|---|---|
| `diproses` | menunggu verifikasi petugas |
| `diterima` | sudah diverifikasi, dihitung di dashboard |
| `ditolak` | ditolak petugas |

Pastikan nilai yang di-insert sesuai daftar di atas. Kalau tidak, MySQL
memotongnya menjadi string kosong dan merusak perhitungan poin.

---

## 8. Update setelah demo selesai

Railway tidak bisa "dijual" per hari. Untuk menghentikan biaya:

1. Railway dashboard -> project Anda
2. **Settings** -> **Pause** (menghemat biaya tapi data disimpan)

Atau menghapus project altogether kalau tidak akan dipakai lagi.

Ingat: file di volume dan database **ikut terhapus** kalau project dihapus.

---

## Troubleshooting

### Foto tidak muncul (404)

`public/storage` belum ter-symlink. Cek via shell:

```bash
php artisan storage:link --force
ls -la public/storage
```

### Halaman tampil tanpa styling

`APP_URL` belum diisi atau tidak sama dengan domain Railway.
Laravel memakai `APP_URL` untuk membuat URL aset.

### Teks Indonesia berantakan di PDF laporan

Font Indonesia tidak ketemu. `Dockerfile` sudah pasang
`fonts-dejavu-core` dan `fonts-liberation`. Kalau masih bermasalah,
tambahkan baris ini di `resources/views/laporan/pdf.blade.php`:

```blade
<style>
    @font-face {
        font-family: 'DejaVu Sans';
        src: url('data-font/truetype/DejaVuSans.ttf');
    }
    body { font-family: 'DejaVu Sans', sans-serif; }
</style>
```

### Lambat saat banyak orang accessing bersamaan

`php artisan serve` memang server bawaan PHP. `Dockerfile` sudah
set `PHP_CLI_SERVER_WORKERS=8` supaya 8 permintaan bisa dilayani
bersamaan. Kalau masih kurang, naikkan angkanya.

### Upload foto besar ditolak

Batas di kode adalah 3 MB (`max:3072` di `AuthController` dan
`SetoranController`). Batas bawaan server adalah 4 MB, jadi foto
masih aman.












